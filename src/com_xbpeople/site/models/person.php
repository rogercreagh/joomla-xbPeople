<?php
/*******
 * @package xbPeople
 * @filesource site/models/person.php
 * @version 0.9.9.0 30th June 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class XbpeopleModelPerson extends JModelItem {
	
    protected $xbfilmsStatus;
    protected $xbbooksStatus;
    
	public function __construct($config = array()) {
		$this->xbfilmsStatus = XbcultureHelper::checkComponent('com_xbfilms');
		$this->xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
		parent::__construct($config);
	}
	
	protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('person.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('person.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.firstname AS firstname, a.lastname AS lastname, a.portrait AS portrait, 
				a.summary AS summary, a.biography AS biography, a.year_born AS year_born, a.year_died AS year_died,
				a.nationality AS nationality, a.ext_links AS ext_links, 
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbpersons AS a');
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
				
				// Convert the JSON-encoded links info into an array
				$item->ext_links = json_decode($item->ext_links);
				$item->ext_links_list ='';
				$item->ext_links_cnt = 0;
				if(is_object($item->ext_links)) {
					$item->ext_links_cnt = count((array)$item->ext_links);
					$item->ext_links_list = '<ul>';
					foreach($item->ext_links as $lnk) {
						$item->ext_links_list .= '<li><a href="'.$lnk->link_url.'" '.$target.'>'.$lnk->link_text.'</a> <i>'.$lnk->link_desc.'</i></li>';
					}
					$item->ext_links_list .= '</ul>';
				}
// 				$item->books = XbbooksGeneral::getPersonRoleArray($item->id);
// 				$item->bcnt = count($item->books);
// 				$cnts = array_count_values(array_column($item->books, 'role'));
// 				$item->acnt = (key_exists('author',$cnts))?$cnts['author'] : 0;
// 				$item->ecnt = (key_exists('editor',$cnts))?$cnts['editor'] : 0;
// 				$item->mcnt = (key_exists('mention',$cnts))?$cnts['mention'] : 0;
// 				$item->ocnt = (key_exists('other',$cnts))?$cnts['other'] : 0;
				
// 				//make author/editor/char lists
// 				if ($item->acnt == 0){
// 					$item->alist = '';
// 				} else {
// 					$item->alist = XbbooksGeneral::makeLinkedNameList($item->books,'author','<br />', true, false, 2);
// 				}
// 				if ($item->ecnt == 0){
// 					$item->elist = '';
// 				} else {
// 					$item->elist = XbbooksGeneral::makeLinkedNameList($item->books,'editor','<br />',true, false, 2);
// 				}
// 				if ($item->mcnt == 0){
// 				    $item->mlist = '';
// 				} else {
// 				    $item->mlist = XbbooksGeneral::makeLinkedNameList($item->books,'mention','<br />',true, false, 2);
// 				}
// 				if ($item->ocnt == 0){
// 				    $item->olist = '';
// 				} else {
// 				    $item->olist = XbbooksGeneral::makeLinkedNameList($item->books,'other','<br />',true, false, 1);
// 				}

//TODO get film and book titles and roles
				
				$item->filmcnt = 0;
				if ($this->xbfilmsStatus) {
				    $db    = Factory::getDbo();
				    $query = $db->getQuery(true);
				    $query->select('COUNT(*)')->from('#__xbfilmperson');
				    $query->where('person_id = '.$db->quote($item->id));
				    $db->setQuery($query);
				    $item->filmcnt = $db->loadResult();
				}
				$item->bookcnt = 0;
				if ($this->xbbooksStatus) {
				    $db    = Factory::getDbo();
				    $query = $db->getQuery(true);
				    $query->select('COUNT(*)')->from('#__xbbookperson');
				    $query->where('person_id = '.$db->quote($item->id));
				    $db->setQuery($query);
				    $item->bookcnt = $db->loadResult();
				    $item->blist = $this->getPersonBookRoles($item->id,'','pubyear');
				}
				
			}
		}
		return $this->item;
	}

	/**
	 * @name getPersonBookRoles()
	 * @desc for given person returns and array of books and roles
	 * @param int $personid
	 * @param string $role - if not blank only get the specified role
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public function getPersonBookRoles(int $personid, $role='',$order='title') {
	    $blink = 'index.php?option=com_xbbooks';
//	    $blink .= $edit ? '&task=book.edit&id=' : '&view=book&id=';
	    $blink .= '&view=book&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note, b.title, b.subtitle, b.pubyear, b.id, b.state AS bstate')
	    ->from('#__xbbookperson AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    $query->where('b.state = 1');
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"')->order('b.'.$order.' ASC');
	    } else {
	        $query->order('a.role ASC')->order('b.'.$order.' ASC'); //this will order roles as author, editor, mention, other, publisher,
	    }
	    $db->setQuery($query);
	    $list = $db->loadObjectList();
	    foreach ($list as $i=>$item){
	        $tlink = Route::_($blink . $item->id);
	        //if not published highlight in yellow if editable or grey if view
	        if ($item->bstate != 1) {
// 	            $flag = $edit ? 'xbhlt' : 'xbdim';
// 	            $item->display = '<span class="'.$flag.'">'.$item->title.'</span>';
	        } else {
	            $item->display = $item->title;
	        }
	        //if item not published only link if to edit page
//	        if (($edit) || ($item->bstate == 1)) {
	            $item->link = '<a href="'.$tlink.'">'.$item->display.'</a>';
//	        } else {
//	            $item->link = $item->display;
//	        }
	    }
	    return $list;
	}

	
	
	
}
	
