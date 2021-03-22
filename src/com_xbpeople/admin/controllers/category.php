<?php
/*******
 * @package xbPeople
 * @filesource admin/controlers/category.php
 * @version 0.4.0 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleControllerCategory extends JControllerAdmin {
    
    public function getModel($name = 'Category', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function categories() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=categories');
    }

    function categoryedit() {
    	$cid =  Factory::getApplication()->input->get('cid');
    	$this->setRedirect('index.php?option=com_categories&task=category.edit&id='.$cid);
    }
        
}