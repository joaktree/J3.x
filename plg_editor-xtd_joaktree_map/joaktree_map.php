<?php
/**
 * Joomla! plugin Joaktree editor extension
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


class plgButtonJoaktree_map extends JPlugin
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
		function jSelectMap(id, title, appid, apptitle) {
			var tag = '{joaktree map|' + appid + '|' + id + '|content}';
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
		$link = 'index.php?option=com_joaktree&amp;view=jt_maps&amp;layout=element&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_JOAKTREE_BUTTON_MAP'));
		$button->set('name', 'map');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
