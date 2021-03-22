<?php
/*******
 * @package xbPeople
 * @filesource admin/controlers/tags.php
 * @version 0.4.2 22nd March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleControllerTags extends JControllerAdmin {
    
    public function getModel($name = 'Tags', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    function tagedit() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id='.$id);
    }
    
    function tagnew() {
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id=0');
    }
    
    function books() {
    	$this->setRedirect('index.php?option=com_xbbooks&view=tags');
    }
    
    function films() {
    	$this->setRedirect('index.php?option=com_films&view=tags');
    }
    
}