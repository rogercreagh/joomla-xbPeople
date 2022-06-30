<?php
/*******
 * @package xbPeople
 * @filesource site/views/person/tmpl/default.php
 * @version 0.9.9.0 29th June 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$item = $this->item;

$imgok = (($this->show_image >0) && (JFile::exists(JPATH_ROOT.'/'.$item->portrait)));
if ($imgok) {
	$src = Uri::root().$item->portrait;
	$tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
}

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbpeopleHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbbooks&view=category' . $itemid.'&id=';

?>
<div class="xbbooks">
<div class="row-fluid">
	<?php if ($imgok && ($this->show_image == 1)) : ?>
		<div class="span2 xbmb12">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>
	<div class="span<?php echo $imgok==true ? '10' : '12'; ?>">
		<div class="xbbox xbboxgrn">
			<h3><?php echo $item->firstname; ?> <?php echo $item->lastname; ?>
			</h3>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<?php if (($item->nationality != '') || (!$this->hide_empty)) : ?>
					<p><span class="xbnit"><?php echo Text::_('XBCULTURE_NATIONALITY').': '; ?> </span>
					<?php if ($item->nationality == '') {
					    echo Text::_('XBCULTURE_UNKNOWN');
					} else {
						echo $item->nationality; 
					} ?>
					</p>
				<?php endif; ?>
				<p>
				<?php if (($item->year_born != 0) || (!$this->hide_empty)) : ?>
					<span class="xbnit "><?php echo ucfirst(Text::_('XBCULTURE_BORN')).': '; ?> </span>
					<?php if ($item->year_born == 0) {
					    echo Text::_('XBCULTURE_UNKNOWN');
					} else {
					    echo $item->year_born; 
					} ?>
					&nbsp;&nbsp;
				<?php endif; ?>
				<?php if (($item->year_died != 0) || (!$this->hide_empty)) : ?>
					<span class="xbnit "><?php echo ucfirst(Text::_('XBCULTURE_DIED')).': '; ?> </span>
					<?php if ($item->year_died == 0) {
					    echo Text::_('XBCULTURE_UNKNOWN');
					} else {
					    echo $item->year_died; 
					} ?>
				<?php endif; ?>
				</p>
				
		<div class="pull-left"><b><?php echo ucfirst(Text::_('XBCULTURE_BOOKS')); ?></b></div>	
		<div class="clearfix"></div>
	<?php if ($item->bookcnt>0) : ?>
		<p class="xbnit">
		<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->bookcnt.' '.Text::_('XBCULTURE_BOOKS'); ?>
		</p>
		<?php echo $this->booklist; ?>
	<?php endif; ?>
	<?php if ($item->filmcnt>0) : ?>
		<p class="xbnit">
		<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->filmcnt.' '.Text::_('XBCULTURE_FILMS'); ?>
		</p>
	<?php endif; ?>
	<?php if ($item->ext_links_cnt > 0) : ?>
			<div class="xbnit xbmb8"><?php echo Text::_('XBBOOKS_EXT_LINKS'); ?></div>
			<div>			
				<?php echo $item->ext_links_list; ?>
			</div><div class="clearfix"></div>
	<?php endif; ?>
			</div>
			<div class="span6">
				<?php if (($item->summary != '' ) && (!empty($item->biography))) : ?>
					<div class="xbnit xbmb8"><?php echo Text::_('XBCULTURE_SUMMARY'); ?></div>
					<div class="xbbox xbboxwht">
					 	<?php echo $item->summary; ?>
					</div>
				<?php  endif;?>
				<p>&nbsp;</p>
				<?php if ((empty($item->biography)) && (trim($item->summary) == '')) : ?>
					<p class="xbnit"><?php echo Text::_('XBBOOKS_NO_BIOG'); ?></p>
				<?php else : ?>
					<div class="xbnit xbmb8"><?php echo Text::_('XBCULTURE_BIOGRAPHY');?></div>
					<div class="xbbox xbboxgrn">
						<?php if (!empty($item->biography)) {
					    	echo $item->biography;
						} else {
	            			echo $item->summary; 
						} ?>
					</div>
				<?php  endif; ?>
			</div>
		</div>
	</div>
	<?php if ($imgok && ($this->show_image == 2)) : ?>
		<div class="span2 xbmb12">
			<img class="img-polaroid hasTooltip xbimgthumb" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>		
</div>	
<div class="row-fluid xbmt16">
	<?php if ($this->show_cat) : ?>
		<div class="span5">
			<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBCULTURE_CATEGORY'); ?></div>
			<div class="pull-left">
				<?php if ($this->show_cat==2) : ?>
					<a href="<?php echo $clink.$item->catid; ?>" class="label label-success"><?php echo $item->category_title; ?></a>
				<?php else : ?>
    				<span class="label label-success"><?php  echo $item->category_title; ?></span>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php endif; ?>
	<?php if(($this->show_tags) && (!empty($item->tags))) : ?>
		<div class="span<?php $this->showcat ? '7' : '12';?>">
			<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBCULTURE_TAGS_U'); ?></div>
			<div class="pull-left">
				<?php  $tagLayout = new FileLayout('joomla.content.tags');
			    	echo $tagLayout->render($item->tags);
			    ?>
			</div>	
		</div>
	<?php endif; ?>			
</div>
<div class="row-fluid">
	<div class="span12 xbbox xbboxgrey">
		<div class="row-fluid">
			<div class="span2">
				<?php if (($item->prev>0) || ($item->next>0)) : ?>
				<span class="xbpop xbcultpop xbinfo fas fa-info-circle" data-trigger="hover" title 
					data-original-title="Prev-Next Info" data-content="<?php echo Text::_('XBBOOKS_INFO_PREVNEXT'); ?>" >
				</span>&nbsp;
				<?php endif; ?>
				<?php if($item->prev > 0) : ?>
					<a href="index.php?option=com_xbbooks&view=person&id=<?php echo $item->prev ?>" class="btn btn-small">
						<?php echo Text::_('XBCULTURE_PREV'); ?></a>
			    <?php endif; ?>
			</div>
			<div class="span8 xbtc">
				<a href="index.php?option=com_xbbooks&view=people" class="btn btn-small">
					<?php echo Text::_('People List'); ?></a>
			</div>
			<div class="span2">
			<?php if($item->next > 0) : ?>
				<a href="index.php?option=com_xbbooks&view=person&id=<?php echo $item->next ?>" class="btn btn-small pull-right">
					<?php echo Text::_('XBCULTURE_NEXT'); ?></a>
		    <?php endif; ?>
			</div>
	      </div>
      </div>
</div>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>
</div>
