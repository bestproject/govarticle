<?php 

/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle.layouts
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 */

defined('JPATH_BASE') or die;

JHtml::_('bootstrap.framework');

$state = $displayData->get('State');
$params = $state->params;

$canEdit = $params->get('access-edit'); 

$doc = JFactory::getDocument();
$doc->addStyleDeclaration('
	td.min,th.min {width:1%}
	td.middle,th.middle {vertical-align:middle}
	.btn.center {text-align:center}
	.btn.block {display:block}
	input.attachment_title {border:1px solid #aaa;padding:3px 5px;border-radius:3px}
'); 
$doc->addScriptDeclaration('
	jQuery(document).ready(function($){
		window.addNewAttachment = function(){
		
			var table = $(\'#article-attachments\');
			var timestamp = new Date().getTime();
			var row = $(\'<tr><td></td><td></td><td class="middle"><label for="attachment-title-\'+timestamp+\'" style="display:none">'.JText::_('COM_GOVARTICLE_ATTACHMENTS_LABEL').'</label><input id="attachment-title-\'+timestamp+\'" name="attachments_titles[]" class="attachment_title" placeholder="'.JText::_('COM_GOVARTICLE_ATTACHMENTS_TITLE_PLACEHOLDER').'" required /></td><td class="middle"><input type="file" name="attachments_upload[]" required  /></td><td></td></tr>\');
			
			var remove_button = $(\'<a href="#" class="btn center block" title="'.JText::_('COM_GOVARTICLE_ATTACHMENTS_BUTTON_REMOVE').'"><span class="icon-remove"></span></a>\');
			remove_button.click(function(){
				$(this).closest("tr").remove();
			});
			
			$(row.find("td").eq(1)).append(remove_button);
			table.append(row)
		};
	});
');?>

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attachments', JText::_('COM_GOVARTICLE_FIELDSET_ATTACHMENTS', true)); ?>
<div class="row-fluid form-horizontal-desktop">
	<div class="span12">
		
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th class="hide-phone min"><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_ID') ?></th>
					<th class="min"><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_OPTIONS') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_TITLE') ?></th>
					<th><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_FILE') ?></th>
					<th class="hide-phone"><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_HEADING_CREATED') ?></th>
				</tr>
			</thead>
			<tbody id="article-attachments">
			<?php foreach( $displayData->attachments AS $attachment ): 
				$attachment->download_url = JRoute::_('index.php?option=com_govarticle&task=article.downloadAttachment&id='.$attachment->id.'&version='.$attachment->version);
				$attachment->created_by_url = JRoute::_('index.php?option=com_users&task=user.edit&id='.$attachment->created_by);
			?>
				<tr>
					<td class="hide-phone middle">
						<?php echo $attachment->id ?>
						<input name="attachments[]" type="hidden" value="<?php echo $attachment->id ?>"/>
					</td>
					<td class="nowrap min">
						<div class="btn-group">
							<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
								<span class="icon-cog"></span>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#" onclick="jQuery(this).closest('tr').remove()"><span class="icon-trash"></span><?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_BUTTON_DELETE') ?></a></li>
							</ul>
						</div>
					</td>
					<td class="middle"><?php echo $attachment->title ?></td>
					<td class="middle"><a href="<?php echo $attachment->download_url ?>" target="_blank"><?php echo $attachment->filename ?></a></td>
					<td class="middle hide-phone"><a href="<?php echo $attachment->created_by_url ?>" target="_blank"><?php echo $attachment->created_by_name ?></a></td>
				</tr>
			<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td>
						<a href="#" class="btn block" onclick="addNewAttachment()" title="<?php echo JText::_('COM_GOVARTICLE_ATTACHMENTS_BUTTON_ADD') ?>"><span class="icon-plus"></span></a>
					</td>
					<td colspan="3"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php echo JHtml::_('bootstrap.endTab'); ?>