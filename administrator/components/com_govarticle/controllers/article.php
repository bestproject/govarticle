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

/**
 * The article controller
 *
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle
 * @since       1.6
 */
class GovArticleControllerArticle extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// An article edit form can come from the articles or featured view.
		// Adjust the redirect view on the value of 'return' in the request.
		if ($this->input->get('return') == 'featured')
		{
			$this->view_list = 'featured';
			$this->view_item = 'article&return=featured';
		}
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		$categoryId = JArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
		$allow = null;

		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it.
			$allow = $user->authorise('core.create', 'com_govarticle.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_govarticle.article.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_govarticle.article.' . $recordId))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Article', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_govarticle&view=articles' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return	void
	 *
	 * @since	3.1
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$model->processAttachments();
		
		return;
	}
	
	public function downloadAttachment() {
		
		// Results from this cannot be cached
		header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
		header("Pragma: no-cache"); //HTTP 1.0
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		
		// Load table classes
		JLoader::register('GovArticleTableFile', JPATH_ADMINISTRATOR . '/components/com_govarticle/tables/file.php');
		JLoader::register('GovArticleTableAttachment', JPATH_ADMINISTRATOR . '/components/com_govarticle/tables/attachment.php');
		
		
		// Get input
		$attachment_id = $this->input->getInt('id');
		$version_id = $this->input->getInt('version');
		
		// Try to load file data
		$file = JTable::getInstance('File','GovArticleTable');
		$attachment = JTable::getInstance('Attachment','GovArticleTable');
		
		// If attachment or file not found in database, return error
		if( 
			!$file->load(array('id'=>$attachment_id,'version'=>$version_id)) OR 
			!$attachment->load($attachment_id) ) 
		{
			die('File version or Attachment not found in database.');
		}
		
		// Prepare internal file path
		$path = JPATH_ROOT.'/media/govarticle/'.$attachment->content_id.'/'.$file->filename_internal;
		
		// Check if file exists, if not return error
		if( !file_exists($path) ) {
			die('File not found.');
		}
		
		// Stream file
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename*=UTF-8\'\''.rawurlencode($file->filename));
		header('Content-Length: ' . filesize($path));
		readfile($path);
		
		JFactory::getApplication()->close();
	}
	
	public function inspect() {
		$canDo = JHelperContent::getActions('com_govarticle','component');
		if( $canDo->get('core.inspect') ) {
			JLoader::register('GovArticleTableLog', JPATH_ADMINISTRATOR . '/components/com_govarticle/tables/log.php');
			
			$table = JTable::getInstance('Log', 'GovArticleTable');
			if( $table->load($this->input->getInt('id')) ) {
				$table->inspected = 1;
				$table->store();
			}
			$this->setMessage(JText::_('COM_GOVARTICLE_ARTICLE_INSPECT_SUCCESS'));
		} else {
			$this->setMessage(JText::_('COM_GOVARTICLE_ARTICLE_INSPECT_ERROR'), 'error');
		}
		$this->setRedirect('index.php');
	}
	
	public function template(){
		JFactory::getApplication()->allowCache(false);
		header('Content-Type: application/json');
		$model = $this->getModel();
		$article = $model->getItem($this->input->getInt('id'));
		
		echo json_encode($article);
		JFactory::getApplication()->close();
	}
}
