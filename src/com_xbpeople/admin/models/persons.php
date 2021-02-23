<?php
/*******
 * @package xbPeople
 * @filesource admin/model/persons.php
 * @version 0.2.1 19th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbpeopleModelPersons extends JModelList {
    
	protected $xbbooksStatus;
	protected $xbfilmsStatus;
	
	public function __construct($config = array()) {
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'a,id',
					'firstname', 'lastname',
					'published', 'a.state',
					'ordering', 'a.ordering',
					'category_title', 'c.title',
					'catid', 'a.catid', 'category_id',
					'sortdate' );
		}
		$this->xbbooksStatus = XbpeopleHelper::checkComponent('com_xbbooks');
		$this->xbfilmsStatus = XbpeopleHelper::checkComponent('com_xbfilms');
		parent::__construct($config);
	}

	protected function getListQuery() {
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.id AS id, a.firstname AS firstname, a.lastname AS lastname, a.alias AS alias,
			a.summary AS summary, a.portrait AS portrait, a.biography AS biography, a.ext_links AS ext_links,
			a.nationality AS nationality, a.year_born AS year_born, a.year_died AS year_died,
			a.catid AS catid, a.state AS published, a.created AS created, a.created_by AS created_by,
			a.created_by_alias AS created_by_alias, a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
		$query->select('IF((year_born>-9999),year_born,year_died) AS sortdate');
		
		$query->from($db->quoteName('#__xbpersons','a'));
		
		if ($this->xbfilmsStatus) {
			$query->join('LEFT',$db->quoteName('#__xbfilmperson', 'f') . ' ON ' . $db->quoteName('f.person_id') . ' = ' .$db->quoteName('a.id'));
		}
		if ($this->xbbooksStatus) {
			$query->join('LEFT',$db->quoteName('#__xbbookperson', 'b') . ' ON ' . $db->quoteName('b.person_id') . ' = ' .$db->quoteName('a.id'));
		}
		
		$query->select('c.title AS category_title')
		->join('LEFT', '#__categories AS c ON c.id = a.catid');
		
		// Filter: like / search
		$search = $this->getState('filter.search');
		
		if (!empty($search)) {
			if (stripos($search, 'i:') === 0) {
				$query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 2));
			} elseif (stripos($search, 'b:') === 0) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(summary LIKE ' . $search.' OR biography LIKE '.$search.')');
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(lastname LIKE ' . $search.' OR firstname LIKE '.$search.')');
			}
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		
		if (is_numeric($published)) {
			$query->where('published = ' . (int) $published);
			//        } elseif ($published === '') {
			//$query->where('(state IN (0, 1))');
		}
		
		//Filter orphans to filter by specific roles do it in the individual components
		$rolefilt = $this->getState('filter.rolefilt');
		if ($rolefilt=='orphans') {
			if ($this->xbfilmsStatus) {
				$query->where('f.id IS NULL');
			}
			if ($this->xbbooksStatus) {
				$query->where('b.id IS NULL');
			}
		}
		
		// Filter by category.
		$app = Factory::getApplication();
		$categoryId = $app->getUserStateFromRequest('catid', 'catid','');
		$app->setUserState('catid', '');
		if ($categoryId=='') {
			$categoryId = $this->getState('filter.category_id');
		}
		$subcats=0;
		if (is_numeric($categoryId))
		{
			$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
		}
		
		//filter by tags
		$tagfilt = $this->getState('filter.tagfilt');
		$taglogic = $this->getState('filter.taglogic');  //0=ANY 1=ALL 2= None
		
		if (($taglogic === '2') && (empty($tagfilt))) {
			//if if we select tagged=excl and no tags specified then only show untagged items
			$subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias = '.$db->quote('com_xbfilms.film').')';
			$query->where('a.id NOT IN '.$subQuery);
		}
		
		if (!empty($tagfilt)) {
			$tagfilt = ArrayHelper::toInteger($tagfilt);
			
			if ($taglogic==2) { //exclude anything with a listed tag
				// subquery to get a virtual table of item ids to exclude
				$subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
					WHERE type_alias = '.$db->quote('com_xbfilms.film').
					' AND tag_id IN ('.implode(',',$tagfilt).'))';
				$query->where('a.id NOT IN '.$subQuery);
			} else {
				if (count($tagfilt)==1)	{ //simple version for only one tag
					$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', 'tagmap')
							. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id') )
							->where(array( $db->quoteName('tagmap.tag_id') . ' = ' . $tagfilt[0],
									$db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_xbfilms.film') )
									);
				} else { //more than one tag
					if ($taglogic == 1) { // match ALL listed tags
						// iterate through the list adding a match condition for each
						for ($i = 0; $i < count($tagfilt); $i++) {
							$mapname = 'tagmap'.$i;
							$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', $mapname).
									' ON ' . $db->quoteName($mapname.'.content_item_id') . ' = ' . $db->quoteName('a.id'));
							$query->where( array(
									$db->quoteName($mapname.'.tag_id') . ' = ' . $tagfilt[$i],
									$db->quoteName($mapname.'.type_alias') . ' = ' . $db->quote('com_xbfilms.film'))
									);
						}
					} else { // match ANY listed tag
						// make a subquery to get a virtual table to join on
						$subQuery = $db->getQuery(true)
						->select('DISTINCT ' . $db->quoteName('content_item_id'))
						->from($db->quoteName('#__contentitem_tag_map'))
						->where( array(
								$db->quoteName('tag_id') . ' IN (' . implode(',', $tagfilt) . ')',
								$db->quoteName('type_alias') . ' = ' . $db->quote('com_xbfilms.film'))
								);
						$query->join(
								'INNER',
								'(' . $subQuery . ') AS ' . $db->quoteName('tagmap')
								. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
								);
						
					} //endif all/any
				} //endif one/many tag
			}
		} //if not empty tagfilt
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'lastname');
		$orderDirn 	= $this->state->get('list.direction', 'asc');
		if ($orderCol == 'a.ordering' || $orderCol == 'a.catid') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		
		$query->group('a.id');
		
		return $query;
	}
	
	public function getItems() {
		$items  = parent::getItems();
		// we are going to add the list of people (with roles) for teach film
		//and apply any film title filter
		$tagsHelper = new TagsHelper;
		
		foreach ($items as $i=>$item) {
			$db    = Factory::getDbo();
			$item->bookcnt = 0;
			if ($this->xbbooksStatus) {
				$item->blist='';
				$query = $db->getQuery(true);
				$query->select('DISTINCT title, bp.role')->from('#__xbbooks AS b');
				$query->join('LEFT', '#__xbbookperson AS bp ON bp.book_id = b.id');
				$query->where('person_id = '.$db->quote($item->id));
				$query->order('b.title ASC');
				$db->setQuery($query);
				$item->blist = $db->loadObjectList();
				$item->bookcnt = count($item->blist);
			}
			
			
			$item->filmcnt = 0;
			if ($this->xbfilmsStatus) {
				$item->flist='';
				$query = $db->getQuery(true);
				$query->select('DISTINCT title, fp.role')->from('#__xbfilms AS f');
				$query->join('LEFT', '#__xbfilmperson AS fp ON fp.film_id = f.id');
				$query->where('person_id = '.$db->quote($item->id));
				$query->order('f.title ASC');
				$db->setQuery($query);
				$item->flist = $db->loadObjectList();
				$item->filmcnt = count($item->flist);
			}
			
			$item->ext_links = json_decode($item->ext_links);
			$item->ext_links_list ='';
			$item->ext_links_cnt = 0;
			if(is_object($item->ext_links)) {
				$item->ext_links_cnt = count((array)$item->ext_links);
				foreach($item->ext_links as $lnk) {
					$item->ext_links_list .= '<a href="'.$lnk->link_url.'" target="_blank">'.$lnk->link_text.'</a>, ';
				}
				$item->ext_links_list = trim($item->ext_links_list,', ');
			} //end if is_object
			$item->filmtags = $tagsHelper->getItemTags('com_xbfilms.person' , $item->id);
			$item->booktags = $tagsHelper->getItemTags('com_xbbooks.person' , $item->id);
		} //end foreach item
		return $items;
	}
	
}