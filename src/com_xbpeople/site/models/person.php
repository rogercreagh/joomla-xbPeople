<?php
/*******
 * @package xbPeople
 * @filesource site/models/person.php
 * @version 0.9.9.4 26th July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbpeopleModelPerson extends JModelItem {
	
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
				
				$item->filmcnt = 0;
				if ($this->xbfilmsStatus) {
				    $item->filmlist = XbcultureHelper::getPersonFilmRoles($item->id,'','rel_year DESC',2);
				    $item->filmcnt = count($item->filmlist);
				}
				$item->bookcnt = 0;
				if ($this->xbbooksStatus) {
				    $item->booklist = XbcultureHelper::getPersonBookRoles($item->id,'','pubyear ASC',2);
				    $item->bookcnt = count($item->booklist);
				}
			}
		}
		return $this->item;
	}
	
}
	
