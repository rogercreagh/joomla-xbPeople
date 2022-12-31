<?php
/*******
 * @package xbPeople
 * @filesource admin/views/dashboard/view.html.php
 * @version 1.0.0.10 31st December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
//use Joomla\CMS\HTML\HTMLHelper;

class XbpeopleViewDashboard extends JViewLegacy
{
 //   protected $buttons;
	protected $films; //why?
	protected $categories;
 
	public function display($tpl = null) {
	    //get uninstall error message
	    $app = Factory::getApplication();
	    $err = $app->input->getString('err'.'');
	    if ($err!='') {
	        $app->enqueueMessage(urldecode($err),'Error');
	    }
	    
	    $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok');
		$this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
		$this->xbevents_ok = Factory::getSession()->get('xbevents_ok');
		
		$this->pcatStates = $this->get('PcatStates');
		$this->perStates = $this->get('PerStates');
		$this->charStates = $this->get('CharStates');
		$this->grpStates = $this->get('GroupStates');
		$this->totPeople = XbcultureHelper::getItemCnt('#__xbpersons');
		$this->totChars = XbcultureHelper::getItemCnt('#__xbcharacters');
		$this->totGroups = XbcultureHelper::getItemCnt('#__xbgroups');
		
		$this->bookPeople=($this->xbbooks_ok) ? $this->get('BookPeople') : 'n/a';
		$this->eventPeople=($this->xbevents_ok) ? $this->get('EventPeople') : 'n/a';
		$this->filmPeople=($this->xbfilms_ok) ? $this->get('FilmPeople') : 'n/a';
		$this->bookChars=($this->xbbooks_ok) ? $this->get('BookChars') : 'n/a';
		$this->eventChars=($this->xbevents_ok) ? $this->get('EventChars') : 'n/a';
		$this->filmChars=($this->xbfilms_ok) ? $this->get('FilmChars') : 'n/a';
		$this->bookGroups=($this->xbbooks_ok) ? $this->get('BookGroups') : 'n/a';
		$this->filmGroups=($this->xbfilms_ok) ? $this->get('FilmGroups') : 'n/a';
		$this->eventGroups=($this->xbevents_ok) ? $this->get('EventGroups') : 'n/a';
				
		$this->people = $this->get('RoleCnts');
		
		$this->cats = $this->get('Cats');
		$this->pcats = $this->get('PeopleCats');
		$this->tags = $this->get('Tagcnts');
		
		$this->xmldata = Installer::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/xbpeople.xml');
		$this->client = $this->get('Client');
		
		$params = ComponentHelper::getParams('com_xbpeople');

		$this->savedata = $params->get('savedata',0);
		
		$this->show_cat = $params->get('show_cats',1);
		$this->show_pcat = $params->get('show_pcat',1);
		$this->show_ccat = $params->get('show_ccat',1);
		$this->show_gcat = $params->get('show_gcat',1);
		
		$this->show_tags = $params->get('show_tags',1);
		$this->show_ptags = $params->get('show_ptags',1);
		$this->show_ctags = $params->get('show_ctags',1);
		$this->show_gtags = $params->get('show_gtags',1);
		
		$this->show_search = $params->get('search_bar');
		
		$this->hide_empty = $params->get('hide_empty');
		
		$this->portraits = $params->get('portrait_path');
		$this->show_people_portraits = $params->get('show_ppiccol');
		$this->show_person_portrait = $params->get('show_pimage');
		
		XbpeopleHelper::addSubmenu('dashboard');
		
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
        
        $clink='index.php?option=com_categories&view=categories&task=category.edit&extension=com_xbpeople&id=';
//         $this->pcatlist = '<ul style="list-style-type: none;">';
//         foreach ($this->pcats as $key=>$value) {
//             $this->pcatlist .= '<li>';
//             if ($value['level']>1) {
//                 $this->pcatlist .= '&boxur;'.str_repeat('&boxh;', $value['level']-1).'&nbsp;';                
//             }
//             $lbl = $value['published']==1 ? 'label-success' : '';
//             $this->pcatlist .= '<a class="label '.$lbl.'" href="'.$clink.$value['id'].'">'.$value['title'].'</a>';
//             $this->pcatlist .= '&nbsp;<span class="badge percnt">'.$value['percnt'].'</span> ';
//             $this->pcatlist .= '<span class="badge grpcnt">'.$value['grpcnt'].'</span> ';
//             $this->pcatlist .= '<span class="badge chcnt">'.$value['chrcnt'].'</span>';
//             $this->pcatlist .= '</li>';
//         }
//         $this->pcatlist .= '</ul>';
        
//         $this->pcatlist = '';
//         foreach ($this->pcats as $key=>$value) {
//             $this->pcatlist .= '<div class="pull-left" style="mix-width:170px;">';
//             if ($value['level']>1) {
//                 $this->pcatlist .= '&boxur;'.str_repeat('&boxh;', $value['level']-1).'&nbsp;';
//             }
//             $lbl = $value['published']==1 ? 'label-success' : '';
//             $this->pcatlist .= '<a class="label '.$lbl.'" href="'.$clink.$value['id'].'">'.$value['title'].'</a>';
//             $this->pcatlist .= '</div>';
//             $this->pcatlist .= '<div class="pull-left">';
//             $this->pcatlist .= '<span class="badge percnt">'.$value['percnt'].'</span> ';
//             $this->pcatlist .= '<span class="badge grpcnt">'.$value['grpcnt'].'</span> ';
//             $this->pcatlist .= '<span class="badge chcnt">'.$value['chrcnt'].'</span>';
//             $this->pcatlist .= '</div>';
//             $this->pcatlist .= '<div class="clearfix"></div>';
//         }
        
//         $tlink='index.php?option=com_xbfilms&view=tag&id=';
//         $this->taglist = '<ul class="inline">';
//         foreach ($this->tags['tags'] as $key=>$value) {
//         	//       	$result[$key] = $t->tagcnt;
//             $this->taglist .= '<li><a class="label label-info" href="'.$tlink.$value['id'].'">'.$key.'</a>&nbsp;(<i>'.$value['tbcnt'].':'.$value['trcnt'].':'.$value['tpcnt'].':'.$value['tccnt'].')</i></li> ';
//         }
//         $this->taglist .= '</ul>';
        //        $result['taglist'] = trim($result['taglist'],', ');
        
        
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
        // Set the document
        $this->setDocument();
	}

    protected function addToolbar() {
 //   	$user 	=Factory::getUser();
//    	$canDo = new JObject;
//    	$assetName = 'com_xbpeople';
//    	$level = 'component';
//    	$actions = JAccess::getActions('com_xbpeople', $level);
 //   	foreach ($actions as $action) {
//    		$canDo->set($action->name, $user->authorise($action->name, $assetName));
//    	}
    	$canDo = ContentHelper::getActions('com_xbpeople', 'component');
    	
        ToolbarHelper::title(Text::_( 'COM_XBPEOPLE' ).': '.Text::_('XBCULTURE_DASHBOARD'),'info-2');
        
        ToolbarHelper::custom('dashboard.books', 'stack', '', 'xbBooks', false) ;
        ToolbarHelper::custom('dashboard.films', 'screen', '', 'xbFilms', false) ;
        ToolbarHelper::custom('dashboard.live', 'music', '', 'xbEvents', false) ;
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbpeople');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbpeople/doc?tmpl=component#admin-panel' );
    }
    
    protected function setDocument() {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('XBPEOPLE_ADMIN_DASHBOARD'));
    }
    
}
