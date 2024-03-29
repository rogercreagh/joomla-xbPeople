<?php
/*******
 * @package xbPeople
 * @filesource admin/models/character.php
 * @version 1.0.2.8 14th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
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

class XbpeopleModelCharacter extends JModelAdmin {
 
	public $typeAlias = 'com_xbpeople.character';
	
	protected $xbbooksStatus;
	protected $xbfilmsStatus;
	
	public function __construct($config = array()) {
		$this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
		$this->xbfilmsStatus = XbcultureHelper::checkComponent('com_xbfilms');
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
				$item->tags =  $tagsHelper->getTagIds($item->id, 'com_xbpeople.character');
			}
		}
		return $item;
	}
	
	public function getTable($type = 'Character', $prefix = 'XbpeopleTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_xbpeople.character', 'character',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );
        
        if (empty($form)) {
            return false;
        }
        
        $params = ComponentHelper::getParams('com_xbpeople');
        $image_path = $params->get('image_path','');
        if ($image_path != '') {
        	$form->setFieldAttribute('image','directory',$image_path);
        }
        
        return $form;
    }
    
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState( 'com_xbpeople.edit.character.data', array() );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $tagsHelper = new TagsHelper;
        $params = ComponentHelper::getParams('com_xbpeople');
        $chartaggroup_parent = $params->get('chartaggroup_parent','');
        if ($chartaggroup_parent && !(empty($data->tags))) {
            $chartaggroup_tags = $tagsHelper->getTagTreeArray($chartaggroup_parent);
            $data->chartaggroup = array_intersect($chartaggroup_tags, explode(',', $data->tags));
        }
        
        if (is_object($data)) {
        	if ($this->xbfilmsStatus) {
        		$data->filmcharlist=$this->getCharacterFilmslist();
        	}
        	if ($this->xbbooksStatus) {
        		$data->bookcharlist=$this->getCharacterBookslist();
        	}
        }
        return $data;
    }

	protected function prepareTable($table) {
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias = ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe($table->name);
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
					->from($db->quoteName('#__xbcharacters'));

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
    	            ->update($db->quoteName('#__xbcharacters'))
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
	        $table = $this->getTable('character');
	        foreach ($pks as $i=>$item) {
	            $table->load($item);	            
	            if (!$table->delete($item)) {
	                $personpeople = ($cnt == 1)? Text::_('XBCULTURE_CHARACTER') : Text::_('XBCULTURE_CHARACTERS');
	                Factory::getApplication()->enqueueMessage($cnt.' '.$personpeople.' deleted');
	                $this->setError($table->getError());
	                return false;
	            }
	            $table->reset();
	            $cnt++;
	        }
	        $personpeople = ($cnt == 1)? Text::_('XBCULTURE_CHARACTER') : Text::_('XBCULTURE_CHARACTERS');
	        Factory::getApplication()->enqueueMessage($cnt.' '.$personpeople.' deleted');
	        return true;
	    }
	}

	public function getCharacterFilmslist() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('a.id as film_id, ba.actor_id AS actor_id, ba.char_note AS char_note,ba.listorder AS oldorder');
	    $query->from('#__xbfilmcharacter AS ba');
	    $query->innerjoin('#__xbfilms AS a ON ba.film_id = a.id');
	    $query->where('ba.char_id = '.(int) $this->getItem()->id);
	    $query->order('a.rel_year DESC');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	    //if actor_id is set we also need to get the actor name
	}
	
	public function getCharacterEventslist() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as book_id, ba.actor_id AS actor_id, ba.char_note AS char_noteba.listorder AS oldorder');
		$query->from('#__xbeventcharacter AS ba');
		$query->innerjoin('#__xbevents AS a ON ba.evemt_id = a.id');
		$query->where('ba.char_id = '.(int) $this->getItem()->id);
		$query->order('a.title ASC');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	public function getCharacterBookslist() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('a.id as book_id, ba.char_note AS char_note,ba.listorder AS oldorder');
	    $query->from('#__xbbookcharacter AS ba');
	    $query->innerjoin('#__xbbooks AS a ON ba.book_id = a.id');
	    $query->where('ba.char_id = '.(int) $this->getItem()->id);
	    $query->order('a.title ASC');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	    //if actor_id is set we also need to get the actor name
	}
	
	public function save($data) {
		$input = Factory::getApplication()->input;

		if ($data['chartaggroup']) {
		    $data['tags'] = ($data['tags']) ? array_unique(array_merge($data['tags'],$data['chartaggroup'])) : $data['chartaggroup'];
		}
		
		if (parent::save($data)) {
			if ($this->xbfilmsStatus) {
				$this->storeCharacterFilms($this->getState('character.id'),$data['filmcharlist']);
			}
			if ($this->xbbooksStatus) {
				$this->storeCharacterBooks($this->getState('character.id'),$data['bookcharlist']);
			}
			return true;
		}
		
		return false;
	}
	
	private function storeCharacterFilms($char_id, $charList) {
		//delete existing role list
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__xbfilmcharacter'));
		$query->where('char_id = '.$char_id.' ');
		$db->setQuery($query);
		try {
		    $db->execute();
		}
		catch (\RuntimeException $e) {
		    throw new \Exception($e->getMessage(), 500);
		    return false;
		}
		//restore the new list
		foreach ($charList as $ch) {
		    if ($ch['film_id']>0) {
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__xbfilmcharacter'));
			$query->columns('char_id,film_id,actor_id,char_note');
			$query->values('"'.$char_id.'","'.$ch['film_id'].'","'.$ch['actor_id'].'","'.$ch['char_note'].'"');
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
	
	private function storeCharacterBooks($char_id, $charList) {
		//delete existing role list
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__xbbookcharacter'));
		$query->where('char_id = '.$char_id.' ');
		$db->setQuery($query);
		try {
		    $db->execute();
		}
		catch (\RuntimeException $e) {
		    throw new \Exception($e->getMessage(), 500);
		    return false;
		}
		//restore the new list
		foreach ($charList as $ch) {
			if ($ch['book_id']>0) {
				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__xbbookcharacter'));
				$query->columns('char_id,book_id,char_note');
				$query->values('"'.$char_id.'","'.$ch['book_id'].'","'.$ch['char_note'].'"');
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