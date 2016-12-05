<?php
/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_stats
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on mod_stats from Joomla!
 */

defined('_JEXEC') or die;
?>
<dl class="govarticle-stats-module<?php echo $moduleclass_sfx ?>">
<?php foreach ($list as $item) : ?>
	<dt><?php echo $item->title;?></dt>
	<dd><?php echo $item->data;?></dd>
<?php endforeach; ?>
</dl>
