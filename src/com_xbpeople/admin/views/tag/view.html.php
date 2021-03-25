<?php 
/*******
 * @package xbPeople
 * @filesource admin/views/tag/view.html.php
 * @version 0.4.4 23rd March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbpeopleViewTag extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
		$this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
		
		$this->addToolBar();
		XbpeopleHelper::addSubmenu('tags');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = XbpeopleHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'COM_XBPEOPLE' ).': '.Text::_( 'XBCULTURE_TITLE_TAGMANAGER' ), 'tag' );
		
		ToolbarHelper::custom('tag.tags', 'tags', '', 'XBCULTURE_TAG_LIST', false) ;
		ToolbarHelper::custom('tag.tagedit', 'edit', '', 'XBCULTURE_EDIT_TAG', false) ;
		ToolbarHelper::custom();
		if ($this->xbfilms_ok) {
			ToolbarHelper::custom('tag.books', 'tag', '', 'Books', false) ;
		}
		if ($this->xbbooks_ok) {
			ToolbarHelper::custom('tag.films', 'tag', '', 'Films', false) ;
		}
		
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbpeople');
		}
	}
	
	protected function setDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_XBPEOPLE_ADMIN_TAGITEMS'));
	}
	
}
