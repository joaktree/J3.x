<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree locations - view.html.php
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
class JoaktreeViewLocations extends JViewLegacy {
	function display($tpl = null) {	
		$this->lists 		= array();

		// Load the parameters.
		$this->params 		= JoaktreeHelper::getJTParams();
		$document			= &JFactory::getDocument();
		$app 				= JFactory::getApplication('site');
		
		// Get data from the model
		$this->treeinfo		= $this->get( 'treeinfo' );   		
		$menus  			= $this->get( 'menus' );

		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
		
		// add script 
		$this->lists['interactiveMap'] 	= $this->get( 'interactiveMap' );
		$this->lists['indFilter']	= $this->get( 'locationFilter' ); 
		$this->lists['tree_id']		= $this->get( 'treeId' );
		$this->lists['script']		= $this->addScript();
		
		// get text
		$this->articles				= Tree::getArticles($this->lists['tree_id']);
		
		// Id's and settings
		$this->lists['userAccess'] 	= $this->get( 'access' );
		$this->lists['menuItemId'] 	= $menus[ $this->lists['tree_id'] ];
		$this->lists['indMap']		= false;
		$tmp						= $this->get( 'mapUrl' );
		if ($tmp) {
			$this->lists['indMap']	= true;
			$this->lists['map']		= explode("|", $tmp);
		}
		
		// distance options
		if ($this->lists['interactiveMap']) {
			$distance					= $app->getUserStateFromRequest( 'com_joaktree.map.distance',	'distance',	0,	'int' );	
			$this->lists['distance']	= $this->getDistanceSelect($distance);
		}
		
		//location list
		$this->lists['index']		= $this->get( 'locationIndex' );
		$this->lists['columns']		= (int) $this->params->get('columnsLoc', '3');
		$this->locationlist  		= $this->get( 'locationlist' ); 
		$this->lists['numberRows']	= (int) ceil( count($this->locationlist) /  $this->lists['columns']);
		
		$this->lists['linkMap'] 	= 'index.php?option=com_joaktree'
										.'&view=interactivemap'
										.'&tmpl=component'
										.'&format=raw'
										.'&treeId='.$this->lists['tree_id'];	
		$this->lists['linkList'] 	=  'index.php?option=com_joaktree'
										.'&view=joaktreelist'
										.'&tmpl=component'
										.'&layout=location'
										.'&treeId='.$this->lists['tree_id'];
		
		// last update
		$this->lists[ 'lastUpdate' ] = $this->get( 'lastUpdate' );

		// copyright
		$this->lists[ 'CR' ]		= JoaktreeHelper::getJoaktreeCR();
		
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
		$script 	= array();
		$indCookie	= $this->params->get('indCookies', true);
		$router 	= JSite::getRouter();
		
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
		$script[] = ($indCookie) ? "      myCookie = Cookie.write('jt_loc_index', ind+'', {duration: 0}); " : " ";
		$script[] = "      if (El.hasClass('jt-ajax')) { ";
		$script[] = "        El.removeClass('jt-ajax'); ";
		$script[] = "        var myRequest = new Request({ ";
		$script[] = "          url: 'index.php?option=com_joaktree&format=raw&tmpl=component&view=locations&layout=_places&treeId=".$this->lists['tree_id']."&filter='+ind, ";
		$script[] = "          method: 'get', ";
		$script[] = "          onFailure: function(xhr) { ";
		$script[] = "            alert('Error occured for url: ' + url); ";
		$script[] = "          }, ";
		$script[] = "          onComplete: function(response) { ";
		$script[] = "            HandleResponseLoc(ind + '-jt-cnt', response); ";
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
		$script[] = "      url: 'index.php?option=com_joaktree&format=raw&tmpl=component&view=locations&layout=_places&treeId=".$this->lists['tree_id']."&filter='+index.replace('-jt-cnt', ''), ";
		$script[] = "      method: 'get', ";
		$script[] = "      onFailure: function(xhr) { ";
		$script[] = "        alert('Error occured for url: ' + url); ";
		$script[] = "      }, ";
		$script[] = "      onComplete: function(response) { ";
		$script[] = "        HandleResponseLoc(index, response); ";
		$script[] = "      } ";
		$script[] = "    }).send(); ";
		$script[] = "  } ";
		$script[] = "} ";
		$script[] = "";
		$script[] = "function HandleResponseLoc(id, response) { ";
		$script[] = "  var El = $(id); ";
		$script[] = "  El.set('html', response); ";
		$script[] = "  loadData(); ";
		$script[] = "} ";
		$script[] = "";
		$script[] = "function jt_show_list(loc, url) { ";
		$script[] = ($indCookie) ? "  var myCookie; " : " ";
		$script[] = "  var MTitl = $('jt-map-title'); ";
		$script[] = "  var MId   = $('jt-map-id'); ";
		$script[] = ($indCookie) ? "  myCookie = Cookie.write('jt_loc_url', loc + '|' + url, {duration: 0}); " : " ";		
		$script[] = "  var html = '<iframe id=\"jt-map-frame\" src=\"' + url + '\" height=\"250px\" style=\"border: 1px solid #dddddd;\" ></iframe>'; ";
		$script[] = "  MTitl.set('html', loc); ";
		$script[] = "  MId.set('html', html); ";
		$script[] = "} ";
		$script[] = "";
		
		if ($this->lists['interactiveMap']) {
			$script[] = "function jt_show_map(loc, url) { ";
			$script[] = ($indCookie) ? "  var myCookie; " : " ";
			$script[] = "  var MTitl = $('jt-map-title'); ";
			$script[] = "  var MDist = $('jt-map-distance'); ";
			$script[] = "  var MId   = $('jt-map-id'); ";
			//if ($router->getMode() == JROUTER_MODE_SEF) {
			//	$script[] = "  url = url + '-' + MDist.value; ";
			//} else {
				$script[] = "  url = url + '&distance=' + MDist.value; ";
			//}
			$script[] = ($indCookie) ? "  myCookie = Cookie.write('jt_loc_url', loc + '|' + url, {duration: 0}); " : " ";
			$script[] = "  var html = '<iframe id=\"jt-map-frame\" src=\"' + url + '\" height=\"250px\" style=\"border: 1px solid #dddddd;\" ></iframe>'; ";
			$script[] = "  MTitl.set('html', loc); ";
			$script[] = "  MId.set('html', html); ";
			$script[] = "} ";
			$script[] = "";
			$script[] = "function jt_upd_radius() { ";
			$script[] = ($indCookie) ? "  var myCookie; " : " ";
			$script[] = "  var Mfr   = $('jt-map-frame'); ";
			$script[] = "  if (Mfr != null) { ";
			$script[] = "    var loc   = $('jt-map-title').get('html'); ";
			$script[] = "    var MDist = $('jt-map-distance'); ";
			$script[] = "    var url   = Mfr.get('src'); ";
			//if ($router->getMode() == JROUTER_MODE_SEF) {
			//	$script[] = "    var ind   = url.lastIndexOf('-'); ";
			//	$script[] = "    url = url.slice(0, ind) + '-' + MDist.value; ";
			//} else {
				$script[] = "    var ind   = url.indexOf('&distance'); ";
				$script[] = "    url = url.slice(0, ind) + '&distance=' + MDist.value; ";
			//}
			$script[] = ($indCookie) ? "    myCookie = Cookie.write('jt_loc_url', loc + '|' + url, {duration: 0}); " : " ";
			$script[] = "    Mfr.set('src', url); ";
			$script[] = "  } ";
			$script[] = "} ";
			$script[] = "";
		}
				
		$script[] = "window.onload = loadData; ";
		
		return implode("\n", $script);		
	}
	
	private function getDistanceSelect($distance) {
		$html = array();

		$html[] = '<select id="jt-map-distance" class="inputbox" size="1" onchange="jt_upd_radius();">';
		$html[] = '<option value="0" '.(($distance == 0) ? 'selected="selected" ': '').'>0 km</option>';
		$html[] = '<option value="1" '.(($distance == 1) ? 'selected="selected" ': '').'>1 km</option>';
		$html[] = '<option value="2" '.(($distance == 2) ? 'selected="selected" ': '').'>2 km</option>';
		$html[] = '<option value="5" '.(($distance == 5) ? 'selected="selected" ': '').'>5 km</option>';
		$html[] = '<option value="10" '.(($distance == 10) ? 'selected="selected" ': '').'>10 km</option>';
		$html[] = '<option value="20" '.(($distance == 20) ? 'selected="selected" ': '').'>20 km</option>';
		$html[] = '<option value="50" '.(($distance == 50) ? 'selected="selected" ': '').'>50 km</option>';
		$html[] = '</select>';
		
		return implode("\n", $html);
	}
		
}
?>