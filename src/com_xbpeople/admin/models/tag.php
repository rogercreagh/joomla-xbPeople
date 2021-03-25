	<?php
/*******
 * @package xbPeople
 * @filesource admin/models/tag.php
 * @version 0.4.4 24th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Component\ComponentHelper;

class XbpeopleModelTag extends JModelItem {
	
	protected function populateState() {
		$app = Factory::getApplication();
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('tag.id', $id);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			$params = ComponentHelper::getParams('com_xbpeople');
			$people_sort = $params->get('people_sort');
			
			$id    = is_null($id) ? $this->getState('tag.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('t.id AS id, t.path AS path, t.title AS title, t.note AS note, t.description AS description, 
				t.alias, t.published AS published');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mfp WHERE mfp.type_alias='.$db->quote('com_xbfilms.person').' AND mfp.tag_id = t.id) AS fpcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mfc WHERE mfc.type_alias='.$db->quote('com_xbfilms.character').' AND mfc.tag_id = t.id) AS fchcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mbp WHERE mbp.type_alias='.$db->quote('com_xbbooks.person').' AND mbp.tag_id = t.id) AS bpcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mbc WHERE mbc.type_alias='.$db->quote('com_xbbooks.character').' AND mbc.tag_id = t.id) AS bchcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mpp WHERE mpp.type_alias='.$db->quote('com_xbpeople.person').' AND mpp.tag_id = t.id) AS ppcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mpc WHERE mpc.type_alias='.$db->quote('com_xbpeople.character').' AND mpc.tag_id = t.id) AS pchcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS ma WHERE ma.tag_id = t.id) AS allcnt ');
			$query->from('#__tags AS t');
			$query->where('t.id = '.$id);
			$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
			
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {				
				$item = &$this->item;
				//calculate how many non person/char items the tag applies to to save doing it later
				$item->othercnt = $item->allcnt - ($item->bpcnt + $item->fpcnt + $item->ppcnt + $item->bchcnt + $item->fchcnt + $item->pchcnt);
				//get titles and ids of people and chars with this tag
				$db    = Factory::getDbo();
				if ($item->ppcnt > 0) {
					$query = $db->getQuery(true);
					if ($people_sort == '0') {
						$query->select('p.id AS pid, CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS title');
					} else {
						$query->select('p.id AS pid, CONCAT(p.lastname,'.$db->quote(', '). ',p.firstname) AS title');
					}
					$query->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbpersons AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbpeople.person'");
					$query->order($people_sort==1 ? 'p.lastname' : 'p.firstname');
					$db->setQuery($query);
					$item->ppeople = $db->loadObjectList();
				} else {
					$item->ppeople=array();
				}
				if ($item->fpcnt > 0) {
					$query = $db->getQuery(true);
					if ($people_sort == '0') {
						$query->select('p.id AS pid, CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS title');
					} else {
						$query->select('p.id AS pid, CONCAT(p.lastname,'.$db->quote(', '). ',p.firstname) AS title');
					}
					$query->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbpersons AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbfilms.person'");
					$query->order($people_sort==1 ? 'p.lastname' : 'p.firstname');
					$db->setQuery($query);
					$item->fpeople = $db->loadObjectList();
				} else {
					$item->fpeople=array();
				}
				if ($item->bpcnt > 0) {
					$query = $db->getQuery(true);
					if ($people_sort == '0') {
						$query->select('p.id AS pid, CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS title');
					} else {
						$query->select('p.id AS pid, CONCAT(p.lastname,'.$db->quote(', '). ',p.firstname) AS title');
					}
					$query->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbpersons AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbbooks.person'");
					$query->order($people_sort==1 ? 'p.lastname' : 'p.firstname');
					$db->setQuery($query);
					$item->bpeople = $db->loadObjectList();
				} else {
					$item->bpeople=array();
				}
				
				if ($item->pchcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('p.id AS pid, p.name AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbcharacters AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbpeople.character'");
					$query->order('p.lastname');
					$db->setQuery($query);
					$item->pchars = $db->loadObjectList();
				} else {
					$item->pchars=array();
				}
				if ($item->bchcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('p.id AS pid, p.name AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbcharacters AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbbooks.character'");
					$query->order('p.lastname');
					$db->setQuery($query);
					$item->bchars = $db->loadObjectList();
				} else {
					$item->bchars=array();
				}
				if ($item->fchcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('p.id AS pid, p.name AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbcharacters AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbfilms.character'");
					$query->order('p.lastname');
					$db->setQuery($query);
					$item->fpchars = $db->loadObjectList();
				} else {
					$item->fchars=array();
				}
				
				if ($item->othercnt > 0) {
					$query = $db->getQuery(true);
					$query->select('m.type_alias AS type_alias, m.core_content_id, c.core_title AS core_title, c.core_content_item_id AS item_id'); 
					$query->from('#__contentitem_tag_map AS m');
					$query->join('LEFT','#__ucm_content AS c ON m.core_content_id = c.core_content_id');
					$query->where('m.tag_id = '.$item->id);
					$query->where('m.type_alias NOT LIKE '.$db->quote('com_xb%.character').' AND m.type_alias NOT LIKE '.$db->quote('com_xb%.person'));
					$query->order('m.type_alias, c.core_title');
					$db->setQuery($query);
					$item->others = $db->loadObjectList();
					$item->othcnts = array();					
					foreach ($item->others as $i=>$oth) {
						$comp = substr($oth->type_alias, 0,strpos($oth->type_alias, '.'));
						if (array_key_exists($comp,$item->othcnts)) {
							$item->othcnts[$comp] ++;
						} else {
							$item->othcnts[$comp] = 1;
						}
					}
				} else {
					$item->others = array();
				}
				$item->pcnt = $item->bpcnt + $item->fpcnt + $item->ppcnt;
				$item->chcnt = $item->bchcnt + $item->fchcnt + $item->pchcnt;
			}
			return $this->item;
		} //endif item set			
	} //end getItem()
}
