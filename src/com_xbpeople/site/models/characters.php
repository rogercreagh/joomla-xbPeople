<?php
/*******
 * @package xbPeople
 * @filesource site/models/characters.php
 * @version 1.0.3.14 17th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Categories;
use Joomla\CMS\Helper\TagsHelper;

class XbpeopleModelCharacters extends JModelList {
	
    public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array ('name', 'category_title','c.title',
					'catid', 'a.catid', 'category_id', 'tagfilt','fcnt','bcnt');
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {
		$app = Factory::getApplication('site');
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
		$categoryId = $app->getUserStateFromRequest('catid', 'catid','');
		$app->setUserState('catid', '');
		$this->setState('categoryId',$categoryId);
		$tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
		$app->setUserState('tagid', '');
		$this->setState('tagId',$tagId);
		
		parent::populateState($ordering, $direction);
		
		//pagination limit
		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 25 );
		$this->setState('limit', $limit);
		$this->setState('list.limit', $limit);
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', $app->get('start'));
		$this->setState('list.start', $limitstart);
		
	}
	
	protected function getListQuery() {
	    $sess = Factory::getSession();
	    $db    = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.id AS id, a.name AS name, 
            a.summary AS summary, a.catid AS catid,
            a.image AS image, a.description AS description, a.state AS published,
            a.created AS created, a.created_by_alias AS created_by_alias,
            a.ordering AS ordering, a.params AS params, a.note AS note');
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
            
            if ($sess->get('xbbooks_ok',false)==1) $query->select('(SELECT COUNT(DISTINCT(bc.book_id)) FROM #__xbbookcharacter AS bc WHERE bc.char_id = a.id) AS bcnt');
            if ($sess->get('xbevents_ok',false)==1) $query->select('(SELECT COUNT(DISTINCT(ec.event_id)) FROM #__xbeventcharacter AS ec WHERE ec.char_id = a.id) AS ecnt');
            if ($sess->get('xbfilms_ok',false)==1) $query->select('(SELECT COUNT(DISTINCT(fc.film_id)) FROM #__xbfilmcharacter AS fc WHERE fc.char_id = a.id) AS fcnt');
            
            $query->select('c.title AS category_title');
            $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
            
            // Filter by published state, we only show published items in the front-end. Both item and its category must be published.
            $query->where('a.state = 1');
            $query->where('c.published = 1');
            
             // Filter by search in title/id/synop
            $search = $this->getState('filter.search');
            
            if (!empty($search)) {
            	if (stripos($search,'s:')===0) {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
            		$query->where('(a.description LIKE ' . $search.' OR a.summary LIKE '.$search.')');
            	} else {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            		$query->where('(a.name LIKE ' . $search. ')');
            	}
            }
            
            $searchbar = (int)$this->getState('params',0)['search_bar'];
            //if menu option set it will take precedence and hide the corresponding filter option
            
           // Filter by category and subcats
            $categoryId = $this->getState('categoryId');
            $this->setState('categoryId','');
            $dosubcats = 0;
            if (empty($categoryId)) {
                $categoryId = $this->getState('params',0,'int')['menu_category_id'];
                $dosubcats=$this->getState('params',0)['menu_subcats'];
            }
            if (($searchbar==1) && ($categoryId==0)){
            	$categoryId = $this->getState('filter.category_id');
            	$dosubcats=$this->getState('filter.subcats');
            }
            if (is_numeric($categoryId))
            {
                $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
            } elseif (is_array($categoryId)) {
                $categoryId = implode(',', $categoryId);
                $query->where($db->quoteName('a.catid') . ' IN ('.$categoryId.')');
            }
            
            //filter by tag
            $tagfilt = $this->getState('tagId');
            // $this->setState('tagId','');
            $taglogic = 0;
            if (empty($tagfilt)) {
                $tagfilt = $this->getState('params')['menu_tag'];
                $taglogic = $this->getState('params')['menu_taglogic']; //1=AND otherwise OR
            }
            
            if (($searchbar==1) && (empty($tagfilt))) { 	//look for menu options
                //look for filter options and ignore menu options
                $tagfilt = $this->getState('filter.tagfilt');
                $taglogic = $this->getState('filter.taglogic'); //1=AND otherwise OR
            }
            
            if (empty($tagfilt)) {
                $subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias LIKE '.$db->quote('com_xbpeople.character').')';
                if ($taglogic === '1') {
                    $query->where('a.id NOT IN '.$subQuery);
                } elseif ($taglogic === '2') {
                    $query->where('a.id IN '.$subQuery);
                }
            } else {
                $tagfilt = ArrayHelper::toInteger($tagfilt);
                $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias = '.$db->quote('com_xbpeople.character').'
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
            $orderCol       = $this->state->get('list.ordering', 'name');
            $orderDirn      = $this->state->get('list.direction', 'ASC');
            switch($orderCol) {
                case 'a.ordering' :
                case 'a.catid' :
                    //needs a menu option to set orderCol to ordering. Also menu option to alllow user to reorder on table
                    $query->order('category_title '.$orderDirn.', a.ordering');
                    break;
                case 'category_title':
                    $query->order('category_title '.$orderDirn.', lastname');
                    break;
                default:
                    $query->order($db->escape($orderCol.' '.$orderDirn));
                    break;
            }
            
            $query->group('a.id');
            return $query;
	}
	
	public function getItems() {
		$items  = parent::getItems();
		$tagsHelper = new TagsHelper;
		
		$app = Factory::getApplication();
		$peep = array();

		if ($items) {
		    for ($i = 0; $i < count($items); $i++) {
		        $peep[$i] = $items[$i]->id;
		    }
		    Factory::getApplication()->setUserState('characters.sortorder', $peep);
		    
		    foreach ($items as $i=>$item) {
		        $item->tags = $tagsHelper->getItemTags('com_xbpeople.character' , $item->id);
		        //get books
		        if ($item->bcnt>0) {
		            $item->books = XbcultureHelper::getPersonBooks($item->id);
		            $item->booklist = XbcultureHelper::makeItemLists($item->books,'','tr',3,'book');
		        }
		        if ($item->ecnt>0) {
		            $item->events = XbcultureHelper::getPersonEvents($item->id);
		            $item->eventlist = XbcultureHelper::makeItemLists($item->events,'','tr',3,'event');
		        }
		        if ($item->fcnt>0) {
		            $item->films = XbcultureHelper::getPersonFilms($item->id);
		            $item->filmlist = XbcultureHelper::makeItemLists($item->films,'','tr',3,'film');
		        }
		        
		    } //end foreach item
		} //end if items
		
		return $items;
	}
		
}
            
            
