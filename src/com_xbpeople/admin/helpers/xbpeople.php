<?php
/*******
 * @package xbPeople
 * @filesource admin/helpers/xbpeople.php
 * @version 0.3.0 19th March 2021
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
				Text::_('COM_XBPEOPLE_ICONMENU_CPANEL'),
				'index.php?option=com_xbpeople&view=cpanel',
				$vName == 'cpanel'
				);
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
				'index.php?option=com_xbpeople&view=categories',
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
					'index.php?option=com_xbfilms&view=cpanel',
					$vName == 'films'
					);			
			JHtmlSidebar::addEntry(
			    Text::_('COM_XBPEOPLE_ICONMENU_FILMPEOPLE'),
			    'index.php?option=com_xbfilms&view=persons',
			    $vName == 'films'
			    );
		}
		if (XbpeopleHelper::checkComponent('com_xbbooks')) {
			JHtmlSidebar::addEntry(
				Text::_('COM_XBPEOPLE_ICONMENU_BOOKS'),
				'index.php?option=com_xbbooks&view=cpanel',
				$vName == 'books'
				);
			JHtmlSidebar::addEntry(
			    Text::_('COM_XBPEOPLE_ICONMENU_BOOKPEOPLE'),
			    'index.php?option=com_xbbooks&view=persons',
			    $vName == 'books'
			    );
		}
		JHtmlSidebar::addEntry(
		    Text::_('COM_XBPEOPLE_ICONMENU_OPTIONS'),
		    'index.php?option=com_config&view=component&component=com_xbpeople',
		    $vName == 'options'
		    );
	}

	/**
	 * @name getItemCnt
	 * @desc returns the number of items in a table
	 * @param string $table
	 * @return integer
	 */
	public static function getItemCnt($table) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')->from($db->quoteName($table));
		$db->setQuery($query);
		$cnt=-1;
		try {
			$cnt = $db->loadResult();
		} catch (Exception $e) {
			$dberr = $e->getMessage();
			Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
		}
		return $cnt;
	}
	
	/***
	 * checkComponent()
	 * test whether a component is installed and enabled. Sets a session variable to save a subsequent db call
	 * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
	 * @param $usedb - if true will ignore session variable an force db check
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
				$credit .= '<br />'.Text::_('XBCULTURE_BEER_TAG');				
				$credit .= Text::_('XBCULTURE_BEER_FORM');
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