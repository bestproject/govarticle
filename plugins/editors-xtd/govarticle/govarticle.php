<?php

/**
 * @package     GovArticle
 * @subpackage  plg_editors-xtd_article
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on plg_editors-xtd_article from Joomla!
 */

defined('_JEXEC') or die;

/**
 * Editor GovArticle buton
 *
 * @since  1.5
 */
class PlgButtonGovArticle extends JPlugin {
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Display the button
	 *
	 * @param   string  $name  The name of the button to add
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	public function onDisplay($name) {
		/*
		 * Javascript to insert the link
		 * View element calls jSelectArticle when an article is clicked
		 * jSelectArticle creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		
		$action = 
			'jQuery.ajax({url: "'.JURI::base().'index.php?option=com_govarticle&task=article.template&id="+id}).done(function(data){
				jInsertEditorText(data.introtext+data.fulltext, "jform_articletext");
				jModalClose();
			})';
		
		$js = "
		function jSelectGovArticle(id, title, catid, object, link, lang)
		{
			var hreflang = '';
			if (lang !== '')
			{
				var hreflang = ' hreflang = \"' + lang + '\"';
			}
			$action
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_govarticle&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		$button = new JObject;
		$button->modal = true;
		$button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('PLG_GOVARTICLE_BUTTON_ARTICLE');
		$button->name = 'stack';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}

}
