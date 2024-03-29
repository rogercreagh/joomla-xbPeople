<?php
/*******
 * @package xbPeople for all xbCulture extensions
 * @filesource admin/helpers/xbculture.php
 * @version 1.0.3.14 17th February 2023
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
	 * @name makeItemLists
	 * @desc takes array of items with name/title, link, role, and note for each and returns a string list of the items
	 * @param array $items required - array of details to turn into list
	 * NB each item must contain $item->name, and may contain ->link ->role ->note
	 * @param string $role default='' - filter by role type
	 * @param string $rowfmt default='t' - t=title, r=role, n=note thre chars will define order of items eg rt=role-title trn=title-role-note
	 * @param int $linked default=1 - 0=no link, 1=text to item, 3=eye to modal, 4=text to item eye to modal
	 * @param string $pvtargid default='pvmodal' - the id of the modal window to be used
	 * @return array containing ullist and commalist
	 */
	public static function makeItemLists($items, $role='', $rowfmt = 't', $linkfmt = 0, $pvtargid = 'pvmodal') {// $modal= array('target'=>'pvmodal','opt'=>'com_xbbooks', 'view'=>'default') ) { //
	    $targ = 'xbmodal';
	    $click= "window.pvid=";
	    switch ($pvtargid) {
	        case 'person' :
	            $click = "window.com='people';window.view='person';window.pvid=";
	            break;
	        case 'group' :
	            $click = "window.com='people';window.view='group';window.pvid=";
	            break;
	        case 'char' :
	        case 'character' :
	            $click = "window.com='people';window.view='character';window.pvid=";
	            break;
	        case 'film' :
	            $click = "window.com='films';window.view='film';window.pvid=";
	            break;
	        case 'event' :
	            $click = "window.com='events';window.view='event';window.pvid=";
	            break;
	        case 'book' :
	            $click = "window.com='books';window.view='book';window.pvid=";
	            break;
	        case 'freview' :
	            $click = "window.com='films';window.view='filmreview';window.pvid=";
	            break;
	        case 'ereview' :
	            $click = "window.com='events';window.view='eventreview';window.pvid=";
	            break;
	        case 'breview' :
	            $click = "window.com='books';window.view='bookreview';window.pvid=";
	            break;
	        default:
	            $targ = $pvtargid;
	        break;
 	    }
	    $ullist = '<ul class="xblist">';
	    $commalist = '';
	    if ($role=='') {
	        $valcnt = count($items);
	    } elseif ($role == 'other') {
	        $valcnt = 3; //fudge
	    } else {
	        $arrvals = array_column($items, 'role');
	       $valcnt = (array_key_exists($role, $arrvals)) ? array_count_values($arrvals)[$role] : 0 ;
	    }
	    $roletitles = array('director'=>Text::_('XBCULTURE_DIRECTOR'),'producer'=>Text::_('XBCULTURE_PRODUCER'), 'crew'=>Text::_('XBCULTURE_CREW'), 
	        'actor'=>Text::_('XBCULTURE_ACTOR'),'appearsin'=>'','char'=>Text::_('XBCULTURE_CHARACTER_U'),
	        'author'=>Text::_('XBCULTURE_AUTHOR'), 'editor'=>Text::_('XBCULTURE_EDITOR'), 'mention'=>''
	    );
        $p = 0;
        $link = '';
    	foreach ($items as $item) {
    	    $doit = false;
    	    if ($role == 'other') {
    	        if (strpos(' author mention editor',$item->role) === false) {
    	            $doit=true;
    	        }
    	    } else {
    	        if (($role=='') || ($role == $item->role)) {
    	            $doit = true;
    	        }
    	    }
    	    if ($doit) {
    	        $listitem = '';
    	        $link = '';
    	        $p ++;
    	        $name = (empty($item->name)) ? $item->title : $item->name;   //for items that have titles instead of names
    	        $name = '<span class="xblistname">'.$name.'</span>';
                $ullist .= '<li>';
                $modref = '<a href="#ajax-'.$targ.'" class="xbpv" data-toggle="modal" data-target="#ajax-'.$targ.'" data-backdrop="static" onclick="'.$click;
                //$modref = '<a href="" data-toggle="modal" data-target="#ajax-'.$modal['target'].'onclick="window.modlink='.$modal['opt'].'&view='.$modal['view'].'&layout=default&tmpl=component&id=';
//                $modref = '<a href="" class="xbpv" data-toggle="modal" data-target="#ajax-'.$targ.'" ';
//                $modref .= 'onclick="window.com='.$com.';window.view='.$view.'window.pvid=';
                switch ($linkfmt) {
                    case 0: //no link
                        $link = $name;
                    case 1: //name to item
                        $link = '<a href="'.$item->link.'">'.$name.'</a>';
                    break;
                    case 2: //name to modal
                    case 3: // eye to modal
                        $link = '<b>'.$name.'</b>';
                        $link .= '&nbsp;'. $modref.$item->id.';" ><i class="far fa-eye"></i></a>';
                        break;
                    case 4: //name to item, eye to modal
                        $link = '<a href="'.$item->link.'"">'.$name.'</a>';
                        $link .= '&nbsp;'.$modref. $item->id.';" ><i class="far fa-eye"></i></a>';
                        break;
                    default:
                        $link = $name;
                        break;
                }
                if (!isset($item->role)) $item->role='';
                // if we have a value for role we will not show role here unless it is 'other'
                $dorole = ((empty($role)) || ($role=='other'));
                $trole = (array_key_exists($item->role, $roletitles)) ? $roletitles[$item->role] : $item->role;
                switch ($rowfmt) {
                    //0='T', 1='T N'  , 2='N: T' , 4='T R', 5='T R (N)', 8='R: T' 9='R: T (N)', 10=R N: T
    	           case 't': //name only
    	               $listitem .= $link;
    	               break;   	               
    	           case 'tn': //title note
    	               $listitem .= $link;
    	               $listitem .= (empty($item->note)) ? '' : ' <span class="xbpostname xbbracket">'.$item->note.'</span>';
    	               break;
    	           case 'nt': //note: title
    	               $listitem .= (empty($item->note)) ? '' : '<span class="xbprename">'.$item->note.'</span>';
    	               $listitem .= $link;
    	               break;
    	           case 'tr': //title role
    	               $listitem .= $link;
    	               if ($dorole) {
        	               if ($item->role == 'other') {
        	                   $listitem .= (empty($item->note)) ? '' : ' <span class="xbpostname">'.$item->note.'</span>';
        	               } else {
        	                   $listitem .= ' <span class="xbpostname">'.$trole.'</span>';
        	               }   	                   
    	               }
    	               break;
    	           case 'trn': // title role (note)
    	               $listitem .= $link;
    	               if ($dorole) {
         	               if ($item->role == 'other') {
        	                   $listitem .= (empty($item->note)) ? '' : ' <span class="xbpostname">'.$item->note.'</span>';
        	               } else {
        	                   $listitem .= ' <span class="xbpostname">'.$trole.'</span>';
        	               }
    	               }
    	               if ($item->role != 'other') {
    	                   $listitem .= (empty($item->note)) ? '' : ' <span class="xbpostname xbbracket">'.$item->note.'</span>';
    	               }
    	               break;
    	           case 'rt': //role: title
    	               if ($dorole) {
        	               if ($item->role == 'other') {
        	                   $listitem .= ' <span class="xbprename">'.$item->note.'</span>';
        	               } else {
        	                   $listitem .= ' <span class="xbprename">'.$trole.'</span>';
        	               }    	                   
    	               }
    	               $listitem .= $link;
    	               break;
    	           case 'rtn': // role: title (note)
    	               if ($dorole) {
        	               if ($item->role == 'other') {
        	                   $listitem .= (empty($item->note)) ? '' : '<span class="xbprename">'.$item->note.'</span>';
        	               } else {
        	                   $listitem .= ' <span class="xbprename">'.$trole.'</span>';
        	               }    	                   
    	               }
    	               $listitem .= $link;
    	               if ($item->role != 'other') {
    	                   $listitem .= (empty($item->note)) ? '' : '<span class="xbpostname xbbracket">'.$item->note.'</span>';
    	               }
    	               break;
    	           case 'rnt': //role note: title 
    	               $listitem .= '<span class="xbprename">';
    	               if ($dorole) {
    	                   if ($item->role == 'other') {
    	                       $listitem .= (empty($item->note)) ? '' : $item->note;
    	                   } else {
    	                       $listitem .= $trole;
    	                   }
    	               }
    	               if ($item->role != 'other') {
        	               $listitem .= (empty($item->note)) ? '' : $item->note;
    	               }
    	               $listitem .= '</span>';
    	               $listitem .= $link;
    	               break;
    	           default:
    	               break;
    	       }
//    	       if ($rowfmt > 2) {
//    	           $listitem .= $modref. $item->id.';" >&nbsp;<i class="far fa-eye"></i></a> ';
//    	       }
    	       $ullist .= $listitem.'</li>';
    	       $commalist .= $listitem;
    	       if (($p == 1) && ($valcnt==2)) {
    	           $commalist .= ' &amp; ';
    	       } else {
    	           $commalist .= ', ';
    	       }    	       
	        }	       
	    } //endfor
	    $ullist .= '</ul>';
	    $commalist = trim($commalist,', ');
// 	    if (substr($commalist,-5)== '&amp;') {
// 	        $commalist = substr($commalist,0,strlen($commalist)-5);
//         }
	    return array('ullist' => $ullist, 'commalist' => $commalist);
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
		$db = Factory::getDbo();
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
	 * @name getStarStr()
	 * @desc given a rating (int or float) and a component name returns a string of stars
	 * @param unknown $rating
	 * @param string $component
	 * @return string
	 */
	public static function getStarStr($rating, string $component) {
	    $starstr ='';
	    $params = ComponentHelper::getParams('com_xbpeople');
	    $zero_rating = $params->get('zero_rating',1);
	    $zero_class = $params->get('zero_class','fas fa-thumbs-down xbred');
	    $star_class = $params->get('star_class','fa fa-star xbgold');
	    $halfstar_class = $params->get('halfstar_class','fa fa-star-half xbgold');
	    $starcnt = (round(($rating)*2)/2);
	    if (($zero_rating) && ($starcnt==0)) {
		    $starstr = '<span class="'.$zero_class.'"></span>';
	    } else {
	        $starstr = str_repeat('<i class="'.$star_class.'"></i>',$starcnt);
	        if (($rating - floor($rating))>0) {
	            $starstr .= '<i class="'.$halfstar_class.'"></i>';
	        }
	    }
        return $starstr;
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
	public static function getGroupMembers(int $groupid) {
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
	        $dates = '';
	        if ($per->joined !='') {
	            if ($per->until != '') {
	                $dates = 'from '.$per->joined.' until '.$per->until;
	            } else {
	                $dates = 'since '.$per->joined;
	            }
	        } else {
	            if ($per->until != '') {
	                $dates = 'left '.$per->until;
	            }
	        }
	        if (strlen(trim($dates))>3) {
	            $per->note .= ' <i>'.$dates.'</i> ';
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
	 * @name deleteFromTable()
	 * @desc deletes items from specified table according to specified condition
	 * @param string $table - the table name
	 * @param string $condition - the text to be in the query WHERE clause
	 * @throws \Exception
	 * @return boolean
	 */
	public static function deleteFromTable(string $table, string $condition) {
	    $db = Factory::getDbo();
	    //delete existing role list
	    $query = $db->getQuery(true);
	    $query->delete($db->quoteName($table));
	    $query->where($condition);
	    $db->setQuery($query);
	    try {
	        $db->execute();
	    }
	    catch (\RuntimeException $e) {
	        throw new \Exception($e->getMessage(), 500);
	        return false;
	    }
	    return true;
	}
	
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
	    
	    $query->select('a.role, a.role_note AS note, f.title AS name, f.rel_year, f.id AS id, f.state AS fstate')
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
	    
	    $query->select('a.role, a.role_note AS note, b.title AS name, b.pubyear, b.id AS id, b.state AS bstate')
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
	    
	    $query->select('a.role, a.role_note AS note, b.title AS name, b.id AS id, b.state AS bstate')
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
	    
	    $query->select('a.role, a.role_note AS note, a.joined AS joined, b.title AS name, b.id AS id, b.state AS bstate')
	    ->from('#__xbgroupperson AS a')
	    ->join('LEFT','#__xbgroups AS b ON b.id=a.group_id')
	    ->where('a.person_id = "'.$personid.'"' );
	    if (!$isadmin) {
	        $query->where('b.state = 1');
	    }
	    if (!empty($role)) {
	        $query->where('a.role = "'.$role.'"');
	    }
	    $query->order(array('a.joined','b.title'));
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
	    
	    $query->select('a.char_note, b.title, b.pubyear, b.id AS id, b.state AS bstate')
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
	    
	    $query->select('a.char_note AS note,a.actor_id AS actorid, f.title AS name, f.id AS id, f.state AS fstate')
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
	    
	    $query->select('a.char_note AS note, f.title AS name, f.rel_year, f.id AS id, f.state AS fstate')
	    ->from('#__xbfilmcharacter AS a')
	    ->join('LEFT','#__xbfilms AS f ON f.id=a.film_id')
//	    ->join('LEFT','#__xbpersons AS p ON p.id=a.actor_id')
//	    ->select('CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS role, p.id')
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
	    if (substr($sqldate,0,4) < $limityear) {
	        if (substr($sqldate,5,5)=='01-01') {
	            $datefmt = 'Y';
	        } elseif (substr($sqldate,8,2) == '01') {
	            $datefmt = 'M Y';
	        }
	    }
	    return $datefmt;
	}
}