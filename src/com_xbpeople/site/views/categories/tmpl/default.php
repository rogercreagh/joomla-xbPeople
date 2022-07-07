<?php 
/*******
 * @package xbPeople
 * @filesource site/views/categories/tmpl/default.php
 * @version 0.9.9.1 1st July 2022
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

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbpeopleHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbpeople&view=category'.$itemid.'&id=';

$plink='index.php?option=com_xbpeople&view=people&catid=';
$chlink='index.php?option=com_xbpeople&view=characters&catid=';

?>
<div class="xbpeople">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo Route::_('index.php?option=com_xbpeople&view=categories'); ?>" method="post" name="adminForm" id="adminForm">

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbcats">	
			<thead>
				<tr>
					<th><?php echo HtmlHelper::_('grid.sort', 'XBCULTURE_TITLE', 'title', $listDirn, $listOrder );?></th>
					<th class="hidden-phone"><?php echo JText::_('XBCULTURE_DESCRIPTION');?></th>
					<th class="center" style="width:50px;"><?php echo HtmlHelper::_('grid.sort', 'XBCULTURE_PEOPLE_U', 'bpcnt', $listDirn, $listOrder );?></th>
					<th class="center" style="width:50px;"><?php echo HtmlHelper::_('grid.sort', 'XBCULTURE_CHARACTERS_U', 'bchcnt', $listDirn, $listOrder );?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
        		<tr>
	 				<td>
						<p class="xbml20">
 						<?php  if ($this->show_parent != 0) : ?>      
					<span class="xbnote"> 
 					<?php 	$path = substr($item->path, 0, strrpos($item->path, '/'));
						$path = str_replace('/', ' - ', $path);
						echo $path.($path!='') ? ' - <br/>' : ''; ?>
						
					 </span>
						<?php endif; //show_parent?>
    					<a href="<?php echo Route::_($clink . $item->id.'&ext='.$item->extension); ?>" title="Details" 
    						class="label label-success" style="padding:2px 8px;">
    						<span class="xb11"><?php echo $item->title; ?></span>
    					</a>
	    				</p>
	    			</td>
					<td class="hidden-phone"><?php echo $item->description; ?></td>
	    			<td class="center">
	   					<?php if ($item->bpcnt >0) : ?> 
	   						<a href="<?php echo $bvlink.$item->id; ?>" class="badge percnt"><?php echo $item->bpcnt; ?></a></span>
	   					<?php endif; ?>
	   				</td>
	    			<td class="center">
	   					<?php if ($item->bchcnt >0) : ?> 
	   						<a href="<?php echo $clink.$item->id; ?>" class="badge chcnt"><?php echo $item->bchcnt; ?></a></span>
	   					<?php endif; ?>
	   				</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	<?php endif; //got items?>
		
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo HtmlHelper::_('form.token'); ?>
	</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>
</div>