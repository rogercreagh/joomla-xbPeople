<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/rolelist.php
 * @version 0.9.9.8 19th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

class JFormFieldRolelist extends JFormFieldList {
	
	protected $type = 'Rolelist';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT nationality AS text, nationality AS value')
		->from('#__xbpersons')
		->where("nationality<>''")
		->order('nationality');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
		$xbfilmsStatus = Factory::getSession()->get('xbfilms_ok',false);
		
		$options = array();
		if ($xbbooksStatus) {
		    $options[] = array('text'=>Text::_('XBCULTURE_ALL_BOOKROLES'), 'value'=>'book');
		}
		if ($xbfilmsStatus) {
		    $options[] = array('text'=>Text::_('XBCULTURE_ALL_FILMROLES'), 'value'=>'film');
		}
		if ($xbbooksStatus) {
		    $options[] = array('text'=>Text::_('XBCULTURE_AUTHORS'), 'value'=>'author');
		    $options[] = array('text'=>Text::_('XBCULTURE_EDITORS'), 'value'=>'editor');
		    $options[] = array('text'=>Text::_('XBCULTURE_MENTIONED'), 'value'=>'mention');
		    $options[] = array('text'=>Text::_('XBCULTURE_OTHER_ROLES'), 'value'=>'other');
		}
		if ($xbfilmsStatus) {
		    $options[] = array('text'=>Text::_('XBCULTURE_ALL_FILMROLES'), 'value'=>'film');
		    $options[] = array('text'=>Text::_('XBCULTURE_DIRECTORS'), 'value'=>'director');
		    $options[] = array('text'=>Text::_('XBCULTURE_PRODUCERS'), 'value'=>'producer');
		    $options[] = array('text'=>Text::_('XBCULTURE_CREW'), 'value'=>'crew');
		    $options[] = array('text'=>Text::_('XBCULTURE_CAST'), 'value'=>'actor');
		    $options[] = array('text'=>Text::_('XBCULTURE_APPEARANCES'), 'value'=>'appearsin');
		}
		$options[] = array('text'=>Text::_('XBCULTURE_ORPHANS'), 'value'=>'orphans');
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
