<?php 
/*******
 * @package xbPeople
 * @filesource admin/views/pcategory/view.html.php
 * @version 0.9.6.a 16th December 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;

class XbpeopleViewPcategory extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->addToolBar();
		XbpeopleHelper::addSubmenu('pcategories');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = ContentHelper::getActions('com_xbpeople', 'component');
		// XbpeopleHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'COM_XBPEOPLE_' ).': '.Text::_( 'XBPEOPLE_TITLE_CATMANAGER' ), 'tag' );
		
		ToolbarHelper::custom('pcategory.pcategories', 'folder', '', 'XBPEOPLE_CAT_LIST', false) ;
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbpeople');
		}
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(Text::_('XBPEOPLE_ADMIN_CATITEMS'));
	}
	
}
