<?php
/*******
 * @package xbPeople
 * @filesource site/views/category/tmpl/default.php
 * @version 0.9.9.8 18th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbpeopleHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbpeople&view=category'.$itemid.'&id=';

//$plink = 'index.php?option=com_xbpeople&view=person&id=';

$itemid = XbpeopleHelperRoute::getPeopleRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbpeople&view=person'.$itemid.'&id=';

$itemid = XbpeopleHelperRoute::getCategoriesRoute();
if ($itemid !== null) {
	$cclink = 'index.php?option=com_xbpeople&Itemid='.$itemid.'';
} else {
	$cclink = 'index.php?option=com_xbpeople&view=categories';
}

$show_catdesc = $this->params->get('show_catdesc',1);

?>
<div class="row-fluid" style="margin-bottom:20px;">
	<div class="span3">
		<h4><?php echo Text::_('XBCULTURE_ITEMS_IN_CAT'); ?></h4>		
	</div>	
	<div class="span9">
		<?php if (($this->show_catpath) && ($item->level>1)) : ?>
			<div class="xb11 pull-left xbmr10 xbpt17 xbit xbgrey">				
				<?php  $path = substr($item->path, 0, strrpos($item->path, '/'));
					$path = str_replace('/', ' - ', $path);
					echo $path.' - ' ; ?>
        	</div>
        <?php endif; ?>
          <div class="badge badge-success pull-left"><h3><?php echo $item->title; ?></h3></div>
	</div>	
</div>
<?php if (($show_catdesc) && ($item->description != '')) : ?>
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
	<?php if($item->extension == 'com_xbpeople') : ?>
	<?php else: ?>
 	<?php endif; ?>
   	<div class= "span6">
    		<div class="xbbox xbboxgrn  xbyscroll xbmh300">
    			<p><?php echo $item->pcnt; ?> people</p>
    			<?php if ($item->pcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->people as $i=>$per) { 
    					echo '<li><a href="'.$plink.$per->pid.'">'.$per->title.'</a></li> ';
    				} ?>				
    				</ul>
    			<?php endif; ?>
    		</div>
    	</div>
    	<div class="span6">
    		<div class="xbbox xbboxcyan  xbyscroll xbmh300">
    			<p><?php echo $item->chcnt; ?> characters</p>
    			<?php if ($item->chcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->chars as $i=>$char) { 
    					echo '<li><a href="'.$clink.$char->pid.'">'.$char->title.'</a></li> ';
    				} ?>			
    				</ul>
    			<?php endif; ?>
    		</div>
    	</div>
</div>
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo $cclink; ?>" class="btn btn-small">
		<?php echo JText::_('XBCULTURE_CAT_COUNTS'); ?>
	</a>
</p>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>

