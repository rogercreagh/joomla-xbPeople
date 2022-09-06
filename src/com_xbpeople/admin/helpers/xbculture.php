<?php
/*******
 * @package xbPeople for all xbCulture extensions
 * @filesource admin/helpers/xbculture.php
 * @version 0.9.9.4 29th July 2022
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
// use Joomla\CMS\Filter\OutputFilter;
// use Joomla\CMS\Application\ApplicationHelper;

class XbcultureHelper extends ContentHelper {
	
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
	 * @desc test whether a component is installed and enabled. Sets a session variable to save a subsequent db call
	 * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
	 * @return boolean|number - true= installed and enabled, 0= installed not enabled, false = not installed
	 */
	public static function checkComponent($name) {
		$sname=substr($name,4).'_ok';
		$sess= Factory::getSession();
		$db = Factory::getDBO();
		$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote($name));
		$res = $db->loadResult();
		$sess->set($sname,$res);
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
		$query->select('a.title, a.description')
		->from('#__categories AS a ')
		->where('a.id = '.$catid);
		$db->setQuery($query);
		return $db->loadObjectList()[0];
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
	 * @name getCharBookRoles()
	 * @desc for given person returns and array of books and roles
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public static function getCharBooks(int $charid, $order='title ASC') {
	    $blink = 'index.php?option=com_xbbooks&view=book&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $query->select('a.char_note, b.title, b.pubyear, b.id, b.state AS bstate')
	    ->from('#__xbbookcharacter AS a')
	    ->join('LEFT','#__xbbooks AS b ON b.id=a.book_id')
	    ->where('a.char_id = "'.$charid.'"' );
	    $query->where('b.state = 1');
	    $query->order('b.'.$order);
	    $db->setQuery($query);
	    $list = $db->loadObjectList();
	    foreach ($list as $i=>$book){
	        $tlink = Route::_($blink . $book->id);
	        $book->link = '<a href="'.$tlink.'">'.$book->title.'</a>';
	        $book->listitem = '<li>'.$book->link;
	        if ($book->char_note !='') {
	            $book->listitem .= ' <i>('.$book->char_note.')</i>';
	        }
	        $book->listitem .= '</li>';
	    }
	    return $list;
	}
	
	/**
	 * @name getCharFilm()
	 * @desc for given person returns and array of films
	 * @param int $charid
	 * @param boolean $order - field to order list by (role first if specified)
	 * @return array
	 */
	public static function getCharFilms(int $charid, $order='title ASC') {
	    $flink = 'index.php?option=com_xbfilms&view=film';
	    $plink = 'index.php?option=com_xbpeople&view=person';
	    if (Factory::getApplication()->isClient('administrator')) {
	        $flink .= '&layout=edit';
	        $plink .= '&layout=edit';
	    }
	    $flink .= '&id=';
	    $plink .= '&id=';
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);	    
	    $query->select('a.char_note, a.actor_id, p.firstname,p.lastname,
            b.title, b.rel_year, b.id, b.state AS bstate')
	    ->from('#__xbfilmcharacter AS a')
	    ->join('LEFT','#__xbfilms AS b ON b.id=a.film_id')
	    ->join('LEFT','#__xbpersons AS p ON p.id=a.actor_id')
	    ->where('a.char_id = "'.$charid.'"' );
	    $query->where('b.state = 1');
	    $query->order('b.'.$order); 
	    $db->setQuery($query);
	    $films = $db->loadObjectList();
	    foreach ($films as $i=>$film){
	        $tlink = Route::_($flink . $film->id);
	        $film->link = '<a href="'.$tlink.'">'.$film->title.'</a>';
	        $film->listitem = '<li>'.$film->link;
	        if ($film->actor_id > 0) {
	            $alink = Route::_($plink.$film->actor_id);
	            $film->listitem .= ' <i>played by <a href="'.$alink.'">'.$film->firstname.' '.$film->lastname.'</a></i>)';
	        }
	        if ($film->char_note !='') {
	            $film->listitem .= ' <i>('.$film->char_note.')</i>';
	        }
	        $film->listitem .= '</li>';
	    }
	    return $films;
	}
	
}