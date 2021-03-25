<?php
/*******
 * @package xbPeople
 * @filesource admin/controlers/categories.php
 * @version 0.4.4 24th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleControllerCategories extends JControllerAdmin {
 
	protected $edcatlink = 'index.php?option=com_categories&task=category.edit&extension=com_xbpeople&id=';
	
    public function getModel($name = 'Categories', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    function categoryedit() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
    	$this->setRedirect($this->edcatlink.$id);    		
    }
    
    function categorylist() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
    	$this->setRedirect('index.php?option=com_xbpeople&view=category&id='.$id);
    }
    
    function categorynew() {
    	$this->setRedirect($this->edcatlink.'0');
    }
    
    function categorynewpeep() {
    	$this->setRedirect($this->edcatlink.'0');
    }
    
    function books() {
    	$this->setRedirect('index.php?option=com_xbbooks&view=categories');
    }
    
    function films() {
    	$this->setRedirect('index.php?option=com_xbfilms&view=categories');
    }
    
}