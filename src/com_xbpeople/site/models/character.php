<?php
/*******
 * @package xbPeople
 * @filesource site/models/character.php
 * @version 0.9.9.4 26th July 2022
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
		
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('char.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.name AS name, a.image AS image, 
				a.summary AS summary, a.description AS description,  
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbcharacters AS a');
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
				
				$item->filmcnt = 0;
				if ($this->xbfilmsStatus) {
				    $item->filmlist = XbcultureHelper::getCharFilms($item->id,'rel_year DESC');
				    $item->filmcnt = count($item->filmlist);
				}
				$item->bookcnt = 0;
				if ($this->xbbooksStatus) {
				    $item->booklist = XbcultureHelper::getCharBooks($item->id,'pubyear ASC');
				    $item->bookcnt = count($item->booklist);
				}												
			}
		}
		return $this->item;
	}

}
	
