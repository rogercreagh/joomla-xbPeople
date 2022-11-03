<?php
/*******
 * @package xbPeople
 * @filesource script.xbpeople.php
 * @version 0.9.9.9 3rd November 2022
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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

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
        $pkguninstall = Factory::getSession()->get('xbpkg');
        if (!$pkguninstall) {
           // this is not a package uninstall so we need to check if xbfilms or xbbooks or xblive are still here
            $db = Factory::getDBO();
            $db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbfilms').' OR element = '.$db->quote('com_xbbooks').' OR element = '.$db->quote('com_xbevents'));
            $res = $db->loadResult();
            if ($res) {
                $message = 'At least one xbCulture component is still installed. xbPeople component must be uninstalled after xbBooks, xbFilms and xbEvents.';
                $targ = Uri::base().'index.php?option=com_xbpeople&view=dashboard&err='.urlencode($message);
                header("Location: ".$targ);
                exit();
            }
        }
        Factory::getSession()->clear('xbpkg');
        //ok to proceed
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbpeople/xbpeople.xml'));
    	$message = 'Uninstalling xbPeople component v.'.$componentXML['version'].' '.$componentXML['creationDate'].' ';
    	
    	//are we also clearing data?
    	$savedata = ComponentHelper::getParams('com_xbpeople')->get('savedata',0);
        if ($savedata == 0) {
            if ($this->uninstalldata()) {
                $message .= ' ... xbPeople data tables deleted';
            }           
            $dest='/images/xbpeople';
            if (JFolder::exists(JPATH_ROOT.$dest)) {
                if (JFolder::delete(JPATH_ROOT.$dest)){
                    $message .= ' ... images/xbpeople folder deleted';
                } else {
                    $err = 'Problem deleting xbPeople images folder "/images/xbpeople" - please check in Media manager';
                    Factory::getApplication()->enqueueMessage($err,'Error');
                }
            }
        } else {
            $message .= ' xbPeople data tables and images folder have NOT been deleted.';
          	// allow categories to be recovered with same id
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
            	$message .= '<br />'.$cnt.' xbPeople categories renamed as "<b>!</b>com_xbpeople<b>!</b>". They will be recovered on reinstall with original ids.';
            }            
        }
              
		Factory::getApplication()->enqueueMessage($message,'Info');
		
   	    // set session that xbpeople no longer exists
   	    $oldval = Factory::getSession()->clear('xbpeople_ok');
    }
    
    function update($parent) {
    	$message = '<br />Visit the <a href="index.php?option=com_xbpeople&view=dashboard" class="btn btn-small btn-info">';
    	$message .= 'xbPeople Dashboard</a> page for overview of status.</p>';
    	$message .= '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbpeople/changelog" target="_blank">
            www.crosborne.co.uk/xbpeople/changelog</a></p>';
    	Factory::getApplication()->enqueueMessage($message,'Message');
    }
    
    function postflight($type, $parent) {
    	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbpeople/xbpeople.xml'));
    	if ($type=='install') {
    		$message = '<b>xbPeople '.$componentXML['version'].' '.$componentXML['creationDate'].'</b><br />';
    		
    		//create xbpeople image folder
        	if (!file_exists(JPATH_ROOT.'/images/xbpeople')) {
         		mkdir(JPATH_ROOT.'/images/xbpeople',0775);
         		$message .= 'Portrait folder created (/images/xbpeople/).<br />';
        	} else{
         		$message .= '"/images/xbpeople/" already exists.<br />';
         	}

        	// Recover categories if they exist assigned to extension !com_xbpeople!
            $db = Factory::getDbo();
			$qry = $db->getQuery(true);
         	$qry->update('#__categories')
         	  ->set('extension='.$db->q('com_xbpeople'))
         		->where('extension='.$db->q('!com_xbpeople!'));
         	$db->setQuery($qry); 
         	try {
	    		$db->execute();
	    		$cnt = $db->getAffectedRows();
         	} catch (Exception $e) {
         	    Factory::getApplication()->enqueueMessage($e->getMessage(),'Error');
         	}
         	$message .= $cnt.' existing xbPeople categories restored. ';
         	
         	// create default categories using category table
         	$cats = array(
                array("title"=>"Uncategorised","alias"=>"uncategorised","desc"=>"default fallback category for all xbPeople items"),
                array("title"=>"Imported","alias"=>"imported","desc"=>"default category for xbPeople imported data"),
                array("title"=>"People","alias"=>"people","desc"=>"default category for People"),
         	    array("title"=>"Chars","alias"=>"chars","desc"=>"default category for Characters"));
         	$message .= $this->createCategory($cats);
         	
            $app = Factory::getApplication();
            $app->enqueueMessage($message,'Info');
         	
         	
            //set up indicies for characters and persons tables - can't be done in install.sql as they may already exists
            //mysql doesn't support create index if not exists. 
            $message = 'Checking indicies... ';
            
            $prefix = $app->get('dbprefix');
            $querystr = 'ALTER TABLE '.$prefix.'xbpersons ADD INDEX personaliasindex (alias)';
            $err=false;
            try {
            	$db->setQuery($querystr);
            	$db->execute();            	
            } catch (Exception $e) {
            	if($e->getCode() == 1061) {
	           		$message .= ' person alias index already exists. ';
	           		$err = true;
            	} else {
	          		$message .= '[ERROR creating personaliasindex: '.$e->getCode().' '.$e->getMessage().']';
	          		$app->enqueueMessage($message, 'Error');
	          		$message = 'Checking indicies... ';
	          		$err = true;
	           	}	           	
            }
            if (!$err) {
            	$message .= ' person alias index created. ';
            }
            $querystr = 'ALTER TABLE '.$prefix.'xbcharacters ADD INDEX characteraliasindex (alias)';
            $err=false;
            try {
            	$db->setQuery($querystr);
            	$db->execute();
            } catch (Exception $e) {
            	if($e->getCode() == 1061) {
            		$message .= '... character alias index already exists';
            		$err = true;
            	} else {
            		$message .= '<br />[ERROR creating characteraliasindex: '.$e->getCode().' '.$e->getMessage().']<br />';
            		$app->enqueueMessage($message, 'Error');
            		$message = '';
            		$err = true;
            	}
            }
            if (!$err) {
            	$message .= '... character alias index created.';
            }
            //set session that we are installed
            $oldval = Factory::getSession()->set('xbpeople_ok', true);           
            $app->enqueueMessage($message,'Info');  
           /**********************/     
            echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
            
            echo '<h3>xbPeople Component</h3>';
            echo '<p>Version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'</p>';
            echo '<p>xbPeople is a minimal component designed to supplement xbCulture components. It will install the people and character data tables if they don&quot;t exist,';
            echo 'and recover any previously saved Categories for people, or create default "Uncat.People" and "Import.People" categories.</p>';
            echo '<p><i>Check the Dashboard for an overview</i>&nbsp;&nbsp;';
            echo '<a href="index.php?option=com_xbpeople&view=dashboard" class="btn btn-small btn-success">xbPeople Dashboard</a></p>';
            echo '</div>';
        
    	}
    }

    protected function createCategory($cats) {
    	$message = 'Creating '.$this->extension.' default categories... ';
    	foreach ($cats as $cat) {
    		$db = Factory::getDBO();
    		$query = $db->getQuery(true);
    		$query->select('id')->from($db->quoteName('#__categories'))
    		->where($db->quoteName('title')." = ".$db->quote($cat['title']))
    		->where($db->quoteName('extension')." = ".$db->quote('com_xbpeople'));
    		$db->setQuery($query);
    		if ($db->loadResult()>0) {
    			// $message .= '"'.$cat['title'].' already exists.  ';
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
    
    protected function uninstalldata() {
        $message = 'this would uninstall the xbpeople data';
        $db = Factory::getDBO();
        $db->setQuery('DROP TABLE IF EXISTS `#__xbpersons`, `#__xbcharacters`');
        $res = $db->execute();
        if ($res === false) {
            $message = 'Error deleting xbPeople tables, please check manually';
            Factory::getApplication()->enqueueMessage($message,'Error');
            return false;
        }
        return true;
    }
}

