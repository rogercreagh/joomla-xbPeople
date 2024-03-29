<?php 
/*******
 * @package xbPeople
 * @filesource site/views/people/tmpl/compact.php
 * @version 1.0.3.14 17th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\FileLayout;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='lastname';
    $listDirn = 'ascending';
}
$orderNames = array('firstname'=>Text::_('XBCULTURE_FIRSTNAME'),'lastname'=>Text::_('XBCULTURE_LASTNAME'),
    'sortdate'=>Text::_('XBCULTURE_DATES'),'category_title'=>Text::_('XBCULTURE_CATEGORY'),
    'bcnt'=>'Number of books','fcnt'=>'Number of films','ecnt'=>'number of events');

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbpeopleHelperRoute::getPeopleRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbpeople&view=person' . $itemid.'&id=';

$itemid = XbpeopleHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbpeople&view=category' . $itemid.'&id=';

?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>
<div class="xbculture">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo Route::_('index.php?option=com_xbpeople&view=people&layout=compact'); ?>" method="post" name="adminForm" id="adminForm">
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ($this->hide_prole) { $hide .= 'filter_prole,';}
				if ((!$this->showcat) || ($this->hide_cat)) { $hide .= 'filter_category_id,filter_subcats,';}
				if ((!$this->showtags) || ($this->hide_tag)) { $hide .= 'filter_tagfilt,filter_taglogic,';}
				echo '<div class="row-fluid"><div class="span12">';
				echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this,'hide'=>$hide));
				echo '</div></div>';
			} 
		?>
		<div class="row-fluid pagination" style="margin-bottom:10px 0;">
			<div class="pull-right">
				<p class="counter" style="text-align:right;margin-left:10px;">
					<?php echo $this->pagination->getResultsCounter().'.&nbsp;&nbsp;'; 
					   echo $this->pagination->getPagesCounter().'&nbsp;&nbsp;'.$this->pagination->getLimitBox().' per page'; ?>
				</p>
			</div>
			<div>
				<?php  echo $this->pagination->getPagesLinks(); ?>
                <?php echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
			</div>
		</div>

		<div class="row-fluid">
        	<div class="span12">		
	<?php if (empty($this->items)) : ?>
    	<div class="alert alert-no-items">
    		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    	</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbpeople">	
    		<colgroup>
    			<col ><!-- name -->
    			<?php if($this->show_sum) : ?>
    				<col class="hidden-phone" style="width:230px;"><!-- summary -->
                <?php endif; ?>
                <?php if ($this->showcnts != 0 ) : ?>
    				<col style="width:150px;"><!-- groups -->
        			<?php if($this->xbbooksStatus) : ?>
    					<col style="width:150px;"><!-- books -->
                    <?php endif; ?>
        			<?php if($this->xbeventsStatus) : ?>
    					<col style="width:150px;"><!-- events -->
                    <?php endif; ?>
        			<?php if($this->xbfilmsStatus) : ?>
    					<col style="width:150px;"><!-- films -->
                    <?php endif; ?>
    			<?php endif; ?>
    			<?php if($this->showcat || $this->showtags) : ?>
    				<col class="hidden-tablet hidden-phone"><!-- cats&tags -->
    			<?php endif; ?>
    		</colgroup>
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Firstname','firstname',$listDirn,$listOrder).' '.
							HTMLHelper::_('searchtools.sort','Lastname','lastname',$listDirn,$listOrder); ?>
				<?php if($this->show_pdates) : ?>
						<?php echo HTMLHelper::_('searchtools.sort','Dates','sortdate',$listDirn,$listOrder); ?>
                <?php endif; ?>
				</th>					
                <?php if ($this->showcnts) : ?>
    				<th>
    					<?php echo Text::_('XBCULTURE_GROUPS');?>
    				</th>
    				<?php if($this->xbbooksStatus) : ?>
        				<th>
        					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_BOOKS_U','bcnt',$listDirn,$listOrder ); ?>
        				</th>
                   <?php endif; ?>
    				<?php if($this->xbeventsStatus) : ?>
        				<th>
        					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_EVENTS','ecnt',$listDirn,$listOrder ); ?>
        				</th>
                   <?php endif; ?>
    				<?php if($this->xbfilmsStatus) : ?>
        				<th>
        					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_FILMS_U','fcnt',$listDirn,$listOrder ); ?>
        				</th>
        			<?php endif; ?>
    				<?php if($this->showcat || $this->showtags) : ?>
        				<th>
        					<?php if ($this->showcat) {
        						echo HtmlHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder );
        					}
        					if (($this->showcat) && ($this->showtags)) {
        					    echo ' &amp; ';
        					}
        					if($this->showtags) {
        					    echo ucfirst(Text::_( 'XBCULTURE_TAGS')); 
        					} ?>                
        				</th>
                    <?php endif; ?>
        		<?php else : ?>
    				<?php if ($this->showcat) : ?>
        				<th class="hidden-tablet hidden-phone">
        					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder ); ?>
    					</th>
        			<?php endif; ?>
    				<?php if ($this->showtags) : ?>
        				<th class="hidden-tablet hidden-phone">
        					<?php echo ucfirst(Text::_( 'XBCULTURE_TAGS'));  ?>                
        				</th>
        			<?php endif; ?>
                <?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
				<td>
					<p class="xbtitlelist">
						<a href="<?php echo Route::_($plink.$item->id);?>" >
							<b><?php echo $this->escape($item->firstname).' '.$this->escape($item->lastname); ?></b>
							</a>&nbsp;<a href="#ajax-xbmodal" data-toggle="modal"  class="xbpv" data-target="#ajax-xbmodal" 
    							onclick="window.com='people';window.view='person';window.pvid= <?php echo $item->id; ?>;">
                				<i class="far fa-eye"></i>
                			</a>					
					</p>
					<?php if($this->show_pdates) : ?>
						<p><?php if ($item->year_born != 0) {						
								echo $item->year_born; 
							}
							if ($item->year_died != 0) {						
								echo ($item->year_born == 0) ? '???? - ': ' - ';
								echo $item->year_died; 
							}              
						?></p>
					<?php endif; ?>
				</td>
                <?php if ($this->showcnts) : ?>
    				<td>
					<?php if ($item->gcnt>0) : ?>
    					<details>
    						<summary><span class="xbnit">
								<?php echo $item->gcnt.' ';
								    echo $item->gcnt ==1 ? Text::_('XBCULTURE_GROUP') : Text::_('XBCULTURE_GROUPS'); ?>       					
    						</span></summary>
    						<?php echo $item->grouplist['ullist']; ?>    						
    					</details>
					<?php endif; ?>
    				</td>
					<?php if ($this->xbbooksStatus) : ?>
        				<td>
    						<?php if ($item->bcnt>0) :?>
            					<details>
            						<summary><span class="xbnit">
        								<?php echo $item->bcnt.' ';
        								    echo $item->bcnt ==1 ? Text::_('XBCULTURE_BOOK') : Text::_('XBCULTURE_BOOKS'); ?>       					
            						</span></summary>
            						<?php echo $item->booklist['ullist']; ?>    						
            					</details>
        					<?php endif; ?>
        				</td>
    				<?php endif; ?>
    				<?php if ($this->xbeventsStatus) : ?>
        				<td>
    						<?php if ($item->ecnt>0) :?>
            					<details>
            						<summary><span class="xbnit">
        								<?php echo $item->ecnt.' ';
        								    echo $item->ecnt ==1 ? lcfirst(Text::_('XBCULTURE_EVENT')) : lcfirst(Text::_('XBCULTURE_EVENTS')); ?>
            						</span></summary>
            						<?php echo $item->eventlist['ullist']; ?>    						
            					</details>
        					<?php endif; ?>
        				</td>
        			<?php endif; ?>
    				<?php if ($this->xbfilmsStatus) : ?>
        				<td>
     						<?php if ($item->fcnt>0) :?>
            					<details>
            						<summary><span class="xbnit">
        								<?php echo $item->fcnt.' ';
        								    echo $item->fcnt ==1 ? Text::_('XBCULTURE_FILM') : Text::_('XBCULTURE_FILMS'); ?>       					
            						</span></summary>
            						<?php echo $item->filmlist['ullist']; ?>    						
            					</details>
        					<?php endif; ?>
        				</td>
    				<?php endif; ?>
        			<?php if(($this->showcat) || ($this->showtags)) : ?>
    					<td class="hidden-phone">
     						<?php if ($this->showcat) : ?>												
    							<p>
    								<?php if($this->showcat == 2) : ?>
        								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>">
        									<?php  echo $item->category_title; ?></a>		
        							<?php else: ?>
        								<span class="label label-success"><?php  echo $item->category_title; ?></span>
    								<?php endif; ?>
    							</p>
    						<?php endif; ?>
    						<?php if ($this->showtags) : ?>	
    							<?php  $tagLayout = new FileLayout('joomla.content.tags');
        							echo $tagLayout->render($item->tags);?>
        					<?php endif; ?>
    					</td>
                    <?php endif; ?>
    			<?php else: ?>
    				<?php if ($this->showcat) : ?>												
    					<td class="hidden-phone">
    						<?php if($this->showcat == 2) : ?>
    							<a class="label label-success" href="<?php echo $clink.$item->catid; ?>">
    								<?php  echo $item->category_title; ?></a>		
    						<?php else: ?>
    							<span class="label label-success"><?php  echo $item->category_title; ?></span>
    						<?php endif; ?>
    					</td>
    				<?php endif; ?>
    				<?php if ($this->showtags) : ?>	
    					<td>
    						<?php  $tagLayout = new FileLayout('joomla.content.tags');
        						echo $tagLayout->render($item->tags);?>
    					</td>
    				<?php endif; ?>	
				<?php endif; ?>
				</tr>
				
			<?php } // endforeach; ?>
		</tbody>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif; ?>
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</div>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbPeople');?></p>
</div>

<?php echo LayoutHelper::render('xbculture.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>

