<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/people.php
 * @version 0.9.9.0 29th June 2022
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
 * Provides an object list of people with state=published
 */
class JFormFieldPeople extends JFormFieldList {
    
    protected $type = 'People';
    
    public function getOptions() {
        
        $published = $this->element['published'] ? (string) $this->element['published'] : '1';
        $params = ComponentHelper::getParams('com_xbpeople');
    	$people_sort = $params->get('people_sort');
    	$select = ($people_sort == 0) ? 'CONCAT(firstname, " ", lastname) AS text' : 'CONCAT(lastname, ", ", firstname ) AS text';
    	//poss param to show state " (", state, ")"
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('DISTINCT p.id As value')
	        ->select($select)
	        ->from('#__xbpersons AS p');
	        if ($published) {
	            $query->where('state = 1');
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
