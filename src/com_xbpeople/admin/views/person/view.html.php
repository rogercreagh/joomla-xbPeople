<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/view.html.php
 * @version 0.9.6.a 16th December 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

class XbpeopleViewPerson extends JViewLegacy {
    
    protected $form = null;
    protected $canDo;
    
    public function display($tpl = null) {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        
        $this->canDo = ContentHelper::getActions('com_xbpeople', 'person', $this->item->id);
        // JHelperContent::getActions('com_xbpeople', 'person', $this->item->id);
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	return false;
        }
               
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {

    	$input = Factory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $user = Factory::getUser();
        $userId = $user->get('id');
        $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        
        $canDo = $this->canDo;
        
        
        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);
        
        $isNew = ($this->item->id == 0);
        
        $title = Text::_( 'COM_XBPEOPLE_' ).': ';
        if ($isNew) {
            $title .= Text::_('XBCULTURE_TITLE_NEWPERSON');
        } elseif ($checkedOut) {
        	$title = Text::_('XBCULTURE_TITLE_VIEWPERSON');
        } else {
            $title .= Text::_('XBCULTURE_TITLE_EDITPERSON');
        }
        
        ToolbarHelper::title($title, 'user');
        
        ToolbarHelper::apply('person.apply');
        ToolbarHelper::save('person.save');
        ToolbarHelper::save2new('person.save2new');
        if (XbcultureHelper::checkComponent('com_xbfilms')) {
	        ToolbarHelper::custom('personcat.save2film', 'users', '', 'Save &amp; Films', false) ;
        }
        if (XbcultureHelper::checkComponent('com_xbbooks')) {
        	ToolbarHelper::custom('personcat.save2book', 'user', '', 'Save &amp; Books', false) ;
        }
        if ($isNew) {
            ToolbarHelper::cancel('person.cancel','JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('person.cancel','JTOOLBAR_CLOSE');
        }
    }
    
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = Factory::getDocument();
        $document->setTitle($isNew ? Text::_('XBCULTURE_PERSON_CREATING') :
            Text::_('XBCULTURE_PERSON_EDITING'));
    }
}