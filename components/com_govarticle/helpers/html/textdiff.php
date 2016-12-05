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

defined('JPATH_PLATFORM') or die;

abstract class JHtmlTextdiff {
	
	protected static $loaded = array();

	public static function textdiff($containerId) {
		// Only load once
		if (isset(static::$loaded[__METHOD__])) {
			return;
		}

		// Depends on jQuery UI
		JHtml::_('bootstrap.framework');
		JHtml::_('script', 'com_contenthistory/diff_match_patch.js', false, true);
		JHtml::_('script', 'com_contenthistory/jquery.pretty-text-diff.min.js', false, true);
		JHtml::_('stylesheet', 'com_contenthistory/jquery.pretty-text-diff.css', false, true, false);

		// Attach diff to document
		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
 					$('#" . $containerId . " tr').prettyTextDiff();
 				});
			})(jQuery);
			"
		);

		// Set static array
		static::$loaded[__METHOD__] = true;

		return;
	}

}
