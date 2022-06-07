<?php
/*******
 * @package xbPeople
 * @filesource admin/xbpeople.php
 * @version 0.9.0 5th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

if (!Factory::getUser()->authorise('core.manage', 'com_xbpeople')) {
    Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'),'warning');
    return false;
}

$document = Factory::getDocument();
$params = ComponentHelper::getParams('com_xbpeople');
$usexbcss = $params->get('use_xbcss',1);
if ($usexbcss<2) {
    $cssFile = Uri::root(true)."/media/com_xbpeople/css/xbculture.css";
    $altcss = $params->get('css_file','');
    if ($usexbcss==0) {
        if ($altcss && file_exists(JPATH_ROOT.$altcss)) {
            $cssFile = $altcss;
        }
    }
    $document->addStyleSheet($cssFile);
}
$exticon = $params->get('ext_icon',0);
if ($exticon) {
    $style = 'a[target="_blank"]:after {font-style: normal; font-weight:bold; content: "\2197";}' ;
    $document->addStyleDeclaration($style);
}

Factory::getLanguage()->load('com_xbculture');

JLoader::register('XbpeopleHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbpeople.php');
JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');
XbcultureHelper::checkComponent('com_xbfilms');
XbcultureHelper::checkComponent('com_xbbooks');			
XbcultureHelper::checkComponent('com_xbgigs');

$controller = JControllerLegacy::getInstance('Xbpeople');

$controller->execute(Factory::getApplication()->input->get('task'));

$controller->redirect();
