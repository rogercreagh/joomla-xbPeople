<?php
/*******
 * @package xbPeople
 * @filesource admin/views/tags/view.html.php
 * @version 0.4.2 21st March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

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
        
        XbpeopleHelper::addSubmenu('tags');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbpeopleHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'COM_XBFILMS_TITLE_TAGSMANAGER' ), 'tags' );
        
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
        	ToolbarHelper::custom('tags.booktags', 'tags', '', 'Book Tags', false) ;
        }
        if ($this->xbbooks_ok) {
        	ToolbarHelper::custom('tags.filmtags', 'tags', '', 'Film Tags', false) ;
        }
        
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbpeople/doc?tmpl=component#admin-tags' );
    }
    
    protected function setDocument()
    {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('COM_XBFILMS_ADMIN_TAGS'));
    }
    
}