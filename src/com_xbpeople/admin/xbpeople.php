<?php
/*******
 * @package xbPeople
 * @filesource admin/xbpeople.php
 * @version 1.0.2.5 11th January 2023
 * @since 0.1.0 8th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\BaseController;

$app = Factory::getApplication();
if (!Factory::getUser()->authorise('core.manage', 'com_xbpeople')) {
    $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'),'warning');
    return false;
}

$document = Factory::getDocument();
Factory::getLanguage()->load('com_xbculture');

$params = ComponentHelper::getParams('com_xbpeople');
if ($params->get('savedata','notset')=='notset') {
    Factory::getApplication()->enqueueMessage(Text::_('XBCULTURE_OPTIONS_UNSAVED'),'Error');
}
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
//add fontawesome5
$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
$document->addStyleSheet($cssFile);

JLoader::register('XbpeopleHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbpeople.php');
JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');

$sess = Factory::getSession();
$sess->set('xbpeople_ok',true);
//if there is no session variable for films/books/events check them.
if (!$sess->has('xbfilms_ok')) {
    XbcultureHelper::checkComponent('com_xbfilms');
}
if (!$sess->has('xbevents_ok')) {
    XbcultureHelper::checkComponent('com_xbevents');
}
if (!$sess->has('xbbooks_ok')) {
    XbcultureHelper::checkComponent('com_xbbooks');
}

$controller = BaseController::getInstance('xbpeople');

$controller->execute(Factory::getApplication()->input->get('task'));

$controller->redirect();
