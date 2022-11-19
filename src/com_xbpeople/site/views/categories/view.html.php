<?php 
/*******
 * @package xbPeople
 * @filesource site/views/categories/view.html.php
 * @version 0.9.9.8 18th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbpeopleViewCategories extends JViewLegacy {
	
	public function display($tpl = null) {
		
		$this->items 		= $this->get('Items');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');
		$this->hide_empty = $this->params->get('hide_empty',1);
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('list_title','','text');
		$this->header['subtitle'] = $this->params->get('list_subtitle','','text');
		$this->header['text'] = $this->params->get('list_headtext','','text');
		
		$this->show_catspath = $this->params->get('show_catspath','1','int');
		$this->show_clist_empty = $this->params->get('show_clist_empty','0','int');
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		parent::display($tpl);
	} // end function display()
	
}
