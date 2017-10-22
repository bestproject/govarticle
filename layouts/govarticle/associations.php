<?php

/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle.layouts
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on com_content layouts from Joomla!
 */

defined('JPATH_BASE') or die;

$items = $displayData;

if (!empty($items)) : ?>
	<ul class="item-associations">
		<?php foreach ($items as $id => $item) : ?>
				<li>
					<?php echo $item->link; ?>
				</li>
		<?php endforeach; ?>
	</ul>
<?php endif;
