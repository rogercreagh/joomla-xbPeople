<?php
/*******
 * @package xbPeople
 * @filesource script.xbpeople.php
 * @version 0.9.6.f 9th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Table\Table;

class com_xbpeopleInstallerScript 
{
    protected $jminver = '3.10';
    protected $jmaxver = '4.0';
    protected $extension = 'com_xbpeople';
    protected $ver = 'v0';
    protected $date = '';
    
    function preflight($type, $parent) {
        $jversion = new Version();
        $jverthis = $jversion->getShortVersion();       
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbFilms requires Joomla version greater than '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
        $message='';
        if ($type=='update') {
        	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbpeople/xbpeople.xml'));
        	$this->ver = $componentXML['version'];
        	$this->date = $componentXML['creationDate'];
        	$message = 'Updating xbPeople component from '.$componentXML['version'].' '.$componentXML['creationDate'];
        	$message .= ' to '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
        }
        if ($message!='') { Factory::getApplication()->enqueueMessage($message,'');}
    }   
    
    function install($parent) {
    }
    
    function uninstall($parent) {   	
    	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbpeople/xbpeople.xml'));
    	$message = 'Uninstalling xbPeople component v.'.$componentXML['version'].' '.$componentXML['creationDate'];
		Factory::getApplication()->enqueueMessage($message,'Info');
		
      	// prevent categories being deleted
		$db = Factory::getDbo();
		$db->setQuery(
    		$db->getQuery(true)
    			->update('#__categories')
    			->set('extension='.$db->q('!com_xbpeople!'))
    			->where('extension='.$db->q('com_xbpeople'))
		)
    		->execute();
        $cnt = $db->getAffectedRows(); 
        if ($cnt>0) {
        	$message .= '<br />'.$cnt.' xbPeople categories extension renamed as "<b>!</b>com_xbpeople<b>!</b>". They will be recovered on reinstall.';
        }
        $message .= '<br /><b>NB</b> xbPeople uninstall: People and Characters data tables, and the images/xbpeople folder have <b>not</b> been deleted.';
   	    Factory::getApplication()->enqueueMessage($message,'Info');
   	    $message = '';
   	    $db = Factory::getDBO();
   	    $db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbbooks'));
   	    $xbbooks_in = $db->loadResult();
   	    $db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbfilms'));
   	    $xbfilms_in = $db->loadResult();
   	    if ($xbfilms_in || $xbbooks_in) {
   	    	$message = '<b>xbPeople</b> has been uninstalled but ';
   	    	$message .= $xbbooks_in ? 'xbBooks' : '';
   	    	$message .= ($xbbooks_in && $xbfilms_in) ? ' and ':''; 
   	    	$message .= $xbfilms_in ? 'xbFilms':'';
   	    	$message .= ' is still installed. No xbPeople data has been deleted, but if you wish to continue using xbBooks/xbFilms you must reinstall xbPeople.';
   	    	$message .= '<br />To install it now copy this url <b> https://www.crosborne.uk/downloads?download=11 </b>, and paste the link into the box on the ';
   	    	$message .= '<a href="index.php?option=com_installer&view=install#url">Install from URL page</a>, ';
   	    	$message .= 'or <a href="https://www.crosborne.uk/downloads?download=11">download here</a> and drag and drop onto the install box on this page.';
   	    	Factory::getApplication()->enqueueMessage($message,'Error');
   	    }
   	    // set session that xbpeople no longer exists
   	    $oldval = Factory::getSession()->set('xbpeople_ok', false);
    }
    
    function update($parent)
    {
    	$message = '<br />Visit the <a href="index.php?option=com_xbpeople&view=cpanel" class="btn btn-small btn-info">';
    	$message .= 'xbPeople Dashboard</a> page for overview of status.</p>';
    	$message .= '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbpeople/changelog" target="_blank">
            www.crosborne.co.uk/xbpeople/changelog</a></p>';
    	Factory::getApplication()->enqueueMessage($message,'Message');
    }
    
    function postflight($type, $parent) {
    	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbpeople/xbpeople.xml'));
    	if ($type=='install') {
    		$message = 'xbPeople '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
    		
    		//create xbpeople image folder
        	if (!file_exists(JPATH_ROOT.'/images/xbpeople')) {
         		mkdir(JPATH_ROOT.'/images/xbpeople',0775);
         		$message .= 'Portrait folder created (/images/xbpeople/).<br />';
        	} else{
         		$message .= '"/images/xbpeople/" already exists.<br />';
         	}
            $db = Factory::getDbo();
        	// Recover categories if they exist assigned to extension !com_xbpeople!
			$qry = $db->getQuery(true);
         	$qry->update('#__categories')
         	  ->set('extension='.$db->q('com_xbpeople'))
         		->where('extension='.$db->q('!com_xbpeople!'));
         	$db->setQuery($qry); 
         	try {
	    		$db->execute();
	    		$cnt = $db->getAffectedRows();
         	} catch (Exception $e) {
         		throw new Exception($e->getMessage());
         	}
         	$message .= $cnt.' existing xbPeople categories restored. ';
         	
         	// create default categories using category table
         	$cats = array(
         			array("title"=>"Uncat.People","alias"=>"uncategorised","desc"=>"default fallback category for all xbPeople items"),
         			array("title"=>"Import.People","alias"=>"imported","desc"=>"default category for xbPeople imported data"));
         	$message .= $this->createCategory($cats);
         	
         	Factory::getApplication()->enqueueMessage($message,'Info');
         	
         	
            //set up indicies for characters and persons tables - can't be done in install.sql as they may already exists
            //mysql doesn't support create index if not exists. 
            $message .= 'Checking indicies... ';
            
            $app = Factory::getApplication();
            $prefix = $app->get('dbprefix');
            $querystr = 'ALTER TABLE '.$prefix.'xbpersons ADD INDEX personaliasindex (alias)';
            $err=false;
            try {
            	$db->setQuery($querystr);
            	$db->execute();            	
            } catch (Exception $e) {
            	if($e->getCode() == 1061) {
	           		$message .= '- person alias index already exists. ';
	           	} else {
	          		$message .= '<br />[ERROR creating personaliasindex: '.$e->getCode().' '.$e->getMessage().']<br />';
	           	}
	           	$err = true;
            }
            if (!$err) {
            	$message .= '- person alias index created. ';
            }
            $querystr = 'ALTER TABLE '.$prefix.'xbcharacters ADD INDEX characteraliasindex (alias)';
            $err=false;
            try {
            	$db->setQuery($querystr);
            	$db->execute();
            } catch (Exception $e) {
            	if($e->getCode() == 1061) {
            		$message .= '- character alias index already exists';
            	} else {
            		$message .= '<br />[ERROR creating characteraliasindex: '.$e->getCode().' '.$e->getMessage().']<br />';
            	}
            	$err = true;
            }
            if (!$err) {
            	$message .= '- character alias index created.';
            }
            //set session that we are installed
            $oldval = Factory::getSession()->set('xbpeople_ok', true);           
	        Factory::getApplication()->enqueueMessage($message,'Info');  
           /**********************/     
            echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
            
            echo '<h3>xbPeople Component</h3>';
            echo '<p>Version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'</p>';
            echo '<p>xbPeople is a minimal component designed to supplement xbCulture components. It will install the people and character data tables if they don&quot;t exist,';
            echo 'and recover any previously saved Categories for people, or create default "Uncat.People" and "Import.People" categories.</p>';
            echo '<p><i>Check the Dashboard for an overview</i>&nbsp;&nbsp;';
            echo '<a href="index.php?option=com_xbpeople&view=cpanel" class="btn btn-small btn-success">xbPeople Dashboard</a></p>';
            echo '</div>';
        
    	}
    }

    public function createCategory($cats) {
    	$message = 'Creating '.$this->extension.' categories. ';
    	foreach ($cats as $cat) {
    		$db = Factory::getDBO();
    		$query = $db->getQuery(true);
    		$query->select('id')->from($db->quoteName('#__categories'))
    		->where($db->quoteName('title')." = ".$db->quote($cat['title']))
    		->where($db->quoteName('extension')." = ".$db->quote('com_xbpeople'));
    		$db->setQuery($query);
    		if ($db->loadResult()>0) {
    			$message .= '"'.$cat['title'].' already exists<br /> ';
    		} else {
    			$category = Table::getInstance('Category');
    			$category->extension = $this->extension;
    			$category->title = $cat['title'];
    			if (array_key_exists('alias', $cat)) { $category->alias = $cat['alias']; }
    			$category->description = $cat['desc'];
    			$category->published = 1;
    			$category->access = 1;
    			$category->params = '{"category_layout":"","image":"","image_alt":""}';
    			$category->metadata = '{"page_title":"","author":"","robots":""}';
    			$category->language = '*';
    			// Set the location in the tree
    			$category->setLocation(1, 'last-child');
    			// Check to make sure our data is valid
    			if ($category->check()) {
    				if ($category->store(true)) {
    					// Build the path for our category
    					$category->rebuildPath($category->id);
    					$message .= $cat['title'].' id:'.$category->id.' created ok. ';
    				} else {
    					throw new Exception(500, $category->getError());
    					//return '';
    				}
    			} else {
    				throw new Exception(500, $category->getError());
    				//return '';
    			}
    		}
    	}
    	return $message;
    }
    
}

