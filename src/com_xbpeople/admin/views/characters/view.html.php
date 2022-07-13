<?php
/*******
 * @package xbPeople
 * @filesource admin/views/characters/view.html.php
 * @version 0.9.6.f 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Layout\FileLayout;

class XbpeopleViewCharacters extends JViewLegacy {

    function display($tpl = null) {

        $this->items		= $this->get('Items');
        
        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');

        $this->searchTitle = $this->state->get('filter.search');
        $this->catid 		= $this->state->get('catid');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
        
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
        
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
            XbpeopleHelper::addSubmenu('characters');
            $this->sidebar = JHtmlSidebar::render();
        }
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
    	$canDo =  ContentHelper::getActions('com_xbpeople', 'component');
    	// XbpeopleHelper::getActions();
                
        ToolbarHelper::title(Text::_('COM_XBPEOPLE').': '.Text::_('XBCULTURE_TITLE_CHARMANAGER'), 'users' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('character.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            ToolbarHelper::editList('character.edit');
        }
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('character.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('character.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('character.archive');
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
           ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'character.delete','JTOOLBAR_EMPTY_TRASH');
        } else if ($canDo->get('core.edit.state')) {
           ToolbarHelper::trash('character.trash');
        }
        
        // Add a batch button
        $bar = Toolbar::getInstance('toolbar');
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
        	ToolbarHelper::custom('characters.books', 'stack', '', 'xbBooks', false) ;
        }
        if ($this->xbfilms_ok) {
        	ToolbarHelper::custom('characters.films', 'screen', '', 'xbFilms', false) ;
        }       
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbpeople');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbpeople/doc?tmpl=component#admin-chars' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('XBPEOPLE_ADMIN_CHARS'));
    }
    
}