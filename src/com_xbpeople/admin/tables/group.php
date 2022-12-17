<?php
/*******
 * @package xbPeople
 * @filesource admin/tables/group.php
 * @version 1.0.0.2 17th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
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
//             $query = $db->getQuery(true);
//         	$query->delete()->from('#__xbfilmgroup')->where('person_id = '. $pk);
//         	$this->_db->setQuery($query);
//         	try {
//         	    $this->_db->execute();
//         	}
//         	catch (\RuntimeException $e) {
//         	    throw new \Exception($e->getMessage(), 500);
//         	    return false;
//         	}
        }
        if ($this->xbbooksStatus) {
//             $query = $db->getQuery(true);
//             $query->delete()->from('#__xbbookgroup')->where('person_id = '. $pk);
//             $this->_db->setQuery($query);
//             try {
//                 $this->_db->execute();
//             }
//             catch (\RuntimeException $e) {
//                 throw new \Exception($e->getMessage(), 500);
//                 return false;
//             }
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
        $query = $db->getQuery(true);
        $query->delete()->from('#__xbgroupperson')->where('group_id = '. $pk);
        $this->_db->setQuery($query);
        try {
            $this->_db->execute();
        }
        catch (\RuntimeException $e) {
            throw new \Exception($e->getMessage(), 500);
            return false;
        }
        return parent::delete($pk);
    }
    
    public function check() {
    	$params = ComponentHelper::getParams('com_xbpeople');
    	
    	$title = trim($this->title);
    	
    	if ($title == '') {
    	    $this->setError(Text::_('XBCULTURE_PROVIDE_VALID_NAME'));
    	    return false;
    	}
    	
    	if (($this->id == 0) && (XbpeopleHelper::checkTitleExists($name,'#__xbcharacters'))) {
    	    $this->setError(Text::_('Character "'.$title.'" already exists; if this is a different individual with the same name please append something to the name to distinguish them'));
    	    return false;
    	}
    	
    	$this->title = $title;
    	
    	if (trim($this->alias) == '') {
    	    $this->alias = $title;
    	}
    	$this->alias = OutputFilter::stringURLSafe($this->alias);
    	
    	
        //set created by alias if not set (default to current user)
        if (trim($this->created_by_alias) == '') {
        	$user = Factory::getUser($this->item->created_by);
        	$name = ($params->get('name_username') == 0) ? $user->name : $user->username;
        	$this->created_by_alias = $name;
        }
        //require category
        if (!$this->catid>0) {
        	$defcat=0;
        	if ($params->get('def_new_groupcat')>0) {
        		$defcat=$params->get('def_new_groupcat');
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
        	
        //require summary, create from desc if missing
        if ((trim($this->summary)=='')) {
        	if (trim($this->description)=='' ) {
        		Factory::getApplication()->enqueueMessage(Text::_('XBCULTURE_MISSING_SUMMARY'));
        	}
        }
        
        //json encode ext_links if set
        if (is_array($this->ext_links)) {
            foreach ($this->ext_links as $ky=>$lnk) {
                if ($lnk['link_url'] == '') unset($this->ext_links[$ky]);
            }
            $this->ext_links = json_encode($this->ext_links);
        }
              
        //set metadata to defaults
        $metadata = json_decode($this->metadata,true);
        // meta.author will be created_by_alias (see above)
        if ($metadata['author'] == '') {
        	if ($this->created_by_alias =='') {
        		$metadata['author'] = $params->get('def_author');
        	} else {
        		$metadata['author'] = $this->created_by_alias;
        	}
        }
        //meta.description can be set to first 150 chars of summary if not otherwise set and option is set
        $summary_metadesc = $params->get('summary_metadesc');
        if (($summary_metadesc) && (trim($metadata['metadesc']) == '')) {
        	$metadata['metadesc'] = HTMLHelper::_('string.truncate', $this->summary,150,true,false);
        }
        //meta.rights will be set to default if not otherwise set
        $def_rights = $params->get('def_rights');
        if (($def_rights != '') && (trim($metadata['rights']) == '')) {
        	$metadata['rights'] = $def_rights;
        }
        //meta.keywords will be set to a list of tags unless otherwise set if the option is set
        //TODO update this when tags are added
        // convert existing keyword list to array, get tag names as array, merge arrays and implode to a list
        $tags_keywords = $params->get('tags_keywords');
        if (($tags_keywords) && (trim($metadata['metakey']) == '')) {
        	$tagsHelper = new TagsHelper;
        	$tags = implode(',',$tagsHelper->getTagNames(explode(',',$tagsHelper->getTagIds($this->id,'com_xbpeople.person'))));
        	$metadata['metakey'] = $tags;
        }
        $this->metadata = json_encode($metadata);
        
        return true;
    }

    public function bind($array, $ignore = '') {
    	if (isset($array['params']) && is_array($array['params'])) {
    		// Convert the params field to a string.
    		$parameter = new Registry;
    		$parameter->loadArray($array['params']);
    		$array['params'] = (string)$parameter;
    	}   	
    	if (isset($array['metadata']) && is_array($array['metadata'])) {
    		$registry = new Registry;
    		$registry->loadArray($array['metadata']);
    		$array['metadata'] = (string)$registry;
    	}
    	return parent::bind($array, $ignore);
//      	if (isset($array['rules']) && is_array($array['rules'])) {
//      		$rules = new JAccessRules($array['rules']);
//      		$this->setRules($rules);
//      	}
    	
    }
    
}