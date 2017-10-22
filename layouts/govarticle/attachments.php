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

JHtml::_('bootstrap.framework');

if( empty($displayData['attachments']) ) {
	?><p><?php echo JText::_('COM_GOVARTICLE_ARTICLE_NO_ATTACHMENTS') ?></p><?php
	return;
}
?>
<div class="row-fluid form-horizontal-desktop">
	<div class="span12">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_TITLE') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_EXTENSION') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_SIZE') ?></th>
					<th class="hide-phone"><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_CREATED') ?></th>
				</tr>
			</thead>
			<tbody id="article-attachments">
			<?php foreach( $displayData['attachments'] AS $attachment ): ?>
				<tr>
					<td><a href="<?php echo $attachment->download_url ?>" target="_blank"><?php echo $attachment->title ?></a></td>
					<td><?php echo $attachment->extension ?></td>
					<td><?php echo $attachment->size ?></td>
					<td class="middle hide-phone"><?php echo $attachment->created_by_name ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
