<?php
/*******
 * @package xbPeople
 * @filesource admin/models/characters.php
 * @version 1.0.2.2 8th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbpeopleModelCharacters extends JModelList {
	
	public function __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
            	'id', 'a.id', 'name', 'a.name',
                'ordering','a.ordering',
                'category_title', 'c.title',
                'catid', 'a.catid', 'category_id',
                'created', 'a.created',
                'published','a.state', 'fcnt', 'bcnt','ecnt');
        }
        
        parent::__construct($config);
    }
    
    protected function getListQuery() {
        $sess = Factory::getSession();
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('a.id AS id, a.name AS name, a.alias AS alias, 
			a.summary AS summary, a.image AS image, a.description AS description, 
			a.catid AS catid, a.state AS published, a.created AS created, a.created_by AS created_by, 
			a.created_by_alias AS created_by_alias, a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note')

            ->from($db->quoteName('#__xbcharacters','a'));
        
            if ($sess->get('xbbooks_ok',false)==1) {
                $query->select('(SELECT COUNT(DISTINCT(bc.book_id)) FROM #__xbbookcharacter AS bc WHERE bc.char_id = a.id) AS bcnt');
            } else {
                $query->select('0 AS bcnt');
            }
            if ($sess->get('xbfilms_ok',false)==1) {
                $query->select('(SELECT COUNT(DISTINCT(fc.film_id)) FROM #__xbfilmcharacter AS fc WHERE fc.char_id = a.id) AS fcnt');
            } else {
                $query->select('0 AS fcnt');
            }
            if ($sess->get('xbevents_ok',false)==1) {
                $query->select('(SELECT COUNT(DISTINCT(ec.event_id)) FROM #__xbeventcharacter AS ec WHERE ec.char_id = a.id) AS ecnt');
            } else {
                $query->select('0 AS ecnt');
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
                $query->where('(summary LIKE ' . $search.' OR description LIKE '.$search.')');
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(name LIKE ' . $search.')');               
            }
        }
        
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('a.state').' = ' . (int) $published);
        }
        
        //Filter orphans
        $orphfilt = $this->getState('filter.orphans');
        if ($orphfilt == '1') {
            $query->select('0');
            $query->having('(bcnt + ecnt + fcnt) = 0');
        } elseif ($orphfilt == '2') {
            $query->select('0');
            $query->having('(bcnt + ecnt + fcnt) > 0');
        }
        
        // Filter by category.
        $app = Factory::getApplication();
        $categoryId = $app->getUserStateFromRequest('catid', 'catid','');
        $app->setUserState('catid', '');
        if ($categoryId=='') {
        	$categoryId = $this->getState('filter.category_id');
        }
//        $subcats=0;
        if (is_numeric($categoryId)) {
        	$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
        } elseif (is_array($categoryId)) {
            $categoryId = implode(',', $categoryId);
            $query->where($db->quoteName('a.catid') . ' IN ('.$categoryId.')');
        }
        
        //filter by tags
        $tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
        $app->setUserState('tagid', '');
        if (!empty($tagId)) {
            $tagfilt = array(abs($tagId));
            $taglogic = $tagId>0 ? 0 : 2;
        } else {
            $tagfilt = $this->getState('filter.tagfilt');
            $taglogic = $this->getState('filter.taglogic');  //0=ANY 1=ALL 2= None
        }
        
        if (($taglogic === '2') && (empty($tagfilt))) {
            //if if we select tagged=excl and no tags specified then only show untagged items
            $subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias LIKE '.$db->quote('com_xb%.person').')';
            $query->where('a.id NOT IN '.$subQuery);
        }
        
        if ($tagfilt && is_array($tagfilt)) {
            $tagfilt = ArrayHelper::toInteger($tagfilt);
            $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias = '.$db->quote('com_xbpeople.person').'
                AND tmap.content_item_id = a.id)';
            switch ($taglogic) {
                case 1: //all
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $query->where($tagfilt[$i].' IN '.$subquery);
                    }
                    break;
                case 2: //none
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $query->where($tagfilt[$i].' NOT IN '.$subquery);
                    }
                    break;
                default: //any
                    if (count($tagfilt)==1) {
                        $query->where($tagfilt[0].' IN '.$subquery);
                    } else {
                        $tagIds = implode(',', $tagfilt);
                        if ($tagIds) {
                            $subQueryAny = '(SELECT DISTINCT content_item_id FROM #__contentitem_tag_map
                                WHERE tag_id IN ('.$tagIds.') AND type_alias = '.$db->quote('com_xbpeople.character').')';
                            $query->innerJoin('(' . (string) $subQueryAny . ') AS tagmap ON tagmap.content_item_id = a.id');
                        }
                    }
                    break;
            }
        } //end if $tagfilt
        
        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'name');
        $orderDirn 	= $this->state->get('list.direction', 'asc');
        
        if ($orderCol == 'a.ordering' || $orderCol == 'a.catid') {
            $orderCol = 'category_title '.$orderDirn.', a.ordering';
        }
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        if ($orderCol != 'name') {
        	$query->order('name ASC');
        }
        
        $query->group('a.id');
        
        return $query;
    }
    
    public function getItems() {
        $items  = parent::getItems();
        // we are going to add the list of characters for each film
        $tagsHelper = new TagsHelper;
        
        foreach ($items as $i=>$item) {
            if ($item->bcnt > 0) {
                $item->books = XbcultureHelper::getCharBooks($item->id);
                $item->booklist = XbcultureHelper::makeLinkedNameList($item->books,'','ul',true, 3);
            }
            if ($item->ecnt > 0) {
                $item->events = XbcultureHelper::getCharEvents($item->id);
                $item->eventlist = XbcultureHelper::makeLinkedNameList($item->events,'','ul',true, 3);
            }
            if ($item->fcnt > 0) {
                $item->films = XbcultureHelper::getCharFilms($item->id);
                $item->filmlist = XbcultureHelper::makeLinkedNameList($item->films,'','ul',true, 3);
            }
                    	        	
        	$item->tags = $tagsHelper->getItemTags('com_xbpeople.character' , $item->id);
        } //end foreach item
	    return $items;
    }

}
