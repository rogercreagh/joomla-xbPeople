<?php
/*******
 * @package xbPeople
 * @filesource site/models/person.php
 * @version 1.1.0.3 29th March 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbpeopleModelPerson extends JModelItem {
	
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('person.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
	    $sess = Factory::getSession();
	    
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('person.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.firstname AS firstname, a.lastname AS lastname, a.portrait AS portrait, 
				a.summary AS summary, a.biography AS biography, a.year_born AS year_born, a.year_died AS year_died,
				a.nationality AS nationality, a.ext_links AS ext_links, 
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbpersons AS a');
			
			$query->select('(SELECT COUNT(DISTINCT(gp.group_id)) FROM #__xbgroupperson AS gp JOIN #__xbgroups AS g ON gp.group_id = g.id  WHERE gp.person_id = a.id AND g.state=1) AS gcnt');
			
			if ($sess->get('xbfilms_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(fp.film_id)) FROM #__xbfilmperson AS fp JOIN #__xbfilms AS f ON fp.film_id = f.id WHERE fp.person_id = a.id AND f.state=1) AS fcnt');
			} else {
			    $query->select('0 AS fcnt');
			}
			if ($sess->get('xbevents_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(ep.event_id)) FROM #__xbeventperson AS ep JOIN #__xbevents AS e ON ep.event_id = e.id WHERE ep.person_id = a.id AND e.state=1) AS ecnt');
			} else {
			    $query->select('0 AS ecnt');
			}
			if ($sess->get('xbbooks_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(bp.book_id)) FROM #__xbbookperson AS bp JOIN #__xbbooks AS b ON bp.book_id = b.id WHERE bp.person_id = a.id AND b.state=1) AS bcnt');
			} else {
			    $query->select('0 AS bcnt');
			}
			
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {
				$item = &$this->item;
				// Load the JSON string
				$params = new Registry;
				$params->loadString($item->params, 'JSON');
				$item->params = $params;
				
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($item->params);
				$item->params = $params;
				$target = ($params->get('extlink_target')==1) ? 'target="_blank"' : '';
				
				// Convert the JSON-encoded links info into an array
				$item->ext_links = json_decode($item->ext_links);
				$item->ext_links_list ='';
				$item->ext_links_cnt = 0;
				if(is_object($item->ext_links)) {
					$item->ext_links_cnt = count((array)$item->ext_links);
					$item->ext_links_list = '<ul>';
					foreach($item->ext_links as $lnk) {
						$item->ext_links_list .= '<li><a href="'.$lnk->link_url.'" '.$target.'>'.$lnk->link_text.'</a> <i>'.$lnk->link_desc.'</i></li>';
					}
					$item->ext_links_list .= '</ul>';
				}
				
				if ($item->gcnt > 0) {
				    $item->groups = XbcultureHelper::getPersonGroups($item->id);
				    $item->grouplist = XbcultureHelper::makeItemLists($item->groups,'','trn', 4,'gpvmodal');
				}
				if ($item->bcnt > 0) {
				    $item->books = XbcultureHelper::getPersonBooks($item->id);
				    $item->booklist = XbcultureHelper::makeItemLists($item->books,'','trn',4,'bpvmodal');
				}
				if ($item->fcnt > 0) {
				    $item->films = XbcultureHelper::getPersonFilms($item->id);
				    $item->filmlist = XbcultureHelper::makeItemLists($item->films,'','trn', 4,'fpvmodal');
				}
				if ($item->ecnt > 0) {
				    $item->events = XbcultureHelper::getPersonEvents($item->id);
				    $item->eventlist = XbcultureHelper::makeItemLists($item->events,'','trn', 4,'epvmodal');
				}
				
			}
		}
		return $this->item;
	}
	
}
	
