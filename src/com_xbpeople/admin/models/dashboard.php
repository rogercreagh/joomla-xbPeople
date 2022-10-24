<?php
/*******
 * @package xbPeople
 * @filesource admin/models/dashboard.php
 * @version 0.4.6 4th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
//use Joomla\CMS\Table\Observer\Tags;

class XbpeopleModelDashboard extends JModelList {
	    
    protected $xbbooks_ok;
    protected $xbfilms_ok;
    
    public function __construct() {
        //$this->xbbooksStatus = XbfilmsGeneral::checkComponent('com_xbbooks');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok',false);
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok',false);
    	parent::__construct();
    }
    
    
    public function getPcatStates() {
     	return $this->stateCnts('#__categories','published');
    }
        
    public function getPerStates() {
     	return $this->stateCnts('#__xbpersons');
    }
    
    public function getCharStates() {
    	return $this->stateCnts('#__xbcharacters');
    }
                
    public function getBookPeople() {
        if ($this->xbbooks_ok) {
            //$db = $this->getDbo();
        	$db = Factory::getDbo();
        	$query = $db->getQuery(true);
        	$query->select('COUNT(DISTINCT person_id)')
        	->from('#__xbbookperson');
        	$db->setQuery($query);
        	return $db->loadResult();
        }
        return '';
}
    
    public function getFilmPeople() {
        if ($this->xbfilms_ok) {
            //$db = $this->getDbo();
        	$db = Factory::getDbo();
        	$query = $db->getQuery(true);
        	$query->select('COUNT(DISTINCT person_id)')
        	->from('#__xbfilmperson');
        	$db->setQuery($query);
        	return $db->loadResult();
        }
        return '';
    }
    
    public function getBookChars() {
        if ($this->xbbooks_ok) {
            //$db = $this->getDbo();
        	$db = Factory::getDbo();
        	$query = $db->getQuery(true);
        	$query->select('COUNT(DISTINCT char_id)')
        	->from('#__xbbookcharacter');
        	$db->setQuery($query);
        	return $db->loadResult();
        }
        return '';
}
    
    public function getFilmChars() {
        if ($this->xbfilms_ok) {
            //$db = $this->getDbo();
        	$db = Factory::getDbo();
        	$query = $db->getQuery(true);
        	$query->select('COUNT(DISTINCT char_id)')
        	->from('#__xbfilmcharacter');
        	$db->setQuery($query);
        	return $db->loadResult();
        }
        return '';
    }
    
    public function getPeopleCats() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*')
        ->select('(SELECT COUNT(*) FROM #__xbcharacters AS c WHERE c.catid=a.id) AS chrcnt')
        ->select('(SELECT COUNT(*) FROM #__xbpersons AS p WHERE p.catid=a.id) AS percnt')
        ->from('#__categories AS a')
        ->where('a.extension = '.$db->quote("com_xbpeople"))
        ->order($db->quoteName('path') . ' ASC');
        $db->setQuery($query);
        return $db->loadAssocList('alias');
    }
    
    public function getClient() {
    	$result = array();
    	$client = Factory::getApplication()->client;
    	$class = new ReflectionClass('Joomla\Application\Web\WebClient');
    	$constants = array_flip($class->getConstants());
    	
    	$result['browser'] = $constants[$client->browser].' '.$client->browserVersion;
    	$result['platform'] = $constants[$client->platform].($client->mobile ? ' (mobile)' : '');
    	$result['mobile'] = $client->mobile;
    	return $result;   	
    }
    
    public function getTagcnts() {
    	//nedds rewrite 
    	$result = array('tagcnts' => array('percnt' => 0, 'charcnt' => 0), 'tags' => array(), 'taglist' => '' );
    	$db = Factory::getDbo();
    	$query =$db->getQuery(true);
    	//first we get the total number of each type of item with one or more tags
    	$query->select('type_alias,core_content_id, COUNT(*) AS numtags')
    	->from('#__contentitem_tag_map')
    	->where('type_alias LIKE '.$db->quote('com_xbfilms%'))
    	->group('core_content_id, type_alias');
    	//not checking that tag is published, not using numtags at this stage - poss in future
    	$db->setQuery($query);
    	$db->execute();
    	$items = $db->loadObjectList();
    	foreach ($items as $it) {
    	    switch ($it->type_alias) {
    	        case 'com_xbfilms.person':
    	            $result['tagcnts']['percnt'] ++;
    	            break;
    	        case 'com_xbfilms.character':
    	            $result['tagcnts']['charcnt'] ++;
    	            break;
    	    }
    	}
    	//now we get the number of each type of item assigned to each tag
    	$query->clear();
    	$query->select('type_alias,t.id, t.title AS tagname ,COUNT(*) AS tagcnt')
    	->from('#__contentitem_tag_map')
    	->join('LEFT', '#__tags AS t ON t.id = tag_id')
    	->where('type_alias LIKE '.$db->quote('%xbfilms%'))
    	->where('t.published = 1') //only published tags
    	->group('type_alias, tagname');
    	$db->setQuery($query);
    	$db->execute();
    	$tags = $db->loadObjectList();
    	foreach ($tags as $k=>$t) {
    	    if (!key_exists($t->tagname, $result['tags'])) {
    	        $result['tags'][$t->tagname]=array('id' => $t->id, 'tbcnt' =>0, 'tpcnt' => 0, 'tccnt' => 0, 'trcnt' => 0, 'tagcnt'=>0);
    	    }
    	    $result['tags'][$t->tagname]['tagcnt'] += $t->tagcnt;
    	    switch ($t->type_alias) {
    	        case 'com_xbfilms.film' :
    	            $result['tags'][$t->tagname]['tbcnt'] += $t->tagcnt;
    	            break;
    	        case 'com_xbfilms.person':
    	            $result['tags'][$t->tagname]['tpcnt'] += $t->tagcnt;
    	            break;
    	        case 'com_xbfilms.character':
    	            $result['tags'][$t->tagname]['tccnt'] += $t->tagcnt;
    	            break;
    	        case 'com_xbfilms.review':
    	            $result['tags'][$t->tagname]['trcnt'] += $t->tagcnt;
    	            break;
    	    }
    	}
    	return $result;
    }
    
    
    private function stateCnts(string $table, string $colname = 'state', string $ext='com_xbpeople') {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT a.'.$colname.', a.alias')
        ->from($db->quoteName($table).' AS a');
        if ($table == '#__categories') {
            $query->where('extension = '.$db->quote($ext));
        }
        if ($table == '#__xbpersons') {
        }
        if ($table == '#__xbcharacters') {
        }
        $db->setQuery($query);
        $col = $db->loadColumn();
        $vals = array_count_values($col);
        $result['total'] = count($col);
        $result['published'] = key_exists('1',$vals) ? $vals['1'] : 0;
        $result['unpublished'] = key_exists('0',$vals) ? $vals['0'] : 0;
        $result['archived'] = key_exists('2',$vals) ? $vals['2'] : 0;
        $result['trashed'] = key_exists('-2',$vals) ? $vals['-2'] : 0;
        return $result;
    }
		
}	
