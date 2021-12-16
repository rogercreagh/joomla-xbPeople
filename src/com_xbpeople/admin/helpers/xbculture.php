<?php
/*******
 * @package xbCulture
 * @filesource admin/helpers/xbculture.php
 * @version 0.9.6 15th December 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
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
	
/* functions used on admin side only */
	
	/**
	 * @name getCat()
	 * @desc given category id returns title and description
	 * @param int $catid
	 * @return objectlist|null
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
	
	
	public static function getExtensionVersion($name) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('manifest_cache');
		$query->from($db->quoteName('#__extensions'));
		$query->where('element = ' . $db->quote($name) );
		$db->setQuery($query);			
		$manifest = json_decode($db->loadResult(), true);
		return $manifest['version'];
	}
	
}