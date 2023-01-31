<?php
/*******
 * @package xbPeople
 * @filesource admin/views/persons/view.html.php
 * @version 1.0.3.3 31st January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Layout\FileLayout;

class XbpeopleViewPersons extends JViewLegacy {

    function display($tpl = null) {

        $this->items		= $this->get('Items');

        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        $this->catid 		= $this->state->get('catid');
        if ($this->catid>0) {
            $this->cat 		= XbcultureHelper::getCat($this->catid);
        }
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
        
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
        $this->xbevents_ok = Factory::getSession()->get('xbevents_ok');
        
        // Set the toolbar & sidebar
        $this->addToolbar();
        XbpeopleHelper::addSubmenu('persons');
        $this->sidebar = JHtmlSidebar::render();
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
    	$canDo = ContentHelper::getActions('com_xbpeople', 'component');
    	// XbpeopleHelper::getActions();
        
        $bar = Toolbar::getInstance('toolbar');
        
        ToolBarHelper::title(Text::_('XBPEOPLE_TITLE_PEOPLE_ADMIN'), 'users' );
        
        if ($canDo->get('core.create') > 0) {
            ToolBarHelper::addNew('person.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            ToolBarHelper::editList('person.edit');
        }
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('person.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('person.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolBarHelper::archiveList('person.archive');
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
           ToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'person.delete','JTOOLBAR_EMPTY_TRASH');
        } else if ($canDo->get('core.edit.state')) {
           ToolBarHelper::trash('person.trash');
        }
        
        // Add a batch button
        if ($canDo->get('core.create') && $canDo->get('core.edit')
        		&& $canDo->get('core.edit.state'))
        {
        	// we use a standard Joomla layout to get the html for the batch button
        	$layout = new FileLayout('joomla.toolbar.batch');
        	$batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
        	$bar->appendButton('Custom', $batchButtonHtml, 'batch');
        }
        ToolbarHelper::custom(); //spacer
        
        if ($this->xbbooks_ok) {
        	ToolbarHelper::custom('persons.books', 'stack', '', 'Book People', false) ;
        }
        if ($this->xbevents_ok) {
            ToolbarHelper::custom('persons.events', 'picture', '', 'Event People', false) ;
        }
        if ($this->xbfilms_ok) {
            ToolbarHelper::custom('persons.films', 'screen', '', 'Film People', false) ;
        }
        
        if ($canDo->get('core.admin')) {
            ToolBarHelper::preferences('com_xbpeople');
        }
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('XBPEOPLE_TITLE_PEOPLE_ADMIN'));
    }
    
}