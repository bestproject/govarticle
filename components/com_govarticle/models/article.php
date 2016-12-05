<?php

/**
 * @package     GovArticle
 * @subpackage  com_govarticle
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on com_content from Joomla!
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * GovArticle Component Article Model
 *
 * @since  1.5
 */
class GovArticleModelArticle extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_govarticle.article';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('article.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_govarticle')) && (!$user->authorise('core.edit', 'com_govarticle')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the article.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$user = JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('article.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select(
						$this->getState(
							'item.select', 'a.id, a.asset_id, a.title, a.alias, a.introtext, a.fulltext, ' .
							// If badcats is not null, this means that the article is inside an unpublished category
							// In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
							'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
							'a.catid, a.created, a.created_by, a.created_by_alias, ' .
							'a.produced_by, a.produced_by_position, ' .
							// Use created if modified is 0
							'CASE WHEN a.modified = ' . $db->quote($db->getNullDate()) . ' THEN a.created ELSE a.modified END as modified, ' .
							'a.modified_by, a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, ' .
							'a.images, a.urls, a.attribs, a.version, a.ordering, ' .
							'a.metakey, a.metadesc, a.access, a.hits, a.metadata, a.featured, a.language, a.xreference'
						)
					);
				$query->from('#__govarticle AS a');

				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
					->join('LEFT', '#__categories AS c on c.id = a.catid');

				// Join on user table.
				$query->select('u.name AS author')
					->join('LEFT', '#__users AS u on u.id = a.created_by');

				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
					->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

				// Join on voting table
				$query->select('ROUND(v.rating_sum / v.rating_count, 0) AS rating, v.rating_count as rating_count')
					->join('LEFT', '#__govarticle_rating AS v ON a.id = v.content_id')

					->where('a.id = ' . (int) $pk);

				if ((!$user->authorise('core.edit.state', 'com_govarticle')) && (!$user->authorise('core.edit', 'com_govarticle')))
				{
					// Filter by start and end dates.
					$nullDate = $db->quote($db->getNullDate());
					$date = JFactory::getDate();

					$nowDate = $db->quote($date->toSql());

					$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
						->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
				}

				// Join to check for category published state in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the article state
				$subquery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
				$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
				$subquery .= 'WHERE parent.extension = ' . $db->quote('com_govarticle');
				$subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
				$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');

				if (is_numeric($published))
				{
					$query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					return JError::raiseError(404, JText::_('COM_GOVARTICLE_ERROR_ARTICLE_NOT_FOUND'));
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived)))
				{
					return JError::raiseError(404, JText::_('COM_GOVARTICLE_ERROR_ARTICLE_NOT_FOUND'));
				}
				
				$this->bindAttachments($data);
				$this->bindHistory($data);
				
				// Convert parameter fields to objects.
				$registry = new Registry;
				$registry->loadString($data->attribs);

				$data->params = clone $this->getState('params');
				$data->params->merge($registry);

				$registry = new Registry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

				// Technically guest could edit an article, but lets not check that to improve performance a little.
				if (!$user->get('guest'))
				{
					$userId = $user->get('id');
					$asset = 'com_govarticle.article.' . $data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$data->params->set('access-edit', true);
					}

					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by)
						{
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($data->catid == 0 || $data->category_access === null)
					{
						$data->params->set('access-view', in_array($data->access, $groups));
					}
					else
					{
						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param   integer  $pk  Optional primary key of the article to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('article.id');

			$table = JTable::getInstance('Content', 'GovArticleTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}

	/**
	 * Save user vote on article
	 *
	 * @param   integer  $pk    Joomla Article Id
	 * @param   integer  $rate  Voting rate
	 *
	 * @return  boolean          Return true on success
	 */
	public function storeVote($pk = 0, $rate = 0)
	{
		if ($rate >= 1 && $rate <= 5 && $pk > 0)
		{
			$userIP = $_SERVER['REMOTE_ADDR'];

			// Initialize variables.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			// Create the base select statement.
			$query->select('*')
				->from($db->quoteName('#__govarticle_rating'))
				->where($db->quoteName('content_id') . ' = ' . (int) $pk);

			// Set the query and load the result.
			$db->setQuery($query);

			// Check for a database error.
			try
			{
				$rating = $db->loadObject();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());

				return false;
			}

			// There are no ratings yet, so lets insert our rating
			if (!$rating)
			{
				$query = $db->getQuery(true);

				// Create the base insert statement.
				$query->insert($db->quoteName('#__govarticle_rating'))
					->columns(array($db->quoteName('content_id'), $db->quoteName('lastip'), $db->quoteName('rating_sum'), $db->quoteName('rating_count')))
					->values((int) $pk . ', ' . $db->quote($userIP) . ',' . (int) $rate . ', 1');

				// Set the query and execute the insert.
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JError::raiseWarning(500, $e->getMessage());

					return false;
				}
			}
			else
			{
				if ($userIP != ($rating->lastip))
				{
					$query = $db->getQuery(true);

					// Create the base update statement.
					$query->update($db->quoteName('#__govarticle_rating'))
						->set($db->quoteName('rating_count') . ' = rating_count + 1')
						->set($db->quoteName('rating_sum') . ' = rating_sum + ' . (int) $rate)
						->set($db->quoteName('lastip') . ' = ' . $db->quote($userIP))
						->where($db->quoteName('content_id') . ' = ' . (int) $pk);

					// Set the query and execute the update.
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						JError::raiseWarning(500, $e->getMessage());

						return false;
					}
				}
				else
				{
					return false;
				}
			}

			return true;
		}

		JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('COM_GOVARTICLE_INVALID_RATING', $rate), "JModelArticle::storeVote($rate)");

		return false;
	}

	protected function bindAttachments($data) {
		
		// Get article
		$article = $this->getTable('Content','GovArticleTable');

		// If this is new article or doesn't exist return empty list
		if( !$article->load((int)$this->getState('article.id')) ) {
			$data->attachments = array();
			
			return;
		}

		// Try to find a version in article history
		$version = $this->getVersionFromData($article);
		
		// If version was found, this article has no attachments
		if( $version===false ) {
			$data->attachments = array();
			
			return;
		}
		
		// Prepare attachments query
		$query = $this->_db->getQuery(true);
		$query->select('f.*,a.content_id');
		$query->from('#__govarticle_files AS f');
		$query->leftJoin('#__govarticle_attachments AS a ON a.id=f.id');
		$query->select('u.name AS `created_by_name`');
		$query->leftJoin('#__users AS u ON u.id=f.created_by');
		$query->where(array(
			'a.content_id='.(int)$article->id,
			'f.version='.(int)$version
		));
		
		// Execute query
		$this->_db->setQuery($query);
		$attachments = $this->_db->loadObjectList();
		
		// Return attachments
		if( is_array($attachments) ) {
			
			$data->attachments = $attachments;
			
			// Add attachment download url and file size with extension
			foreach( $data->attachments AS $attachment ) {
				
				// Download URL
				$attachment->download_url = JRoute::_('index.php?option=com_govarticle&task=article.downloadAttachment&id='.$attachment->id.'&version='.$attachment->version);
				
				// File size
				$attachment->size = $this->getHumanReadableFileSize(filesize(JPATH_SITE.'/media/govarticle/'.$attachment->content_id.'/'.$attachment->filename_internal));

				// Add file extension
				$attachment->extension = pathinfo($attachment->filename, PATHINFO_EXTENSION);
			}
			
			return;
			
		} else {
			
			$data->attachments = array();
			
			return;
		}

	}
	
	/**
	 * Return human readable file size.
	 * 
	 * @param    Integer   $size   File size in bytes
	 * 
	 * @return   String
	 */
	protected function getHumanReadableFileSize($size) {

		if( ($size >= 1<<30)) {
			
			return number_format($size/(1<<30),2)." GB";
		}
		
		if( ($size >= 1<<20)){
			
			return number_format($size/(1<<20),2)." MB";
			
		}
		
		if( ($size >= 1<<10)){
			
			return number_format($size/(1<<10),2)." KB";
			
		}
		
		return number_format($size)." bytes";
	}
	
	/**
	 * Bind a article history to article object.
	 * 
	 * @param   JTable   $article   Article object.
	 * 
	 * @return   boolean
	 */
	protected function getVersionFromData($article) {
		
		// If $data is array, convert it to string
		if( is_array($article) ) {
			$data = json_encode($article);
		} elseif( $article instanceof JTable ) {
			$data = json_encode($article->getProperties(true));
		}

		// Get history table class
		$historyTable = JTable::getInstance('Contenthistory', 'JTable');
		
		// get content type table class
		$typeTable = JTable::getInstance('Contenttype', 'JTable');
		$typeTable->load(array('type_alias' => 'com_govarticle.article'));
		
		// Get data hash
		$sha1 = $historyTable->getSha1($data, $typeTable);
		
		// If hash content found with hash, return it
		if($historyTable->load(array(
			'sha1_hash'=>$sha1,
			'ucm_type_id'=>$typeTable->type_id
		))) {
			
			return $historyTable->version_id;
			
		// If not found return false;
		} else {
			return false;
		}
	}
	
	/**
	 * Bind article history to article object.
	 * 
	 * @param   JTable   $article   Article object.
	 */
	protected function bindHistory(&$article) {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		
		// Prepare query
		$query->select(
			'l.*, a.title AS `article_title`,u.name AS `user_name`,hn.version_note,'.
			'hn.save_date AS `version_new_date`,ho.save_date AS `version_old_date`'
		);
		$query->from('#__govarticle_log AS l');
		$query->leftJoin('#__govarticle AS a ON a.id=l.content_id');
		$query->leftJoin('#__users AS u ON u.id=l.created_by');
		$query->leftJoin('#__ucm_history AS hn ON hn.version_id=l.version_new');
		$query->leftJoin('#__ucm_history AS ho ON ho.version_id=l.version_old');
		
		// Filter results
		$query->where('l.content_id='.(int)$article->id);
		
		// Order data
		$query->order('l.created ASC');
		
		// Run query
		$db->setQuery($query);
		
		$article->history = $db->loadObjectList();
		$this->bindAttachmentsHistory($article->history);
	}
	
	/**
	 * Binds attachments history into article history array.
	 * 
	 * @param   Array   $history   Article history array.
	 * 
	 * @return  boolean
	 */
	protected function bindAttachmentsHistory(&$history) {
		
		// No history so no attachments to bind
		if( empty($history) ) {
			
			return false;
			
		}
		
		// Get article ID
		$article_id = current($history)->content_id;
		
		
		// Load article attachmetns from all versions
		$query = $this->_db->getQuery(true);
		$query->select('f.*');
		$query->from('#__govarticle_attachments AS a');
		$query->leftJoin('#__govarticle_files AS f ON f.id=a.id');
		$query->where('a.content_id='.(int)$article_id);
		$query->order('f.version ASC');
		
		$this->_db->setQuery($query);
		
		$attachments = $this->_db->loadObjectList();

		// Group attachments by versions
		$attachments_grouped = array();
		foreach( $attachments AS $attachment ) {
			if( !isset($attachments_grouped[$attachment->version]) ) {
				$attachments_grouped[$attachment->version] = array();
			}
			
			$attachments_grouped[$attachment->version][$attachment->id] = $attachment;
		}

		// Compare each article version attachments
		foreach( $history As $idx=>&$version ) {
			$version->attachments = array('added'=>array(),'removed'=>array());
			
			// Previous entry exists so we can compare data
			if ( isset($history[$idx-1]) AND !empty($attachments_grouped[$history[$idx-1]->version_new]) ) {
				
				// Old version ID
				$old_version = $history[$idx-1]->version_new;
				$new_version = $version->version_new;
				
				// Get ID's of old and new version
				$attachments_old = array_keys($attachments_grouped[$old_version]);
				$attachments_new = array_keys($attachments_grouped[$new_version]);
				
				// Find which attachments where removed, which added
				$attachments_removed = array_diff($attachments_old, $attachments_new);
				$attachments_added = array_diff($attachments_new, $attachments_old);
				
				// Add data about removed attachments
				foreach( $attachments_removed AS $attachment_id ) {
					$version->attachments['removed'][] = $attachments_grouped[$old_version][$attachment_id];
				}
				
				// Add data about added attachments
				foreach( $attachments_added AS $attachment_id ) {
					$version->attachments['added'][] = $attachments_grouped[$new_version][$attachment_id];
				}
				
			// If this is first entry with attachments, add them
			} elseif ( isset($attachments_grouped[$version->version_new]) ) {
				
				$version->attachments['added'] = $attachments_grouped[$version->version_new];
				
			}
		}

//		array_map(function($ver){
//			var_dump($ver->attachments);
//		}, $history);
//		die;
		
		return true;
	}
}
