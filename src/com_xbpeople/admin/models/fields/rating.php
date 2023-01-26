<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/rating.php
 * @version 1.0.3.1 26th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldRating extends JFormFieldList {
    
    protected $type = 'Rating';
    
    /**
     * @desc 
     * {@inheritDoc}
     * @see JFormFieldList::getOptions()
     */
    public function getOptions() {
        
    	//get the params for star icons
        $component = (string) $this->element['component'];
        
        $params = ComponentHelper::getParams($component);
    	$zero_rating = $params->get('zero_rating');
        $options = array();
        if ($zero_rating){
        	$options[] = (object) array('value' => 0, 'text' => 'zero');
        }
        for ($i = 1; $i < 8; $i++) {
        	$options[] = (object) array('value' => $i, 'text' => str_repeat('&#11088;', $i));
        }
                
        // Merge any additional options in the XML definition 
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
