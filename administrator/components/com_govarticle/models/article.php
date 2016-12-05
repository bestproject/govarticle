<?php

/**
 * @package     GovArticle.Administrator
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
use Joomla\Utilities\ArrayHelper;

JLoader::register('GovArticleHelper', JPATH_ADMINISTRATOR . '/components/com_govarticle/helpers/content.php');

/**
 * Item Model for an Article.
 *
 * @since  1.6
 */
class GovArticleModelArticle extends JModelAdmin
{
	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_GOVARTICLE';

	/**
	 * The type alias for this content type (for example, 'com_govarticle.article').
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_govarticle.article';

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId = (int) $value;

		$newIds = array();

		if (!parent::checkCategoryId($categoryId))
		{
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $this->table->alias, $this->table->title);
			$this->table->title = $data['0'];
			$this->table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// Reset hits because we are making a copy
			$this->table->hits = 0;

			// Unpublish because we are making a copy
			$this->table->state = 0;

			// New category ID
			$this->table->catid = $categoryId;

			// TODO: Deal with ordering?
			// $table->ordering	= 1;

			// Get the featured state
			$featured = $this->table->featured;

			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());
				return false;
			}

			parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());
				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$pk] = $newId;

			// Check if the article was featured and update the #__govarticle_frontpage table
			if ($featured == 1)
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__govarticle_frontpage'))
					->values($newId . ', 0');
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return false;
			}
			$user = JFactory::getUser();

			return $user->authorise('core.delete', 'com_govarticle.article.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing article.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_govarticle.article.' . (int) $record->id);
		}
		// New article, so check against the category.
		elseif (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_govarticle.category.' . (int) $record->catid);
		}
		// Default to component settings if neither article nor category known.
		else
		{
			return parent::canEditState('com_govarticle');
		}
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		// Set the publish date to now
		$db = $this->getDbo();

		if ($table->state == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = JFactory::getDate()->toSql();
		}

		if ($table->state == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $db->getNullDate();
		}

		// Increment the content version number.
		$table->version++;

		// Reorder the articles within the category so the new article is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Content', $prefix = 'GovArticleTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new Registry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new Registry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			// Convert the images field to an array.
			$registry = new Registry;
			$registry->loadString($item->images);
			$item->images = $registry->toArray();

			// Convert the urls field to an array.
			$registry = new Registry;
			$registry->loadString($item->urls);
			$item->urls = $registry->toArray();

			$item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;

			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_govarticle.article');
			}
		}

		// Load associated content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_govarticle', '#__govarticle', 'com_govarticle.item', $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_govarticle.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('article.id'))
		{
			$id = $this->getState('article.id');

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_govarticle.article.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_govarticle')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Prevent messing with article language and category when editing existing article with associations
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		// Check if article is associated
		if ($this->getState('article.id') && $app->isSite() && $assoc)
		{
			$associations = JLanguageAssociations::getAssociations('com_govarticle', '#__govarticle', 'com_govarticle.item', $id);

			// Make fields read only
			if (!empty($associations))
			{
				$form->setFieldAttribute('language', 'readonly', 'true');
				$form->setFieldAttribute('catid', 'readonly', 'true');
				$form->setFieldAttribute('language', 'filter', 'unset');
				$form->setFieldAttribute('catid', 'filter', 'unset');
			}
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_govarticle.edit.article.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
			if ($this->getState('article.id') == 0)
			{
				$filters = (array) $app->getUserState('com_govarticle.articles.filter');
				$data->set('state', $app->input->getInt('state', (!empty($filters['published']) ? $filters['published'] : null)));
				$data->set('catid', $app->input->getInt('catid', (!empty($filters['category_id']) ? $filters['category_id'] : null)));
				$data->set('language', $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null)));
				$data->set('access', $app->input->getInt('access', (!empty($filters['access']) ? $filters['access'] : JFactory::getConfig()->get('access'))));
				$data->set('created_by', JFactory::getUser()->id);
			}
		}

		$this->preprocessData('com_govarticle.article', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$this->preprocessAttachments($data);
		
		$input = JFactory::getApplication()->input;
		$filter  = JFilterInput::getInstance();
		
		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
		}

		if (isset($data['created_by_alias']))
		{
			$data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
		}

		if (isset($data['images']) && is_array($data['images']))
		{
			$registry = new Registry;
			$registry->loadArray($data['images']);
			$data['images'] = (string) $registry;
		}

		if (isset($data['urls']) && is_array($data['urls']))
		{
			foreach ($data['urls'] as $i => $url)
			{
				if ($url != false && ($i == 'urla' || $i == 'urlb' || $i == 'urlc'))
				{
					$data['urls'][$i] = JStringPunycode::urlToPunycode($url);
				}
			}

			$registry = new Registry;
			$registry->loadArray($data['urls']);
			$data['urls'] = (string) $registry;
		}

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['title'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] == 0))
		{
			if ($data['alias'] == null)
			{
				if (JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['title']);
				}

				$table = JTable::getInstance('Content', 'GovArticleTable');

				if ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])))
				{
					$msg = JText::_('COM_GOVARTICLE_SAVE_WARNING');
				}

				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['alias'] = $alias;

				if (isset($msg))
				{
					JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		if (parent::save($data))
		{

			if (isset($data['featured']))
			{
				$this->featured($this->getState($this->getName() . '.id'), $data['featured']);
			}

			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JError::raiseNotice(403, JText::_('COM_GOVARTICLE_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_govarticle.item'))
					->where('id IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);

					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $id)
					{
						$query->values($id . ',' . $db->quote('com_govarticle.item') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_GOVARTICLE_NO_ITEM_SELECTED'));

			return false;
		}

		$table = $this->getTable('Featured', 'GovArticleTable');

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
						->update($db->quoteName('#__govarticle'))
						->set('featured = ' . (int) $value)
						->where('id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();

			if ((int) $value == 0)
			{
				// Adjust the mapping table.
				// Clear the existing features settings.
				$query = $db->getQuery(true)
							->delete($db->quoteName('#__govarticle_frontpage'))
							->where('content_id IN (' . implode(',', $pks) . ')');
				$db->setQuery($query);
				$db->execute();
			}
			else
			{
				// First, we find out which of our new featured articles are already featured.
				$query = $db->getQuery(true)
					->select('f.content_id')
					->from('#__govarticle_frontpage AS f')
					->where('content_id IN (' . implode(',', $pks) . ')');
				$db->setQuery($query);

				$old_featured = $db->loadColumn();

				// We diff the arrays to get a list of the articles that are newly featured
				$new_featured = array_diff($pks, $old_featured);

				// Featuring.
				$tuples = array();

				foreach ($new_featured as $pk)
				{
					$tuples[] = $pk . ', 0';
				}

				if (count($tuples))
				{
					$db = $this->getDbo();
					$columns = array('content_id', 'ordering');
					$query = $db->getQuery(true)
						->insert($db->quoteName('#__govarticle_frontpage'))
						->columns($db->quoteName($columns))
						->values($tuples);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();

		$this->cleanCache();

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since    3.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_GOVARTICLE_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;

			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_article');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Custom clean the cache of com_govarticle and content modules
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_govarticle');
	}
	
	/**
	 * Get attachments from article of selected version.
	 * 
	 * @param   Int   $article_id   Article ID number.
	 * @param   Int   $version      Article version number.
	 * 
	 * @return  Array
	 */
	public function getAttachments($article_id = null, $version = null) {
		
		// Get article
		if( is_null($article_id) ) {
			$article_id = (int)$this->getState('article.id');
		}
		$article = $this->getTable();
		
		// If this is new article or doesn't exist return empty list
		if( $article_id<1 OR !$article->load($article_id) ) {
			return array();
		}
		
		// Get article version if it was not provided
		if( is_null($version) ) {
			
			// Try to find a version in article history
			$version = $this->getVersionFromData($article);
			
			// If version was found, this article has no attachments
			if( $version===false ) {
				return array();
			}
			
		}
		
		// Prepare attachments query
		$query = $this->_db->getQuery(true);
		$query->select('f.*');
		$query->from('#__govarticle_files AS f');
		$query->leftJoin('#__govarticle_attachments AS a ON a.id=f.id');
		$query->select('u.name AS `created_by_name`');
		$query->leftJoin('#__users AS u ON u.id=f.created_by');
		$query->where(array(
			'a.content_id='.(int)$article_id,
			'f.version='.(int)$version
		));

		
		// Execute query
		$this->_db->setQuery($query);
		$attachments = $this->_db->loadObjectList();
		
		// Return attachments
		if( is_array($attachments) ) {
			return $attachments;
		} else {
			return array();
		}

	}
	
	public function getVersionFromData($data) {
		
		// If $data is object, convert it to array
		if( is_object($data) ) {
			$data = $data->getProperties(true);
		}
		
		// If $data is array, convert it to string
		if( is_array($data) ) {
			$data = json_encode($data);
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
	
	public function loadHistory($version_id, \JTable &$table) {
		
		// Get current version data
		$historyTable = JTable::getInstance('Contenthistory', 'JTable');
		
		// Try to load version data
		if( $historyTable->load(array('version_id'=>$version_id)) ) {
			$article = $this->getTable();
			
			// If article exist
			if( $article->load($historyTable->ucm_item_id) ) {
				$this->setState('article.id', $historyTable->ucm_item_id);
				
				// Try to determinate article version
				$this->_old_version = $this->getVersionFromData($article);
			}
			
		}
			
		return parent::loadHistory($version_id, $table);
	}
	
	public function processAttachments() {
		
		$input = JFactory::getApplication()->input;
		
		// This is reversing task, so add it to log but do not process attachments.
		if( $input->get('task')=='loadhistory' ) {

			// Add log entry about reversing version
			$this->addLogEntry($this->getState('article.id'), $this->_old_version, $input->get('version_id'));
			
			return false;
		}
		
		
		// Article data
		$article_id = $this->getState('article.id');
		
		// Try to get version of old data
		$version_old = $this->_old_version;
		
		// Get current version data
		$current = $this->getTable();
		if( !$current->load($article_id) ) {
			return false;
		}
		$version_current = $this->getVersionFromData($current);
		
		if( $version_old===$version_current ) {
			return true;
		}
		
		$this->addLogEntry($current->id, $version_old, $version_current);
		
		// Get remove list (if this is a new version)
		if( $version_old!==false ) {
			
			// Get list of new attachments ID's (those should be copied to new version)
			$attachments_new = $input->get('attachments', array(), 'array');
			$attachments_new = ArrayHelper::toInteger($attachments_new);
			
			// Copy attachments to new version
			$this->copyAttachmentsToVersion($attachments_new, $version_old, $version_current);
			
		}
		
		// Add new attachments
		$this->addAttachments($article_id, $version_current);
		
	}
	
	/**
	 * Add new attachments to article with given version
	 * 
	 * @param   Integer   $article_id   Article ID that is the owner of attachments
	 * @param   Integer   $version      Article version number
	 * 
	 * @return   boolean
	 */
	protected function addAttachments($article_id, $version) {
		
		// Include required libraries
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		// Get input data
		$input = JFactory::getApplication()->input;
		$files = $input->files->get('attachments_upload');
		$titles = $input->get('attachments_titles', array(), 'array');
		$user_id = JFactory::getUser()->id;
		
		// Create directory and path
		$path = JPATH_ROOT.'/media/govarticle/'.$article_id;
		if( !JFolder::exists($path) AND !JFolder::create($path) ) {
			return false;
		}
		
		// Add each attachment
		$added_counter = 0;
		foreach( $files AS $idx=>$file ) {
			
			// Get attachment table object
			$attachment = $this->getTable('Attachment');
			
			// Prepare attachment data
			$data = array(
			   'content_id'=>$article_id,
			);
			
			// If attachment successfully saved upload it
			if( $attachment->bind($data) AND $attachment->check() AND $attachment->store() ) {
				
				// Prepare internal filename
				$info = pathinfo($file['name']);
				$filename = JFilterOutput::stringURLSafe($info['filename']).'_'.$version.'.'.$info['extension'];
				
				// Get file record table
				$table = $this->getTable('File');
				
				// Prepare file data
				$data = array(
				   'id'=>$attachment->id,
				   'version'=>$version,
				   'filename'=>$file['name'],
				   'filename_internal'=>$filename,
				   'title'=>$titles[$idx],
				   'created_by'=>$user_id,
				);
				
				
				// If file record is saved successfully upload the file
				if( $table->bind($data) AND $table->check() AND $table->store() ) {
					
					
					// If upload failed delete record in base
					if( !JFile::upload($file['tmp_name'], $path.'/'.$filename) ) {
						$table->delete();
						$attachment->delete();
					} else {
						$added_counter++;
					}
					
				// Filed to save file record, remove the attachment
				} else {
					$attachment->delete();
				}
				
			}
		}
		
		if( $added_counter>0 ) {
			JFactory::getApplication()->enqueueMessage(JText::plural('COM_GOVARTICLE_ARTICLE_ADDED_N_ATTACHMENTS', $added_counter));
		}
		
		return true;
	}
	
	/**
	 * Copy attachments from old version to new one
	 * 
	 * @param   Array     $ids           Array of attachments ID's to move
	 * @param   Integer   $version_old   Article version to copy from
	 * @param   Integer   $version_new   Article version to copy to
	 * 
	 * @return   boolean
	 */
	protected function copyAttachmentsToVersion($ids, $version_old, $version_new) {
		
		// Nothing to copy so return success
		if ( !is_array($ids) OR empty($ids) ) {
			return true;
		}
		
		// Get existing attachments
		$attachments_data = $this->getAttachmentsVersion($ids, $version_old);
		
		// Prepare inserts
		$columns = array('id','version','title','filename','filename_internal','created_by');
		$inserts = array();
		foreach( $attachments_data AS $attachment ) {
			$inserts[] = implode(',',array(
			   $this->_db->q($attachment->id),
			   $this->_db->q($version_new),
			   $this->_db->q($attachment->title),
			   $this->_db->q($attachment->filename),
			   $this->_db->q($attachment->filename_internal),
			   $this->_db->q($attachment->created_by)
			));
		}
		
		// Prepare query
		/* @var $query JDatabaseQueryMySQLi */
		$query = $this->_db->getQuery(true);
		$query->insert('#__govarticle_files');
		$query->columns($columns);
		$query->values($inserts);
		
		// Execute query
		$this->_db->setQuery($query);
		
		return $this->_db->execute();
	}
	
	/**
	 * Returns attachments data from given version
	 * 
	 * @param   Array $ids     Array of attachments ID's  
	 * @param   Int   $version Version of article/attachments
	 * 
	 * @return   Array
	 */
	protected function getAttachmentsVersion(Array $ids, $version) {
		// Sanitize ID's
		$ids = ArrayHelper::toInteger($ids);
		
		// Prepare query
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__govarticle_files');
		$query->where(array(
			'id IN('.implode(',', $ids).')',
			'version = '.(int)$version
		));
		
		// Execute query and return result
		$this->_db->setQuery($query);
		
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Stores the version number of article before save. Also makes sure that article 
	 * will be saved as new version if there was any changes in attachments.
	 * 
	 * @param   Array   $data Array data of article waiting for save
	 */
	protected function preprocessAttachments(Array &$data) {
		
		// If this is old article
		if( isset($data['id']) ) {
			
			// Load article before changes
			$table = $this->getTable();
			
			// If loaded store backup
			if( $table->load($data['id']) ) {
				
				// Store version of old article
				$this->_old_version = $this->getVersionFromData($table->getProperties(true));
				
				// Check if there was changes in attachments
				$input = JFactory::getApplication()->input;
				$titles = $input->get('attachments_titles', array(), 'array');
				$attachments_new = $input->get('attachments', array(), 'array');
				$files = $input->files->get('attachments_upload', array(), 'array');
				
				// Attachments from old version
				if( $this->_old_version!==false ) {
					$attachments_old = $this->getAttachments($data['id'], $this->_old_version);
				} else {
					$attachments_old = array();
				}
				
				// Any changes in attachments?
				if ( 
					count($attachments_new)!==count($attachments_old) OR // Some attachments where removed
					!empty($titles) OR !empty($files) // New attachments where added 
				) {
					$data['changes_marker'] = (string)microtime(true);
				}
				
			}
			
		} else {
			$this->_old_version = false;
		}
		
	}
	
	/**
	 * Adds a version changes log entry
	 * 
	 * @param   Integer  $article_id       Article ID
	 * @param   Integer  $version_old      Previous version number
	 * @param   Integer  $version_current  Current version number
	 * 
	 * @return   boolean
	 */
	protected function addLogEntry($article_id, $version_old, $version_current) {
		
		$input = JFactory::getApplication()->input;
		$data = $input->get('jform', array(), 'array');
		
		// Prepare a message
		if( empty($data) ) {
			$message = 'COM_GOVARTICLE_LOG_ARTICLE_VERSION_RESTORED_S';
		} else if( (!isset($data['id']) OR $data['id']==0) ) {
			$message = 'COM_GOVARTICLE_LOG_ARTICLE_CREATED_S';
		} else {
			$message = 'COM_GOVARTICLE_LOG_ARTICLE_CHANGED_S';
		}
		
		// Who added the log entry
		$changed_by = JFactory::getUser()->id;
		
		// Create the log entry table object
		$log = $this->getTable('Log');
		
		$date = JFactory::getDate();
		
		// Prepare log entry data
		$log->bind(array(
		   'title'=>$message,
		   'content_id'=>$article_id,
		   'created_by'=>$changed_by,
		   'created'=>$date->toSql(true),
		   'version_old'=>(($version_old=='' OR is_null($version_old)) ? null : $version_old),
		   'version_new'=>$version_current,
		));
		
		// If saving failed return false
		if( !$log->check() OR !$log->store(true) ) {
			return false;
		}
		
		// Everything went fine, return success
		return true;
	}

}
