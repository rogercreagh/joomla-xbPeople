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

        echo '<h3>xbPeople component installed</h3>';
        echo '<p>Version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'</p>';
        echo '<p>For help and information see <a href="http://crosborne.co.uk/xbpeopledoc" target="_blank">
            www.crosborne.co.uk/xbpeopledoc</a></p>';
        echo '</div>';
    }
    
    function uninstall($parent)
    {
      	$db = Factory::getDbo();
    	// prevent categories being deleted
    	$db->setQuery(
    		$db->getQuery(true)
    			->update('#__categories')
    			->set('extension='.$db->q('!com_xbpeople!'))
    			->where('extension='.$db->q('com_xbpeople'))
		)
    		->execute();
        $cnt = $db->getAffectedRows(); 
   	    $message = $cnt.' xbPeople categories renamed as !<i>name</i>!, run cpanel RestorePeopleCats in xbBooks/Films cpanel to recover them';
        Factory::getApplication()->enqueueMessage($message,'Info');
        echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
            border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';   	
    	echo '<p>Uninstalling xbPeople component v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'.</p>';
    	echo '</div>';
/***
    	    //test if xbbooks or xbfilms installed
    	$showcats='default';
    	$db = Factory::getDBO();
    	$db->setQuery('SELECT extension_id FROM #__extensions WHERE element = '.$db->quote('com_xbfilms'));
    	$eid = $db->loadResult();
    	if ($eid) {
    		$showcats = $parent->getParam('show_cats',$eid);
    	}
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
    	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';   	
    	echo '<p>The xbPeople component has been updated to version ' . $parent->get('manifest')->version .' '
    			.$parent->get('manifest')->creationDate. '</p>';
    	echo '<p>For full changelogs visit <a href="http://crosborne.co.uk/xbPeople#changelog" target="_blank">
            www.crosborne.co.uk/xbPeople#changelog</a></p>';
        echo '</div>';
    }
    
    function postflight($type, $parent) {
    	$message = 'Postflight messages: <br />';
    	if ($type=='install') {
        	//create portrait folder
        	if (!file_exists(JPATH_ROOT.'/images/xbpeople')) {
         		mkdir(JPATH_ROOT.'/images/xbpeople',0775);
         		$message .= 'Portrait folder created (/images/xbpeople/).<br />';
        	} else{
         		$message .= '"/images/xbpeople/" already exists.<br />';
         	}
            $db = Factory::getDbo();
            $app = Factory::getApplication();
            $prefix = $app->get('dbprefix');
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
         	$message .= $cnt.' xbPeople categories restored';
/****          	
         	$db = JFactory::getDbo();   		
			$qry = $db->getQuery(true);
         	$qry->update('#__categories')
         		>set('extension=SUBSTR(extension, 2, CHAR_LENGTH(extension) -2)')
         		>where('extension='.$db->q('!com_xbpeople!'));
         	$db->setQuery($qry); 
         	try {
	    		$db->execute();
	    		$cnt = $db->getAffectedRows();
         	} catch (Exception $e) {
         		throw new Exception($e->getMessage());
         	}
         	$message = $cnt.' xbPeople categories restored';
         	JFactory::getApplication()->enqueueMessage($message,'Info');
         	
         	// set up com_xbpeople categories. Need to test if they already exist. Uncategorised-P and Imported-P. For persons and characters

        	$message='PostInstall Actions:<br />';
        	$db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id')->from($db->quoteName('#__categories'))->where($db->quoteName('alias')." = ".$db->quote('uncategorised'));
            $query->where($db->quoteName('extension')." = ".$db->quote('com_xbpeople'));
            $db->setQuery($query);
            $id =0;
            $res = $db->loadResult();
            if (!($res>0)) {
                $category_data['extension'] = 'com_xbpeople';
                $category_data['title'] = 'Uncat.People';
                $category_data['alias'] = 'uncategorised';
                $category_data['description'] = 'Default category for xbPeople and Characters not otherwise assigned';
                if(!$category_model->save($category_data)){
                    $message .= '[Error creating Uncategorised category for people: '.$category_model->getError().'] ';
                }else{
                    $message .= '"Uncat.People" (id='. $category_model->getItem()->id.') - OK ';
                }   
            } else{
            	$message .= '"Uncat.People" already exists. ';
            }
            $query->clear();
            $query->select('id')->from($db->quoteName('#__categories'))->where($db->quoteName('alias')." = ".$db->quote('imported'));
            $query->where($db->quoteName('extension')." = ".$db->quote('com_xbpeople'));
            $db->setQuery($query);
            $res =0;
            $res = $db->loadResult();
            if (!($res>0)) {
                $category_data['extension'] = 'com_xbpeople';
                $category_data['title'] = 'Import.People';
                $category_data['alias'] = 'imported';
                $category_data['description'] = 'Default category for imported xbPeople and Characters';
                if(!$category_model->save($category_data)){
                    $message .= '[Error creating Imported category for people: '.$category_model->getError().'] ';
                }else{
                    $message .= '"Import.People" (id='. $category_model->getItem()->id.') - OK ';
                }
            } else{
            	$message .= '"Import.People" already exists. ';
            }           
****/                       
/*********************/
            //set up indicies for characters and persons tables - can't be done in install.sql as they may already exists
            //mysql doesn't support create index if not exists. 
            $message .= '<br />Testing people & character indicies ';
            
            $querystr = 'ALTER TABLE `'.$prefix.'xbpersons` ADD INDEX `personaliasindex` (`alias`)';
            $err=false;
            try {
            	$db->setQuery($querystr);
            	$db->execute();            	
            } catch (Exception $e) {
            	if($e->getCode() == 1061) {
	           		$message .= '- person alias index exists OK';
	           	} else {
	          		$message .= '- ERROR creating personaliasindex: '.$e->getCode().' '.$e->getMessage();
	           	}
	           	$err = true;
            }
            $message .= '<br />';
            if (!$err) {
            	$message .= '- personaliasindex created OK.';
            }
            $querystr = 'ALTER TABLE `'.$prefix.'xbcharacters` ADD INDEX `characteraliasindex` (`alias`)';
            $err=false;
            try {
            	$db->setQuery($querystr);
            	$db->execute();
            } catch (Exception $e) {
            	if($e->getCode() == 1061) {
            		$message .= '- character alias index exists OK';
            	} else {
            		$message .= '- ERROR creating characteraliasindex: '.$e->getCode().' '.$e->getMessage();
            	}
            	$err = true;
            }
            if (!$err) {
            	$message .= '- characteraliasindex created OK.';
            }
           /**********************/     

           Factory::getApplication()->enqueueMessage($message,'Info');  
        }
        
    }
}

