<?php 
/*******
 * @package xbPeople
 * @filesource admin/views/category/view.html.php
 * @version 0.4.0 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbpeopleViewCategory extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->addToolBar();
		XbpeopleHelper::addSubmenu('categories');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = XbpeopleHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'COM_XBPEOPLE' ).': '.Text::_( 'COM_XBPEOPLE_TITLE_CATMANAGER' ), 'tag' );
		
		ToolbarHelper::custom('categories.categories', 'folder', '', 'COM_XBPEOPLE_CAT_LIST', false) ;
		ToolbarHelper::custom('categories.categoryedit', 'edit', '', 'COM_XBPEOPLE_EDIT_CAT', false) ;
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbpeople');
		}
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_XBPEOPLE_ADMIN_CATITEMS'));
	}
	
}
