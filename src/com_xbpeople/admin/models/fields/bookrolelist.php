<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/bookrolelist.php
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

class JFormFieldBookrolelist extends JFormFieldList {
	
	protected $type = 'Bookrolelist';
	
	public function getOptions() {
		
		$options = parent::getOptions();
		
		if (Factory::getSession()->get('xbbooks_ok',false)==1) {
					
			$db = Factory::getDbo();
			$query  = $db->getQuery(true);
			
			$query->select('DISTINCT role AS text, role AS value')
			->from($db->qn('#__xbbookperson'))
			->where($db->qn('role')." NOT IN ('author','editor','mention')")
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
