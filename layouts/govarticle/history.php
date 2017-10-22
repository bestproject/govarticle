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

if( empty($displayData['history']) ) {
	?><p><?php echo JText::_('COM_GOVARTICLE_ARTICLE_NO_HISTORY') ?></p><?php
	return;
}
$token = JSession::getFormToken().'=1';
?>
<div class="row-fluid form-horizontal-desktop">
	<div class="span12">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_HEADING_TITLE') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_HEADING_DATE') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_HEADING_CREATOR') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_HEADING_OPTIONS') ?></th>
				</tr>
			</thead>
			<tbody id="article-attachments">
			<?php foreach( $displayData['history'] AS $entry ): ?>
				<tr>
					<td>
						<?php 
						switch($entry->title) {
							case 'COM_GOVARTICLE_LOG_ARTICLE_VERSION_RESTORED_S':echo JText::plural($entry->title,$entry->version_new_date);break;
							default : echo JText::_($entry->title);
						}
						
						if( !empty( $entry->attachments['removed'] ) ): ?>
							<h5><?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_LABEL_ATTACHMENTS_REMOVED') ?></h5>
							<ul>
								<?php foreach( $entry->attachments['removed'] AS $attachment ): 
								$attachment->download_url = JRoute::_('index.php?option=com_govarticle&task=article.downloadAttachment&id='.$attachment->id.'&version='.$attachment->version);
								?>
									<li><a href="<?php echo $attachment->download_url ?>"><?php echo $attachment->title ?></a></li>
								<?php endforeach ?>
							</ul>	
						<?php 
						endif;
						if( !empty( $entry->attachments['added'] ) ): ?>
							<h5><?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_LABEL_ATTACHMENTS_ADDED') ?></h5>
							<ul>
								<?php foreach( $entry->attachments['added'] AS $attachment ): 
								$attachment->download_url = JRoute::_('index.php?option=com_govarticle&task=article.downloadAttachment&id='.$attachment->id.'&version='.$attachment->version);
								?>
									<li><a href="<?php echo $attachment->download_url ?>"><?php echo $attachment->title ?></a></li>
								<?php endforeach ?>
							</ul>	
						<?php endif ?>
					</td>
					<td class="middle hide-phone">
						<span class="icon-calendar"></span>
						<?php echo JHtml::_('date', $entry->created, JText::_('DATE_FORMAT_LC2')) ?>
					</td>
					<td class="middle hide-phone">
						<span class="icon-user"></span>
						<?php echo $entry->user_name ?>
					</td>
					<td class="middle hide-phone center">
						<?php if( $entry->version_old_date!='' AND $entry->version_new_date!='' ): ?>
						<a 
							class="btn jgrid hasTooltip" 
							title="<?php echo JText::_('COM_GOVARTICLE_ARTICLE_HISTORY_COMPARE') ?>" 
							target="_blank"
							onclick="window.open('<?php echo JRoute::_('index.php?option=com_govarticle&view=compare&layout=compare&tmpl=component&id1='.$entry->version_new.'&id2='.$entry->version_old.'&'.$token); ?>', '', 'height=600,width=800,scrollbars=yes');return false;"
							href="#">
							<span class="icon-copy"></span>
						</a>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
