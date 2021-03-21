<?php
/*******
 * @package xbPeople
 * @filesource admin/tables/character.php
 * @version 0.4.1 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

//use Joomla\CMS\Language\Text;
//use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Observer\Tags;

class XbpeopleTableCharacter extends JTable {
	
    protected $xbbooksStatus;
    protected $xbfilmsStatus;
    	
   function __construct(&$db) {
    	parent::__construct('#__xbcharacters', 'id', $db);
        $this->setColumnAlias('published', 'state');
        Tags::createObserver($this, array('typeAlias' => 'com_xbpeople.character'));
        $this->xbbooksStatus = XbpeopleHelper::checkComponent('com_xbbooks');
        $this->xbfilmsStatus = XbpeopleHelper::checkComponent('com_xbfilms');
   }
    
    public function delete($pk=null) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        if ($this->xbfilmsStatus) {
        	$query->delete()->from('#__xbfilmcharacter')->where('char_id = '. $pk);
        }
        if ($this->xbbooksStatus) {
        	$query->delete()->from('#__xbbookcharacter')->where('char_id = '. $pk);
        }
        $this->$db->setQuery($query);
        $this->$db->execute();
        return parent::delete($pk);
    }
    
    public function check() {
    	$params = ComponentHelper::getParams('com_xbpeople');
    	
    	$title = trim($this->name);
    	
    	if ($title == '') {
    	    $this->setError(JText::_('COM_XBFILMS_PROVIDE_VALID_NAME'));
    	    return false;
    	}
    	
    	if (($this->id == 0) && (XbpeopleHelper::checkTitleExists($name,'#__xbcharacters'))) {
    		$this->setError(JText::_('Character "'.$title.'" already exists; if this is a different individual with the same name please append something to the name to distinguish them'));
    	    return false;
    	}
    	
    	$this->name = $title;
    	
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
        
        //set default or require category
        if (!$this->catid>0) {
            $defcat=0;
            if ($params->get('def_new_charcat')>0) {
                $defcat=$params->get('def_new_charcat');
            } else {
                $defcat = XbpeopleHelper::getIdFromAlias('#__categories', 'uncategorised', 'com_xbpeople');
            }
            if ($defcat>0) {
                $this->catid = $defcat;
                Factory::getApplication()->enqueueMessage(JText::_('COM_XBFILMS_DEFAULT_CATEGORY').' ('.XbpeopleHelper::getCat($this->catid)->title.')');
            } else {
            	// this shouldn't happen unless uncategorised has been deleted or xbpeople not installed
            	$this->setError(JText::_('Please set a category'));
            	return false;
            }
        }
        
        //warn re missing summary and description
        if ((trim($this->summary)=='')) {
        	if (trim($this->description)=='' ) {
        		Factory::getApplication()->enqueueMessage(JText::_('COM_XBFILMS_MISSING_SUMMARY'));
        	}
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
        	$metadata['metadesc'] = JHtml::_('string.truncate', $this->summary,150,true,false);
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
        	$tags = implode(',',$tagsHelper->getTagNames(explode(',',$tagsHelper->getTagIds($this->id,'com_xbpeople.film'))));
        	$metadata['metakey'] = $tags;
        }
        $this->metadata = json_encode($metadata);
        
        return true;
    }

    public function bind($array, $ignore = '') {
    	if (isset($array['params']) && is_array($array['params'])) {
    		// Convert the params field to a string.
    		$parameter = new JRegistry;
    		$parameter->loadArray($array['params']);
    		$array['params'] = (string)$parameter;
    	}
    	
    	if (isset($array['metadata']) && is_array($array['metadata'])) {
    		$registry = new JRegistry;
    		$registry->loadArray($array['metadata']);
    		$array['metadata'] = (string)$registry;
    	}
    	return parent::bind($array, $ignore);
     	if (isset($array['rules']) && is_array($array['rules'])) {
     		$rules = new JAccessRules($array['rules']);
     		$this->setRules($rules);
     	}
    	
    }
    
}