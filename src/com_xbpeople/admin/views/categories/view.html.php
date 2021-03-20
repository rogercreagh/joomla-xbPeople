<?php
/*******
 * @package xbPeople
 * @filesource admin/views/categories/view.html.php
 * @version 0.4.0 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class XbpeopleViewCategories extends JViewLegacy {
    
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
        
        XbpeopleHelper::addSubmenu('categories');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbpeopleHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'COM_XBPEOPLE' ).': '.Text::_( 'COM_XBPEOPLE_TITLE_CATSMANAGER' ), 'folder' );
        
        //index.php?option=com_categories&view=category&layout=edit&extension=com_xbpeople
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::custom('categories.categorynewpeep','new','','COM_XBPEOPLE_NEW_PCAT',false);
        }
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::editList('categories.categoryedit', 'COM_XBPEOPLE_EDIT_CAT');       	
         }
         
         ToolbarHelper::custom('categories.categorylist','list-2','','COM_XBPEOPLE_LIST_CAT',true);
         
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
