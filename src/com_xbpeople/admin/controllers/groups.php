<?php
/*******
 * @package xbPeople
 * @filesource admin/controllers/groups.php
 * @version 1.0.0.4 18th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbpeopleControllerGroups extends JControllerAdmin {
    
    public function getModel($name = 'Person', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
}