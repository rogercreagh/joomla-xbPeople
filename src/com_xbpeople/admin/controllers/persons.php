<?php
/*******
 * @package xbPeople
 * @filesource admin/controllers/persons.php
 * @version 1.0.3.3 31st January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleControllerPersons extends JControllerAdmin {
    
    public function getModel($name = 'Person', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }

    function books() {
        $this->setRedirect('index.php?option=com_xbbooks&view=persons');
    }
    
    function events() {
        $this->setRedirect('index.php?option=com_xbevents&view=persons');
    }
    
    function films() {
    	$this->setRedirect('index.php?option=com_xbfilms&view=persons');
    }
        
}