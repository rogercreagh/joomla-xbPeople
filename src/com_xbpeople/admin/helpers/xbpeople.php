<?php
/*******
 * @package xbPeople
 * @filesource admin/helpers/xbpeople.php
 * @version 0.1.0 9th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Installer\Installer;

// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\HTML\HTMLHelper;
// use Joomla\CMS\Filter\OutputFilter;
// use Joomla\CMS\Application\ApplicationHelper;

class XbpeopleHelper extends ContentHelper {
	
	public static function addSubmenu($vName = 'persons') {
		JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_PEOPLE'),
				'index.php?option=com_xbpeople&view=persons',
				$vName == 'persons'
				);
		JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_NEWPERSON'),
				'index.php?option=com_xbpeople&view=person&layout=edit',
				$vName == 'person'
				);
		JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_CHARS'),
				'index.php?option=com_xbpeople&view=characters',
				$vName == 'characters'
				);
		JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_NEWCHAR'),
				'index.php?option=com_xbpeople&view=character&layout=edit',
				$vName == 'character'
				);
		JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_CATEGORIES'),
				'index.php?option=com_categories&extension=com_xbpeople',
				$vName == 'categories'
				);
		JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_NEWCATEGORY'),
				'index.php?option=com_categories&view=category&layout=edit&extension=com_xbpeople',
				$vName == 'category'
				);
		if (XbpeopleHelper::checkComponent('com_xbfilms')) {
			JHtmlSidebar::addEntry(
					JText::_('COM_XBPEOPLE_ICONMENU_FILMS'),
					'index.php?option=com_xbfilms&view=persons',
					$vName == 'films'
					);			
		}
		if (XbpeopleHelper::checkComponent('com_xbbooks')) {
			JHtmlSidebar::addEntry(
				JText::_('COM_XBPEOPLE_ICONMENU_BOOKS'),
				'index.php?option=com_xbbooks&view=persons',
				$vName == 'books'
				);
		}
	}

	public static function getActions($component = 'com_xbpeople', $section = 'component', $categoryid = 0) {
		//$extension = 'com_xbpeople';
		
		$user 	=Factory::getUser();
		$result = new JObject;
		if (empty($categoryid)) {
			$assetName = $component;
			$level = $section;
		} else {
			$assetName = $component.'.category.'.(int) $categoryid;
			$level = 'category';
		}
		$actions = JAccess::getActions('com_xbpeople', $level);
		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
		return $result;
	}
	
	/***
	 * checkComponent()
	 * test whether a component is installed, and if installed whether enabled
	 * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
	 * @return boolean|number - true= installed and enabled, 0= installed not enabled, false = not installed
	 */
	public static function checkComponent($name) {
		$db = Factory::getDBO();
		$db->setQuery('SELECT extension_id,enabled FROM #__extensions WHERE element = '.$db->quote($name));
		$ans = $db->loadObject();
		if ($ans) {
		    if ($ans->enabled) {
		        return true;
		    } else {
		        return 0;
		    }
		}
		return false;
	}
	
	public static function credit() {
		$xmldata = JApplicationHelper::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_xbfilms/xbpeople.xml');
		$credit='<div class="xbcredit"><a href="http://crosborne.uk/xbpeople" target="_blank">
            xbFilms Component '.$xmldata['version'].' '.$xmldata['creationDate'].'</a></div>';
		return $credit;
	}
	
	
}