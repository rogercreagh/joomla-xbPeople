<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/view.html.php
 * @version 0.1.0 2nd February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleViewPerson extends JViewLegacy {
    
    protected $form = null;
    protected $canDo;
    
    public function display($tpl = null) {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        
        $this->canDo = JHelperContent::getActions('com_xbpeople', 'person', $this->item->id);
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	return false;
        }
               
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
        $input = JFactory::getApplication()->input;
        
        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);
        
        $isNew = ($this->item->id == 0);
        
        $title = JText::_( 'COM_XBPEOPLE' ).': ';
        if ($isNew) {
            $title .= JText::_('COM_XBPEOPLE_TITLE_NEWPERSON');
        } else {
            $title .= JText::_('COM_XBPEOPLE_TITLE_EDITPERSON');
        }
        
        JToolbarHelper::title($title, 'user');
        
        JToolbarHelper::apply('person.apply');
        JToolbarHelper::save('person.save');
        JToolbarHelper::save2new('person.save2new');
        if ($isNew) {
            JToolbarHelper::cancel('person.cancel','JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('person.cancel','JTOOLBAR_CLOSE');
        }
    }
    
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_XBPEOPLE_PERSON_CREATING') :
            JText::_('COM_XBPEOPLE_PERSON_EDITING'));
    }
}