<?php
/*******
 * @package xbPeople
 * @filesource admin/views/tags/tmpl/default.php
 * @version 0.4.2 21st March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId         = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));

$telink = 'index.php?option=com_tags&view=tag&task=tag.edit&id=';
$tvlink = 'index.php?option=com_xbfilms&view=tag&id=';
$bvlink = 'index.php?option=com_xbfilms&view=films&tagid=';
$rvlink = 'index.php?option=com_xbfilms&view=reviews&tagid=';
$pvlink = 'index.php?option=com_xbfilms&view=persons&tagid=';
$chvlink = 'index.php?option=com_xbfilms&view=characters&tagid=';
?>
<form action="index.php?option=com_xbpeople&view=tags" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
	
	<div>
      <h3><?php echo JText::_('COM_XBFILMS_TAGSPAGE_TITLE'); ?></h3>
      <p><?php echo JText::_('COM_XBFILMS_TAGSPAGE_INFO'); ?></p>
      </div>
	
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. JText::_(($fnd==1)?'COM_XBFILMS_ONETAG':'COM_XBFILMS_MANYTAGS').' '.JText::_('COM_XBFILMS_FOUND'); ?>
		</p>
	</div>
	<div class="clearfix"></div>

	<?php
        // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	

<table class="table table-striped table-hover">
<thead>
<tr>
					<th class="hidden-phone center" style="width:25px;">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_XBFILMS_CAPTAG', 'path', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo JText::_('COM_XBFILMS_CAPDESCRIPTION') ;?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_XBFILMS_CAPPEOPLE', 'pcnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_XBFILMS_CAPCHARACTERS', 'chcnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo JText::_('COM_XBFILMS_CAPOTHERS') ;?>
			</th>
			<th class="nowrap hidden-tablet hidden-phone" style="width:45px;">
				<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCheckin = $user->authorise('core.manage', 'com_checkin')
			?>
			<tr class="row<?php echo $i % 2; ?>" >	
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
				<td class="center">
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'tag.', false, 'cb'); ?>
							<?php if ($item->note!=''){ ?>
								<span class="btn btn-micro active hasTooltip" title="" 
									data-original-title="<?php echo '<b>'.JText::_( 'COM_XBFILMS_CAPNOTE' ) .'</b>: '. htmlentities($item->note); ?>">
									<i class="icon- xbinfo"></i>
								</span>
							<?php } else {?>
								<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
							<?php } ?>
					</div>
				</td>
 				<td>
					<?php if ($item->checked_out) {
    					$couname = Factory::getUser($item->checked_out)->username;
    					echo JHtml::_('jgrid.checkedout', $i, JText::_('COM_XBFILMS_OPENEDBY').': '.$couname, $item->checked_out_time, 'tags.', false);
    				} ?>
					<span class="xbnote"> 
 					<?php 	$path = substr($item->path, 0, strrpos($item->path, '/'));
						$path = str_replace('/', ' - ', $path);
						echo $path; ?>
					  - </span>    				
    				<a href="<?php echo JRoute::_($tvlink . $item->id); ?>" title="Details" 
    					class="label label-info" style="padding:4px 8px;">
    					<span class="xb12"><?php echo $item->title; ?></span>
    				</a>
     			</td>
    			<td>
    				<p class="xb09"><?php echo $item->description; ?></p>
    			</td>
    			<td align="center">
   					<?php if ($item->pcnt >0) : ?> 
   						<span class="badge percnt">
   							<a href="<?php echo $pvlink.$item->id;?>"><?php echo $item->pcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->chcnt >0) : ?>
   						<span class="badge chcnt">
   							<a href="<?php echo $chvlink.$item->id;?>"><?php echo $item->chcnt; ?>  						
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->othcnt >0) : ?>
   						<span class="badge othcnt">
   							<a href="<?php echo $tvlink.$item->id;?>"><?php echo $item->othcnt; ?>  						
   						</a></span>
   					<?php endif; ?>
   				</td>
  				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbpeopleHelper::credit();?></p>

