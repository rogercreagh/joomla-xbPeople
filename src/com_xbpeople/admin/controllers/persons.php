<?php
/*******
 * @package xbPeople
 * @filesource admin/controllers/persons.php
 * @version 0.1.0 8th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbpeopleControllerPersons extends JControllerAdmin {
    
    public function getModel($name = 'Person', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
}