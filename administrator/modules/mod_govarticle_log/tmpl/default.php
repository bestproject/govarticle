<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_log
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (c) 2015, Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 */

defined('_JEXEC') or die;

$canDo = JHelperContent::getActions('com_govarticle','component');
$can_inspect = $canDo->get('core.inspect');

$token = JSession::getFormToken().'=1';
		 
?>
<?php if( !empty($items) ): ?>
	<div class="row-striped mod-govarticle-log<?php echo $params->get('moduleclass_sfx') ?>">
	<?php foreach($items AS $entry): ?>
	<div class="row-fluid">
		<div class="span9">
			<?php if( $can_inspect ): ?>
				<div class="btn-group pull-left">
					<a class="btn jgrid hasTooltip" title="<?php echo JText::_('MOD_GOVARTICLE_LOG_INSPECT') ?>" href="<?php echo JRoute::_('index.php?option=com_govarticle&task=article.inspect&id=' . $entry->id); ?>">
						<span class="icon-publish"></span>
					</a>
					<?php if( $entry->version_old_date!='' AND $entry->version_new_date!='' ): ?>
					<a 
						class="btn jgrid hasTooltip" 
						title="<?php echo JText::_('MOD_GOVARTICLE_LOG_COMPARE') ?>" 
						target="_blank"
						onclick="window.open('<?php echo JRoute::_('index.php?option=com_contenthistory&view=compare&layout=compare&tmpl=component&id1='.$entry->version_new.'&id2='.$entry->version_old.'&'.$token); ?>', '', 'height=600,width=800');return false;"
						href="#">
						<span class="icon-copy"></span>
					</a>
					<?php endif ?>
				</div>
			<?php endif ?>
			<?php //echo $entry->version_old.'-'.$entry->version_new ?>
			<div class="pull-left" style="margin-left:10px">
				<strong class="row-title break-word">
					<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_govarticle&task=article.edit&id=' . $entry->content_id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>"><?php echo $entry->article_title ?></a>
				</strong>
				<span class="small">
					<?php 
					switch($entry->title) {
						case 'COM_GOVARTICLE_LOG_ARTICLE_VERSION_RESTORED_S':echo JText::plural($entry->title,$entry->version_new_date);break;
						default : echo JText::_($entry->title);
					}
					?>
				</span>
				<?php if( $entry->version_note!='' ): ?>
				<div class="note small">
					<i><?php echo JText::plural('MOD_GOVARTICLE_LOG_NOTE_S', $entry->version_note) ?></i>
				</div>
				<?php endif ?>
			</div>
		</div>
		<div class="span2 small"><span class="icon-calendar"></span><?php echo JHtml::_('date', $entry->created, JText::_('DATE_FORMAT_LC2')) ?></div>
		<div class="span1 small">
			<span class="icon-user"></span>
			<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $entry->created_by); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>"><?php echo $entry->user_name ?></a>
		</div>
	</div>
	<?php endforeach ?>
	</div>
<?php else: ?>
	<div class="row-striped mod-govarticle-log<?php echo $params->get('moduleclass_sfx') ?>"><div class="row-fluid">
		<div class="span12"><?php echo JText::_('MOD_GOVARTICLE_LOG_LIST_EMPTY') ?></div>
	</div></div>
<?php endif ?>
