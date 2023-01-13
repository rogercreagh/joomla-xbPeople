<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/grouprolelist.php
 * @version 1.0.2.6 13th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;

FormHelper::loadFieldClass('list');

class JFormFieldGrouprolelist extends JFormFieldList {
	
	protected $type = 'Grouprolelist';
	
	public function getOptions() {
		
		$options = parent::getOptions();
				    
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT role AS text, role AS value')
		->from($db->qn('#__xbgroupperson'))
		->order($db->qn('text'));
		
		// Get the options.
		$db->setQuery($query);
		$list = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge( $options, $list);
		
		return $options;
	}
}
