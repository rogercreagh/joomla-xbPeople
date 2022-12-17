<?php
/*******
 * @package xbPeople
 * @filesource admin/helpers/xbpeople.php
 * @version 1.0.0.2 17th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Access\Access;

class XbpeopleHelper extends ContentHelper {
	
	public static function addSubmenu($vName = 'persons') {
		if ($vName != 'categories') {
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_DASHBOARD'),
				'index.php?option=com_xbpeople&view=dashboard',
				$vName == 'dashboard'
				);
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_PEOPLE'),
			    'index.php?option=com_xbpeople&view=persons',
			    $vName == 'persons'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_NEWPERSON'),
			    'index.php?option=com_xbpeople&view=person&layout=edit',
			    $vName == 'person'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_GROUPS'),
			    'index.php?option=com_xbpeople&view=groups',
			    $vName == 'groups'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_NEWGROUP'),
			    'index.php?option=com_xbpeople&view=group&layout=edit',
			    $vName == 'group'
			    );
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_CHARS'),
					'index.php?option=com_xbpeople&view=characters',
					$vName == 'characters'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_NEWCHAR'),
					'index.php?option=com_xbpeople&view=character&layout=edit',
					$vName == 'character'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_PEOPLECATS'),
					'index.php?option=com_xbpeople&view=pcategories',
					$vName == 'pcategories'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_NEWCAT'),
					'index.php?option=com_categories&view=category&task=category.edit&extension=com_xbpeople',
					$vName == 'category'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_EDITCATS'),
					'index.php?option=com_categories&view=categories&extension=com_xbpeople',
					$vName == 'categories'
					);
// 			JHtmlSidebar::addEntry(
// 					Text::_('XBCULTURE_ICONMENU_SUBBOOKCATS'),
// 					'index.php?option=com_xbbooks&view=bcategories',
// 					$vName == 'pcategories'
// 					);
// 			JHtmlSidebar::addEntry(
// 					Text::_('XBCULTURE_ICONMENU_SUBFILMCATS'),
// 					'index.php?option=com_xbfilms&view=fcategories',
// 					$vName == 'pcategories'
// 					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_TAGS'),
					'index.php?option=com_xbpeople&view=tags',
					$vName == 'tags'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_NEWTAG'),
					'index.php?option=com_tags&view=tag&layout=edit',
					$vName == 'tag'
					);
			if (XbcultureHelper::checkComponent('com_xbfilms')) {
				JHtmlSidebar::addEntry(
						Text::_('XBCULTURE_ICONMENU_FILMS'),
						'index.php?option=com_xbfilms&view=dashboard',
						$vName == 'films'
						);			
			}
			if (XbcultureHelper::checkComponent('com_xbbooks')) {
				JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_BOOKS'),
					'index.php?option=com_xbbooks&view=dashboard',
					$vName == 'books'
					);
			}
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_OPTIONS'),
			    'index.php?option=com_config&view=component&component=com_xbpeople',
			    $vName == 'options'
			    );
		} else {
			JHtmlSidebar::addEntry(
					Text::_('XBPEOPLE_DASHBOARD'),
					'index.php?option=com_xbpeople&view=dashboard',
					$vName == 'dashboard'
					);
			
			JHtmlSidebar::addEntry(
					ucfirst(Text::_('XBPEOPLE_PEOPLE')),
					'index.php?option=com_xbpeople&view=persons',
					$vName == 'films'
					);
			JHtmlSidebar::addEntry(
					ucfirst(Text::_('XBPEOPLE_CHARACTERS')),
					'index.php?option=com_xbpeople&view=characters',
					$vName == 'characters'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBPEOPLE_CATCOUNT'),
					'index.php?option=com_xbpeople&view=pcategories',
					$vName == 'pcategories'
					);
		}
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

	public static function checkTitleExists($title, $table) {
		$col = ($table == '#__xbcharacters') ? 'name' : 'title';
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->quoteName($table))
		->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($title)));
		$db->setQuery($query);
		$res = $db->loadResult();
		if ($res > 0) {
			return true;
		}
		return false;
	}
	
	
	public static function credit() {
		if (XbcultureHelper::penPont()) {
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