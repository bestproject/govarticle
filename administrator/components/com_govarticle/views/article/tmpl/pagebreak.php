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

$script  = 'function insertPagebreak() {' . "\n\t";

// Get the pagebreak title
$script .= 'var title = document.getElementById("title").value;' . "\n\t";
$script .= 'if (title != \'\') {' . "\n\t\t";
$script .= 'title = "title=\""+title+"\" ";' . "\n\t";
$script .= '}' . "\n\t";

// Get the pagebreak toc alias -- not inserting for now
// don't know which attribute to use...
$script .= 'var alt = document.getElementById("alt").value;' . "\n\t";
$script .= 'if (alt != \'\') {' . "\n\t\t";
$script .= 'alt = "alt=\""+alt+"\" ";' . "\n\t";
$script .= '}' . "\n\t";
$script .= 'var tag = "<hr class=\"system-pagebreak\" "+title+" "+alt+"/>";' . "\n\t";
$script .= 'window.parent.jInsertEditorText(tag, ' . json_encode($this->eName) . ');' . "\n\t";
$script .= 'window.parent.jModalClose();' . "\n\t";
$script .= 'return false;' . "\n";
$script .= '}' . "\n";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form class="form-horizontal">

	<div class="control-group">
		<label for="title" class="control-label"><?php echo JText::_('COM_GOVARTICLE_PAGEBREAK_TITLE'); ?></label>
		<div class="controls"><input type="text" id="title" name="title" /></div>
	</div>

	<div class="control-group">
		<label for="alias" class="control-label"><?php echo JText::_('COM_GOVARTICLE_PAGEBREAK_TOC'); ?></label>
		<div class="controls"><input type="text" id="alt" name="alt" /></div>
	</div>

	<button onclick="insertPagebreak();" class="btn btn-primary"><?php echo JText::_('COM_GOVARTICLE_PAGEBREAK_INSERT_BUTTON'); ?></button>

</form>
