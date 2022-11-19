<?php
/*******
 * @package xbPeople
 * @filesource site/models/category.php
 * @version 0.9.11.2 18th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class XbpeopleModelCategory extends JModelItem {
	
    public function __construct($config = array()) {
        $showcat = ComponentHelper::getParams('com_xbpeople')->get('show_cats',1);
        if (!$showcat) {
            header('Location: index.php?option=com_xbpeople&view=people');
            exit();
        }
        parent::__construct($config);
    }
    
    protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('cat.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('cat.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('c.id AS id, c.path AS path, c.title AS title, c.description AS description, c.note AS note, 
                c.level AS level, c.metadata AS metadata, c.extension AS extension');
			$query->select('(SELECT COUNT(*) FROM #__xbpersons AS mp WHERE mp.catid = c.id) AS pcnt');
			$query->select('(SELECT COUNT(*) FROM #__xbcharacters AS mch WHERE mch.catid = c.id) AS chcnt');
			$query->from('#__categories AS c');
			$query->where('c.id = '.$id);
			
			try {
				$db->setQuery($query);
				$this->item = $db->loadObject();
			} catch (Exception $e) {
				$dberr = $e->getMessage();
				Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
			}
			if ($this->item) {
				$item = &$this->item;
				//get titles and ids of people and chars in this category
				if ($item->pcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('p.id AS pid, CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS title')
						->from('#__categories AS c');
					$query->join('LEFT','#__xbpersons AS p ON p.catid = c.id');
					$query->where('c.id='.$item->id);
					$query->order('p.lastname');
					$db->setQuery($query);
					$item->people = $db->loadObjectList();
				} else {
					$item->people='';
				}
				if ($item->chcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('ch.id AS pid, ch.name AS title')
					->from('#__categories AS c');
					$query->join('LEFT','#__xbcharacters AS ch ON ch.catid = c.id');
					$query->where('c.id='.$item->id);
					$query->order('ch.name');
					$db->setQuery($query);
					$item->chars = $db->loadObjectList();
				} else {
					$item->chars='';
				}
			}
			
			return $this->item;
		} //endif isset
	} //end function getItem
}
		
