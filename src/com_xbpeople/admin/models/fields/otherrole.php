<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/otherrole.php
 * @version 0.1.0 8th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;

JFormHelper::loadFieldClass('combo');

class JFormFieldOtherrole extends JFormFieldCombo {
	
	protected $type = 'Otherrole';
	
	public function getOptions() {
		
		$options = parent::getOptions();
		
		if (XbpeopleHelper::checkComponent('com_xbfilms')) {
					
			$db = JFactory::getDbo();
			$query  = $db->getQuery(true);
			
			$query->select('DISTINCT role_note AS text, role_note AS value')
			->from('#__xbbookperson')
			->where("role = 'other'")
			->order('text');
			
			// Get the options.
			$db->setQuery($query);
			$list = $db->loadObjectList();
			// Merge any additional options in the XML definition.
			$options = array_merge( $options, $list);
		}
		return $options;
	}
}
