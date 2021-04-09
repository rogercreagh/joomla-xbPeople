<?php
/*******
 * @package xbPeople
 * @filesource admin/controlers/pcategory.php
 * @version 0.9.1.1 9th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleControllerPcategory extends JControllerAdmin {
    
    public function getModel($name = 'Category', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function categories() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=pcategories');
    }

    function categoryedit() {
    	$cid =  Factory::getApplication()->input->get('cid');
    	$this->setRedirect('index.php?option=com_categories&task=category.edit&id='.$cid);
    }
        
}