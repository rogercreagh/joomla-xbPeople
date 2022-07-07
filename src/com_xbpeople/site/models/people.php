<?php
/*******
 * @package xbPeople
 * @filesource site/models/people.php
 * @version 0.9.9.1 5th July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Categories;
use Joomla\CMS\Helper\TagsHelper;

class XbpeopleModelPeople extends JModelList {

    protected $xbfilmsStatus;
    protected $xbbooksStatus;
    
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array ( 'firstname', 'lastname',
					'catid', 'a.catid', 'category_id', 'tagfilt',
					'category_title', 'c.title',
					'sortdate' );
		}
		$this->xbfilmsStatus = Factory::getSession()->get('xbfilms_ok',false);
		$this->xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
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
		
//		$catid = $app->input->getStr('catid');
//		$this->setState('catid', $catid);
//		$app->setUserState('catid', $catid);
		
		parent::populateState($ordering, $direction);
		
		//pagination limit
		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 25 );
		$this->setState('limit', $limit);
		$this->setState('list.limit', $limit);
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', $app->get('start'));
		$this->setState('list.start', $limitstart);
		
	}
	
	protected function getListQuery() {
		$searchbar = (int)$this->getState('params',0)['search_bar'];
		
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.id AS id, a.firstname AS firstname, a.lastname AS lastname, 
            a.summary AS summary, a.year_born AS year_born, a.year_died AS year_died, a.catid AS catid,
            a.portrait AS portrait, a.biography AS biography, a.state AS published,
            a.created AS created, a.created_by_alias AS created_by_alias,
            a.ordering AS ordering, a.params AS params, a.note AS note');
		$query->select('IF((year_born>-9999),year_born,year_died) AS sortdate');
//            ->select('(GROUP_CONCAT(p.person_id SEPARATOR '.$db->quote(',') .')) AS personlist');
 		$query->from($db->quoteName('#__xbpersons','a'));
//             	->join('LEFT OUTER',$db->quoteName('#__xbbookperson', 'p') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('p.person_id'))
//                 ->where('p.book_id IS NOT NULL');
// 		$query->select('COUNT(DISTINCT p.book_id) AS bcnt');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
        $query->select('c.title AS category_title');
           
        // Filter by published state, we only show published items in the front-end. Both item and its category must be published.
        $query->where('a.state = 1');
            //$query->where('c.published = 1'); 
            
        // Filter by search in title/id/synop
        $search = $this->getState('filter.search');
            
            if (!empty($search)) {
            	if (stripos($search,'s:')===0) {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
            		$query->where('(a.biography LIKE ' . $search.' OR a.summary LIKE '.$search.')');
            	} else {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            		$query->where('(a.firstname LIKE ' . $search . ' OR a.lastname LIKE ' . $search . ')');
            	}
            }
            
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
//            if ($this->getState('catid')>0) { $categoryId = $this->getState('catid'); }
            if ($categoryId > 0) {
            	if ($dosubcats) {
            		$catlist = $categoryId;
            		$subcatlist = XbpeopleHelper::getChildCats($categoryId,'com_xbpeople');
            		if ($subcatlist) { $catlist .= ','.implode(',',$subcatlist);}
            		$query->where('a.catid IN ('.$catlist.')');
            	} else {
            		$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
            	}
            }
//TODO add book film and event role filter           
//         	switch ($prole) {
//         		case 1: //all
//          			break;
//         		case 2: //authors
//         			$query->where('p.role = '. $db->quote('author'));
//         			break;
//         		case 3: //editors
//          			$query->where('p.role = '. $db->quote('editor'));
//        			break;
//         		case 4: //mention
//         			$query->where('p.role = '. $db->quote('mention'));
//         			break;
//         		case 5: //other
//         		    $query->where('p.role = '. $db->quote('other'));
//         		    break;
//         		default:
//         			break;        			
//         	}
           
            //filter by tag
            $tagfilt = $this->getState('tagId');
 //           $this->setState('tagId','');
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
            // Run simplified query when filtering by one tag.
            if (is_array($tagfilt) && count($tagfilt) === 1) {
            	$tagfilt = $tagfilt[0];
            }
            
            if ($tagfilt && is_array($tagfilt)) {
            	$tagfilt = ArrayHelper::toInteger($tagfilt);           	
            	if ($taglogic) { //AND logic
            		for ($i = 0; $i < count($tagfilt); $i++) {
            			$mapname = 'tagmap'.$i;
            			$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', $mapname).
            					' ON ' . $db->quoteName($mapname.'.content_item_id') . ' = ' . $db->quoteName('a.id'));
            			$query->where( array(
            					$db->quoteName($mapname.'.tag_id') . ' = ' . $tagfilt[$i],
            					$db->quoteName($mapname.'.type_alias') . ' = ' . $db->quote('com_xbpeople.person'))
            					);
            		}
            	} else { //OR logic            		
            		$subQuery = $db->getQuery(true)
            		->select('DISTINCT ' . $db->quoteName('content_item_id'))
            		->from($db->quoteName('#__contentitem_tag_map'))
            		->where(
            				array(
            						$db->quoteName('tag_id') . ' IN (' . implode(',', $tagfilt) . ')',
            						$db->quoteName('type_alias') . ' = ' . $db->quote('com_xbpeople.person'),
            				)
            				);
            		
            		$query->join(
            				'INNER',
            				'(' . $subQuery . ') AS ' . $db->quoteName('tagmap')
            				. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
            				);            		
            	}            	
            } elseif ($tag = (int) $tagfilt) { //simple query for one tag
            	$query->join(
            			'INNER',
            			$db->quoteName('#__contentitem_tag_map', 'tagmap')
            			. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
            			)
            			->where(
            					array(
            							$db->quoteName('tagmap.tag_id') . ' = ' . $tagfilt,
            							$db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_xbpeople.person')
            					)
            					);
            } //endif tagfilt
            
            // Add the list ordering clause.
            $orderCol       = $this->state->get('list.ordering', 'lastname');
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
                case 'bcnt':
                	$query->order('bcnt '.$orderDirn.', lastname');
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
		for ($i = 0; $i < count($items); $i++) {
			$peep[$i] = $items[$i]->id;
		}
		$app->setUserState('people.sortorder', $peep);
		
		foreach ($items as $i=>$item) {
// 			//get books by role if they are being displayed...
// 			$item->books = XbbooksGeneral::getPersonRoleArray($item->id);
// 			$cnts = array_count_values(array_column($item->books, 'role'));
// 			$item->acnt = (key_exists('author',$cnts))?$cnts['author'] : 0;
// 			$item->ecnt = (key_exists('editor',$cnts))?$cnts['editor'] : 0;
// 			$item->ocnt = (key_exists('other',$cnts))?$cnts['other'] : 0;
// 			$item->mcnt = (key_exists('mention',$cnts))?$cnts['mention'] : 0;
// 			if (count($item->books)>0) {
// 			    $item->allbooks = XbbooksGeneral::makeLinkedNameList($item->books,'','<br />',true,false,2);
// 			} else {
// 			    $item->allbooks='';
// 			}
// 			//make author/editor/char lists
// 			if ($item->acnt == 0){
// 				$item->alist = '';
// 			} else {
// 				$item->alist = XbbooksGeneral::makeLinkedNameList($item->books,'author',',', true);
// 			}
// 			if ($item->ecnt == 0){
// 				$item->elist = '';
// 			} else {
// 				$item->elist = XbbooksGeneral::makeLinkedNameList($item->books,'editor',',',true);
// 			}
// 			if ($item->ocnt == 0){
// 				$item->olist = '';
// 			} else {
// 				$item->olist = XbbooksGeneral::makeLinkedNameList($item->books,'other',', ',true);
// 			}
// 			if ($item->mcnt == 0){
// 				$item->mlist = '';
// 			} else {
// 				$item->mlist = XbbooksGeneral::makeLinkedNameList($item->books,'mention',', ',true);
// 			}
			
			$item->tags = $tagsHelper->getItemTags('com_xbpeople.person' , $item->id);
			
			$item->bookcnt = 0;
			if ($this->xbbooksStatus) {
			    $db    = Factory::getDbo();
			    $query = $db->getQuery(true);
			    $query->select('COUNT(*)')->from('#__xbbookperson');
			    $query->where('person_id = '.$db->quote($item->id));
			    $db->setQuery($query);
			    $item->bookcnt = $db->loadResult();
			}
			$item->filmcnt = 0;
			if ($this->xbfilmsStatus) {
				$db    = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('COUNT(*)')->from('#__xbfilmperson');
				$query->where('person_id = '.$db->quote($item->id));
				$db->setQuery($query);
				$item->filmcnt = $db->loadResult();
			}
		} //end foreach item
		return $items;
	}
		
}
            
            