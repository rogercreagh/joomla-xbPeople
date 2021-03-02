<?php
/*******
 * @package xbPeople
 * @filesource admin/helpers/xbpeople.php
 * @version 0.2.2 2nd March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\HTML\HTMLHelper;
// use Joomla\CMS\Filter\OutputFilter;
// use Joomla\CMS\Application\ApplicationHelper;

class XbpeopleHelper extends ContentHelper {
	
	public static function addSubmenu($vName = 'persons') {
		JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_PEOPLE'),
				'index.php?option=com_xbpeople&view=persons',
				$vName == 'persons'
				);
		JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_NEWPERSON'),
				'index.php?option=com_xbpeople&view=person&layout=edit',
				$vName == 'person'
				);
		JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_CHARS'),
				'index.php?option=com_xbpeople&view=characters',
				$vName == 'characters'
				);
		JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_NEWCHAR'),
				'index.php?option=com_xbpeople&view=character&layout=edit',
				$vName == 'character'
				);
		JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_CATEGORIES'),
				'index.php?option=com_categories&extension=com_xbpeople',
				$vName == 'categories'
				);
		JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_NEWCATEGORY'),
				'index.php?option=com_categories&view=category&layout=edit&extension=com_xbpeople',
				$vName == 'category'
				);
		if (XbpeopleHelper::checkComponent('com_xbfilms')) {
			JHtmlSidebar::addEntry(
					Text::_('COM_XBPEOPLE_ICONMENU_FILMS'),
					'index.php?option=com_xbfilms&view=persons',
					$vName == 'films'
					);			
		}
		if (XbpeopleHelper::checkComponent('com_xbbooks')) {
			JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_BOOKS'),
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
	
	public static function getIdFromAlias($table,$alias, $ext = 'com_xbpeople') {
		$alias = trim($alias,"' ");
		$table = trim($table,"' ");
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->quoteName($table))->where($db->quoteName('alias')." = ".$db->quote($alias));
		if ($table === '#__categories') {
			$query->where($db->quoteName('extension')." = ".$db->quote($ext));
		}
		$db->setQuery($query);
		$res =0;
		$res = $db->loadResult();
		return $res;
	}
	
	/***
	 * checkComponent()
	 * test whether a component is installed, and if installed whether enabled
	 * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
	 * @return boolean|number - true= installed and enabled, 0= installed not enabled, false = not installed
	 */
	public static function checkComponent($name) {
		$sname=substr($name,4).'_ok';
		$sess= Factory::getSession();
			$db = Factory::getDBO();
			$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote($name));
			$res = $db->loadResult();
			$sess->set($sname,$res);
			return $res;
	}
	
	public static function checkPersonExists($firstname, $lastname) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')->from('#__xbpersons')
		->where('LOWER('.$db->quoteName('firstname').')='.$db->quote(strtolower($firstname)).' AND LOWER('.$db->quoteName('lastname').')='.$db->quote(strtolower($lastname)));
		$db->setQuery($query);
		$res = $db->loadResult();
		if ($res > 0) {
			return true;
		}
		return false;
	}
	
	public static function credit() {
		if (self::penPont()) {
			return '';
		} else {
			$xmldata = Installer::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_xbpeople/xbpeople.xml');
			$credit='<div class="xbcredit"><a href="http://crosborne.uk/xbculture" target="_blank">
                xbPeople Component '.$xmldata['version'].' '.$xmldata['creationDate'].'</a>';
			if (Factory::getApplication()->isClient('administrator')==true) {
				$credit .= '<br />'.Text::_('COM_XBCULTURE_BEER_TAG');				
				$credit .= Text::_('COM_XBCULTURE_BEER');
			}
			$credit .= '</div>';
			return $credit;
		}
	}
	
	public static function penPont() {
		$params = ComponentHelper::getParams('com_xbpeople');
		$beer = trim($params->get('roger_beer'));
		//Factory::getApplication()->enqueueMessage(password_hash($beer));
		$hashbeer = $params->get('penpont');
		if (password_verify($beer,$hashbeer)) { return true; }
		return false;
	}
/*
$credit .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="69BAH2Z3TRKYW">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!" style="width:120px;">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>';
 */		
}