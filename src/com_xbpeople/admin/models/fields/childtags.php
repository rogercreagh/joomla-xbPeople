<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/childtags.php
 * @version 0.9.10.0 11th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * except where notified code from joomla3-/libraires/src/Form/Field/TagField.php
 * Joomla! Content Management System
 *
 * @copyright  (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Utilities\ArrayHelper;

FormHelper::loadFieldClass('list');

/**
 * List of Tags field.
 *
 * @since  3.1
 */
class JFormFieldChildtags extends Joomla\CMS\Form\Field\TagField 
{
	/**
	 * An extension to the built in TagField to allow limiting selection to children of a specified parent
	 */
	public $type = 'Childtags';

	/**
	 * Method to get a list of tags
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.1
	 * 
	 * Modified Roger C-O Nov 2022 to allow options to limit values to children of a specified tag.
	 * Additional elements on the childtag
	 *     component - the component in whose options the parent tag is specified
	 *     tagoption - the id of the parent tag specified in a tag field of this name in the component options
	 */
	protected function getOptions()
	{
        $published = (string) $this->element['published'] ?: array(0, 1);		
//		$component = (string) $this->element['component'];
//		$tagoption = (string) $this->element['tagoption'];
		
		$parent_id = 0;
		$parent_definition = (string) $this->element['parent_definition'];
		if ($parent_definition && (substr($parent_definition,0,4) == 'com_'))  {
		    //for php8 use str_starts_with(string $haystack, string $needle): bool
		    $parent_definition = explode('.',$parent_definition);
		    $params = ComponentHelper::getParams($parent_definition[0]);
		    if ($params) $parent_id = $params->get($parent_definition[1],1);		    
		}

        $app       = Factory::getApplication();
		$tag       = $app->getLanguage()->getTag();

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT a.id AS value, a.path, a.title AS text, a.level, a.published, a.lft')
			->from('#__tags AS a')
			->join('LEFT', $db->qn('#__tags') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		
		// Limit options to only children of parent
	    if ($parent_id > 1) {
	        $query->where('b.id = '. $parent_id);
	    }
			
		// Limit Options in multilanguage
		if ($app->isClient('site') && Multilanguage::isEnabled())
		{
			$lang = ComponentHelper::getParams('com_tags')->get('tag_list_language_filter');

			if ($lang == 'current_language')
			{
				$query->where('a.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}
		}
		// Filter language
		elseif (!empty($this->element['language']))
		{
			if (strpos($this->element['language'], ',') !== false)
			{
				$language = implode(',', $db->quote(explode(',', $this->element['language'])));
			}
			else
			{
				$language = $db->quote($this->element['language']);
			}

			$query->where($db->quoteName('a.language') . ' IN (' . $language . ')');
		}

		$query->where($db->qn('a.lft') . ' > 0');

		// Filter on the published state
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif (is_array($published))
		{
			$published = ArrayHelper::toInteger($published);
			$query->where('a.published IN (' . implode(',', $published) . ')');
		}

		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			return array();
		}

		// Block the possibility to set a tag as it own parent
		if ($this->form->getName() === 'com_tags.tag')
		{
			$id   = (int) $this->form->getValue('id', 0);

			foreach ($options as $option)
			{
				if ($option->value == $id)
				{
					$option->disable = true;
				}
			}
		}

		// Merge any additional options in the XML definition.
          $grandparent = $this->get_grandparent_class($this);
          $options = ($grandparent) ? array_merge($grandparent::getOptions(), $options) : $options;
        // 
        //$options = array_merge(get_parent_class(get_parent_class(get_class($this)))::getOptions(), $options);   

		// Prepare nested data
		if ($this->isNested())
		{
			$this->prepareOptionsNested($options);
		}
		else
		{
			$options = TagsHelper::convertPathsToNames($options);
		}

		return $options;
	}

    /**
    * Get the grand parent class of the specified class
    *
    * @param $currentClass
    * @return string
    */
    private function get_grandparent_class($currentClass)
    {
        if (is_object($currentClass)) {
            $currentClass = get_class($currentClass);
        }
        return get_parent_class(get_parent_class($currentClass));      
    }

}
