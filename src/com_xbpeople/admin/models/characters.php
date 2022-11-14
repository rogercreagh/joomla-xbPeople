<?php
/*******
 * @package xbPeople
 * @filesource admin/models/characters.php
 * @version 0.9.10.3 14th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbpeopleModelCharacters extends JModelList {

	protected $xbbooksStatus;
	protected $xbfilmsStatus;
	
	public function __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
            	'id', 'a.id',
                'name', 'a.name',
                'ordering','a.ordering',
                'category_title', 'c.title',
                'catid', 'a.catid', 'category_id',
                'published','a.state' );
        }
        $this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
        $this->xbfilmsStatus = XbcultureHelper::checkComponent('com_xbfilms');
        
        parent::__construct($config);
    }
    
    protected function getListQuery() {
        //TODO need to also get roles list with film titles
        // Initialize variables.
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        
        // Create the base select statement.
        $query->select('a.id AS id, a.name AS name, a.alias AS alias, 
			a.summary AS summary, a.image AS image, a.description AS description, 
			a.catid AS catid, a.state AS published, a.created AS created, a.created_by AS created_by, 
			a.created_by_alias AS created_by_alias, a.checked_out AS checked_out, a.checked_out_time AS checked_out_time, 
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note')

            ->from($db->quoteName('#__xbcharacters','a'));
        
            if ($this->xbfilmsStatus) {
            	$query->join('LEFT',$db->quoteName('#__xbfilmcharacter', 'f') . ' ON ' . $db->quoteName('f.char_id') . ' = ' .$db->quoteName('a.id'));
            	$query->select('COUNT(DISTINCT f.film_id) AS fcnt');
            } else {
            	$query->select('0 AS fcnt');
            }
            if ($this->xbbooksStatus) {
            	$query->join('LEFT',$db->quoteName('#__xbbookcharacter', 'b') . ' ON ' . $db->quoteName('b.char_id') . ' = ' .$db->quoteName('a.id'));
            	$query->select('COUNT(DISTINCT b.book_id) AS bcnt');
            } else {
            	$query->select('0 AS bcnt');
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
            $query->where('state = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(state IN (0, 1))');
        }
        
        // Filter by category.
        $app = Factory::getApplication();
        $categoryId = $app->getUserStateFromRequest('catid', 'catid','');
        $app->setUserState('catid', '');
        if ($categoryId=='') {
        	$categoryId = $this->getState('filter.category_id');
        }
//        $subcats=0;
        if (is_numeric($categoryId))
        {
        	$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
        }
        
//         //        $subcats = $this->getState('filter.subcats');
//         if (is_numeric($categoryId)) {
//             if ($subcats) {
//                 //                $query->where('a.catid IN ('.(int)$categoryId.','.self::getSubCategoriesList($categoryId).')');
//             } else {
//                 $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
//             }
//         }

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
        
        $db    = Factory::getDbo();
        foreach ($items as $i=>$item) {
        	$item->bookcnt = 0;
        	$item->blist='';
        	if ($item->bcnt>0) {
        		//we want a list of book title and role for each character (item)
        		$query = $db->getQuery(true);
        		$query->select('b.title')->from('#__xbbooks AS b');
        		$query->join('LEFT', '#__xbbookcharacter AS bp ON bp.book_id = b.id');
        		$query->where('bp.char_id = '.$db->quote($item->id));
        		$query->order('b.title ASC');
        		$db->setQuery($query);
        		$item->blist = $db->loadObjectList();
        		$item->bookcnt = count($item->blist);
        	} //bcnt is the number of books, bookcnt is the number of roles (maybe 2 roles in a book)
        	
        	
        	$item->filmcnt = 0;
        	$item->flist='';
        	if ($this->xbfilmsStatus) {
        		$query = $db->getQuery(true);
        		$query->select('DISTINCT f.title')->from('#__xbfilms AS f');
        		$query->join('LEFT', '#__xbfilmcharacter AS fp ON fp.film_id = f.id');
        		$query->where('fp.char_id = '.$db->quote($item->id));
        		$query->order('f.title ASC');
        		$db->setQuery($query);
        		$item->flist = $db->loadObjectList();
        		$item->filmcnt = count($item->flist);
        	}
        	
        	$item->tags = $tagsHelper->getItemTags('com_xbpeople.character' , $item->id);
        } //end foreach item
	        return $items;
    }

}
