<?php
/*******
 * @package xbPeople
 * @filesource admin/models/dashboard.php
 * @version 1.0.0.10 31st December 2022
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
    protected $xbevents_ok;
    
    public function __construct() {
        //$this->xbbooksStatus = XbfilmsGeneral::checkComponent('com_xbbooks');
        $this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok',false);
        $this->xbevents_ok = Factory::getSession()->get('xbevents_ok',false);
        $this->xbfilms_ok = Factory::getSession()->get('xbfilms_ok',false);
        parent::__construct();
    }
    
    
    public function getPcatStates() {
     	return $this->stateCnts('#__categories','published');
    }
        
    public function getPerStates() {
        return $this->stateCnts('#__xbpersons');
    }
    
    public function getGroupStates() {
        return $this->stateCnts('#__xbgroups');
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
    
    public function getEventPeople() {
        if ($this->xbevents_ok) {
            //$db = $this->getDbo();
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('COUNT(DISTINCT person_id)')
            ->from('#__xbeventperson');
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
    
    public function getBookGroups() {
//         if ($this->xbbooks_ok) {
//             //$db = $this->getDbo();
//             $db = Factory::getDbo();
//             $query = $db->getQuery(true);
//             $query->select('COUNT(DISTINCT group_id)')
//             ->from('#__xbbookgroup');
//             $db->setQuery($query);
//             return $db->loadResult();
//         }
        return '';
    }
    
    public function getEventGroups() {
        if ($this->xbevents_ok) {
            //$db = $this->getDbo();
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('COUNT(DISTINCT group_id)')
            ->from('#__xbeventgroup');
            $db->setQuery($query);
            return $db->loadResult();
        }
        return '';
    }
    
    public function getFilmGroups() {
//         if ($this->xbfilms_ok) {
//             //$db = $this->getDbo();
//             $db = Factory::getDbo();
//             $query = $db->getQuery(true);
//             $query->select('COUNT(DISTINCT group_id)')
//             ->from('#__xbfilmperson');
//             $db->setQuery($query);
//             return $db->loadResult();
//         }
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
    
    public function getEventChars() {
        if ($this->xbevents_ok) {
            //$db = $this->getDbo();
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('COUNT(DISTINCT char_id)')
            ->from('#__xbeventcharacter');
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
        ->select('(SELECT COUNT(*) FROM #__xbgroups AS g WHERE g.catid=a.id) AS grpcnt')
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
        //we need number of people, chars & groups tagged, number of distinct tags used for people, chars & groups
        $result = array('peeptagged' => 0, 'groupstagged' =>0, 'charstagged' => 0,
            'tagspeep' => 0, 'tagsgroups' => 0, 'tagschars' => 0 );
        
        $result['peeptagged'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.person','');
        $result['groupstagged'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.group','');
        $result['charstagged']= XbcultureHelper::getTagtypeItemCnt('com_xbpeople.character','');
        $result['tagspeep'] = XbcultureHelper::getTagtypeTagCnt('com_xbpeople.person','');
        $result['tagsgroups'] = XbcultureHelper::getTagtypeTagCnt('com_xbpeople.group','');
        $result['tagschars']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.character','');
        return $result;
        
//         $result = array('bookper' => 0, 'filmper' =>0, 'eventper' => 0, 'allper' => 0, 
//     	    'bookchar' => 0, 'filmchar' => 0, 'eventchar' => 0, 'allchar' => 0,
//     	    'bookpertags' => 0, 'bookchartags' => 0, 'allbook' => 0,
//     	    'filmpertags' => 0, 'filmchartags' => 0, 'allfilm' => 0,);
    	
//     	if ($this->xbbooks_ok) {
//         	$result['bookper'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.person','book');
//         	$result['bookchar'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.character','book');
//         	$result['bookpertags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.person','book');
//         	$result['bookchartags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.character','book');    	    
//     	}
//     	if ($this->xbevents_ok) {
//     	    $result['eventper'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.person','book');
//     	    $result['eventchar'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.character','book');
//     	    $result['bookpertags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.person','book');
//     	    $result['bookchartags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.character','book');
//     	}
//     	if ($this->xbfilms_ok) {
//         	$result['filmper'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.person','film');
//         	$result['filmchar'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.character','film');
//         	$result['filmpertags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.person','film');
//         	$result['filmchartags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.character','film');    	    
//     	}
//     	$result['allper'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.person','');
//     	$result['allchar'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.character','');
//     	return $result;
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
