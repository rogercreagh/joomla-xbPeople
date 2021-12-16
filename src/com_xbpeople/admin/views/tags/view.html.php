<?php
/*******
 * @package xbPeople
 * @filesource admin/views/tags/view.html.php
 * @version 0.9.6.a 16th December 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;

class XbpeopleViewTags extends JViewLegacy {
    
    function display($tpl = null) {
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
        
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
        
        XbpeopleHelper::addSubmenu('tags');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
    	$canDo = ContentHelper::getActions('com_xbpeople', 'component');
    	// XbpeopleHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'COM_XBPEOPLE' ).': '.Text::_( 'XBCULTURE_TITLE_TAGSMANAGER' ), 'tags' );
        
        if ($canDo->get('core.create') > 0) {
        	ToolbarHelper::addNew('tags.tagnew');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
        	ToolbarHelper::editList('tags.tagedit');
        }
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbpeople');
        }
        ToolbarHelper::custom();
        if ($this->xbfilms_ok) {
        	ToolbarHelper::custom('tags.books', 'tags', '', 'xbBooks', false) ;
        }
        if ($this->xbbooks_ok) {
        	ToolbarHelper::custom('tags.films', 'tags', '', 'xbFilms', false) ;
        }
        
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbpeople/doc?tmpl=component#admin-tags' );
    }
    
    protected function setDocument()
    {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('COM_XBPEOPLE_ADMIN_TAGS'));
    }
    
}