<?php
/*******
 * @package xbPeople
 * @filesource site/views/tag/tmpl/default.php
 * @version 0.9.9.8 18th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Uri\Uri;

$item = $this->item;
$xblink = 'index.php?option=com_xbpeople&view=';

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbpeopleHelperRoute::getPeopleRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = $xblink.'person' . $itemid.'&id=';

$itemid = XbpeopleHelperRoute::getCharsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$chlink = $xblink.'character' . $itemid.'&id=';

$itemid = XbpeopleHelperRoute::getTagsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$tclink = $xblink.'tags' . $itemid;

// $item = $this->item;
// $xblink = 'index.php?option=com_xbpeople&view=';
// $blink = $xblink.'book&id=';
// $plink = $xblink.'person&id=';
// $rlink = $xblink.'bookreview&id=';
// $chlink = $xblink.'character&id=';
// $tclink = $xblink.'tags';
?>
<div class="xbpeople">
<div class="row-fluid" style="margin-bottom:20px;">
	<div class="span3">
		<h4><?php echo JText::_('XBCULTURE_ITEMSTAGGED').': '; ?></h4>		
	</div>	
	<div class="span9">
		<?php if (($this->show_tagpath) && (strpos($item->path,'/')!==false)) : ?>
			<div class="xb11 pull-left xbpt17 xbmr20 xbit xbgrey" >
				<?php  $path = substr($item->path, 0, strrpos($item->path, '/'));
					$path = str_replace('/', ' - ', $path);
					echo $path; ?>
        	</div>
        <?php endif; ?>
		<div class="badge badge-info pull-left"><h3><?php echo $item->title; ?></h3></div>
	</div>	
</div>
<?php if ($item->description != '') : ?>
	<div class="row-fluid">
		<div class= "span2">
			<p><i>Description:</i></p>
		</div>
		<div class="span10">
			<?php echo $item->description; ?>
		</div>
	</div>
<?php endif; ?>
<div class="row-fluid">
	<div class= "span6">
		<div class="xbbox xbboxgrn xbmh200 xbyscroll">
			<p><?php echo $item->pcnt; ?> people tagged</p>
			<?php if ($item->pcnt > 0 ) : ?>
				<ul>
				<?php foreach ($item->people as $i=>$per) { 
					echo '<li><a href="'.$plink.$per->pid.'">'.$per->title.'</a></li> ';
				} ?>				
				</ul>
			<?php endif; ?>
		</div>
	</div>
	<div class= "span6">
		<div class="xbbox xbboxblue xbmh200 xbyscroll">
			<p><?php echo $item->chcnt; ?> characters tagged</p>
			<?php if ($item->chcnt > 0 ) : ?>
				<ul>
				<?php foreach ($item->characters as $i=>$per) { 
					echo '<li><a href="'.$chlink.$per->pid.'">'.$per->title.'</a></li> ';
				} ?>				
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="xbbox xbboxgrey xbmh200 xbyscroll">
			<p><?php echo $item->othercnt; ?> other (not xbPeople) items tagged <?php echo $item->title; ?></p>
			<?php if ($item->othercnt > 0 ) : ?>
						<?php $span = intdiv(12, count($item->othcnts)); ?>
						<div class="row-fluid">
						<?php $thiscomp=''; $firstcomp=true; $thisview = ''; $firstview=true; 
						foreach ($item->others as $i=>$oth) {
							$comp = substr($oth->type_alias, 0,strpos($oth->type_alias, '.'));
							$view = substr($oth->type_alias,strpos($oth->type_alias, '.')+1);
							if (($view=='review') && ($comp == 'com_xbfilms')) {								
								$view = 'filmreview';								
							}
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
							echo '<li><i>'.ucfirst($view);
							echo '</i> : <a href="index.php?option='.$comp.'&view='.$view.'&id='.$oth->othid.'">'.$oth->core_title.'</a></li> ';
							// 				<ul>
// 				<?php foreach ($item->others as $i=>$oth) { 
// 					$comp = substr($oth->type_alias,0,strpos($oth->type_alias,'.'));
// 					$view = substr($oth->type_alias,strpos($oth->type_alias,'.')+1);
// 					echo '<li><i>'.ucfirst(str_replace('.',' - ',substr($oth->type_alias,strpos($oth->type_alias,'_')+1)));
// 					echo '</i> : <a href="index.php?option='.$comp.'&view='.$view.'&id='.$oth->othid.'">'.$oth->core_title.'</a></li> ';
				} ?>			
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo $tclink; ?>" class="btn btn-small">
		<?php echo JText::_('XBCULTURE_TAG_COUNTS'); ?>
	</a>
</p>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>
</div>

