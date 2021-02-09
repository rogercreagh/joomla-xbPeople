<?php
/*******
 * @package xbPeople
 * @filesource admin/models/persons.php
 * @version 0.1.0 8th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Table\Table;

class XbpeopleModelPerson extends JModelAdmin {
    
	protected $xbbooksStatus;
	protected $xbfilmsStatus;
	
	public $typeAlias = 'com_xbfilms.person';
	
	public function __construct($config = array()) {
		$this->xbbooksStatus = XbpeopleHelper::checkComponent('com_xbbooks');
		$this->xbfilmsStatus = XbpeopleHelper::checkComponent('com_xbfilms');
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
				$item->tags = $tagsHelper->getTagIds($item->id, 'com_xbfilms.person');
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
		$form = $this->loadForm('com_xbfilms.person', 'person',
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
				if (!($db->execute())) {
					$this->setError($db->getErrorMsg());
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
					$personpeople = ($cnt == 1)? Text::_('ONEPERSON') : Text::_('MANYPEOPLE');
					Factory::getApplication()->enqueueMessage($cnt.$personpeople.' deleted');
					$this->setError($table->getError());
					return false;
				}
				$table->reset();
				$cnt++;
			}
			$personpeople = ($cnt == 1)? Text::_('ONEPERSON') : Text::_('MANYPEOPLE');
			Factory::getApplication()->enqueueMessage($cnt.$personpeople.' deleted');
			return true;
		}
	}
	
	public function getPersonFilmslist($role) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as film_id, ba.role_note AS role_note');
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
		$query->select('a.id as book_id, ba.role_note AS role_note');
		$query->from('#__xbbookperson AS ba');
		$query->innerjoin('#__xbbooks AS a ON ba.book_id = a.id');
		$query->where('ba.person_id = '.(int) $this->getItem()->id);
		$query->where('ba.role = "'.$role.'"');
		$query->order('a.title ASC');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	public function save($data) {
		$input = Factory::getApplication()->input;
		// allow nulls for year (therwise empty value defaults to 0)
		if ($data['year_born']=='') { $data['year_born'] = NULL; }
		if ($data['year_died']=='') { $data['year_died'] = NULL; }
		if (parent::save($data)) {
			if ($this->xbfilmsStatus) {
				$this->storePersonFilms($this->getState('person.id'),'director', $data['filmdirectorlist']);
				$this->storePersonFilms($this->getState('person.id'),'producer', $data['filmproducerlist']);
				$this->storePersonFilms($this->getState('person.id'),'crew', $data['filmcrewlist']);
				$this->storePersonFilms($this->getState('person.id'),'actor', $data['filmactorlist']);
				$this->storePersonFilms($this->getState('person.id'),'appearsin', $data['filmappearslist']);
			}
			if ($this->xbbooksStatus) {
				$this->storePersonBooks($this->getState('person.id'),'author', $data['bookauthorlist']);
				$this->storePersonBooks($this->getState('person.id'),'editor', $data['bookeditorlist']);
				$this->storePersonBooks($this->getState('person.id'),'mention', $data['bookmenlist']);
				$this->storePersonBooks($this->getState('person.id'),'other', $data['bookotherlist']);				
			}
			return true;
		}
		
		return false;
	}
	
	private function storePersonFilms($person_id, $role, $personList) {
		//delete existing role list
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__xbfilmperson'));
		$query->where('person_id = '.$person_id.' AND role = "'.$role.'"');
		$db->setQuery($query);
		$db->execute();
		//restore the new list
		foreach ($personList as $per) {
			if ($per['film_id']>0) {
				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__xbfilmperson'));
				$query->columns('person_id,film_id,role,role_note');
				$query->values($db->quote($person_id).','.$db->quote($per['film_id']).','.$db->quote($role).','.$db->quote($per['role_note']));
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	private function storePersonBooks($person_id, $role, $personList) {
		//delete existing role list
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__xbbookperson'));
		$query->where('person_id = '.$person_id.' AND role = "'.$role.'"');
		$db->setQuery($query);
		$db->execute();
		//restore the new list
		foreach ($personList as $per) {
			if ($per['book_id']>0) {
				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__xbbookperson'));
				$query->columns('person_id,book_id,role, role_note');
				$query->values($db->quote($person_id).','.$db->quote($per['book_id']).','.$db->quote($role).','.$db->quote($per['role_note']));
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
}