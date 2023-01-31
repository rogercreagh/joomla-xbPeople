<?php
/*******
 * @package xbPeople
 * @filesource admin/models/group.php
 * @version 1.0.3.3 31st January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;

class XbpeopleModelGroup extends JModelAdmin {
 
	public $typeAlias = 'com_xbpeople.group';
	
	protected $xbbooksStatus;
	protected $xbfilmsStatus;
	protected $xbeventsStatus;
	
	public function __construct($config = array()) {
		$this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
		$this->xbfilmsStatus = XbcultureHelper::checkComponent('com_xbfilms');
		$this->xbeventsStatus = XbcultureHelper::checkComponent('com_xbevents');
		parent::__construct($config);
	}
	
	public function getItem($pk = null) {
		
		if ($item = parent::getItem($pk)) {
			// Convert the metadata field to an array.
			$registry = new Registry($item->metadata);
			$item->metadata = $registry->toArray();
			if (!empty($item->id))
			{
				$tagsHelper = new TagsHelper;
				$item->tags =  $tagsHelper->getTagIds($item->id, 'com_xbpeople.group');
			}
		}
		return $item;
	}
	
	public function getTable($type = 'Group', $prefix = 'XbpeopleTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_xbpeople.group', 'group',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );
        
        if (empty($form)) {
            return false;
        }
        
        $params = ComponentHelper::getParams('com_xbpeople');
        $image_path = $params->get('portrait_path','');
        if ($image_path != '') {
        	$form->setFieldAttribute('picture','directory',$image_path);
        }
        
        return $form;
    }
    
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState( 'com_xbpeople.edit.group.data', array() );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $tagsHelper = new TagsHelper;
        $params = ComponentHelper::getParams('com_xbpeople');
        $grouptaggroup_parent = $params->get('grouptaggroup_parent','');
        if ($grouptaggroup_parent && !(empty($data->tags))) {
            $grouptaggroup_tags = $tagsHelper->getTagTreeArray($grouptaggroup_parent);
            $data->grouptaggroup = array_intersect($grouptaggroup_tags, explode(',', $data->tags));
        }
        
        if (is_object($data)) {
        	if ($this->xbfilmsStatus) {
        	    $data->groupfilmlist=$this->getGroupFilmslist();
        	}
        	if ($this->xbbooksStatus) {
        	    $data->groupbooklist=$this->getGroupBookslist();
        	}
        	if ($this->xbeventsStatus) {
        	    $data->groupeventlist=$this->getGroupEventslist();
        	}
        	$data->grouppersonlist=$this->getGroupPeoplelist();
        }
        return $data;
    }

	protected function prepareTable($table) {
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias = ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe($table->title);
		}

		// Set the values
		if (empty($table->created)) {
			$table->created = $date->toSql();
		}
		if (empty($table->created_by)) {
			$table->created_by = Factory::getUser()->id;
		}
		if (empty($table->created_by_alias)) {
			$table->created_by_alias = Factory::getUser()->username; //make it an option to use name instead of username
		}
		if (empty($table->id)) {
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select('MAX(ordering)')
					->from($db->quoteName('#__xbgroups'));

				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		} else {
			$table->modified    = $date->toSql();
			$table->modified_by = $user->id;			
		}
	}

	public function publish(&$pks, $value = 1) {
	    if (!empty($pks)) {
	        foreach ($pks as $item) {
	            $db = $this->getDbo();
	            $query = $db->getQuery(true)
    	            ->update($db->quoteName('#__xbgroups'))
    	            ->set('state = ' . (int) $value)
    	            ->where('id='.$item);
	            $db->setQuery($query);
	            try {
	                $db->execute();
	            }
	            catch (\RuntimeException $e) {
	                throw new \Exception($e->getMessage(), 500);
	                return false;
	            }	            
	        }
	        return true;
	    }
	}

	public function delete(&$pks) {
	    if (!empty($pks)) {
	        $cnt = 0;
	        $table = $this->getTable('group');
	        foreach ($pks as $i=>$item) {
	            $table->load($item);	            
	            if (!$table->delete($item)) {
	                $grouppeople = ($cnt == 1)? Text::_('XBCULTURE_GROUP') : Text::_('XBCULTURE_GROUPS');
	                Factory::getApplication()->enqueueMessage($cnt.' '.$grouppeople.' deleted. Error deleting the next one.');
	                $this->setError($table->getError());
	                return false;
	            }
	            $table->reset();
	            $cnt++;
	        }
	        $grouppeople = ($cnt == 1)? Text::_('XBCULTURE_GROUP') : Text::_('XBCULTURE_GROUPS');
	        Factory::getApplication()->enqueueMessage($cnt.' '.$grouppeople.' deleted');
	        return true;
	    }
	}

	public function getGroupFilmslist() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('ba.film_id as film_id, ba.role AS role, ba.role_note AS role_note');
		$query->from('#__xbfilmgroup AS ba');
		$query->innerjoin('#__xbfilms AS a ON ba.film_id = a.id');
		$query->where('ba.group_id = '.(int) $this->getItem()->id);
		$query->order('a.title ASC');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	public function getGroupBookslist() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('ba.book_id as book_id, ba.role AS role, ba.role_note AS role_note');
		$query->from('#__xbbookgroup AS ba');
		$query->innerjoin('#__xbbooks AS a ON ba.book_id = a.id');
		$query->where('ba.group_id = '.(int) $this->getItem()->id);
		$query->order('a.title ASC');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
    public function getGroupEventslist() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('ba.event_id as event_id, ba.role AS role, ba.role_note AS role_note');
	    $query->from('#__xbeventgroup AS ba');
	    $query->innerjoin('#__xbevents AS a ON ba.event_id = a.id');
	    $query->where('ba.group_id = '.(int) $this->getItem()->id);
	    $query->order('a.title ASC');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}

	public function getGroupPeoplelist() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('ba.person_id as person_id, ba.role AS role, ba.joined AS joined, ba.until AS until, ba.role_note AS role_note');
	    $query->from('#__xbgroupperson AS ba');
	    $query->innerjoin('#__xbpersons AS a ON ba.person_id = a.id');
	    $query->where('ba.group_id = '.(int) $this->getItem()->id);
	    $query->order('ba.listorder ASC');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}
	
	public function save($data) {
		$input = Factory::getApplication()->input;

		if ($data['grouptaggroup']) {
		    $data['tags'] = ($data['tags']) ? array_unique(array_merge($data['tags'],$data['grouptaggroup'])) : $data['grouptaggroup'];
		}
		
		if (parent::save($data)) {
			if ($this->xbfilmsStatus) {
				$this->storeGroupFilms($this->getState('group.id'),$data['groupfilmlist']);
			}
			if ($this->xbbooksStatus) {
			    $this->storeGroupBooks($this->getState('group.id'),$data['groupbooklist']);
			}
			if ($this->xbeventsStatus) {
			    $this->storeGroupEvents($this->getState('group.id'),$data['groupeventlist']);
			}
		    $this->storeGroupPeople($this->getState('group.id'),$data['grouppersonlist']);
			return true;
		}
		
		return false;
	}
	
	private function storeGroupFilms($group_id, $groupList) {
		$db = $this->getDbo();
		//delete existing role list
		$where = $db->qn('group_id').' = '.$db->q($group_id);
		if (XbcultureHelper::deleteFromTable('#__xbfilmgroup', $where)) {		    
    		//restore the new list
		    foreach ($groupList as $item) {
		        if ($item['film_id']>0) {
    		        $listorder = ($item['oldorder']!=='') ? $item['oldorder'] : '99';
    		        $query = $db->getQuery(true);
        			$query->insert($db->quoteName('#__xbfilmgroup'));
        			$query->columns('group_id,film_id,role,role_note,listorder');
        			$query->values($db->q($group_id).','.$db->q($item['film_id']).
        			    ','.$db->q($item['role']).','.$db->q($item['role_note']).','.$db->q($listorder));
        			$db->setQuery($query);
        			try {
        			    $db->execute();
        			}
        			catch (\RuntimeException $e) {
        			    throw new \Exception($e->getMessage(), 500);
        			    return false;
        			}
    		    }
    		}
		}
	}
	
	private function storeGroupBooks($group_id, $groupList) {
		$db = $this->getDbo();
		//delete existing role list
		$where = $db->qn('group_id').' = '.$db->q($group_id);
		if (XbcultureHelper::deleteFromTable('#__xbbookgroup', $where)) {
		    //restore the new list
    		foreach ($groupList as $item) {
    		    if ($item['book_id']>0) {
    		        $listorder = ($item['oldorder']!=='') ? $item['oldorder'] : '99';
    		        $query = $db->getQuery(true);
    				$query->insert($db->quoteName('#__xbbookgroup'));
    				$query->columns('group_id,book_id,role_note,listorder');
    				$query->values($db->q($group_id).','.$db->q($item['book_id']).
    				    ','.$db->q($item['role_note']).','.$db->q($listorder));
    				$db->setQuery($query);
    				try {
    				    $db->execute();
    				}
    				catch (\RuntimeException $e) {
    				    throw new \Exception($e->getMessage(), 500);
    				    return false;
    				}
    			}
    		}
		}
	}
	
	private function storeGroupEvents($group_id, $groupList) {
	    $db = $this->getDbo();
	    //delete existing role list
	    $where = $db->qn('group_id').' = '.$db->q($group_id);
	    if (XbcultureHelper::deleteFromTable('#__xbeventgroup', $where)) {
	        //restore the new list
	        foreach ($groupList as $item) {
	            if ($item['event_id']>0) {
	                $listorder = ($item['oldorder']!=='') ? $item['oldorder'] : '99';
	                $query = $db->getQuery(true);
    	            $query->insert($db->quoteName('#__xbeventgroup'));
    	            $query->columns('group_id,event_id,role,role_note,listorder');
    	            $query->values($db->q($group_id).','.$db->q($item['event_id']).
    	                ','.$db->q($item['role']).','.$db->q($item['role_note']).','.$db->q($listorder));
    	            $db->setQuery($query);
    	            try {
    	                $db->execute();
    	            }
    	            catch (\RuntimeException $e) {
    	                throw new \Exception($e->getMessage(), 500);
    	                return false;
    	            }
	            }
	        }
	    }
	}
	
	private function storeGroupPeople($group_id, $grouppersonList) {
	    $db = $this->getDbo();
	    //delete existing people list
	    $where = $db->qn('group_id').' = '.$db->q($group_id);
	    if (XbcultureHelper::deleteFromTable('#__xbgroupperson', $where)) {
	        //restore the new list
    	    $listorder=0;
    	    foreach ($grouppersonList as $item) {
    	        if ($item['person_id'] > 0) {
    	            $listorder ++;
    	            $thisrole = ($item['role']=='') ? $item['newrole'] : $item['role'];
    	            $query = $db->getQuery(true);
    	            $query->insert($db->quoteName('#__xbgroupperson'));
    	            $query->columns('group_id,person_id,role,role_note,joined,until,listorder');
    	            $query->values($db->q($group_id).','.$db->q($item['person_id']).
    	                ','.$db->q($thisrole).','.$db->q($item['role_note']).
    	                ','.$db->q($item['joined']).','.$db->q($item['until']).
    	                ','.$db->q($listorder));
    	            $db->setQuery($query);
    	            try {
    	                $db->execute();
    	            }
    	            catch (\RuntimeException $e) {
    	                throw new \Exception($e->getMessage(), 500);
    	                return false;
    	            }
    	        }
    	    }
	    }
	}
	
}
