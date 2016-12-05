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

JLoader::register('GovArticleHistoryHelper', JPATH_COMPONENT . '/helpers/govarticlehistory.php');

class GovArticleModelCompare extends JModelItem {

	public function getItems() {
		$input = JFactory::getApplication()->input;

		/** @var JTableContenthistory $table1 */
		$table1 = JTable::getInstance('Contenthistory');

		/** @var JTableContenthistory $table2 */
		$table2 = JTable::getInstance('Contenthistory');

		$id1 = $input->getInt('id1');
		$id2 = $input->getInt('id2');
		$result = array();

		if ($table1->load($id1) && $table2->load($id2)) {
			// Get the first history record's content type record so we can check ACL
			/** @var JTableContenttype $contentTypeTable */
			$contentTypeTable = JTable::getInstance('Contenttype');
			$ucmTypeId = $table1->ucm_type_id;

			if (!$contentTypeTable->load($ucmTypeId)) {
				// Assume a failure to load the content type means broken data, abort mission
				return false;
			}

			// All's well, process the records
			foreach (array($table1, $table2) as $table) {
				$object = new stdClass;
				$object->data = GovArticleHistoryHelper::prepareData($table);
				$object->version_note = $table->version_note;
				$object->save_date = $table->save_date;
				$result[] = $object;
			}

			return $result;
		}

		return false;
	}

}
