<?php
/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Migrates attachments from version 3.0.1 to 3.0.2
 */
defined('_JEXEC') or die;

class com_govarticleInstallerScript
{
    /**
     * Current (old) version of com_govarticle
     *
     * @var   String
     */
    protected $component_version;

    /**
     * Content Type ID of com_govarticle article.
     *
     * @var   Integer
     */
    protected $content_type_id;

    /**
     * Method to install the component
     */
    function install($parent)
    {
        //die('install');
//		JFactory::getApplication()->enqueueMessage('Running install');
    }

    /**
     * Method to uninstall the component
     */
    function uninstall($parent)
    {
        //die('uninstall');
//		JFactory::getApplication()->enqueueMessage('Running uninstall');
    }

    /**
     * Method to update the component
     */
    function update($parent)
    {
        //die('update');
//		JFactory::getApplication()->enqueueMessage('Running update');
    }

    /**
     * Method to run before an install/update/uninstall method
     *
     * @param   String   $type   Name of actions (update,install,uninstall,discover_install)
     * @param   Object   $parent Manifest file instance
     */
    function preflight($type, $parent)
    {
//		JFactory::getApplication()->enqueueMessage('Running preflight');
        //die('pre');
        // If this is update get previous number version
        if ($type == 'update') {
            $extension               = JTable::getInstance('Extension');
            $extension->load(array('name' => 'com_govarticle'));
            $manifest                = json_decode($extension->manifest_cache);
            $this->component_version = $manifest->version;
        }
    }

    /**
     * Method to run after an install/update/uninstall method
     *
     * @param   String   $type   Name of actions (update,install,uninstall,discover_install)
     * @param   Object   $parent Manifest file instance
     */
    function postflight($type, $parent)
    {
        //die('post');
//		JFactory::getApplication()->enqueueMessage('Running postflight');
        // Only after update
        if ($type !== 'update') return;

        // Required objects
        $db = JFactory::getDbo();

        // Get type ID
        $typeTable             = JTable::getInstance('Contenttype', 'JTable');
        $typeTable->load(array('type_alias' => 'com_govarticle.article'));
        $this->content_type_id = $typeTable->type_id;

        // Check if migartion is required by getting top article version numbers (before upgrade)
        $query = $db->getQuery(true);
        $query->select('MAX(version_id) AS version_id, ucm_item_id');
        $query->from('#__ucm_history');
        $query->where('ucm_type_id='.$db->q($this->content_type_id));
        $query->group('ucm_item_id');

        $db->setQuery($query);
        $versions_old = $db->loadAssocList('ucm_item_id');

        // Prepare versions array
        $versions_old = array_map(function($element) {
            return $element['version_id'];
        }, $versions_old);

        // If this is old version and there are articles to migrate
        if (version_compare($this->component_version, '3.0.2', '<') AND ! empty($versions_old)) {

            list($counter_versions, $counter_attachments) = $this->migrateAttachments($versions_old);

            JFactory::getApplication()->enqueueMessage(JText::plural('COM_GOVARTICLE_MIGRATION_S',
                    $counter_versions, $counter_attachments), 'message');
        }
    }

    /**
     * Migrates attachments from old version of component to new one.
     *
     * @param   Array   $versions_old   Array of TOP version of current articles.
     */
    protected function migrateAttachments(Array $versions_old)
    {

        // Load definition of GovArticleTableContent
        require JPATH_ADMINISTRATOR.'/components/com_govarticle/tables/content.php';

        // Database instance
        $db = JFactory::getDbo();

        // Count articles versions and attachments migrations
        $counter_new_versions         = 0;
        $counter_articles_attachments = 0;

        // Foreach article
        foreach ($versions_old AS $content_id => $old_version_id) {

            // Generate new version entry
            $article = JTable::getInstance('Content', 'GovArticleTable');

            if ($article->load($content_id)) {
                // Generate data change so observer will create new version
                $data                   = $article->getProperties(true);
                $data['changes_marker'] = $data['changes_marker'] = (string) microtime(true);

                // If new data stored (generated new version)
                if ($article->bind($data) AND $article->check() AND $article->store()) {

                    // Generated new version
                    $counter_new_versions++;

                    // Get new article version
                    $query = $db->getQuery(true);
                    $query->select('MAX(version_id)');
                    $query->from('#__ucm_history');
                    $query->where('ucm_item_id='.$db->q($content_id).' AND ucm_type_id='.$db->q($this->content_type_id));

                    $db->setQuery($query);
                    $new_version_id = (int) $db->loadResult();

                    // Copy attachments (increase attachments copied counter)
                    $counter_articles_attachments += $this->copyAttachments($old_version_id,
                        $new_version_id);
                }
            }
        }

        return array($counter_new_versions, $counter_articles_attachments);
    }

    protected function copyAttachments($version_old, $version_new)
    {

        // Database Instance
        $db = JFactory::getDbo();

        // Copy attachments from old article version to new one
        $query_insert = $db->getQuery(true);
        $query_select = $db->getQuery(true);

        $query_insert->insert('#__govarticle_files');

        $query_select->select($db->qn('id').', '.$db->q($version_new).' AS '.$db->qn('version').', '.$db->qn('title').', '.$db->qn('filename').', '.$db->qn('filename_internal').', '.$db->qn('created_by'));
        $query_select->from('#__govarticle_files');
        $query_select->where($db->qn('version').'='.$db->q($version_old));

        $query = $query_insert;
        $query .= ' ('.implode(',',
                $db->qn(array('id', 'version', 'title', 'filename', 'filename_internal',
                    'created_by'))).')';
        $query .= $query_select;

        $db->setQuery($query);
        $db->execute();

        return $db->getAffectedRows();
    }

    protected function createCategory()
    {

    }
}