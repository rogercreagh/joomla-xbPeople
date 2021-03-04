<?php
/*******
 * @package xbPeople
 * @filesource admin/xbpeople.php
 * @version 0.2.1 19th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

if (!Factory::getUser()->authorise('core.manage', 'com_xbpeople')) {
	//Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNODIRECTOR'),'warning');
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
	return false;
}

$document = Factory::getDocument();
$cssFile = Uri::root(true)."/media/com_xbpeople/css/xb.css";
$document->addStyleSheet($cssFile);


JLoader::register('XbpeopleHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbpeople.php');
XbpeopleHelper::checkComponent('com_xbfilms');
XbpeopleHelper::checkComponent('com_xbbooks');

$controller = JControllerLegacy::getInstance('Xbpeople');

$controller->execute(Factory::getApplication()->input->get('task'));

$controller->redirect();
