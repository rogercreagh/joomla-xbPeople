<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/characters.php
 * @version 0.12.0.1 7th Deceber 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldCharacters extends JFormFieldList {
    
    protected $type = 'Characters';
    
    public function getOptions() {
        
	    $published = (isset($this->element['published'])) ? $this->element['published'] : '1';    	
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('id As value')
	        ->select('name AS text')
	        ->from('#__xbcharacters');
        if (strpos($published,',')!==false) {
            $query->where('state IN  ('.$published.')');
        } else {
            $query->where('state = '.$db->q($published));
        }
        $query->order('text');
        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
