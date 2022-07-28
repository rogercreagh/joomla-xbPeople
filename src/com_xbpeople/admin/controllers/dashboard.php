<?php
/*******
 * @package xbPeople
 * @filesource admin/controllers/dashboard.php
 * @version 0.9.8.7 5th June 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

class XbpeopleControllerDashboard extends JControllerAdmin {

    public function getModel($name = 'Dashboard', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config );
        return $model;
    }
    
    function books() {
    	$xbbooks_ok = XbcultureHelper::checkComponent('com_xbbooks');
    	//Factory::getSession()->get('xbbooks_ok',false);
    	if ($xbbooks_ok == true) {
    		$this->setRedirect('index.php?option=com_xbbooks&view=dashboard');
    	} elseif ($xbbooks_ok === 0) {
    	    Factory::getApplication()->enqueueMessage('<span class="xbhlt" style="padding:5px 10px;">xbBooks '.Text::_('XBCULTURE_COMP_DISABLED').'</span>', 'warning');
    		$this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbbooks');
    	} else {
    	    Factory::getApplication()->enqueueMessage('<span class="xbhlt" style="padding:5px 10px;">xbBooks '.Text::_('XBCULTURE_COMP_MISSING').'</span>', 'info');
    		$this->setRedirect('index.php?option=com_xbpeople&view=dashboard');
    	}
    }
    
    function films() {
    	$xbfilms_ok = XbcultureHelper::checkComponent('com_xbfilms');
    	//Factory::getSession()->get('xbbooks_ok',false);
    	if ($xbfilms_ok == true) {
    		$this->setRedirect('index.php?option=com_xbfilms&view=dashboard');
    	} elseif ($xbbooks_ok === 0) {
    	    Factory::getApplication()->enqueueMessage('<span class="xbhlt" style="padding:5px 10px;">xbFilms '.Text::_('XBCULTURE_COMP_DISABLED').'</span>', 'warning');
    		$this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbfilms');
    	} else {
    	    Factory::getApplication()->enqueueMessage('<span class="xbhlt" style="padding:5px 10px;">xbFilms '.Text::_('XBCULTURE_COMP_MISSING').'</span>', 'info');
    		$this->setRedirect('index.php?option=com_xbpeople&view=dashboard');
    	}
    }
    
    function live() {
        $xbevents_ok = XbcultureHelper::checkComponent('com_xbevents');
        //$xbevents_ok = Factory::getSession()->get('xbevents_ok',false);
        if ($xbevents_ok == true) {
            $this->setRedirect('index.php?option=com_xbevents&view=dashboard');
        } elseif ($xbevents_ok === 0) {
            Factory::getApplication()->enqueueMessage('<span class="xbhlt" style="padding:5px 10px;">xbEvents '.Text::_('XBCULTURE_COMP_DISABLED').'</span>', 'warning');
            $this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbevents');
        } else {
            Factory::getApplication()->enqueueMessage('<span class="xbhlt" style="padding:5px 10px;">xbEvents '.Text::_('XBCULTURE_COMP_MISSING').'</span>', 'info');
            $this->setRedirect('index.php?option=com_xbpeople&view=dashboard');
        }
    }
        
}
