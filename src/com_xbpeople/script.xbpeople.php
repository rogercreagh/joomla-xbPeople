<?php
/*******
 * @package xbPeople
 * @filesource script.xbpeople.php
 * @version 0.1.0 9th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class com_xbpeopleInstallerScript 
{
	protected $ver;
	
	public function preflight($route, $installer)
	{
    }
    
    function install($parent)
    {
    	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';

        echo '<h3>Installing xbPeople component...</h3>';
        echo '<p>Version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'</p>';
        echo '<p>For help and information see <a href="http://crosborne.co.uk/xbpeopledoc" target="_blank">
            www.crosborne.co.uk/xbpeopledoc</a></p>';
        echo '</div>';
    }
    
    function uninstall($parent)
    {   	
		$message = 'Uninstalling xbPeople component v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
		$message .= '<br /> --------------------------------------- ';
		Factory::getApplication()->enqueueMessage($message,'Info');
      	$db = Factory::getDbo();
      	$db->setQuery('SELECT extension_id FROM #__extensions
			WHERE element = '.$db->quote('com_xbfilms').' OR element = '.$db->quote('com_xbbooks'));
      	$eid = $db->loadResult();
      	if ($eid) {
      		
      	}
      	// prevent categories being deleted
    	$db->setQuery(
    		$db->getQuery(true)
    			->update('#__categories')
    			->set('extension='.$db->q('!com_xbpeople!'))
    			->where('extension='.$db->q('com_xbpeople'))
		)
    		->execute();
        $cnt = $db->getAffectedRows(); 
   	    $message = $cnt.' xbPeople categories renamed as "<b>!</b><i>name</i><b>!</b>". They ill be recovered on reinstall, or delete manually.';
        $message .= '<br /><b>NB</b> People and Characters data tables, and imgaes/xbpeople folder have not been deleted.';
        $message .= '<br /> --------------------------------------- ';
   	    Factory::getApplication()->enqueueMessage($message,'Warn');
/***
    	    //test if xbbooks or xbfilms installed
    	$showcats='default';
    	$db = Factory::getDBO();
    	//if they are then 
    		//message to effect that categories need restoring
	     	$db = JFactory::getDbo();
	    	// Preserve categories - they need restoring by install script and/or films/books controller
	    	$db->setQuery(
	    	$db->getQuery(true)
	    		->update('#__categories')
	    		->set('extension=CONCAT('.$db->q('!').',extension,'.$db->q('!').')')
	    		->where('extension='.$db->q('com_xbpeople')))
	    		->execute();
	    //else we can let them be deleted 
    	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';   	
    	echo $shocats.'<p>The xbPeople component version '.$this->ver.' has been uninstalled.</p>';
    	echo '</div>';
    	return false;
    	***/
    }
    
    function update($parent)
    {
    	$message = 'Updating xbPeople component to v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
    	$message .= '<br /> --------------------------------------- ';
    	Factory::getApplication()->enqueueMessage($message,'Info');
    	
//     	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
// 	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';   	
//     	echo '<p>The xbPeople component has been updated to version ' . $parent->get('manifest')->version .' '
//     			.$parent->get('manifest')->creationDate. '</p>';
//     	echo '<p>For full changelogs visit <a href="http://crosborne.co.uk/xbPeople#changelog" target="_blank">
//             www.crosborne.co.uk/xbPeople#changelog</a></p>';
//         echo '</div>';
    }
    
    function postflight($type, $parent) {
    	$message = 'Postflight messages ('.$type.') '.$parent->get('manifest')->name.' : <br />';
    	if ($type=='install') {
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
           /**********************/     
        }
        $message .= '<br /> --------------------------------------- ';
        Factory::getApplication()->enqueueMessage($message,'Info');  
        
    }
}

