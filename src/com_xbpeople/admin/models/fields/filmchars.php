<?php
/*******
 * @package xbBooks
 * @filesource admin/models/fields/filmchars.php
 * @version 0.12.0.1 11th Deceber 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

/**
 * Provides an object list of people who are in a film and state=published
 */
class JFormFieldFilmchars extends JFormFieldList {
    
    protected $type = 'Filmchars';
    
    public function getOptions() {
        
        $published = (isset($this->element['published'])) ? $this->element['published'] : '1';
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('DISTINCT c.id AS value')
	        ->select('c.name AS text')
	        ->from('#__xbcharacters AS c')
	        ->join('LEFT', '#__xbfilmcharacter AS fc ON fc.char_id = c.id')
	        ->where('fc.id IS NOT NULL');
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
