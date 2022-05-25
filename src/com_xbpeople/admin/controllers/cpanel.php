<?php
/*******
 * @package xbPeople
 * @filesource admin/controllers/cpanel.php
 * @version 0.9.8.3 25th May 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class XbpeopleControllerCpanel extends JControllerAdmin {

    public function getModel($name = 'Cpanel', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config );
        return $model;
    }
    
    function books() {
    	$xbbooks_ok = XbcultureHelper::checkComponent('com_xbbooks');
    	//Factory::getSession()->get('xbbooks_ok',false);
    	if ($xbbooks_ok == true) {
    		$this->setRedirect('index.php?option=com_xbbooks&view=cpanel');
    	} elseif ($xbbooks_ok === 0) {
    		Factory::getApplication()->enqueueMessage('xbBooks '.JText::_('XBCULTURE_COMP_DISABLED'), 'warning');
    		$this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbbooks');
    	} else {
    		Factory::getApplication()->enqueueMessage('xbBooks '.JText::_('XBCULTURE_COMP_MISSING'), 'info');
    		$this->setRedirect('index.php?option=com_xbpeople&view=cpanel');
    	}
    }
    
    function films() {
    	$xbfilms_ok = XbcultureHelper::checkComponent('com_xbfilms');
    	//Factory::getSession()->get('xbbooks_ok',false);
    	if ($xbfilms_ok == true) {
    		$this->setRedirect('index.php?option=com_xbfilms&view=cpanel');
    	} elseif ($xbbooks_ok === 0) {
    		Factory::getApplication()->enqueueMessage('xbFilms '.JText::_('XBCULTURE_COMP_DISABLED'), 'warning');
    		$this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbfilms');
    	} else {
    		Factory::getApplication()->enqueueMessage('xbFilms '.JText::_('XBCULTURE_COMP_MISSING'), 'info');
    		$this->setRedirect('index.php?option=com_xbpeople&view=cpanel');
    	}
    }
    
    function live() {
    	$xbgigs_ok = XbcultureHelper::checkComponent('com_xblive');
    	//$xbgigs_ok = Factory::getSession()->get('xbgigs_ok',false);
        if ($xbgigs_ok == true) {
            $this->setRedirect('index.php?option=com_xblive&view=cpanel');
        } elseif ($xbgigs_ok === 0) {
            Factory::getApplication()->enqueueMessage('xbLive '.JText::_('XBCULTURE_COMP_DISABLED'), 'warning');
            $this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbgigs');
        } else {
            Factory::getApplication()->enqueueMessage('xbLive '.JText::_('XBCULTURE_COMP_MISSING'), 'info');
            $this->setRedirect('index.php?option=com_xbpeople&view=cpanel');
        }
    }
        
}
