<?php
/*******
 * @package xbPeople
 * @filesource admin/xbpeople.php
 * @version 0.1.0 2nd February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$document = Factory::getDocument();
$cssFile = Uri::root(true)."/media/com_people/css/xb.css";
$document->addStyleSheet($cssFile);

if (!Factory::getUser()->authorise('core.manage', 'com_xbpeople')) {
	Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNODIRECTOR'),'warning');
	return false;
}

JLoader::register('XbpeopleHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbpeople.php');

$controller = JControllerLegacy::getInstance('Xbpeople');

$controller->execute(Factory::getApplication()->input->get('task'));

$controller->redirect();
