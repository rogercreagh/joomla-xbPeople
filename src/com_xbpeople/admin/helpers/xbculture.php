<?php
/*******
 * @package xbPeople for all xbCulture extensions
 * @filesource admin/helpers/xbculture.php
 * @version 1.0.2.1 8th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Filter\OutputFilter;
// use Joomla\CMS\Application\ApplicationHelper;

class XbcultureHelper extends ContentHelper {
	
/********************** functions used by both site and admin *************************/    
    
    /**
	 * @name makeSummaryText
	 * @desc returns a plain text version of the source trunctated at the first or last sentence within the specified length
	 * @param string $source the string to make a summary from
	 * @param int $len the maximum length of the summary
	 * @param bool $first if true truncate at end of first sentence, else at the last sentence within the max length
	 * @return string
	 */
	public static function makeSummaryText(string $source, int $len=250, bool $first = true) {
		if ($len == 0 ) {$len = 100; $first = true; }
		//first strip any html and truncate to max length
		$summary = HTMLHelper::_('string.truncate', $source, $len, true, false);
		//strip off ellipsis if present (we'll put it back at end)
		$hadellip = false;
		if (substr($summary,strlen($summary)-3) == '...') {
			$summary = substr($summary,0,strlen($summary)-3);
			$hadellip = true;
		}
		// get a version with '? ' and '! ' replaced by '. '
		$dotsonly = str_replace(array('! ','? '),'. ',$summary.' ');
		if ($first) {
			// look for first ". " as end of sentence
			$dot = strpos($dotsonly,'. ');
		} else {
			// look for last ". " as end of sentence
			$dot = strrpos($dotsonly,'. ');
		}
		// are we going to cut some more off?)
		if (($dot!==false) && ($dot < strlen($summary)-3)) {
			$hadellip = true;
		}
		if ($dot>3) {
			$summary = substr($summary,0, $dot+1);
		}
		if ($hadellip) {
			// put back ellipsis with a space
			$summary .= ' ...';
		}
		return $summary;
	}
	
	/**
	 * @name makeLinkedNameList
	 * @desc takes array of items with name/title, link, role, and note for each and returns a string list of the items
	 * @param array $items required - array of details to turn into list
	 * NB each item must contain $item->name, and may contain ->link ->role ->note
	 * @param string $role default '' - filter by role type
	 * @param string $sep default comma - separtor between list elements (eg comma | br | li | [string]
	 * NB comma = ', ' if only 2 items then comma = ' &amp; '. li will be '[li]item[/li]' 
	 * NB [string] can be html eg '[p]/' where the / tells it to close the tag at end of row
	 * @param boolean $linked default true - if true link names to detail view 
	 * @param int $rowfmt default 0 - 0=role-name, 1=role-name-note, 2=name-role, 3=name-role-note , 4=name.(note), 5==name only
	 * NB if filtering by role then role not shown so 2=0, 3=1
	 * @return string
	 */
	public static function makeLinkedNameList($items, $role='', $sep='comma', $linked=true, $rowfmt = 0) {
	    $list = '';
	    $roletitles = array('director'=>Text::_('XBCULTURE_DIRECTOR'),'producer'=>Text::_('XBCULTURE_PRODUCER'), 'crew'=>Text::_('XBCULTURE_CREW'), 
	        'actor'=>Text::_('XBCULTURE_ACTOR'),'appearsin'=>'','char'=>Text::_('XBCULTURE_CHARACTER_U'),
	        'author'=>Text::_('XBCULTURE_AUTHOR'), 'editor'=>Text::_('XBCULTURE_EDITOR'), 'mention'=>'', 'other'=>''
	    );
	    $cnt = count($items);
	    if ($sep == 'ul') {
	        $list .= '<ul class="xblist">';
	    } elseif ($sep == 'ol') {
	        $list .= '<ol>';
	    }
        $p = 0;
    	foreach ($items as $item) {
	        if (($role=='') || ($role == $item->role)) {
    	        $p ++;
    	        $name = (empty($item->name)) ? $item->title : $item->name;   //for items that have titles instead of names
    	        $name = '<span class="xblistname">'.$name.'</span>';
	            if (($sep == 'ul') || ($sep == 'ol')) {
	               $list .= '<li>';
    	        } elseif ($sep[-1] == '/') {
    	           $list .= trim($sep,'/');
    	        }
                if ($linked) {
                    $name = '<a href="'.$item->link.'" class="xblistlink">'.$name.'</a>';
                }
                if (!isset($item->role)) $item->role='';
               $trole = (array_key_exists($item->role, $roletitles)) ? $roletitles[$item->role] : $item->role;
    	       switch ($rowfmt) {
    	           case 0: // role name
    	               $list .= (empty($role)) ? '' : '<span class="xblistrolefirst">'.$trole.'</span> ';
    	               $list .= $name;
    	               break;
    	           case 1: //role name.(note)
    	               $list .= (empty($role)) ? '' : '<span class="xblistrolefirst">'.$trole.'</span> ';
    	               $list .= $name;
    	               $list .= (empty($item->note)) ? '' : ' <span class="xbnote">'.$item->note.'</span>';
    	               break;
    	           case 2: //name.role
    	               $list .= $name;
    	               $list .= (empty($role)) ? '' : ' <span class="xblistrolesecond">'.$trole.'</span>';
    	               break;
    	           case 3: //name.(role).(note)
    	               $list .= $name;
    	               if (empty($role)) {
           	               $list .= '<span class="xblistrolesecond">'.$trole.'</span> ';   	                   
    	               }
    	               $list .= (empty($item->note)) ? '' : ' <span class="xbnote">'.$item->note.'</span>';
    	               break;
    	           case 4: //name.(note)
    	               $list .= $name;
    	               $list .= (empty($item->note)) ? '' : ' <span class="xbnote">'.$item->note.'</span>';
    	               break;
    	           case 5: //name only
    	               $list .= $name;
    	               break;   	               
    	           default:
    	               ;
    	               break;
    	       }
    	       switch ($sep) {
    	           case 'ul':
    	           case 'ol':
    	               $list .= '</li>';
    	               break;
    	           case 'comma':
    	               if ($p == 1) {
    	                   $list .= ' &amp; ';
    	               } else {
    	                   $list .= ', ';
    	               }
    	               break;
    	           case 'br':
    	               $list .= '<br />';
    	               break;
    	           default:
    	               $list .= $sep;
    	               break;
    	       }
	        }	       
	    } //endfor
	    switch ($sep) {
	        case 'ul':
	            $list .= '</ul>';
    	        break;
	        case 'ol':
	            $list .= '</ol>';
	            break;
	        case 'comma' :
	            $list = trim($list,', ');
	            if (substr($list,-5)== '&amp;') {
	                $list = substr($list,0,strlen($list)-5);
	            }
	            break;
	        case 'br':
	            if (substr($list,-6)== '<br />') {
	               $list = substr($list,0,strlen($list)-6);
	            }
	        default:
	                $list = trim($list,$sep);
	        break;
	    }
	    return $list;
	}
	
	/**
	 * @name getItemCnt
	 * @desc returns the number of items in a table
	 * @param string $table
	 * @return integer
	 */
	public static function getItemCnt($table) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')->from($db->quoteName($table));
		$db->setQuery($query);
		$cnt=-1;
		try {
			$cnt = $db->loadResult();
		} catch (Exception $e) {
			$dberr = $e->getMessage();
			Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
		}
		return $cnt;
	}

	public static function penPont() {
		$params = ComponentHelper::getParams('com_xbpeople');
		$beer = trim($params->get('roger_beer'));
		//Factory::getApplication()->enqueueMessage(password_hash($beer));
		$hashbeer = $params->get('penpont');
		if (password_verify($beer,$hashbeer)) { return true; }
		return false;
	}
	
	/***
	 * @name checkComponent()
	 * @desc test whether a component is installed and enabled. 
	 * NB This sets the seesion variable if component installed to 1 if enabled or 0 if disabled.
	 * Test sess variable==1 if wanting to use component
	 * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
	 * @return boolean|number - true= installed and enabled, 0= installed not enabled, null = not installed
	 */
	public static function checkComponent($name) {
		$sname=substr($name,4).'_ok';
		$sess= Factory::getSession();
		$db = Factory::getDBO();
		$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote($name));
		$res = $db->loadResult();
		if (is_null($res)) { 
		    $sess->clear($sname);
		} else {
		    $sess->set($sname,$res);		    
		}
		return $res;
	}

	/**
	 * @name credit()
	 * @desc tests if reg code is installed and returns blank, or credit for site and PayPal button for admin
	 * @param string $ext - extension name to display, must match 'com_name' and xml filename and crosborne link page when converted to lower case
	 * @return string - empty is registered otherwise for display
	 */
	public static function credit(string $ext) {
	    if (XbcultureHelper::penPont()) {
	        return '';
	    }
	    $lext = strtolower($ext);
	    $credit='<div class="xbcredit">';
	    if (Factory::getApplication()->isClient('administrator')==true) {
	        $xmldata = Installer::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_'.$lext.'/'.$lext.'.xml');
	        $credit .= '<a href="http://crosborne.uk/'.$lext.'" target="_blank">'
	            .$ext.' Component '.$xmldata['version'].' '.$xmldata['creationDate'].'</a>';
	            $credit .= '<br />'.Text::_('XBCULTURE_BEER_TAG');
	            $credit .= Text::_('XBCULTURE_BEER_FORM');
	    } else {
	        $credit .= $ext.' by <a href="http://crosborne.uk/'.$lext.'" target="_blank">CrOsborne</a>';
	    }
	    $credit .= '</div>';
	    return $credit;
	}

	public static function adjlum($l, $ladj) {
	    if ($ladj>0) {
	        $l += (1-$l) * $ladj/100;
	    } elseif ($ladj<0) {
	        $l += $l * $ladj/100;
	    }
	    return $l;
	}
	
	public static function hex2rgb($hexstr) {
	    $hexstr = ltrim($hexstr, '#');
	    if (strlen($hexstr) == 3) {
	        $hexstr = $hexstr[0] . $hexstr[0] . $hexstr[1] . $hexstr[1] . $hexstr[2] . $hexstr[2];
	    }
	    $R = hexdec($hexstr[0] . $hexstr[1]);
	    $G = hexdec($hexstr[2] . $hexstr[3]);
	    $B = hexdec($hexstr[4] . $hexstr[5]);
	    return array($R,$G,$B);
	}
	
	public static function hex2hsl($RGB, $ladj = 0) {
	    if (!is_array($RGB)) {
	        $RGB = self::hex2rgb($RGB);
	    }
	    $r = $RGB[0]/255;
	    $g = $RGB[1]/255;
	    $b = $RGB[2]/255;
	    // using https://gist.github.com/brandonheyer/5254516
	    $max = max( $r, $g, $b );
	    $min = min( $r, $g, $b );
	    // lum
	    $l = ( $max + $min ) / 2;
	    
	    // sat
	    $d = $max - $min;
	    if( $d == 0 ){
	        $h = $s = 0; // achromatic
	    } else {
	        $s = $d / ( 1 - abs( (2 * $l) - 1 ) );
	        // hue
	        switch( $max ){
	            case $r:
	                $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
	                if ($b > $g) {
	                    $h += 360;
	                }
	                break;
	            case $g:
	                $h = 60 * ( ( $b - $r ) / $d + 2 );
	                break;
	            case $b:
	                $h = 60 * ( ( $r - $g ) / $d + 4 );
	                break;
	        }
	    }
	    $hsl = array( round( $h, 2 ), round( $s, 2 ), round( $l, 2 ) );
	    if ($ladj!= 0){
	        $l = self::adjlum($hsl[2], $ladj);
	        $hsl[2] = $l;
	    }
	    $hslstr = 'hsl('.($hsl[0]).','.($hsl[1]*100).'%,'.($hsl[2]*100).'%)';
	    return $hslstr;
	}
	
	public static function popstylecolours($pophex) {
	    $stylestr = '.xbhover, .xbhover:hover {text-decoration-color:'.$pophex.';} ';
	    $stylestr .= '.xbfocus, .xbfocus:hover {text-decoration-color:'.$pophex.';} ';
	    $stylestr .= '.xbclick, .xbclick:hover {text-decoration-color:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop + .popover {border-color:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop + .popover > .popover-title {border-bottom-colour:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop + .popover > .popover-title {background-color:'.self::hex2hsl($pophex,85).' !important; ';
	    $stylestr .= 'color:'.$pophex.';border-bottom-color:'.$pophex.';font-weight:bold} ';
	    $stylestr .= '.xbcultpop  + .popover > .popover-content {background-color:'.self::hex2hsl($pophex,97).' !important; ';
	    $stylestr .= 'color:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop  + .popover > .popover-content > a {color:'.self::hex2hsl($pophex,-30).';font-weight:bold;} ';
	    $stylestr .= '.xbcultpop + .popover.right>.arrow:after { border-right-color:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop + .popover.left>.arrow:after { border-left-color:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop + .popover.bottom>.arrow:after { border-bottom-color:'.$pophex.';} ';
	    $stylestr .= '.xbcultpop + .popover.top>.arrow:after { border-top-color:'.$pophex.';}';
	    return $stylestr;
	}
	
	/**
	 * @name getGroupMembers()
	 * @param int $groupid
	 * @return array of objects
	 */
	public static function getGroupmembers(int $groupid) {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $plink = 'index.php?option=com_xbpeople&view=person';
	    if ($isadmin) {
	        $plink .= '&layout=edit';
	    }
	    $plink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note AS note, a.joined AS joined, a.until AS until,
            CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS name, p.id, p.state AS pstate')
	    ->from('#__xbgroupperson AS a')
	    ->join('LEFT','#__xbpersons AS p ON p.id=a.person_id')
	    ->where('a.group_id = "'.$groupid.'"' );
	    if (!$isadmin) {
	        $query->where('p.state = 1');
	    }
        $query->order('a.listorder ASC');
	    $db->setQuery($query);
	    $persons = $db->loadObjectList();
	    foreach ($persons as $per){
	        $per->link = Route::_($plink . $per->id);
	        if ($per->pstate != 1) {
	            $per->name = '<span class="xbhlt">'.$per->name.'</span>';
	        }
	        $dates = $per->joined.' - '.$per->until;
	        if (strlen(trim($dates))>3) {
	            $per->note .= ' <i>['.$dates.']</i> ';
	        }
	    }
	    return $persons;
	}
	
	/**
	 * @name getGroupEvents()
	 * get an array of event objects for a group
	 * @param int $groupid
	 * @return array of objects
	 */
	public static function getGroupEvents(int $groupid) {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $elink = 'index.php?option=com_xbevents&view=event';
	    if ($isadmin) {
	       $elink .= '&layout=edit';
	    }
	    $elink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('a.role AS role, a.role_note AS note, e.title, e.state as estate, e.id AS id')
	    ->from('#__xbeventgroup AS a')
	    ->join('LEFT','#__xbevents AS e ON e.id=a.event_id')
	    ->where('a.group_id = "'.$groupid.'"' );
	    if (!$isadmin) {
	        $query->where('e.state = 1');
	    }
        $query->order('a.listorder ASC');
	    $db->setQuery($query);
	    $events = $db->loadObjectList();	    
	    foreach ($events as $evnt){
	        $evnt->link = Route::_($elink . $evnt->id);
	        if ($evnt->estate != 1) {
	            $evnt->title = '<span class="xbhlt">'.$evnt->title.'</span>';
	        }
	    }
	    return $events;
	}
	
	/**
	 * @name getGroupBooks()
	 * get an array of book objects for a group
	 * @param int $groupid
	 * @return array of objects
	 */
	public static function getGroupBooks(int $groupid) {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $blink = 'index.php?option=com_xbbooks&view=book';
	    if ($isadmin) {
	        $blink .= '&layout=edit';
	    }
	    $blink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('a.role AS role, a.role_note AS note, b.title, b.state as bstate, b.id AS id')
	    ->from('#__xbbookgroup AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.group_id = "'.$groupid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }
	    $query->order('a.listorder ASC');
	    $db->setQuery($query);
	    $books = $db->loadObjectList();
	    foreach ($books as $book){
	        $book->link = Route::_($blink . $book->id);
	        if ($book->bstate != 1) {
	            $book->title = '<span class="xbhlt">'.$book->title.'</span>';
	        }
	    }
	    return $books;
	}

	/**
	 * @name getGroupFilms()
	 * get an array of book objects for a group
	 * @param int $groupid
	 * @return array of objects
	 */
	public static function getGroupFilms(int $groupid) {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $flink = 'index.php?option=com_xbfilms&view=film';
	    if ($isadmin) {
	        $flink .= '&layout=edit';
	    }
	    $flink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('a.role AS role, a.role_note AS note, b.title, b.state as bstate, b.id AS id')
	    ->from('#__xbfilmgroup AS a')
	    ->join('LEFT','#__xbfilms AS b ON b.id=a.film_id')
	    ->where('a.group_id = "'.$groupid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }
	    $query->order('a.listorder ASC');
	    $db->setQuery($query);
	    $films = $db->loadObjectList();
	    foreach ($films as $film){
	        $film->link = Route::_($flink . $film->id);
	        if ($film->bstate != 1) {
	            $film->title = '<span class="xbhlt">'.$film->title.'</span>';
	        }
	    }
	    return $films;
	}
	
	
	/************** functions used on admin side only *********************/

	/**
	 * @name getExtensionInfo()
	 * @param string $element 'mod_...' or 'com_...' for component or module, for plugin the plugin=string from the xml plus the folder (type of plugin))
	 * @return false if not installed, version string if installed followed but '(not enabled)' if not enabled
	 */
	public static function getExtensionInfo($element, $folder=null) {
	    $db = Factory::getDBO();
	    $qry = $db->getQuery(true);
	    $qry->select('enabled, manifest_cache')
	    ->from($db->quoteName('#__extensions'))
	       ->where('element = '.$db->quote($element));
	       if ($folder) {
	           $qry->where('folder = '.$db->quote($folder));
	       }
	    $db->setQuery($qry);
	    $res = $db->loadAssoc();
	    if (is_null($res)) { 
	        return false; 
	    } else {
	        $manifest = json_decode($res['manifest_cache'],true);
	    }
	    $manifest['enabled'] = $res['enabled'];
	    return $manifest;
	}
	
	/**
	 * @name getCat()
	 * @desc given category id returns title and description
	 * @param int $catid
	 * @return object|null
	 */
	public static function getCat($catid) {
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')
		->from('#__categories AS a ')
		->where('a.id = '.$catid);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * @name getTag()
	 * @desc gets a tag's details given its id
	 * @param (int) $tagid
	 * @return unknown|mixed
	 */
	public static function getTag($tagid) {
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('*')
	    ->from('#__tags AS a ')
	    ->where('a.id = '.$tagid);
	    $db->setQuery($query);
	    return $db->loadObject(); 
	}
	
	/**
	 * @name getTagsItemCnts()
	 * @desc returns the number of distinct items tagged with a specific typealias (eg com_xbbook.book)
	 * @param string $typealias
	 * @param string $itemtype optional book or film or event
	 * @return number
	 */
	public static function getTagtypeItemCnt(string $typealias, $itemtype='')	{
	    $linktable = '';
	    $linkfield = '';
	    $xbbooks_ok = Factory::getSession()->get('xbbooks_ok',false);
	    $xbfilms_ok = Factory::getSession()->get('xbfilms_ok',false);
	    switch ($itemtype) {
	        case 'book':
	            if ($xbbooks_ok){
    	            if ($typealias == 'com_xbpeople.person') {
        	            $linktable = '#__xbbookperson';
    	                $linkfield = 'person_id';
    	            } elseif ($typealias == 'com_xbpeople.character') {
    	                $linktable = '#__xbbookcharacter';
    	                $linkfield = 'char_id';
    	            }                
	            } else {
	                return 0;
	            }
	            break;	        
	        case 'film':
	            if ($xbfilms_ok) {
    	            if ($typealias == 'com_xbpeople.person') {
    	                $linktable = '#__xbfilmperson';
    	                $linkfield = 'person_id';
    	            } elseif ($typealias == 'com_xbpeople.character') {
    	                $linktable = '#__xbfilmcharacter';
    	                $linkfield = 'char_id';
    	            }
	            } else {
	                return 0;
	            }
	            break;
	        default:
    	        break;
	    }
	    $db = Factory::getDbo();
	    $query =$db->getQuery(true);
	    $query->select('a.content_item_id')
	    ->from($db->quoteName('#__contentitem_tag_map').' AS a');
	    if ($linktable) {
	        $query->innerJoin($linktable.' AS lnk ON lnk.'.$linkfield.' = a.content_item_id');
	    }
	    $query->where('a.type_alias = '.$db->quote($typealias))
	    ->group('a.core_content_id');
	    $db->setQuery($query);
	    $res = $db->loadColumn();
	    if ($res) return count($res);
	    return 0;
	}
	
	/**
	 * @name getTagtypeTagCnt()
	 * @desc returns number of distinct tags used by a component for people and chars
	 * can optional restrict to only items in a particular link table (if type alias is LIKE %xbpeople%)
	 * @param string $typealias
	 * @param string $itemtype optional book or film or event
	 * @return number
	 */
	public static function getTagtypeTagCnt(string $typealias, $itemtype='') {
	    $linktable = '';
	    $linkfield = '';
	    $xbbooks_ok = Factory::getSession()->get('xbbooks_ok',false);
	    $xbfilms_ok = Factory::getSession()->get('xbfilms_ok',false);
	    switch ($itemtype) {
	        case 'book':
	            if ($xbbooks_ok){
	                if ($typealias == 'com_xbpeople.person') {
    	                $linktable = '#__xbbookperson';
    	                $linkfield = 'person_id';
	                } elseif ($typealias == 'com_xbpeople.character') {
    	                $linktable = '#__xbbookcharacter';
    	                $linkfield = 'char_id';
	                }
	            } else {
	                return 0;
	            }
	            break;
	        case 'film':
	            if ($xbfilms_ok){
	                if ($typealias == 'com_xbpeople.person') {
    	                $linktable = '#__xbfilmperson';
    	                $linkfield = 'person_id';
	                } elseif ($typealias == 'com_xbpeople.character') {
    	                $linktable = '#__xbfilmcharacter';
    	                $linkfield = 'char_id';
	                }
	            } else {
	                return 0;
	           }
	            break;
	        default:
	            break;
	    }
	    $db = Factory::getDbo();
	    $query =$db->getQuery(true);
	    $query->select('a.tag_id')
	    ->from($db->quoteName('#__contentitem_tag_map').' AS a');
	    if ($linktable) {
	        $query->innerJoin($linktable.' AS lnk ON lnk.'.$linkfield.' = a.content_item_id');
	    }
	    $query->where('a.type_alias = '.$db->quote($typealias))
	    ->group('a.tag_id');
	    $db->setQuery($query);
	    $res = $db->loadColumn();
	    if ($res) return count($res);
	    return 0;
	}
	
	/**
	 * @name createCategory()
	 * @desc creates a new category if it doesn't exist, returns id of category
	 * NB passing a name and no alias will check for alias based on name.
	 * @param (string) $name for category
	 * @param string $alias - usually lowercase name with hyphens for spaces, must be unique, will be created from name if not supplied
	 * @param number $parentid - id of parent category (defaults to root
	 * @param string $ext - the extension owning the category
	 * @param string $desc - optional description
	 * @return integer - id of new or existing category, or false if error. Error message is enqueued
	 */
	public static function createCategory($name, $alias='',$parentid = 1,  $ext='com_xbpeople', $desc='' ) {
	    if ($alias=='') {
	        //create alias from name
	        $alias = OutputFilter::stringURLSafe(strtolower($name));
	    }
	    //check category doesn't already exist
	    $catid = XbcultureHelper::getIdFromAlias('#__categories',$alias, $ext);
	    if ($catid>0) {
	        return $catid;
	    } else {	        
    	    $db = Factory::getDbo();
    	    $query = $db->getQuery(true);
    	    //get category model
    	    $basePath = JPATH_ADMINISTRATOR.'/components/com_categories';
    	    require_once $basePath.'/models/category.php';
    	    $config  = array('table_path' => $basePath.'/tables');
    	    //setup data for new category
    	    $category_model = new CategoriesModelCategory($config);
    	    $category_data['id'] = 0;
    	    $category_data['parent_id'] = $parentid;
    	    $category_data['published'] = 1;
    	    $category_data['language'] = '*';
    	    $category_data['params'] = array('category_layout' => '','image' => '');
    	    $category_data['metadata'] = array('author' => '','robots' => '');
    	    $category_data['extension'] = $ext;
    	    $category_data['title'] = $name;
    	    $category_data['alias'] = $alias;
    	    $category_data['description'] = $desc;
    	    if(!$category_model->save($category_data)){
    	        Factory::getApplication()->enqueueMessage('Error creating category: '.$category_model->getError(), 'error');
    	        return false;
    	    }
    	    $id = $category_model->getItem()->id;
    	    return $id;
	    }
	}
	
	/**
	 * @name getIdFromALias()
	 * @desc given a table name and an alias string returns the id of the corresponding item
	 * @param (string) $table
	 * @param (string) $alias
	 * @param string $ext
	 * @return mixed|void|NULL
	 */
	public static function getIdFromAlias($table,$alias, $ext = 'com_xbpeople') {
	    $alias = trim($alias,"' ");
	    $table = trim($table,"' ");
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('id')->from($db->quoteName($table))->where($db->quoteName('alias')." = ".$db->quote($alias));
	    if ($table === '#__categories') {
	        $query->where($db->quoteName('extension')." = ".$db->quote($ext));
	    }
	    $db->setQuery($query);
	    $res =0;
	    $res = $db->loadResult();
	    return $res;
	}
	
	/**
	 * @name checkPersonExists()
	 * @desc returns true if person with same names already exists (case insensitive)
	 * @param string $firstname
	 * @param string $lastname
	 * @return boolean
	 */
	public static function checkPersonExists( $firstname,  $lastname) {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('id')->from('#__xbpersons')
	    ->where('LOWER('.$db->quoteName('firstname').')='.$db->quote(strtolower($firstname)).' AND LOWER('.$db->quoteName('lastname').')='.$db->quote(strtolower($lastname)));
	    $db->setQuery($query);
	    $res = $db->loadResult();
	    if ($res > 0) {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * @name checkTitleExists()
	 * @desc returns true if given title exists in given table (case insensitive)
	 * If table is xbcharacters then uses name column rather than title
	 * @param string $title
	 * @param string $table
	 * @return boolean
	 */
	public static function checkTitleExists( $title,  $table) {
	    $col = ($table == '#__xbcharacters') ? 'name' : 'title';
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('id')->from($db->quoteName($table))
	    ->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($title)));
	    $db->setQuery($query);
	    $res = $db->loadResult();
	    if ($res > 0) {
	        return true;
	    }
	    return false;
	}	
	
	/************ functions used on site side only **********************/
	
	/**
	 * @name sitePageHeader()
	 * @desc builds a page header string from passed data
	 * @param array $displayData
	 * @return string
	 */
	public static function sitePageheader($displayData) {
	    $header ='';
	    if (!empty($displayData)) {
	        $header = '	<div class="row-fluid"><div class="span12 xbpagehead">';
	        if ($displayData['showheading']) {
	            $header .= '<div class="page-header"><h1>'.$displayData['heading'].'</h1></div>';
	        }
	        if ($displayData['title'] != '') {
	            $header .= '<h3>'.$displayData['title'].'</h3>';
	            if ($displayData['subtitle']!='') {
	                $header .= '<h4>'.$displayData['subtitle'].'</h4>';
	            }
	            if ($displayData['text'] != '') {
	                $header .= '<p>'.$displayData['text'].'</p>';
	            }
	        }
	    }
	    return $header;
	}
	
	/**
	 * @name getChildCats()
	 * @desc for a given category returns an array of child category ids
	 * @param int $pid - id of the parent category
	 * @param string $ext - the extension the parent belongs to (or null to look it up)
	 * @param boolean $incroot - whether to include the parent id in the return array
	 * @return array of ids
	 */
	public static function getChildCats(int $pid, $ext = null, $incroot = true) {
	    $db    = Factory::getDbo();
	    $query = $db->getQuery(true);
	    if (is_null($ext)) {
	        $query->select($db->quoteName('extension'))
	           ->from($db->quoteName('#__categories')
	           ->where($db->quoteName('id').'='.$pid));
	        $ext = $db->loadResult();
	    }
	    $query->clear();
	    $query->select('*')->from('#__categories')->where('id='.$pid);
	    $db->setQuery($query);
	    $pcat=$db->loadObject();
	    $start = $incroot ? '>=' : '>';
	    $query->clear();
	    $query->select('id')->from('#__categories')->where('extension = '.$db->quote($ext));
	    $query->where(' lft'.$start.$pcat->lft.' AND rgt <='.$pcat->rgt);
	    $db->setQuery($query);
	    return $db->loadColumn();
	}
	
	/**
	 * @name getPersonBookRoles()
	 * @desc for given person returns an array of books and roles
	 * @param int $personid
	 * @param string $role - if not blank only get the specified role
	 * @param string $order - field to order list by (role first if specified)
	 * @param int $listfmt - 0=title only, 1=title,role,note  2=title,note (sort by role first)
	 * @return array of objects with title,subtitle,pubyear,role,role_note,link,listitem
	 * where link is [a] link to the book, 
	 * and listitem is [li] formatted according to $listfmt with title linked to item
	 */
	public static function getPersonBookRoles(int $personid, $role='',$order='title ASC', $listfmt = 0) {
	    $blink = 'index.php?option=com_xbbooks&view=book&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note, b.title, b.subtitle, b.pubyear, b.id, b.state AS bstate')
	    ->from('#__xbbookperson AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    $query->where('b.state = 1');
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    } elseif ($listfmt == 2) {
	        $query->order('a.role ASC'); //this will order roles as author, editor, mention, other, publisher,
	    }
        $query->order('b.'.$order);
	    $db->setQuery($query);
	    $booklist = $db->loadObjectList(); //list of books for the person	    
	    foreach ($booklist as $book){
	        $booklink = Route::_($blink . $book->id);
	        $book->link = "<a href='".$booklink."'>".$book->title."</a>";
	        $book->listitem = '<li>';
            $book->listitem .= $book->link;
            if ($listfmt == 1) {
                $book->listitem .= ' : ';
                switch ($book->role) {
                    case 'mention':
                        $book->listitem .= Text::_('XBCULTURE_APPEARS_IN');
                        break;
                    case 'other':
                        $book->listitem .= Text::_('XBCULTURE_OTHER_ROLE');
                        break;
                    default:
                        $book->listitem .= ucfirst($book->role);
                        break;
                }
            }
            if (($listfmt==2) && ($book->role_note != '')) $book->listitem .= ' : ';
            if (($listfmt > 0) && ($book->role_note != '')) {
                $book->listitem .= ' <i>('. $book->role_note.')</i>';
            }
            $book->listitem .= '</li>';	    
	    }
	    return $booklist;
	}
	
	/**
	 * @name getPersonFilmRoles()
	 * @desc for given person returns an array of films and roles
	 * @param int $personid
	 * @param string $role - if not blank only get the specified role
	 * @param string $order - field to order list by (role first if specified)
	 * @param int $listfmt - 0=title only, 1=title,role,note  2=title,note (sort by role first)
	 * @return array of objects with title,subtitle,rel_year,role,role_note,link,listitem
	 * where link is [a] link to the book, 
	 * and listitem is [li] formatted according to $listfmt with title linked to item
	 */
	public static function getPersonFilmRoles(int $personid, $role='',$order='title ASC', $listfmt = 0) {
//	    $app = Factory::getApplication();
	    $flink = 'index.php?option=com_xbfilms&view=film';
	    if (Factory::getApplication()->isClient('administrator')) {
	        $flink .= '&layout=edit';
	    }
	    $flink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note, b.title, b.rel_year, b.id, b.state AS bstate')
	    ->from('#__xbfilmperson AS a')
	    ->join('LEFT','#__xbfilms AS b ON b.id=a.film_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    $query->where('b.state = 1');
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    } elseif ($listfmt == 2) {
	        $query->order('a.role DESC'); //this will order roles as producer,director,crew,cast,appears
	    }
	    $query->order('b.'.$order);
	    $db->setQuery($query);
	    $filmlist = $db->loadObjectList();
	    foreach ($filmlist as $film){
	        $filmlink = Route::_($flink . $film->id);
	        $film->link = "<a href='".$filmlink."'>".$film->title."</a>";
	        $film->listitem = '<li>'.$film->link;
	        if ($listfmt == 1) {
	            $film->listitem .= ' : ';
	            switch ($film->role) {
	                case 'appearsin':
	                    $film->listitem .= Text::_('XBCULTURE_APPEARS_IN');
	                    break;
	                default:
	                    $film->listitem .= ucfirst($film->role);
	                    break;
	            }
	        }
	        if (($listfmt==2) && ($film->role_note != '')) $film->listitem .= ' : ';
	        if (($listfmt > 0) && ($film->role_note != '')) {
	            $film->listitem .= ' <i>('. $film->role_note.')</i>';
	        }
	        $film->listitem .= '</li>';
	    }
	    return $filmlist;
	}
	
	/**
	 * @name getPersonFilms()
	 * @desc for given person returns an array of films and roles
	 * @param int $personid
	 * @param string $role - if not blank only get the specified role
	 * @return array of objects with name,rel_year,role,role_note,link
	 */
	public static function getPersonFilms(int $personid, $role='') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $flink = 'index.php?option=com_xbfilms&view=film';
	    if ($isadmin) {
	        $flink .= '&layout=edit';
	    }
	    $flink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note AS note, f.title AS name, f.rel_year, f.id, f.state AS fstate')
	    ->from('#__xbfilmperson AS a')
	    ->join('LEFT','#__xbfilms AS f ON f.id=a.film_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    if (!$isadmin) {
	        $query->where('f.state = 1');
	    }
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    } else { //order by role, listorder before title
	        $query->order('(case a.role when '.$db->quote('director').' then 0
            when '.$db->quote('producer').' then 1
            when '.$db->quote('crew').' then 2
            when '.$db->quote('actor').' then 3
            when '.$db->quote('appearsin').' then 4
            end)');
	        $query->order('a.listorder ASC');
	    }
	    $query->order('f.title');
	    $db->setQuery($query);
	    $films = $db->loadObjectList();
	    foreach ($films as $film){
	        $film->link = Route::_($flink . $film->id);
	        if ($film->fstate != 1) {
	            $film->name = '<span class="xbhlt">'.$film->name.'</span>';
	        }
	    }
	    return $films;
	}
	
	/**
	 * @name getPersonBooks()
	 * @desc for given person returns an array of books and roles
	 * @param int $personid
	 * @param string $role - if not blank only get the specified role
	 * @return array of objects with name,rel_year,role,role_note,link
	 */
	public static function getPersonBooks(int $personid, $role='') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $blink = 'index.php?option=com_xbbooks&view=book';
	    if ($isadmin) {
	        $blink .= '&layout=edit';
	    }
	    $blink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note AS note, b.title AS name, b.pubyear, b.id, b.state AS bstate')
	    ->from('#__xbbookperson AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    } else { //order by role, listorder before title
	        $query->order('(case a.role when '.$db->quote('author').' then 0
                when '.$db->quote('editor').' then 1
                when '.$db->quote('other').' then 2
                when '.$db->quote('mention').' then 3
                end)');
	        $query->order('a.listorder ASC');
	    }
	    $query->order('b.title');
	    $db->setQuery($query);
	    $books = $db->loadObjectList();
	    foreach ($books as $book){
	        $book->link = Route::_($blink . $book->id);
	        if ($book->bstate != 1) {
	            $book->name = '<span class="xbhlt">'.$book->name.'</span>';
	        }
	    }
	    return $books;
	}
	
	/**
	 * @name getPersonEvents()
	 * @desc for given person returns array of events
	 * @param int $personid
	 * @param string $role
	 * @return array of objects with eventname eventlink role role_note
	 */
	public static function getPersonEvents(int $personid, $role='') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $elink = 'index.php?option=com_xbevents&view=event';
	    if ($isadmin) {
	        $elink .= '&layout=edit';
	    }
	    $elink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note AS note, b.title AS name, b.id, b.state AS bstate')
	    ->from('#__xbeventperson AS a')
	    ->join('LEFT','#__xbevents AS b ON b.id=a.event_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    }
	    $query->order('b.title');
	    $db->setQuery($query);
	    $events = $db->loadObjectList();
	    foreach ($events as $event){
	        $event->link = Route::_($elink . $event->id);
	        if ($event->bstate != 1) {
	            $event->name = '<span class="xbhlt">'.$event->name.'</span>';
	        }
	    }
	    return $events;
	}
	
	/**
	 * @name getPersonGroups()
	 * @desc for given person returns array of groups
	 * @param int $personid
	 * @param string $role
	 * @return array of objects with groupname grouplink role role_note
	 */
	public static function getPersonGroups(int $personid, $role='') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $glink = 'index.php?option=com_xbpeople&view=group';
	    if ($isadmin) {
	        $glink .= '&layout=edit';
	    }
	    $glink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.role, a.role_note AS note, a.joined AS joined, b.title AS name, b.id, b.state AS bstate')
	    ->from('#__xbgroupperson AS a')
	    ->join('LEFT','#__xbgroups AS b ON b.id=a.group_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    }
	    $query->order(array('a.joined',b.title));
	    $db->setQuery($query);
	    $groups = $db->loadObjectList();
	    foreach ($groups as $group){
	        $group->link = Route::_($glink . $group->id);
	        if ($group->bstate != 1) {
	            $group->name = '<span class="xbhlt">'.$group->name.'</span>';
	        }
	    }
	    return $groups;
	}
	
/**
	 * @name getCharBooks()
	 * @desc for given person returns and array of books and roles
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public static function getCharBooks(int $charid, $order='title ASC') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $blink = 'index.php?option=com_xbbooks&view=book';
	    if ($isadmin) {
	        $blink .= '&layout=edit';
	    }
	    $blink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.char_note, b.title, b.pubyear, b.id, b.state AS bstate')
	    ->from('#__xbbookcharacter AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.char_id = "'.$charid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }   
	    $query->order('b.'.$order);
	    $db->setQuery($query);
	    $books = $db->loadObjectList();
	    foreach ($books as $i=>$book){
	        $book->link = Route::_($blink . $book->id);
	        if ($book->bstate != 1) {
	            $book->name = '<span class="xbhlt">'.$book->name.'</span>';
	        }
	    }
	    return $books;
	}
	
	/**
	 * @name getCharEvents()
	 * @desc for given person returns and array of events
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public static function getCharEvents(int $charid, $order='title ASC') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $elink = 'index.php?option=com_xbevent&view=event';
	    if ($isadmin) {
	        $elink .= '&layout=edit';
	    }
	    $elink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.char_note AS note,a.actor_id AS actorid, f.title AS name, f.id, f.state AS fstate')
	    ->from('#__xbeventcharacter AS a')
	    ->join('LEFT','#__xbevents AS f ON f.id=a.event_id')
	    ->join('LEFT','#__xbpersons AS p ON p.id=a.actor_id')
	    ->select('CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS role, p.id')
	    ->where('a.char_id = "'.$charid.'"' );
	    if (!$isadmin) {
	        $query->where('f.state = 1');
	    }
	    $query->order('f.'.$order);
	    $db->setQuery($query);
	    $events = $db->loadObjectList();
	    foreach ($events as $event){
	        $event->link = Route::_($elink . $event->id);
	        if ($event->fstate != 1) {
	            $event->name = '<span class="xbhlt">'.$event->name.'</span>';
	        }
	    }
	    return $events;
	}
	
	/**
	 * @name getCharFilms()
	 * @desc for given person returns an array of films
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public static function getCharFilms(int $charid, $order='title ASC') {
	    $isadmin = Factory::getApplication()->isClient('administrator');
	    $flink = 'index.php?option=com_xbfilms&view=film';
	    if ($isadmin) {
	        $flink .= '&layout=edit';
	    }
	    $flink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.char_note AS note, f.title AS name, f.rel_year, f.id, f.state AS fstate')
	    ->from('#__xbfilmcharacter AS a')
	    ->join('LEFT','#__xbfilms AS f ON f.id=a.film_id')
	    ->join('LEFT','#__xbpersons AS p ON p.id=a.actor_id')
	    ->select('CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS role, p.id')
	    ->where('a.char_id = "'.$charid.'"' );
	    if (!$isadmin) {
	        $query->where('f.state = 1');
	    }
	    $query->order('f.'.$order);
	    $db->setQuery($query);
	    $films = $db->loadObjectList();
	    foreach ($films as $film){
	        $film->link = Route::_($flink . $film->id);
	        if ($film->fstate != 1) {
	            $film->name = '<span class="xbhlt">'.$film->name.'</span>';
	        }
	    }
	    return $films;
	}
	
	/**
	 * @name getDateFmt()
	 * @desc funtion to shorten date formats before $limityear and month-year only if 1st of month and year only if 1st january
	 * Used to format vague dates appropriately - it will screw up any dates that were actually the 1st of month! (one in 30 on average)
	 * @param string $sqldate - date string from database in format yyyy-mm-dd (optional time will be ignored)
	 * @param string $datefmt - the default format
	 * @param string $limityear - the year early than which to shorten vague dates
	 * @return string
	 */
	public static function getDateFmt(string $sqldate, $datefmt = 'D j M Y', $limityear = '2011') {
	    if (substr($sqldate,0,4) < 2011) {
	        if (substr($sqldate,5,5)=='01-01') {
	            $datefmt = 'Y';
	        } elseif (substr($sqldate,8,2) == '01') {
	            $datefmt = 'M Y';
	        }
	    }
	    return $datefmt;
	}
}