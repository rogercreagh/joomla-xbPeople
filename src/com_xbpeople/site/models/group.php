<?php
/*******
 * @package xbPeople
 * @filesource site/models/group.php
 * @version 1.0.2.3 9th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class XbpeopleModelGroup extends JModelItem {
	    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('group.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
	    $sess = Factory::getSession();
	    
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('group.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.title AS title, a.picture AS picture, 
				a.summary AS summary, a.description AS description, a.ext_links AS ext_links,
            a.year_formed AS year_formed, a.year_disolved AS year_disolved, 
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbgroups AS a');

			$query->select('(SELECT COUNT(DISTINCT(gp.person_id)) FROM #__xbgroupperson AS gp JOIN #__xbpersons AS p ON gp.person_id = p.id  WHERE gp.group_id = a.id AND p.state=1) AS pcnt');
			
			if ($sess->get('xbbooks_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(bg.book_id)) FROM #__xbbookgroup AS bg JOIN #__xbbooks AS b ON bg.book_id = b.id WHERE bg.group_id = a.id AND b.state=1) AS bcnt');
			} else {
			    $query->select('0 AS bcnt');
			}
			if ($sess->get('xbfilms_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(fg.film_id)) FROM #__xbfilmgroup AS fg JOIN #__xbfilms AS f ON fg.film_id = f.id WHERE fg.group_id = a.id AND f.state=1) AS fcnt');
			} else {
			    $query->select('0 AS fcnt');
			}
			if ($sess->get('xbevents_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(eg.event_id)) FROM #__xbeventgroup AS eg JOIN #__xbevents AS e ON eg.event_id = e.id WHERE eg.group_id = a.id AND e.state=1) AS ecnt');
			} else {
			    $query->select('0 AS ecnt');
			}
			
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {
				$item = &$this->item;
				$item->summary = trim($item->summary);
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
				
				if ($item->pcnt>0) {
				    $item->members = XbcultureHelper::getGroupMembers($item->id);
				    $item->pcnt = count($item->members); //there could be only unpublished people who wont show on frontend
				    $item->memberlist = XbcultureHelper::makeLinkedNameList($item->members,'','ul',true,3);
				}
				if ($item->bcnt > 0) {
				    $item->books = XbcultureHelper::getGroupBooks($item->id);
				    $item->booklist = XbcultureHelper::makeLinkedNameList($item->books,'','ul',true, 3);
				}
				if ($item->fcnt > 0) {
				    $item->films = XbcultureHelper::getGroupFilms($item->id);
				    $item->filmlist = XbcultureHelper::makeLinkedNameList($item->films,'','ul',true, 3);
				}
				if ($item->ecnt > 0) {
				    $item->events = XbcultureHelper::getGroupEvents($item->id);
				    $item->eventlist = XbcultureHelper::makeLinkedNameList($item->events,'','ul',true, 3);
				}
			}
		}
		return $this->item;
	}

}
	
