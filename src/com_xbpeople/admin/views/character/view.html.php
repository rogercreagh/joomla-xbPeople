<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/view.html.php
 * @version 1.0.2.8 14th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

class XbpeopleViewCharacter extends JViewLegacy {
    
    protected $form = null;
    
    public function display($tpl = null) {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->canDo = ContentHelper::getActions('com_xbpeople', 'character', $this->item->id);
        // XbpeopleHelper::getActions('com_xbpeople', 'character', $this->item->id);
        
        $this->params      = $this->get('State')->get('params');
        $this->chartaggroup_parent = $this->params->get('chartaggroup_parent',0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, description')->from($db->quoteName('#__tags'))
        ->where('id = '.$this->chartaggroup_parent);
        $db->setQuery($query);
        $this->taggroupinfo = $db->loadAssocList('id');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	return false;
        }
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
        $this->xbevents_ok = Factory::getSession()->get('xbevents_ok');
        
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
        
        $isNew = ($this->item->id == 0);
        
        $title = Text::_( 'COM_XBPEOPLE' ).': ';
        if ($isNew) {
            $title .= Text::_('XBPEOPLE_TITLE_NEWCHAR');
        } elseif ($checkedOut) {
            $title .= Text::_('XBCULTURE_TITLE_VIEWPERSON');
        } else {
            $title .= Text::_('XBPEOPLE_TITLE_EDITCHAR');
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
        ToolbarHelper::custom(); //spacer
        
        $bar = Toolbar::getInstance( 'toolbar' );
        if ($this->item->id > 0) {
            $dhtml = '<a href=""
             	data-toggle="modal" data-target="#ajax-cpvmodal"  onclick="window.pvid='.$this->item->id.';"
             	class="btn btn-small btn-primary"><i class="far fa-eye"></i> '.Text::_('Preview').'</a>';
            $bar->appendButton('Custom', $dhtml);
        }
//         $dhtml = '<a href="index.php?option=com_xbpeople&view=character&layout=modalpv&tmpl=component&id='.$this->item->id.'"
//             	data-toggle="modal" data-target="#ajax-pvmodal"
//             	class="btn btn-small btn-primary"><i class="icon-eye"></i> '.JText::_('Preview').'</a>';
//             $bar->appendButton('Custom', $dhtml);
//         }
    }
    
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = Factory::getDocument();
        $document->setTitle($isNew ? Text::_('XBPEOPLE_NEW_CHAR') :
            Text::_('XBPEOPLE_EDIT_CHAR'));
    }
}