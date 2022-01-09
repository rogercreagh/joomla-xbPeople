<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/films.php
 * @version 0.9.6.f 9th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldFilms extends JFormFieldList {
    
    protected $type = 'Films';
    
    /**
     * @desc gets a list of all films with three most recently added at top,
     * then published ones sorted by title, then any unpublished ones at the end
     * {@inheritDoc}
     * @see JFormFieldList::getOptions()
     */
    public function getOptions() {
        
    	$options = parent::getOptions();
    	
    	if (XbcultureHelper::checkComponent('com_xbfilms')) {
    		
    		$db = Factory::getDbo();
	        $query  = $db->getQuery(true);
	        
	        $query->select('id As value')
	            ->select('CONCAT(title, IF (state <>1, " (unpub)", "") ) AS text') 
	            ->from('#__xbfilms')
	            ->where('state IN (0,1)')  //exclude trashed and archived
	            ->order('state DESC, title ASC'); //pub published first and unpublished at end
	        $db->setQuery($query);
	        $all = $db->loadObjectList();
	        
	        $query->clear();
	        $query->select('id As value')
	        ->select('CONCAT(title, " (", state, ")") AS text')
	        ->from('#__xbfilms')       
	        ->order('created DESC')
	        ->setLimit('3');
	        $recent = $db->loadObjectList();
	        //add a separator between recent and alpha
	        $blank = new stdClass();
	        $blank->value = 0;
	        $blank->text = '------------';
	        $recent[] = $blank;
	        
	        // Merge any additional options in the XML definition with recent (top 3) and alphabetical list.
	        $options = array_merge($options, $recent, $all);
    	}
        return $options;
    }
}
