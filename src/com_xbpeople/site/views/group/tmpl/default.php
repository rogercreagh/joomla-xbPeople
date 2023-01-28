<?php
/*******
 * @package xbPeople
 * @filesource site/views/group/tmpl/default.php
 * @version 1.0.3.2 28th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$imgok = (($this->show_image >0) && (JFile::exists(JPATH_ROOT.'/'.$item->picture)));
if ($imgok) {
    $src = Uri::root().$item->picture;
    $tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
}

$itemid = XbpeopleHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbpeople&view=category'.$itemid.'&id=';

?>
<style type="text/css" media="screen">
	.xbpvmodal .modal-content {padding:15px;max-height:calc(100vh - 190px); overflow:scroll; }
    <?php if($this->tmpl == 'component') : ?>
        .fa-eye {visibility:hidden;}
    <?php endif; ?>
</style>
<div class="xbculture">
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
				<p>
				<?php if (($item->year_formed != 0) || (!$this->hide_empty)) : ?>
					<span class="xbnit "><?php echo ucfirst(Text::_('XBCULTURE_FORMED')).': '; ?> </span>
					<?php if ($item->year_formed == 0) {
					    echo Text::_('XBCULTURE_UNKNOWN');
					} else {
					    echo $item->year_formed; 
					} ?>
				<?php endif; ?>
				<br />
				<?php if (($item->year_disolved != 0) || (!$this->hide_empty)) : ?>
					<span class="xbnit "><?php echo ucfirst(Text::_('XBCULTURE_DISBANDED')).': '; ?> </span>
					<?php if ($item->year_disolved == 0) {
					    echo Text::_('XBCULTURE_UNKNOWN');
					} else {
					    echo $item->year_disolved; 
					} ?>
				<?php endif; ?>
				</p>
			</div>
			<h3><?php echo $item->title; ?>
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
<div class="row-fluid">
	<?php if (($item->pcnt == 0) || ($item->summary == '')) : ?>
		<div class="span3"></div>
	<?php endif; ?>
    <?php if ($item->summary != '') : ?>
       	<div class="span6">
			<div class="xbbox xbboxwht">
				<div class="pull-left">
					<span class="xbnit"><?php echo Text::_('XBCULTURE_SUMMARY'); ?>  : </span>
				</div>
				<div><?php echo $item->summary; ?></div> 
			</div>
		</div>
    <?php  endif;?>
    <?php if ($item->pcnt>0) : ?>
		<div class="span6">
            <div class="xbbox xbboxwht">
    		<p><b><?php echo ucfirst(Text::_('XBCULTURE_MEMBERS')); ?></b>
    		</p>
    		<?php echo $item->memberlist; ?>
            </div>
		</div>
    <?php endif; ?>
</div>
<div class="row-fluid">
	<?php if (($item->bcnt + $item->ecnt + $item->fcnt)>0) {
	    $cols = 0;
    	if ($item->bcnt>0) $cols++;
    	if ($item->ecnt>0) $cols++;
    	if ($item->fcnt>0) $cols++;
    	$cols = intdiv(12, $cols);
	}
	?>
	<?php if ($item->bcnt>0) : ?>
    	<div class="span<?php echo $cols; ?>">
    		<p><b><?php echo ucfirst(Text::_('XBCULTURE_BOOKS')); ?></b>
    			<span class="xbnit">
    				<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->bcnt.' ';
    				echo ($item->bcnt == 1) ? Text::_('XBCULTURE_BOOK') : Text::_('XBCULTURE_BOOKS'); ?>
    			</span>
    		</p>
    		<?php echo $item->booklist; ?>
    	</div>
	<?php endif; ?>
	<?php if ($item->ecnt>0) : ?>
    	<div class="span<?php echo $cols; ?>">
    		<p><b><?php echo ucfirst(Text::_('XBCULTURE_EVENTS')); ?></b>
    			<span class="xbnit">
    				<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->fcnt.' ';
    				echo ($item->ecnt == 1) ? Text::_('XBCULTURE_EVENT') : Text::_('XBCULTURE_EVENTS'); ?>
    				</span>
    		</p>
    		<?php echo $item->eventlist; ?>
    	</div>
	<?php endif; ?>
	<?php if ($item->fcnt>0) : ?>
    	<div class="span<?php echo $cols; ?>">
    		<p><b><?php echo ucfirst(Text::_('XBCULTURE_FILMS')); ?></b>
    			<span class="xbnit">
    				<?php echo Text::_('XBCULTURE_LISTED_WITH').' '.$item->fcnt.' ';
    				echo ($item->fcnt == 1) ? Text::_('XBCULTURE_FILM') : Text::_('XBCULTURE_FILMS'); ?>
    				</span>
    		</p>
    		<?php echo $item->filmlist; ?>
    	</div>
	<?php endif; ?>
</div>
<?php  if (!empty($item->description)) :?>
	<div class="xbnit xbmb8"><?php echo Text::_('XBCULTURE_DESCRIPTION');?></div>
    <div class="xbbox xbboxcyan">
    	<?php echo $item->description; ?>
    </div>
<?php else: ?>
	<?php if (!$this->hide_empty) {
	   echo '<p class="xbnit">'.Text::_('XBCULTURE_NO_DESCRIPTION').'</p>';
	} ?>
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

<?php if($this->tmpl != 'component') : ?>
    <div class="row-fluid">
    	<div class="span12 xbbox xbboxgrey">
    		<div class="row-fluid">
    			<div class="span2">
    				<?php if (($item->prev>0) || ($item->next>0)) : ?>
    				<span class="xbpop xbcultpop xbinfo fas fa-info-circle" data-trigger="hover" title 
    					data-original-title="Prev-Next Info" data-content="<?php echo JText::_('XBCULTURE_INFO_PREVNEXT'); ?>" >
    				</span>&nbsp;
    				<?php endif; ?>
    				<?php if($item->prev > 0) : ?>
    					<a href="index.php?option=com_xbpeople&view=group&id=<?php echo $item->prev ?>" class="btn btn-small">
    						<?php echo Text::_('XBCULTURE_PREV'); ?></a>
    			    <?php endif; ?>
    			</div>
    			<div class="span8"><center>
    				<a href="index.php?option=com_xbpeople&view=groups" class="btn btn-small">
    					<?php echo Text::_('XBCULTURE_GROUP_LIST'); ?></a></center>
    			</div>
    			<div class="span2">
    			<?php if($item->next > 0) : ?>
    				<a href="index.php?option=com_xbpeople&view=group&id=<?php echo $item->next ?>" class="btn btn-small pull-right">
    					<?php echo Text::_('XBCULTURE_NEXT'); ?></a>
    		    <?php endif; ?>
    			</div>
    	      </div>
          </div>
    </div>
    <div class="clearfix"></div>
    <p><?php echo XbcultureHelper::credit('xbPeople');?></p>
    </div>
<?php endif; ?>
