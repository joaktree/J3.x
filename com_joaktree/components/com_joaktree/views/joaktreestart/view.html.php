<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree start - view.html.php
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewJoaktreeStart extends JViewLegacy {
	function display($tpl = null) {	
		$this->lists 		= array();

		// Load the parameters.
		$this->params 		= JoaktreeHelper::getJTParams();		
		$document = &JFactory::getDocument();
		$app 				= JFactory::getApplication('site');
		
		// Get data from the model
		$this->treeinfo		= $this->get( 'treeinfo' );  
		$menus  			= $this->get( 'menus' ); 
		
		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
		
		// add script 
		$this->lists['indFilter']	= $this->get( 'nameFilter' ); 
		$this->lists['tree_id']		= $this->get( 'treeId' );
		$this->lists['script']		= $this->addScript();
		
		// get text
		$this->articles		= Tree::getArticles($this->lists['tree_id']);

		// Id's and settings
		$this->lists['userAccess'] 		= $this->get( 'access' );
		$this->lists['menuItemId'] 		= $menus[ $this->lists['tree_id'] ];
		
		if ($this->treeinfo->indPersonCount) {
			$this->lists['personCount'] 	= $this->get( 'personCount' );
		}
		
		if ($this->treeinfo->indMarriageCount) {
			$this->lists['marriageCount']	= $this->get( 'marriageCount' );
		}
		
		//namelist
		$this->lists['index']		= $this->get( 'nameIndex' );
		$this->lists['columns']		= (int) $this->params->get('columns', '3');
		$this->namelist	  			= $this->get( 'namelist' );
		$this->lists['numberRows']	= (int) ceil( count($this->namelist) /  $this->lists['columns']);

		$this->lists['link'] 		=  'index.php?option=com_joaktree'
										.'&view=joaktreelist'
										.'&Itemid='.$this->lists['menuItemId']
										.'&treeId='.$this->lists['tree_id'];								
		
		// last update
		$this->lists[ 'lastUpdate' ]	= $this->get( 'lastUpdate' );

		// copyright
		$this->lists[ 'CR' ]		= JoaktreeHelper::getJoaktreeCR();
		
		$this->assignRef('treeinfo', 	$this->treeinfo );
		$this->assignRef('html', 	$html );
		$this->assignRef('lists',	$this->lists);
		
		if ($this->lists['userAccess']) {
			// set title, meta title
			if ($this->params->get('treeName')) {
				$title = $this->params->get('treeName');
				$document->setTitle($title);
				$document->setMetadata('title', $title);
			}
			
			// set additional meta tags
			if ($this->params->get('menu-meta_description')) {
				$document->setDescription($this->params->get('menu-meta_description'));
			}

			if ($this->params->get('menu-meta_keywords')) {
				$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
			}

			// robots
			if ($this->params->get('treeRobots') > 0) {
				$document->setMetadata('robots', JoaktreeHelper::stringRobots($this->params->get('treeRobots')));
			} else if ($this->params->get('robots')) {
				$document->setMetadata('robots', $this->params->get('robots'));
			}				
		}		
		
		parent::display($tpl);
	}
	
	protected function addScript() {
		$script = array();
		$indCookie	= $this->params->get('indCookies', true);
		
		$script[] = "window.addEvent('domready', function(){ ";
		$script[] = "  var jtAccordion = new Fx.Accordion($('jt-accordion'),'#jt-accordion .jt-content-accordion', '#jt-accordion .content', { ";
		$script[] = "    opacity: true, ";
		$script[] = "    initialDisplayFx : false, ";
		$script[] = "    display: ".$this->lists['indFilter'].", ";
		$script[] = "    onActive: function(toggler, element) { ";
		$script[] = "      var ind = this.previous; ";
		$script[] = ($indCookie) ? "      var myCookie; " : " ";
		$script[] = "      toggler.removeClass('jt-content-accordion'); ";
		$script[] = "      toggler.addClass('jt-content-accordion-active'); ";
		$script[] = "      var El = $(ind + '-jt-cnt'); ";
		$script[] = ($indCookie) ? "      myCookie = Cookie.write('jt_nam_index', ind+'', {duration: 0}); " : " ";
		$script[] = "      if (El.hasClass('jt-ajax')) { ";
		$script[] = "        El.removeClass('jt-ajax'); ";
		$script[] = "        var myRequest = new Request({ ";
		$script[] = "          url: 'index.php?option=com_joaktree&format=raw&tmpl=component&view=joaktreestart&layout=_names&treeId=".$this->lists['tree_id']."&filter='+ind, ";
		$script[] = "          method: 'get', ";
		$script[] = "          onFailure: function(xhr) { ";
		$script[] = "            alert('Error occured for url: ' + url); ";
		$script[] = "          }, ";
		$script[] = "          onComplete: function(response) { ";
		$script[] = "            HandleResponseInd(ind + '-jt-cnt', response); ";
		$script[] = "          } ";
		$script[] = "        }).send(); ";
		$script[] = "      } } , ";
		$script[] = "    onBackground: function(toggler) { ";
		$script[] = "      toggler.removeClass('jt-content-accordion-active'); ";
		$script[] = "      toggler.addClass('jt-content-accordion'); } ";
		$script[] = "  }); ";
		$script[] = "}); ";
		$script[] = "";
		$script[] = "function loadData() { ";
		$script[] = "  var jtElement = $('jt-accordion').getElement('div.jt-ajax'); ";
		$script[] = "  if ((jtElement) && (jtElement.hasClass('jt-ajax'))) { ";
		$script[] = "    jtElement.removeClass('jt-ajax'); ";
		$script[] = "    var index = jtElement.id; ";
		$script[] = "    var myRequest = new Request({ ";
		$script[] = "      url: 'index.php?option=com_joaktree&format=raw&tmpl=component&view=joaktreestart&layout=_names&treeId=".$this->lists['tree_id']."&filter='+index.replace('-jt-cnt', ''), ";
		$script[] = "      method: 'get', ";
		$script[] = "      onFailure: function(xhr) { ";
		$script[] = "        alert('Error occured for url: ' + url); ";
		$script[] = "      }, ";
		$script[] = "      onComplete: function(response) { ";
		$script[] = "        HandleResponseInd(index, response); ";
		$script[] = "      } ";
		$script[] = "    }).send(); ";
		$script[] = "  } ";
		$script[] = "} ";
		$script[] = "";
		$script[] = "function HandleResponseInd(id, response) { ";
		$script[] = "  var El = $(id); ";
		$script[] = "  El.set('html', response); ";
		$script[] = "  loadData(); ";
		$script[] = "} ";
		$script[] = "";
						
		$script[] = "window.onload = loadData; ";
		
		return implode("\n", $script);		
	}	
}
?>