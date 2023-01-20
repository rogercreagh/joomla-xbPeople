<?php
/*******
 * @package xbPeople
 * @filesource admin/tables/person.php
 * @version 1.0.0.1 16th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Table\Observer\Tags;
use Joomla\Registry\Registry;
use Joomla\CMS\Table\Table;

class XbpeopleTablePerson extends Table {
	
	protected $xbbooksStatus;
	protected $xbfilmsStatus;
	protected $xbeventsStatus;
	
	function __construct(&$db) {
        parent::__construct('#__xbpersons', 'id', $db);
        $this->setColumnAlias('published', 'state');
        $this->_supportNullValue = true;  //write empty checkedouttime as null
        Tags::createObserver($this, array('typeAlias' => 'com_xbpeople.person'));
        $this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
        $this->xbfilmsStatus = XbcultureHelper::checkComponent('com_xbfilms');
        $this->xbeventsStatus = XbcultureHelper::checkComponent('com_xbevents');
	}
    
    public function delete($pk=null) {
        $db = $this->getDbo();
        if ($this->xbfilmsStatus) {
            $query = $db->getQuery(true);
        	$query->delete()->from('#__xbfilmperson')->where('person_id = '. $pk);
        	$this->_db->setQuery($query);
        	try {
        	    $this->_db->execute();
        	}
        	catch (\RuntimeException $e) {
        	    throw new \Exception($e->getMessage(), 500);
        	    return false;
        	}
        }
        if ($this->xbbooksStatus) {
            $query = $db->getQuery(true);
            $query->delete()->from('#__xbbookperson')->where('person_id = '. $pk);
            $this->_db->setQuery($query);
            try {
                $this->_db->execute();
            }
            catch (\RuntimeException $e) {
                throw new \Exception($e->getMessage(), 500);
                return false;
            }
        }
        if ($this->xbeventsStatus) {
            $query = $db->getQuery(true);
            $query->delete()->from('#__xbeventgroup')->where('group_id = '. $pk);
            $this->_db->setQuery($query);
            try {
                $this->_db->execute();
            }
            catch (\RuntimeException $e) {
                throw new \Exception($e->getMessage(), 500);
                return false;
            }
        }
        return parent::delete($pk);
    }
    
    public function check() {
    	$params = ComponentHelper::getParams('com_xbpeople');
    	
        //require category
        if (!$this->catid>0) {
        	$defcat=0;
        	if ($params->get('def_new_personcat')>0) {
        		$defcat=$params->get('def_new_personcat');
        	} else {
        	    $defcat = XbcultureHelper::getIdFromAlias('#__categories', 'uncategorised', 'com_xbpeople');
        	}
        	if ($defcat>0) {
        		$this->catid = $defcat;
        		Factory::getApplication()->enqueueMessage(Text::_('XBCULTURE_DEFAULT_CATEGORY'));
        	} else {
        		$this->setError(Text::_('XBCULTURE_CATEGORY_MISSING'));
        		return false;
        	}
        }
        	
        
        return true;
    }

    
}