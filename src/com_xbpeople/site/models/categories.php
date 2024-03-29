<?php
/*******
 * @package xbPeople
 * @filesource site/models/categories.php
 * @version 0.9.9.3 14th July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

class XbpeopleModelCategories extends JModelList {
	
    protected $xbfilmsStatus;
    protected $xbbooksStatus;
    
    public function __construct($config = array()) {
	    $showcat = ComponentHelper::getParams('com_xbpeople')->get('show_cats',1);
	    if (!$showcat) {
	        header("location:javascript://history.go(-1)");
	        exit();
	    }
	    if (empty($config['filterfileds'])) {
			$config['filter_fields'] = array ('id','title','path', 'parent','bcnt','bpcnt','rccnt','bchcnt' );
		}
		$this->xbfilmsStatus = Factory::getSession()->get('xbfilms_ok',false);
		$this->xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
		parent::__construct($config);
	}
	
 	protected function populateState($ordering = null, $direction = null) {
		// Load state from the request.
		$app = Factory::getApplication();
		
		// Load the parameters.
		$params = Factory::getApplication()->getParams();
		$this->setState('params', $params);
		
		parent::populateState($ordering, $direction);
		//pagination limit
//		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 25 );
//		$this->setState('limit', $limit);
//		$this->setState('list.limit', $limit);
//		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', $app->get('start'));
//		$this->setState('list.start', $limitstart);		
		
	}
 	
	protected function getListQuery() {
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT c.id AS id, c.path AS path, c.level AS level, c.title AS title, 
            c.description AS description, c.extension AS extension, c.lft');
		$query->from('#__categories AS c');
		
		if ($this->xbbooksStatus) {
		    $query->select('(SELECT COUNT(DISTINCT p.id) FROM #__xbpersons AS p LEFT JOIN #__xbbookperson AS bp ON bp.person_id = p.id WHERE p.catid = c.id AND bp.id IS NOT NULL ) AS bpcnt');
            $query->select('(SELECT COUNT(DISTINCT ch.id) FROM #__xbcharacters AS ch LEFT JOIN #__xbbookcharacter AS bc ON bc.char_id = ch.id WHERE ch.catid = c.id AND bc.id IS NOT NULL ) AS bchcnt');
		}
		
		
		if ($this->xbfilmsStatus) {
		    $query->select('(SELECT COUNT(DISTINCT p.id) FROM #__xbpersons AS p LEFT JOIN #__xbfilmperson AS fp ON fp.person_id = p.id WHERE p.catid = c.id AND fp.id IS NOT NULL ) AS fpcnt');
            $query->select('(SELECT COUNT(DISTINCT ch.id) FROM #__xbcharacters AS ch LEFT JOIN #__xbfilmcharacter AS fc ON fc.char_id = ch.id WHERE ch.catid = c.id AND fc.id IS NOT NULL ) AS fchcnt');
		}
		
		$query->where('c.extension IN ('.$db->quote('com_xbpeople').')');
		
		// Filter by published state
		$query->where('published = 1');
		
		/**
		 * need to count all people, chars and seprately for groups and books, films and events if components are installed
		 * add to filters filter by film,bokk,event, group roles
		 */
		
/* 		// Search in title/id/synop
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search,'s:')===0) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(c.description LIKE ' . $search.')');
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(c.title LIKE ' . $search . ')');
			}
		}
		
 */	
		//filter by branch, using alias to include all children
/* 		$branch = $this->getState('filter.branch');
		if ($branch != '') {
			$query->where('c.alias LIKE '.$db->quote('%'.$branch.'%'));
		}
 */		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape('extension, '.$orderCol.' '.$orderDirn));
		
		//$query->group('t.id');
		
		return $query;		
		
	}
	
	public function getItems() {
		$items  = parent::getItems();
 		foreach ($items as $cat) {
 		    if (is_null($cat->bpcnt)) $cat->bpcnt = 0;
 		    if (is_null($cat->bchcnt)) $cat->bchcnt = 0;
 		    if (is_null($cat->fpcnt)) $cat->fpcnt = 0;
 		    if (is_null($cat->fchcnt)) $cat->fchcnt = 0;
 		    $cat->allcnt = $cat->bpcnt + $cat->bchcnt + $cat->fpcnt + $cat->fchcnt;
 		}
		return $items;
	}
}
