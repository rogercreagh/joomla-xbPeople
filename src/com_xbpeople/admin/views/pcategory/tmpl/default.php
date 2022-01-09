<?php
/*******
 * @package xbPeople
 * @filesource admin/views/pcategory/tmpl/edit.php
 * @version 0.9.6.f 8th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\HTML\HTMLHelper;

$item = $this->item;
$xblink = 'index.php?option=com_xbpeople';
$celink = 'index.php?option=com_categories&task=category.edit&id=';
?>
<div class="row-fluid">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>
		<form action="index.php?option=com_xbpeople&view=pcategory" method="post" id="adminForm" name="adminForm">
		<div class="row-fluid xbmb8">
			<div class= "span3">
				  <h3><?php echo JText::_('XBCULTURE_CAT_ITEMS'); ?></h3>
			</div>
			<div class= "span5">
				<a href="<?php echo $celink.$item->id; ?>" class="badge badge-success">
					<h2><?php echo $item->title; ?></h2>
				</a></div>
            <div class="span2">
                <p><?php echo '<i>'.JText::_('XBCULTURE_ALIAS').'</i>: '.$item->alias; ?></p>
            </div>
			<div class= "span2">
				<p><?php echo '<i>'.JText::_('JGRID_HEADING_ID').'</i>: '.$item->id; ?></p>
 			</div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span6">
					<p class="xb11">
						<i><?php JText::_('XBCULTURE_CATEGORY').' '.JText::_('XBCULTURE_HEIRARCHY'); ?></i> 
						<?php $path = str_replace('/', ' - ', $item->path);
						echo 'root - '.$path; ?>
					</p>
			</div>
			<div class= "span6">
				<p><i><?php Jtext::_('XBCULTURE_ADMIN_NOTE'); ?>:</i>  <?php echo $item->note; ?></p>
			</div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span2">
				<p><i><?php echo JText::_('XBCULTURE_DESCRIPTION'); ?>:</i></p>
			</div>
   			<div class="span10">
			<?php if ($item->description != '') : ?>
     			<div class="xbbox xbboxgrey" style="max-width:400px;">
    				<?php echo $item->description; ?>
    			</div>
    		<?php else: ?>
    			<p><i><?php echo JText::_('XBCULTURE_NO_DESCRIPTION'); ?></i></p>
			<?php endif; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class= "span6">
				<div class="xbbox xbboxgrn">
					<p><?php echo $item->pcnt.' '.JText::_('XBCULTURE_PEOPLE_IN_CAT'); ?>  <span class="label label-success"><?php echo $item->title; ?></span></p>
					<?php if ($item->pcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->people as $i=>$per) { 
							echo '<li><a href="'.$xblink.'&view=person&task=person.edit&id='.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>
			<div class= "span6">
				<div class="xbbox xbboxgrey">
					<p><?php echo $item->chcnt.' '.JText::_('XBCULTURE_CHARS_IN_CAT'); ?>  <span class="label label-success"><?php echo $item->title; ?></span></p>
					<?php if ($item->chcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->chars as $i=>$char) { 
							echo '<li><a href="'.$xblink.'&view=character&task=character.edit&id='.$char->pid.'">'.$char->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="tid" value="<?php echo $item->id;?>" />
		<?php echo HtmlHelper::_('form.token'); ?>
		</form>
	</div>
</div>
<center>
		<a href="<?php echo $xblink; ?>&view=pcategories" class="btn btn-small">
			<?php echo JText::_('XBCULTURE_CAT_LIST'); ?></a>
		</center>
<div class="clearfix"></div>
<p><?php echo XbpeopleHelper::credit();?></p>

