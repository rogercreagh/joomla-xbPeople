<?php
/*******
 * @package xbPeople
 * @filesource admin/views/pcategories/view.html.php
 * @version 0.9.6.a 16th December 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

class XbpeopleViewPcategories extends JViewLegacy {
    
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
        
        XbpeopleHelper::addSubmenu('pcategories');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
    	$canDo =  ContentHelper::getActions('com_xbpeople', 'component');
    	// XbpeopleHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'COM_XBPEOPLE' ).': '.Text::_( 'COM_XBPEOPLE_TITLE_CATSMANAGER' ), 'folder' );
        
        //index.php?option=com_categories&view=category&layout=edit&extension=com_xbpeople
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::custom('pcategories.categorynewpeep','new','','COM_XBPEOPLE_NEW_PCAT',false);
        }
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::editList('pcategories.categoryedit', 'XBCULTURE_EDIT_CAT');       	
         }        
//         ToolbarHelper::custom('pcategories.categorylist','list-2','','COM_XBPEOPLE_LIST_CAT',true);
         
         ToolbarHelper::custom(); //spacer
         if ($this->xbbooks_ok) {
         	ToolbarHelper::custom('pcategories.books', 'stack', '', 'xbBooks', false) ;
         }
         if ($this->xbfilms_ok) {
         	ToolbarHelper::custom('pcategories.films', 'screen', '', 'xbFilms', false) ;
         }
         
         if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbpeople');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbpeople/doc?tmpl=component#admin-cats' );
    }

    protected function setDocument() {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('COM_XBPEOPLE_ADMIN_CATS'));
    }
}
