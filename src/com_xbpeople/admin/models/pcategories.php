<?php
/*******
 * @package xbPeople
 * @filesource admin/models/pcategories.php
 * @version 0.9.6.a 16th December 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;

class XbpeopleModelPcategories extends JModelList {
	
    protected $xbbooks_ok;
    protected $xbfilms_ok;
    
    public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'title', 'path','bcnt','pcnt','rcnt','chcnt',
					'published', 'parent'
			);
		}
		$this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok',false);
		$this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok',false);
		parent::__construct($config);
	}
		
	protected function getListQuery() {
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT c.id AS id, c.path AS path, c.level AS level, c.title AS title, c.description AS description,
		 c.note AS note, c.published AS published,  c.checked_out AS checked_out, c.extension,
         c.checked_out_time AS checked_out_time, c.lft');
		$query->from('#__categories AS c');
		
		$query->select('(SELECT COUNT(*) FROM #__xbpersons AS tp WHERE tp.catid = c.id ) AS pcnt');
		$query->select('(SELECT COUNT(*) FROM #__xbcharacters AS tch WHERE tch.catid = c.id ) AS chcnt');
		if ($this->xbbooks_ok) {
    		$query->select('(SELECT COUNT(DISTINCT p.id) FROM #__xbpersons AS p LEFT JOIN #__xbbookperson AS bp ON bp.person_id = p.id WHERE p.catid = c.id AND bp.id IS NOT NULL ) AS bpcnt');
    		$query->select('(SELECT COUNT(DISTINCT ch.id) FROM #__xbcharacters AS ch LEFT JOIN #__xbbookcharacter AS bc ON bc.char_id = ch.id WHERE ch.catid = c.id AND bc.id IS NOT NULL ) AS bchcnt');		    
		}
		if ($this->xbfilms_ok) {
    		$query->select('(SELECT COUNT(DISTINCT p.id) FROM #__xbpersons AS p LEFT JOIN #__xbfilmperson AS bp ON bp.person_id = p.id WHERE p.catid = c.id AND bp.id IS NOT NULL ) AS fpcnt');
    		$query->select('(SELECT COUNT(DISTINCT ch.id) FROM #__xbcharacters AS ch LEFT JOIN #__xbfilmcharacter AS bc ON bc.char_id = ch.id WHERE ch.catid = c.id AND bc.id IS NOT NULL ) AS fchcnt');		    
		}
		
		$query->where('c.extension = '.$db->quote('com_xbpeople'));
		$query->order('c.path');
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('published = ' . (int) $published);
		}
		
		//filter by branch
		$branch = $this->getState('filter.branch');
		if ($branch != '') {
			$query->where('c.alias LIKE '.$db->quote('%'.$branch.'%'));
		}
				
		// Filter by search in title/id/desc
		$search = $this->getState('filter.search');		
		if (!empty($search)) {
			if (stripos($search, 'i:') === 0) {
				$query->where($db->quoteName('c.id') . ' = ' . (int) substr($search, 2));
			} elseif ((stripos($search,'s:')===0) || (stripos($search,'d:')===0)) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(c.description LIKE ' . $search.')');
			} elseif (stripos($search,':')!= 1) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(c.title LIKE ' . $search . ')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');		
		$query->order('extension, '.$db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}

	public function getItems() {
		$items  = parent::getItems();
		return $items;
	}
}
