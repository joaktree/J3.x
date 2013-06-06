<?php
/**
 * Joomla! component Joaktree
 * file	   front end tree object - tree.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class Tree extends JObject { 

	public function getArticles($tree_id) {
		// general input
		$app			= JFactory::getApplication();
		$languageFilter	= $app->getLanguageFilter();
		$db				= JFactory::getDBO();
		$query 			= $db->getQuery(true);
		
		// get user access info
		$levels			= JoaktreeHelper::getUserAccessLevels();
		
		// initialization
		$retArticles = array();
		$nullDate	= $db->getNullDate();
		$date		= JFactory::getDate();
		$now  		= $date->toSql();		
		
		
		//remove single space after commas in keywords
		$attribs = array(); 
		$attribs[] = '","';
		$attribs[] = 'REPLACE( a.metakey, ", ", "," )';
		$attribs[] = '","';
		$concat = $query->concatenate($attribs, ',');
		// end concats		
		
		// select from content
		$query->select(' a.id             AS id ');
		$query->select(' a.title          AS title ');
		$query->select(' a.introtext      AS introtext ');
		$query->select(' a.fulltext       AS \'fulltext\' ');
		$query->select(' DATE_FORMAT( a.modified, "%e %b %Y" ) AS modified  ');
		$query->select(' a.attribs        AS attribs ');
		$query->from(  ' #__content       AS a ');
		$query->where( ' a.state           = 1 ');
		$query->where( ' a.access         IN ' .$levels .' ');
		$query->where( ' (   a.publish_up   =  '.$db->Quote($nullDate).' '
					 .'  OR  a.publish_up   <= '.$db->Quote($now).' '
					 .'  ) '
					 );
		$query->where( ' (   a.publish_down =  '.$db->Quote($nullDate).' '
					 .'  OR  a.publish_down >= '.$db->Quote($now).' '
					 .'  ) '
					 );

		// Filter by language
		if ($languageFilter) {
			$query->where('a.language in  ('.$db->Quote(JFactory::getLanguage()->getTag())
										.','.$db->Quote('*')
										.') ');
		}
			 
		// select from categories
		// join with tree to find matching category
		$query->select(' cc.id            AS cat_id ');
		$query->innerJoin(' #__categories       AS cc '
						 .' ON (   cc.id        = a.catid '
						 .'    AND cc.access    IN ' .$levels .' '
						 .'    ) '
						 );			
		$query->innerJoin(' #__joaktree_trees   AS jte '
						 .' ON (   cc.id        = jte.catid '
						 .'    AND jte.id       = '.(int) $tree_id.' '
						 .'    ) '
						 );		
						
		$db->setQuery($query);
		$articles = $db->loadObjectList();
		
		foreach ($articles as $article) {
			// Convert parameter fields to objects for article.
			if (isset($article->attribs)) {
				$registry = new JRegistry;
				$registry->loadString($article->attribs);		
				$indIntrotext = (int) $registry->get('show_intro', 1);
				$article->showTitle = (int) $registry->get('show_title', 1);
			} else {
				$indIntrotext = 0;
				$article->showTitle = 1;
			}
			
			// Are we showing introtext with the article
			if ($indIntrotext == 1) {
				$article->text = $article->introtext. chr(13).chr(13) . $article->fulltext;
			}
			elseif (!empty($article->fulltext)) {
				$article->text = $article->fulltext;
			}
			else  {
				$article->text = $article->introtext;
			}

			// formating last update date
			if (!empty($article->modified)) {
				$article->modified = JoaktreeHelper::lastUpdateDateTimePerson($article->modified);
			}
		
			// check content using the content plugin - if it is available
			$plug     = JPATH_SITE.DS.'plugins'.DS.'content'.DS.'joaktree'.DS.'joaktree.php'; 
		
			// check if plugin file exists - only then we will use it
			if ( JFile::exists( $plug) ) {
				// load the plugin
				JLoader::register('plgContentJoaktree',  $plug);
				$params = array();
				plgContentJoaktree::onContentPrepare('com_content', $article, $params);
			}
			
			$retArticles[] = $article;
		}
		
		return $retArticles;
	}
}
