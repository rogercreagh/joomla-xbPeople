<?php
/*******
 * @package xbPeople
 * @filesource site/views/person/tmpl/default.php
 * @version 0.9.9.0 30th June 2022
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
$clink = 'index.php?option=com_xbpeople&view=category' . $itemid.'&id=';

?>
<div class="xbpeople">
<div class="xbbox xbboxgrn">
	<div class="row-fluid">
		<?php if ($imgok && ($this->show_image == 1 )) : ?>
			<div class="span2">
				<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
					data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
			</div>
		<?php endif; ?>
		<div class="<?php echo $imgok==true ? 'span10' : 'span12'; ?>">
			<div class="pull-right xbmr20" style="text-align:right;">
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
				<?php endif; ?>
				<br />
				<?php if (($item->year_died != 0) || (!$this->hide_empty)) : ?>
					<span class="xbnit "><?php echo ucfirst(Text::_('XBCULTURE_DIED')).': '; ?> </span>
					<?php if ($item->year_died == 0) {
					    echo Text::_('XBCULTURE_UNKNOWN');
					} else {
					    echo $item->year_died; 
					} ?>
				<?php endif; ?>
				</p>
			</div>
			<h3><?php echo $item->firstname; ?> <?php echo $item->lastname; ?>
			</h3>
		</div>
		<?php if ($imgok && ($this->show_image == 2 )) : ?>
			<div class="span2">
				<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
			</div>
		<?php endif; ?>
	</div>
</div>
<?php if (trim($item->summary) != '') : ?>
    <div class="row-fluid">
    	<div class="span2"></div>
    	<div class="span8">
			<div class="xbbox xbboxwht">
				<div class="pull-left">
					<span class="xbnit"><?php echo Text::_('XBCULTURE_SUMMARY'); ?>  : </span>
				</div>
				<div><?php echo $item->summary; ?></div> 
			</div>
		</div>
		<div class="span2"></div>
	</div>
<?php  endif;?>
<div class="row-fluid">
	<?php if ($item->bookcnt>0) : ?>
    	<div class="span<?php echo $item->filmcnt>0 ? '6' : '12'; ?>">
    		<p><b><?php echo ucfirst(Text::_('XBCULTURE_BOOKS')); ?></b>
    			<span class="xbnit">
    				<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->bookcnt;
    				echo ($item->bookcnt == 1) ? Text::_('XBCULTURE_BOOK') : Text::_('XBCULTURE_BOOKS'); ?>
    			</span>
    		</p>
    		<?php echo $this->booklist; ?>
    	</div>
	<?php endif; ?>
	<?php if ($item->filmcnt>0) : ?>
    	<div class="span<?php echo $item->bookcnt>0 ? '6' : '12'; ?>">
    		<p><b><?php echo ucfirst(Text::_('XBCULTURE_FILMS')); ?></b>
    			<span class="xbnit">
    				<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->filmcnt.' '.Text::_('XBCULTURE_FILMS'); ?>
    			</span>
    		</p>
    		<?php echo $this->filmlist; ?>
    	</div>
	<?php endif; ?>
</div>
<?php  if (!empty($item->biography)) :?>
	<div class="xbnit xbmb8"><?php echo Text::_('XBCULTURE_BIOGRAPHY');?></div>
    <div class="xbbox xbboxgrn">
    	<?php echo $item->biography; ?>
    </div>
<?php else: ?>
	<?php if (!$this->hide_empty) {
	   echo '<p class="xbnit">'.Text::_('XBCULTURE_NO_BIOG').'</p>';
	} ?>
<?php endif; ?>
<?php if ($item->ext_links_cnt > 0) : ?>
		<div class="xbnit xbmb8"><?php echo Text::_('XBCULTURE_EXTLINK_LBL'); ?></div>
		<div>			
			<?php echo $item->ext_links_list; ?>
		</div><div class="clearfix"></div>
<?php endif; ?>
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
		<div class="span<?php echo ($this->show_cat) ? '7' : '12';?>">
			<div class="pull-left xbnit xbmr10"><?php echo ucfirst(Text::_('XBCULTURE_TAGS')); ?></div>
			<div class="pull-left">
				<?php  $tagLayout = new FileLayout('joomla.content.tags');
			    	echo $tagLayout->render($item->tags);
			    ?>
			</div>	
			<div class="clearfix"></div>
		</div>
	<?php endif; ?>			
</div>
<div class="row-fluid">
	<div class="span12 xbbox xbboxgrey">
		<div class="row-fluid">
			<div class="span2">
				<?php if (($item->prev>0) || ($item->next>0)) : ?>
				<span class="xbpop xbcultpop xbinfo fas fa-info-circle" data-trigger="hover" title 
					data-original-title="Prev-Next Info" data-content="<?php echo Text::_('XBCULTURE_INFO_PREVNEXT'); ?>" >
				</span>&nbsp;
				<?php endif; ?>
				<?php if($item->prev > 0) : ?>
					<a href="index.php?option=com_xbpeople&view=person&id=<?php echo $item->prev ?>" class="btn btn-small">
						<?php echo Text::_('XBCULTURE_PREV'); ?></a>
			    <?php endif; ?>
			</div>
			<div class="span8 xbtc">
				<a href="index.php?option=com_xbpeople&view=people" class="btn btn-small">
					<?php echo Text::_('XBCULTURE_PEOPLE_LIST'); ?></a>
			</div>
			<div class="span2">
			<?php if($item->next > 0) : ?>
				<a href="index.php?option=com_xbpeople&view=person&id=<?php echo $item->next ?>" class="btn btn-small pull-right">
					<?php echo Text::_('XBCULTURE_NEXT'); ?></a>
		    <?php endif; ?>
			</div>
	      </div>
      </div>
</div>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>
</div>
