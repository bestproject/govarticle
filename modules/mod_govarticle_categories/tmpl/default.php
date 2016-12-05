<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_categories
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on mod_article_categories from Joomla!
 */

defined('_JEXEC') or die;
?>
<ul class="govarticle-categories-module<?php echo $moduleclass_sfx; ?>">
	<?php require JModuleHelper::getLayoutPath('mod_govarticle_categories', $params->get('layout', 'default') . '_items'); ?>
</ul>
