<?php
/*******
 * @package xbPeople
 * @filesource admin/models/persons.php
 * @version 1.0.2.8 14th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;

class XbpeopleModelPerson extends JModelAdmin {
    
    public $typeAlias = 'com_xbpeople.person';
    
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
				$item->tags = $tagsHelper->getTagIds($item->id, 'com_xbpeople.person');
			}
		}
		return $item;
	}
	
	public function getTable($type = 'Person', $prefix = 'XbpeopleTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_xbpeople.person', 'person',
					array('control' => 'jform','load_data' => $loadData)
				);
		
		if (empty($form)) {
			return false;
		}
		
		$params = ComponentHelper::getParams('com_xbpeople');
		$portrait_path = $params->get('portrait_path','');
		if ($portrait_path != '') {
			$form->setFieldAttribute('portrait','directory',$portrait_path);
		}
		
		return $form;
	}
	
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState( 'com_xbpeople.edit.person.data', array() );
		
		if (empty($data)) {
			$data = $this->getItem();
		}
		$tagsHelper = new TagsHelper;
		$params = ComponentHelper::getParams('com_xbpeople');
		$peeptaggroup_parent = $params->get('peeptaggroup_parent','');
		if ($peeptaggroup_parent && !(empty($data->tags))) {
		    $peeptaggroup_tags = $tagsHelper->getTagTreeArray($peeptaggroup_parent);
		    $data->peeptaggroup = array_intersect($peeptaggroup_tags, explode(',', $data->tags));
		}
		
		if (is_object($data)) {
			if ($this->xbfilmsStatus) {
				$data->filmdirectorlist=$this->getPersonFilmslist('director');
				$data->filmproducerlist=$this->getPersonFilmslist('producer');
				$data->filmcrewlist=$this->getPersonFilmslist('crew');
				$data->filmactorlist=$this->getPersonFilmslist('actor');
				$data->filmappearslist=$this->getPersonFilmslist('appearsin');
			}
			if ($this->xbbooksStatus) {
				$data->bookauthorlist=$this->getPersonBookslist('author');
				$data->bookeditorlist=$this->getPersonBookslist('editor');
				$data->bookmenlist=$this->getPersonBookslist('mention');
				$data->bookotherlist=$this->getPersonBookslist('other');
			}
			if ($this->xbeventsStatus) {
			    $data->eventpersonlist=$this->getPersonEventslist('');
			}
			$data->persongrouplist=$this->getPersonGroupslist('');
			
		}
		
		return $data;
	}
	
	protected function prepareTable($table) {
		$date = Factory::getDate();
		$user = Factory::getUser();
		
		$table->firstname = htmlspecialchars_decode($table->firstname, ENT_QUOTES);
		$table->lastname = htmlspecialchars_decode($table->lastname, ENT_QUOTES);
		$table->alias = ApplicationHelper::stringURLSafe($table->alias);
		
		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe($table->firstname.' '.$table->lastname);
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
				->from($db->quoteName('#__xbpersons'));
				
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
				->update($db->quoteName('#__xbpersons'))
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
			$table = $this->getTable('person');
			foreach ($pks as $i=>$item) {
				$table->load($item);
				if (!$table->delete($item)) {
					$personpeople = ($cnt == 1)? Text::_('XBCULTURE_PERSON') : Text::_('XBCULTURE_PEOPLE');
					Factory::getApplication()->enqueueMessage($cnt.$personpeople.' deleted');
					$this->setError($table->getError());
					return false;
				}
				$table->reset();
				$cnt++;
			}
			$personpeople = ($cnt == 1)? Text::_('XBCULTURE_PERSON') : Text::_('XBCULTURE_PEOPLE');
			Factory::getApplication()->enqueueMessage($cnt.$personpeople.' deleted');
			return true;
		}
	}
	
	public function getPersonFilmslist($role) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as film_id, ba.role_note AS role_note, ba.listorder AS oldorder');
		$query->from('#__xbfilmperson AS ba');
		$query->innerjoin('#__xbfilms AS a ON ba.film_id = a.id');
		$query->where('ba.person_id = '.(int) $this->getItem()->id);
		$query->where('ba.role = "'.$role.'"');
		$query->order('a.rel_year DESC');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	public function getPersonBookslist($role) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as book_id, ba.role AS role, ba.role_note AS role_note, ba.listorder AS oldorder');
		$query->from('#__xbbookperson AS ba');
		$query->innerjoin('#__xbbooks AS a ON ba.book_id = a.id');
		$query->where('ba.person_id = '.(int) $this->getItem()->id);
		if ($role == 'other') {
		    $query->where($db->qn('ba.role')." NOT IN ('author','editor','mention')");		    
		} else {
            $query->where('ba.role = "'.$role.'"');
		}
		$query->order('a.title ASC');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	public function getPersonEventslist() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('a.id as event_id, ba.role AS role, ba.role_note AS role_note, ba.listorder AS oldorder');
	    $query->from('#__xbeventperson AS ba');
	    $query->innerjoin('#__xbevents AS a ON ba.event_id = a.id');
	    $query->where('ba.person_id = '.(int) $this->getItem()->id);
//	    $query->where('ba.role = "'.$role.'"');
	    $query->order('a.start_datetime DESC');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}
	
	public function getPersonGroupslist() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('a.id as group_id, ba.role AS role, ba.role_note AS role_note, ba.joined AS joined, ba.until AS until, ba.listorder AS oldorder');
	    $query->from('#__xbgroupperson AS ba');
	    $query->innerjoin('#__xbgroups AS a ON ba.group_id = a.id');
	    $query->where('ba.person_id = '.(int) $this->getItem()->id);
//	    $query->where('ba.role = "'.$role.'"');
	    $query->order('a.title ASC');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}
	
	public function save($data) {
		$input = Factory::getApplication()->input;
		// allow nulls for year (therwise empty value defaults to 0)
		if ($data['year_born']=='') { $data['year_born'] = NULL; }
		if ($data['year_died']=='') { $data['year_died'] = NULL; }
		
		if ($data['peeptaggroup']) {
		    $data['tags'] = ($data['tags']) ? array_unique(array_merge($data['tags'],$data['peeptaggroup'])) : $data['peeptaggroup'];
		}
		
		if (parent::save($data)) {
		    $pid = $this->getState('person.id');
			if ($this->xbfilmsStatus) {
				$this->storePersonFilms($pid,'director', $data['filmdirectorlist']);
				$this->storePersonFilms($pid,'producer', $data['filmproducerlist']);
				$this->storePersonFilms($pid,'crew', $data['filmcrewlist']);
				$this->storePersonFilms($pid,'actor', $data['filmactorlist']);
				$this->storePersonFilms($pid,'appearsin', $data['filmappearslist']);
			}
			if ($this->xbbooksStatus) {
				$this->storePersonBooks($pid,'author', $data['bookauthorlist']);
				$this->storePersonBooks($pid,'editor', $data['bookeditorlist']);
				$this->storePersonBooks($pid,'mention', $data['bookmenlist']);
				$this->storePersonBooks($pid,'other', $data['bookotherlist']);				
			}
			if ($this->xbeventsStatus) {
			    $this->storePersonEvents($pid, $data['eventpersonlist']);
			}
			$this->storePersonGroups($pid, $data['persongrouplist']);
			return true;
		}
		
		return false;
	}
	
	private function storePersonFilms($person_id, $role, $personList) {
		//delete existing role list
		$db = $this->getDbo();
		$where = $db->qn('person_id').' = '.$db->q($person_id).' AND '.$db->qn('role').' = '.$db->q($role);
		if (XbcultureHelper::deleteFromTable('#__xbbookperson', $where)) {
		    
    		//restore the new list
    		foreach ($personList as $item) {
    		    if ($item['film_id']>0) {
    		        $listorder = ($item['oldorder']!=='') ? $item['oldorder'] : '99';
    			    $query = $db->getQuery(true);
    				$query->insert($db->quoteName('#__xbfilmperson'));
    				$query->columns('person_id,film_id,role,role_note,listorder');
    				$query->values($db->quote($person_id).','.$db->quote($item['film_id']).','.$db->quote($role).','.$db->quote($item['role_note']).','.$db->q($listorder));
    				$db->setQuery($query);
    				try {
    				    $db->execute();
    				}
    				catch (\RuntimeException $e) {
    				    throw new \Exception($e->getMessage(), 500);
    				    return false;
    				}
    				//if actor id is set we also need to check the filmperson table
    				//to see if that link already exists and if no add it
    			}
    		}
		}
	}

	private function storePersonBooks($person_id, $role, $bookList) {
	    //delete existing role list
	    $db = $this->getDbo();
	    $where = $db->qn('person_id').' = '.$db->q($person_id).' AND '.$db->qn('role').' = '.$db->q($role);
	    if (XbcultureHelper::deleteFromTable('#__xbbookperson', $where)) {
	        
	    //restore the new list
    	    foreach ($bookList as $bk) {
    	        if ($bk['book_id']>0) {
        	        if ($role == 'other') {
        	           $thisrole = ($bk['role']=='') ? $bk['newrole'] : $bk['role'];
        	        } else {
        	            $thisrole = $role;
        	        }
    	            $listorder = ($bk['oldorder']!=='') ? $bk['oldorder'] : '99';
    	            $query = $db->getQuery(true);
    	            $query->insert($db->quoteName('#__xbbookperson'));
    	            $query->columns('person_id,book_id,role,role_note,listorder');
    	            $query->values($db->q($person_id).','.$db->q($bk['book_id']).','.$db->q($thisrole).','.$db->q($bk['role_note']).','.$db->q($listorder));
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
	
	private function storePersonEvents($person_id, $eventList) {
        $db = Factory::getDbo();
        //delete existing role list
        $where = $db->qn('person_id').' = '.$db->q($person_id);
        if (XbcultureHelper::deleteFromTable('#__xbgroupperson', $where)) {
            
        //restore the new list
            foreach ($eventList as $item) {
                if ($item['event_id']>0) {
                    $listorder = ($item['oldorder']!=='') ? $item['oldorder'] : '99';
                    $query = $db->getQuery(true);
                    $query->insert($db->quoteName('#__xbeventperson'));
                    $query->columns('person_id,event_id,role,role_note,listorder');
                    $query->values($db->q($person_id).','.$db->q($item['book_id']).
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
	
	private function storePersonGroups($person_id, $groupList) {
	    $db = Factory::getDbo();
	    //delete existing role list
	    $where = $db->qn('person_id').' = '.$db->q($person_id);
	    if (XbcultureHelper::deleteFromTable('#__xbgroupperson', $where)) {	    
    	    //restore the new list
    	    foreach ($groupList as $item) {
    	        if ($item['group_id']>0) {
    	            $listorder = ($item['oldorder']!=='') ? $item['oldorder'] : '99';
    	            $query = $db->getQuery(true);
    	            $query->insert($db->quoteName('#__xbgroupperson'));
    	            $query->columns('person_id,group_id,role,role_note,listorder');
    	            $query->values($db->q($person_id).','.$db->q($item['group_id']).','.$db->q($item['role']).','.$db->q($item['role_note']).','.$db->q($listorder));
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