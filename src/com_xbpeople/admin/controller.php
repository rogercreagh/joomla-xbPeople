<?php
/*******
 * @package xbPeople
 * @filesource admin/controller.php
 * @version 0.9.0 5th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbpeopleController extends JControllerLegacy
{
	protected $default_view = 'cpanel';
	
	public function display ($cachable = false, $urlparms = false){
		require_once JPATH_COMPONENT.'/helpers/xbpeople.php';
		require_once JPATH_COMPONENT.'/helpers/xbculture.php';
		
		return parent::display();
	}
}