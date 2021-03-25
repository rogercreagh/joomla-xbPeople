<?php
/*******
 * @package xbPeople
 * @filesource admin/views/tag/tmpl/edit.php
 * @version 0.4.4 24th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

$item = $this->item;
$telink = 'index.php?option=com_tags&task=tag.edit&id=';
$iolink = 'index.php?option=';
$pelink = '&view=person&task=person.edit&id=';
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
		<form action="index.php?option=com_xbpeople&view=tag" method="post" id="adminForm" name="adminForm">
		<div class="row-fluid xbmb8">
			<div class= "span4">
				  <h3><?php echo JText::_('COM_XBPEOPLE').' '. JText::_('XBCULTURE_TAG_ITEMS').':'; ?></h3>
			</div>
			<div class="span6">
				<a href="<?php echo $telink.$item->id; ?>" class="badge badge-info">
					<h2><?php echo $item->title; ?></h2>
				</a>
			</div>
            <div class="span2">
				<p><?php echo '<i>'.JText::_('JGRID_HEADING_ID').'</i>: '.$item->id; ?></p>
                <p><?php echo '<i>'.JText::_('XBCULTURE_CAPALIAS').'</i>: '.$item->alias; ?></p>
            </div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span6">
					<p class="xb11"><i><?php echo JText::_('XBCULTURE_CAPTAG').' '.Jtext::_('XBCULTURE_CAPHEIRARCHY'); ?>: </i>
					<?php $path = str_replace('/', ' - ', $item->path);
						echo 'root - '.$path; ?>
					</p>
			</div>
			<div class= "span6">
				<p><i><?php echo Jtext::_('XBCULTURE_ADMIN_NOTE'); ?>:</i>  <?php echo $item->note; ?></p>
			</div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span2">
				<p><i><?php echo JText::_('XBCULTURE_CAPDESCRIPTION'); ?>:</i></p>
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
   				<div class="xbbox xbboxgrn xbmh200 xbyscroll">
					<p><?php echo $item->pcnt; ?> people tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if (count($item->ppeople) > 0 ) : ?>
						<p class="xbnote">Tagged by xbPeople:</p>
						<ul>
						<?php foreach ($item->ppeople as $i=>$per) { 
							echo '<li><a href="'.$iolink.'com_xbpeople'.$pelink.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
					<?php if (count($item->bpeople) > 0 ) : ?>
						<p class="xbnote">Tagged by xbBooks:</p>
						<ul>
						<?php foreach ($item->bpeople as $i=>$per) { 
							echo '<li><a href="'.$iolink.'com_xbbooks'.$pelink.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
					<?php if (count($item->fpeople) > 0 ) : ?>
						<ul>
						<p class="xbnote">Tagged by xbFilms:</p>
						<?php foreach ($item->fpeople as $i=>$per) { 
							echo '<li><a href="'.$iolink.'com_xbfilms'.$pelink.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>

            <div class= "span6">
   				<div class="xbbox xbboxcyan xbmh200 xbyscroll">
					<p><?php echo $item->chcnt; ?> people tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if (count($item->ppeople) > 0 ) : ?>
						<p class="xbnote">Tagged by xbPeople:</p>
						<ul>
						<?php foreach ($item->ppeople as $i=>$per) { 
							echo '<li><a href="'.$iolink.'com_xbpeople'.$pelink.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
					<?php if (count($item->bpeople) > 0 ) : ?>
						<p class="xbnote">Tagged by xbBooks:</p>
						<ul>
						<?php foreach ($item->bpeople as $i=>$per) { 
							echo '<li><a href="'.$iolink.'com_xbbooks'.$pelink.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
					<?php if (count($item->fpeople) > 0 ) : ?>
						<ul>
						<p class="xbnote">Tagged by xbFilms:</p>
						<?php foreach ($item->fpeople as $i=>$per) { 
							echo '<li><a href="'.$iolink.'com_xbfilms'.$pelink.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
				<div class="xbbox ">
					<p><?php echo $item->chcnt; ?> characters tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if ($item->chcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->chars as $i=>$per) { 
							echo '<li><a href="'.$xblink.'&view=person&task=character.edit&id='.$per->pid.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class= "span12">
				<div class="xbbox xbboxgrey xbmh200 xbyscroll">
					<p><?php echo $item->othercnt; ?> other items also tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if ($item->othercnt > 0 ) : ?>
						<?php $span = intdiv(12, count($item->othcnts)); ?>
						<div class="row-fluid">
						<?php $thiscomp=''; $firstcomp=true; $thisview = ''; $firstview=true; 
						foreach ($item->others as $i=>$oth) { 
							$comp = substr($oth->type_alias, 0,strpos($oth->type_alias, '.'));
							$view = substr($oth->type_alias,strpos($oth->type_alias, '.')+1);
                            $isnewcomp = ($comp!=$thiscomp) ? true : false;
                            $newview= ($view!=$thisview) ? true : false;
                            // if it isnewcomp
                            if ($isnewcomp) {
                            	if ($firstcomp) {
                                  $firstcomp = false;
                                } else {
	                                  echo '</ul></div>';                            		
                            	}
								$thiscomp = $comp;
								$firstview=true;
								echo '<div class="span'.$span.'"><ul>';
							}
							if ($newview) {
								if ($firstview) {
                                  $firstview = false;
                                } else {
									echo '<br />';
								}
								$thisview = $view;
							}
							echo '<li><a href="index.php?option='.$comp.'">'
								.$comp.'</a> - '.$view.': <a href="index.php?option='.$comp.'&task='.$view.'.edit&id='.$oth->item_id.'">'
                				.$oth->core_title.'</a></li>';
						} 
						echo '</ul></div>'; ?>
						</div>				
					<?php endif; ?>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="tid" value="<?php echo $item->id;?>" />
		<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo $xblink; ?>&view=tags" class="btn btn-small">
		<?php echo JText::_('XBCULTURE_TAG_LIST'); ?></a>
</div>
<p><?php echo XbpeopleHelper::credit();?></p>
