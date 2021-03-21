<?php
/*******
 * @package xbPeople
 * @filesource admin/controlers/tag.php
 * @version 0.4.2 21st March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleControllerTag extends JControllerAdmin {
    
    public function getModel($name = 'Tag', $prefix = 'XbpeopleModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function tags() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=tags');
    }

    function tagedit() {
    	$id =  Factory::getApplication()->input->get('tid');
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id='.$id);
    }
    
    function booktag() {
    	$id =  Factory::getApplication()->input->get('tid');
    	$this->setRedirect('index.php?option=com_books&view=tag&id='.$id);
    }
    
    function filmtag() {
    	$id =  Factory::getApplication()->input->get('tid');
    	$this->setRedirect('index.php?option=com_films&view=tag&id='.$id);
    }
    
}