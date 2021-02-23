<?php
/*******
 * @package xbPeople
 * @filesource script.xbpeople.php
 * @version 0.2.0 15th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class com_xbpeopleInstallerScript 
{
    protected $jminver = '3.9';
    protected $jmaxver = '4.0';
    
    function preflight($type, $parent)
    {
        $jversion = new JVersion();
        $jverthis = $jversion->getShortVersion();       
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbFilms requires Joomla version greater than '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
    }   
    
    function install($parent)
    {
    }
    
    function uninstall($parent)
    {   	
		$message = 'Uninstalling xbPeople component v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
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
   	    $message = $cnt.' xbPeople categories renamed as "<b>!</b> <i>name</i> <b>!</b>". They will be recovered on reinstall.';
        $message .= '<br /><b>NB</b> xbPeople uninstall: People and Characters data tables, and imgaes/xbpeople folder have <b>not</b> been deleted.';
   	    Factory::getApplication()->enqueueMessage($message,'Warning');
   	    // set session that xbpeople no longer exists
   	    $oldval = Factory::getSession()->set('xbpeople_ok', false);
    }
    
    function update($parent)
    {
    	$message = 'Updating xbPeople component to v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
    	Factory::getApplication()->enqueueMessage($message,'Info');
    }
    
    function postflight($type, $parent) {
    	if ($type=='install') {
	    	$message = $parent->get('manifest')->name.' ('.$type.') : <br />';
        	//create xbpeople image folder
        	if (!file_exists(JPATH_ROOT.'/images/xbpeople')) {
         		mkdir(JPATH_ROOT.'/images/xbpeople',0775);
         		$message .= 'Portrait folder created (/images/xbpeople/).<br />';
        	} else{
         		$message .= '"/images/xbpeople/" already exists.<br />';
         	}
            $db = Factory::getDbo();
        // Recover categories if they exist
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
         	
         	// set up com_xbpeople categories. Need to test if they already exist. Uncategorised-P and Imported-P. For persons and characters
         	$category_data['id'] = 0;
         	$category_data['parent_id'] = 0;
         	$category_data['extension'] = 'com_xbpeople';
         	$category_data['published'] = 1;
         	$category_data['language'] = '*';
         	$category_data['params'] = array('category_layout' => '','image' => '');
         	$category_data['metadata'] = array('author' => '','robots' => '');
         	
         	$basePath = JPATH_ADMINISTRATOR.'/components/com_categories';
         	require_once $basePath.'/models/category.php';
         	$config  = array('table_path' => $basePath.'/tables');
         	$category_model = new CategoriesModelCategory($config);
         	
         	$query = $db->getQuery(true);
            $query->select('id')->from($db->quoteName('#__categories'))
            	->where($db->quoteName('alias')." = ".$db->quote('uncategorised'))
            	->where($db->quoteName('extension').' = '.$db->quote('com_xbpeople'));
            $db->setQuery($query);
            if ($db->loadResult()>0) {
            	$message .= 'Category "Uncat.People" already exists. ';
            } else{
                $category_data['title'] = 'Uncat.People';
                $category_data['alias'] = 'uncategorised';
                $category_data['description'] = 'Default category for xbPeople and Characters not otherwise assigned';
                if(!$category_model->save($category_data)){
                    $message .= '<br />[Error creating Uncategorised category for people: '.$category_model->getError().']<br /> ';
                }else{
                    $message .= 'Category "Uncat.People" (id='. $category_model->getItem()->id.') created. ';
                }   
            }
            $query->clear();
            $query->select('id')->from($db->quoteName('#__categories'))->where($db->quoteName('alias')." = ".$db->quote('imported'));
            $query->where($db->quoteName('extension')." = ".$db->quote('com_xbpeople'));
            $db->setQuery($query);
            if ($db->loadResult()>0) {
            	$message .= 'Category "Import.People" already exists. ';
            } else{
            	$category_data['title'] = 'Import.People';
                $category_data['alias'] = 'imported';
                $category_data['description'] = 'Default category for imported xbPeople and Characters';
                if(!$category_model->save($category_data)){
                    $message .= '<br />[Error creating Imported category for people: '.$category_model->getError().']<br />';
                }else{
                    $message .= 'Category "Import.People" (id='. $category_model->getItem()->id.') created.<br />';
                }
            }           
            //set up indicies for characters and persons tables - can't be done in install.sql as they may already exists
            //mysql doesn't support create index if not exists. 
            $message .= 'Checking indicies... ';
            
            $app = Factory::getApplication();
            $prefix = $app->get('dbprefix');
            $querystr = 'ALTER TABLE `'.$prefix.'xbpersons` ADD INDEX `personaliasindex` (`alias`)';
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
            $querystr = 'ALTER TABLE `'.$prefix.'xbcharacters` ADD INDEX `characteraliasindex` (`alias`)';
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
            echo '<p>xbPeople is a minimal component designed to supplement xbFilms, xbGigs and xbBooks. It will install the people and character data tables if they don&quot;t exist,';
            echo 'and recover any previously saved Categories for people, or create default "Uncategorised" and "Imported" categories.</p>';
            echo '<br />It does include two admin views - a list view of all people and and edit form for an individual person if required. These are also available directly in the main components.';
            echo 'All other functionality resides in the main components (including the ability to create and assign fiction characters to works).<p>';
            echo 'The main benefit of xbPeople is that it allows people to be assigned to categories irrespective of the component they are viewed in. ';
            echo 'If you are not bothered about using categories for people, but are simply using tags instead, then you can dispense with xbPeople.';
            echo '</div>';
        
    	}
    }
}

