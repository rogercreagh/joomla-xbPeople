<?php 
/*******
 * @package xbPeople
 * @filesource site/views/characters/view.html.php
 * @version 0.9.9.1 6th July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class XbpeopleViewCharacters extends JViewLegacy {
	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		$this->searchTitle = $this->state->get('filter.search');
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('list_title','','text');
		$this->header['subtitle'] = $this->params->get('list_subtitle','','text');
		$this->header['text'] = $this->params->get('list_headtext','','text');
		
		$this->search_bar = $this->params->get('search_bar','','int');
		$this->hide_cat = $this->params->get('menu_category_id',0)>0 ? true : false;
		$this->hide_tag = (!empty($this->params->get('menu_tag',''))) ? true : false;
		
		$show_cats = $this->params->get('show_cats','1','int');
		$this->showcats = ($show_cats) ? $this->params->get('show_bcat','1','int') : 0;
		
		$show_tags = $this->params->get('show_tags','1','int');
		$this->showtags = ($show_tags) ? $this->params->get('show_ptags','1','int') : 0;
		
		$this->show_ctcol = $this->showcats + $this->showtags;
		
		$this->show_pic = $this->params->get('show_cpiccol','1','int');
		$this->show_sum = $this->params->get('show_csumcol','1','int');
		
		$this->xbfilmsStatus = Factory::getSession()->get('xbfilms_ok',false);
		$this->xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		//set metadata
		$document=$this->document;
		$document->setMetaData('title', Text::_('XBCULTURE_CHARS_CATALOGUE').': '.$document->title);
		$metadesc = $this->params->get('menu-meta_description');
		if (!empty($metadesc)) { $document->setDescription($metadesc); }
		$metakey = $this->params->get('menu-meta_keywords');
		if (!empty($metakey)) { $document->setMetaData('keywords', $metakey);}
		$metarobots = $this->params->get('robots');
		if (!empty($metarobots)) { $document->setMetaData('robots', $metarobots);}
		$document->setMetaData('generator', $this->params->get('def_generator'));
		$metaauthor = $this->params->get('def_author');
		if (!empty($metaauthor)) { $document->setMetaData('author',$metaauthor);}
		
		parent::display($tpl);
	} // end function display()
	
	
}