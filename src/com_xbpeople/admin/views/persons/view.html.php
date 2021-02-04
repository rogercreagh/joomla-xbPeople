<?php
/*******
 * @package xbFilms
 * @filesource admin/views/persons/view.html.php
 * @version 0.1.0 22nd November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbfilmsViewPersons extends JViewLegacy {

    function display($tpl = null) {
        // Get application
        $app = JFactory::getApplication();
        $context = "xbfilms.list.admin.persons";
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');
        $this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'lastname', 'cmd');
        $this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
        $this->searchTitle = $this->state->get('filter.search');
        $this->catid 		= $this->state->get('catid');
        if ($this->catid>0) {
            $this->cat 		= XbfilmsHelper::getCat($this->catid);
        }
        
        if ($this->getLayout() !== 'modal') {
            XbfilmsHelper::addSubmenu('persons');
        }
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
        
        if ($this->getLayout() !== 'modal') {
        // Set the toolbar & sidebar
            $this->addToolbar();
            $this->sidebar = JHtmlSidebar::render();
        }
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
        $canDo = XbfilmsHelper::getActions();
        
        $bar = JToolbar::getInstance('toolbar');
        
        JToolBarHelper::title(JText::_('COM_XBFILMS').': '.JText::_('COM_XBFILMS_TITLE_PEOPLEMANAGER'), 'users' );
        
        if ($canDo->get('core.create') > 0) {
            JToolBarHelper::addNew('person.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            JToolBarHelper::editList('person.edit');
        }
        if ($canDo->get('core.edit.state')) {
            JToolbarHelper::publish('person.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('person.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            JToolBarHelper::archiveList('person.archive');
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
           JToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'person.delete','JTOOLBAR_EMPTY_TRASH');
        } else if ($canDo->get('core.edit.state')) {
           JToolBarHelper::trash('person.trash');
        }
        
        // Add a batch button
        if ($canDo->get('core.create') && $canDo->get('core.edit')
        		&& $canDo->get('core.edit.state'))
        {
        	// we use a standard Joomla layout to get the html for the batch button
        	$layout = new JLayoutFile('joomla.toolbar.batch');
        	$batchButtonHtml = $layout->render(array('title' => JText::_('JTOOLBAR_BATCH')));
        	$bar->appendButton('Custom', $batchButtonHtml, 'batch');
        }
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_xbfilms');
        }
    }
    
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_XBFILMS_ADMIN_PEOPLE'));
    }
    
}