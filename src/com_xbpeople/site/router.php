<?php
/*******
 * @package xbPeople
 * @filesource site/router.php
 * @version 0.9.8.9 10th June 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;

class XbpeopleRouter extends JComponentRouterBase {
    
	public function build(&$query)
	{
		//      Factory::getApplication()->enqueueMessage('<pre>'.print_r($query,true).'</pre>','build');
		$segments = array();
		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			unset($query['view']);
		}
		if ( (!empty($segments)) && (isset($query['id'])) )
		{
			$db = Factory::getDbo();
			$qry = $db->getQuery(true);
			$qry->select('alias');
			switch($segments[0])
			{
				case 'person':
					$qry->from('#__xbpersons');
					break;
				case 'character':
					$qry->from('#__xbcharacters');
					break;
				case 'category':
					$qry->from('#__categories');
					break;
				case 'tag':
				    $qry->from('#__tags');
				    break;
			}
			$qry->where('id = ' . $db->quote($query['id']));
			$db->setQuery($qry);
			$alias = $db->loadResult();
			$segments[] = $alias;
			unset($query['id']);
		}
		return $segments;
	}
	
	public function parse(&$segments)
	{
		$vars = array();
		
		$db = Factory::getDbo();
		$qry = $db->getQuery(true);
		$qry->select('id');
		switch($segments[0])
		{
			case 'people':
				$vars['view'] = 'people';
				break;
			case 'characters':
				$vars['view'] = 'characters';
				break;
			case 'categories':
				$vars['view'] = 'categories';
				break;
			case 'tags':
			    $vars['view'] = 'tags';
			    break;
			case 'person':
				$vars['view'] = 'person';
				$qry->from('#__xbpersons');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'character':
			    $vars['view'] = 'character';
			    $qry->from('#__xbcharacters');
			    $qry->where('alias = ' . $db->quote($segments[1]));
			    $db->setQuery($qry);
			    $id = $db->loadResult();
			    $vars['id'] = (int) $id;
			    break;
			case 'category':
				$app= Factory::getApplication();
				$ext = $app->input->get('ext');
				if ($ext=='') {$ext='com_xbpeople'; }
				$vars['view'] = 'category';
				$qry->from('#__categories');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$qry->where('extension = ' . $db->quote($ext));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'tag':
			    $vars['view'] = 'tag';
			    $qry->from('#__tags');
			    $qry->where('alias = ' . $db->quote($segments[1]));
			    $db->setQuery($qry);
			    $id = $db->loadResult();
			    $vars['id'] = (int) $id;
			    break;
		}
				
		return $vars;
	}
	
	public function preprocess($query)
	{
		return $query;
	}
	
}
