<?php
/*******
 * @package xbPeople
 * @filesource site/models/character.php
 * @version 1.0.3.4 31st January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class XbpeopleModelCharacter extends JModelItem {
	
    protected $xbfilmsStatus;
    protected $xbbooksStatus;
    
    public function __construct($config = array()) {
        $this->xbfilmsStatus = XbcultureHelper::checkComponent('com_xbfilms');
        $this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
        parent::__construct($config);
    }
    
    protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('char.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
	    $sess = Factory::getSession();
	    
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('char.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.name AS name, a.image AS image, 
				a.summary AS summary, a.description AS description,  
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbcharacters AS a');
			
			if ($sess->get('xbbooks_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(bc.book_id)) FROM #__xbbookcharacter AS bc JOIN #__xbbooks AS b ON bc.book_id = b.id WHERE bc.char_id = a.id AND b.state=1) AS bcnt');
			} else {
			    $query->select('0 AS bcnt');
			}
			if ($sess->get('xbfilms_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(fc.film_id)) FROM #__xbfilmcharacter AS fc JOIN #__xbfilms AS f ON fc.film_id = f.id WHERE fc.char_id = a.id AND f.state=1) AS fcnt');
			} else {
			    $query->select('0 AS fcnt');
			}
			if ($sess->get('xbevents_ok',false)==1) {
			    $query->select('(SELECT COUNT(DISTINCT(ec.event_id)) FROM #__xbeventcharacter AS ec JOIN #__xbevents AS e ON ec.event_id = e.id WHERE ec.char_id = a.id AND e.state=1) AS ecnt');
			} else {
			    $query->select('0 AS ecnt');
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
				
				
				if ($item->bcnt > 0) {
				    $item->books = XbcultureHelper::getPersonBooks($item->id);
				    $item->booklist = XbcultureHelper::makeItemLists($item->books,'','trn',4,'bpvmodal');
				}
				if ($item->ecnt > 0) {
				    $item->events = XbcultureHelper::getPersonEvents($item->id);
				    $item->eventlist = XbcultureHelper::makeItemLists($item->events,'','trn', 4,'epvmodal');
				}
				if ($item->fcnt > 0) {
				    $item->films = XbcultureHelper::getPersonFilms($item->id);
				    $item->filmlist = XbcultureHelper::makeItemLists($item->films,'','trn', 4,'fpvmodal');
				}
			}
		}
		return $this->item;
	}

}
	
