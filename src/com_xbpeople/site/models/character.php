<?php
/*******
 * @package xbPeople
 * @filesource site/models/character.php
 * @version 0.9.9.1 1st July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class XbpeopleModelCharacter extends JModelItem {
	
	protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('cnar.id', $id);
		
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
				    $item->filmlist = $this->getCharFilms($item->id,'','rel_year DESC');
				    $item->filmcnt = count($item->filmlist);
				}
				$item->bookcnt = 0;
				if ($this->xbbooksStatus) {
				    $item->booklist = $this->getCharBookRoles($item->id,'','pubyear ASC');
				    $item->bookcnt = count($item->booklist);
				}
												
			}
		}
		return $this->item;
	}

	/**
	 * @name getCharBookRoles()
	 * @desc for given person returns and array of books and roles
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public function getCharBookRoles(int $charid, $order='title ASC') {
	    $blink = 'index.php?option=com_xbbooks';
	    $blink .= '&view=book&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.char_note, b.title, b.pubyear, b.id, b.state AS bstate')
	    ->from('#__xbbookperson AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.char_id = "'.$charid.'"' );
	    $query->where('b.state = 1');
	    $query->order('b.'.$order); 
	    $db->setQuery($query);
	    $list = $db->loadObjectList();
	    foreach ($list as $i=>$item){
	        $tlink = Route::_($blink . $item->id);
	        $item->display = $item->title;
	        $item->link = '<a href="'.$tlink.'">'.$item->display.'</a>';
	    }
	    return $list;
	}
	
	/**
	 * @name getCharFilm()
	 * @desc for given person returns and array of films
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public function getCharFilms(int $charid,$order='title ASC') {
	    $flink = 'index.php?option=com_xbfilms';
	    $flink .= '&view=film&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.char_note, b.title, b.rel_year, b.id, b.state AS bstate')
	    ->from('#__xbfilmcharacter AS a')
	    ->join('LEFT','#__xbfilms AS b ON b.id=a.film_id')
	    ->where('a.char_id = "'.$charnid.'"' );
	    $query->where('b.state = 1');
        $query->order('b.'.$order); //this will order roles as author, editor, mention, other, publisher,
	    $db->setQuery($query);
	    $list = $db->loadObjectList();
	    foreach ($list as $i=>$item){
	        $tlink = Route::_($flink . $item->id);
	        $item->display = $item->title;
	        $item->link = '<a href="'.$tlink.'">'.$item->display.'</a>';
	    }
	    return $list;
	}
	
}
	
