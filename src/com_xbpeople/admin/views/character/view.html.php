<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/view.html.php
 * @version 0.4.1 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbpeopleViewCharacter extends JViewLegacy {
    
    protected $form = null;
    
    public function display($tpl = null) {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->canDo = XbpeopleHelper::getActions('com_xbpeople', 'character', $this->item->id);
        
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
        
        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);
        
        $isNew = ($this->item->id == 0);
        
        $title = Text::_( 'COM_XBPEOPLE' ).': ';
        if ($isNew) {
            $title .= Text::_('COM_XBPEOPLE_TITLE_NEWCHAR');
        } else {
            $title .= Text::_('COM_XBPEOPLE_TITLE_EDITCHAR');
        }
        
        ToolbarHelper::title($title, 'user');
        
        ToolbarHelper::apply('character.apply');
        ToolbarHelper::save('character.save');
        ToolbarHelper::save2new('character.save2new');
        if ($isNew) {
            ToolbarHelper::cancel('character.cancel','JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('character.cancel','JTOOLBAR_CLOSE');
        }
    }
    
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = Factory::getDocument();
        $document->setTitle($isNew ? Text::_('COM_XBPEOPLE_NEW_CHAR') :
            Text::_('COM_XBPEOPLE_EDIT_CHAR'));
    }
}