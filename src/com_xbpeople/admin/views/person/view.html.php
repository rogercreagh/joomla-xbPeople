<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/view.html.php
 * @version 1.0.3.3 31st January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

class XbpeopleViewPerson extends JViewLegacy {
    
    protected $form = null;
//    protected $canDo;
    
    public function display($tpl = null) {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        
        $this->canDo = ContentHelper::getActions('com_xbpeople', 'person', $this->item->id);
        // JHelperContent::getActions('com_xbpeople', 'person', $this->item->id);
        
        $this->params      = $this->get('State')->get('params');
        $this->peeptaggroup_parent = $this->params->get('peeptaggroup_parent',0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, description')->from($db->quoteName('#__tags'))
        ->where('id = '.$this->peeptaggroup_parent);
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
        
        if ($isNew) {
            $title = Text::_('XBPEOPLE_TITLE_PERSON_NEW');
        } elseif ($checkedOut) {
        	$title = Text::_('XBPEOPLE_TITLE_PERSON_VIEW');
        } else {
            $title = Text::_('XBPEOPLE_TITLE_PERSON_EDIT');
        }
        
        ToolbarHelper::title($title, 'user');
        
        ToolbarHelper::apply('person.apply');
        ToolbarHelper::save('person.save');
        ToolbarHelper::save2new('person.save2new');
        
//         if (XbcultureHelper::checkComponent('com_xbfilms')) {
//         }
//         if (XbcultureHelper::checkComponent('com_xbbooks')) {
//         	ToolbarHelper::custom('personcat.save2book', 'user', '', 'Save &amp; Books', false) ;
//         }

        if ($isNew) {
            ToolbarHelper::cancel('person.cancel','JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('person.cancel','JTOOLBAR_CLOSE');
        }
        ToolbarHelper::custom(); //spacer
        
        $bar = Toolbar::getInstance( 'toolbar' );
        if ($this->item->id > 0) {
            $dhtml = '<a href=""
             	data-toggle="modal" data-target="#ajax-ppvmodal"  onclick="window.pvid='.$this->item->id.';"
             	class="btn btn-small btn-primary"><i class="far fa-eye"></i> '.Text::_('XBCULTURE_PREVIEW').'</a>';
            $bar->appendButton('Custom', $dhtml);
        }
//         $dhtml = '<a href="index.php?option=com_xbpeople&view=person&layout=modalpv&tmpl=component&id='.$this->item->id.'"
//             	data-toggle="modal" data-target="#ajax-pvmodal"
//             	class="btn btn-small btn-primary"><i class="icon-eye"></i> '.Text::_('Preview').'</a>';
//             $bar->appendButton('Custom', $dhtml);
//         }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbpeople/doc?tmpl=component#personedit' );
    }
    
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = Factory::getDocument();
        $document->setTitle($isNew ? Text::_('XBPEOPLE_TITLE_PERSON_NEW') :
            Text::_('XBPEOPLE_TITLE_PERSON_EDIT'));
    }
}