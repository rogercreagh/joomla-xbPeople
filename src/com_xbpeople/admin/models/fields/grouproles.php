<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/bkotherroles.php
 * @version 0.9.6.f 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;

FormHelper::loadFieldClass('combo');

class JFormFieldOtherrole extends JFormFieldCombo {
	
	protected $type = 'Otherrole';
	
	public function getOptions() {
		
		$options = parent::getOptions();
		
		if (XbcultureHelper::checkComponent('com_xbfilms')) {
					
			$db = Factory::getDbo();
			$query  = $db->getQuery(true);
			
			$query->select('DISTINCT role_note AS text, role_note AS value')
			->from($db->qn('#__xbbookperson'))
			->where($db->qn('role').' = '.$db->q('other'))
			->order($db->qn('text'));
			
			// Get the options.
			$db->setQuery($query);
			$list = $db->loadObjectList();
			// Merge any additional options in the XML definition.
			$options = array_merge( $options, $list);
		}
		return $options;
	}
}
