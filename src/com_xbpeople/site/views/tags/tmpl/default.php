<?php 
/*******
 * @package xbPeople
 * @filesource site/views/tags/tmpl/default.php
 * @version 0.9.11.2 17th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HtmlHelper::_('behavior.multiselect');
HtmlHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
HtmlHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$xblink = 'index.php?option=com_xbpeople&view=';

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbpeopleHelperRoute::getTagsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$tvlink = $xblink.'tag'.$itemid.'&id=';

$itemid = XbpeopleHelperRoute::getPeopleRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$pllink = $xblink.'people'.$itemid.'&tagid=';

$itemid = XbpeopleHelperRoute::getCharsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$chllink = $xblink.'characters'.$itemid.'&tagid=';

$ctlink = 'index.php?option=com_tags&view=tag&id=';
?>

<div class="xbpeople">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo Route::_('index.php?option=com_xbpeople&view=tags'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbtags">	
			<thead>
				<tr>
					<th><?php echo HtmlHelper::_('grid.sort', 'XBCULTURE_TAG_U', 'title', $listDirn, $listOrder );?></th>
				<?php  if ($this->show_desc != 0) : ?>      
					<th class="hidden-phone"><?php echo JText::_('XBCULTURE_DESCRIPTION');?></th>
				<?php endif; ?>
				<th class="center" style="width:50px;"><?php echo HtmlHelper::_('grid.sort', 'XBCULTURE_PEOPLE_U', 'pcnt', $listDirn, $listOrder );?></th>
				<th class="center" style="width:50px;"><?php echo HtmlHelper::_('grid.sort', 'XBCULTURE_CHARS', 'chcnt', $listDirn, $listOrder );?></th>
				<th class="center" style="width:50px;"><?php echo HtmlHelper::_('grid.sort', 'Others', 'ocnt', $listDirn, $listOrder );?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : 
				//only show tags that are assigned to people or chars
				    if (($item->pcnt + $item->chcnt) > 0) : ?>
				<tr>
	 				<td>
						<p class="xbml10">
 						<?php  if ($this->show_tag_parent != 0) : ?>
 						    <span class="xbnote xb09">
 						    <?php if (substr_count($item->path,'/')>0) {
 						    	$ans = substr($item->path, 0, strrpos($item->path, '/'));
 						    	echo str_replace('/',' - ',$ans);
 						    } ?>
                        	</span><br />
						<?php endif; //show_tag_parent?>
	    				<span  class="xb11 xbbold">
	    					<a href="<?php echo Route::_($tvlink . $item->id); ?>" title="Details">
	    						<?php echo $item->title; ?>
	    					</a>
	    				</span>
	    				</p>
	    			</td>
				<?php  if ($this->show_desc != 0) : ?>      
					<td class="hidden-phone"><?php echo $item->description; ?></td>
				<?php endif; ?>
	    			<td class="center">
	   					<?php if ($item->pcnt >0) : ?> 
	   						<a class="badge percnt" href="<?php  echo $pllink.$item->id; ?>"><?php echo $item->pcnt; ?></a>
	   					<?php endif; ?>
	   				</td>
	    			<td class="center">
	   					<?php if ($item->chcnt >0) : ?> 
	   						<a class="badge percnt" href="<?php  echo $chllink.$item->id; ?>"><?php echo $item->chcnt; ?></a>
	   					<?php endif; ?>
	   				</td>
	    			<td class="center">
	   					<?php if (($item->ocnt >0) && (($item->pcnt >0) || ($item->chcnt >0)) ) : ?> 
	   						<a class="badge" href="<?php echo $ctlink.$item->id; ?>"><?php echo $item->ocnt; ?></a>
	   					<?php endif; ?>
	   				</td>
				</tr>
				
				<?php endif; endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo HtmlHelper::_('form.token'); ?>
	</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>
</div>
