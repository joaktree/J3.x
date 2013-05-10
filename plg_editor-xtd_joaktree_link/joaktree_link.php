<?php
/**
 * Joomla! plugin Joaktree content
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 *
 */
 
// no direct access
defined('_JEXEC') or die;


class plgButtonJoaktree_link extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * View element calls jSelectArticle when an article is clicked
		 * jSelectArticle creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function jSelectPerson(id, title, appid, apptitle, treeid) {
			var tag = '{joaktree person|' + appid + '|' + id + '|extended}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_joaktree&amp;view=jt_persons&amp;layout=element&amp;select=1&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_JOAKTREE_BUTTON_PERSONLINK'));
		$button->set('name', 'person_link');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
