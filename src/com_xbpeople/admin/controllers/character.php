<?php
/*******
 * @package xbPeople
 * @filesource admin/controlers/character.php
 * @version 0.4.1 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class XbpeopleControllerCharacter extends FormController {
    
	protected function postSaveHook(JModelLegacy $model, $validData = array()) {
		$item = $model->getItem();
		
		if (isset($item->params) && is_array($item->params)) {
			$registry = new Registry($item->params);
			$item->params = (string) $registry;
		}
		
		if (isset($item->metadata) && is_array($item->metadata)) {
			$registry = new Registry($item->metadata);
			$item->metadata = (string) $registry;
		}
	}
	
	public function publish() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('character');
        $wynik = $model->publish($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=characters');
        $this->setRedirect($redirectTo, $msg );
    }
    
    public function unpublish() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('character');
        $wynik = $model->publish($pid,0);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=characters');
        $this->setRedirect($redirectTo, $msg );
    }

    public function archive() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('charcater');
        $wynik = $model->publish($pid,2);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=characters');
        $this->setRedirect($redirectTo, '' );
    }
    
    public function delete() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('character');
        $wynik = $model->delete($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=characters');
        $this->setRedirect($redirectTo, '' );
    }
    
    public function trash() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('character');
        $wynik = $model->publish($pid,-2);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=characters');
        $this->setRedirect($redirectTo, '' );
    }
       
    public function checkin() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('character');
        $wynik = $model->checkin($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=characters');
        $this->setRedirect($redirectTo, '' );
    }
    
    public function batch($model = null)
    {
    	$model = $this->getModel('character');
    	$this->setRedirect((string)Uri::getInstance());
    	return parent::batch($model);
    }
    
}
