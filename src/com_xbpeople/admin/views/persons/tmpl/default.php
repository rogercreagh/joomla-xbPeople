<?php
/*******
 * @package xbFilms
 * @filesource admin/views/people/tmpl/default.php
 * @version 0.2.5 25th January 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
JHtml::_('formbehavior.chosen', 'select');


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
	$listOrder='lastname';
	$listDirn = 'ascending';
}
$orderNames = array('firstname'=>Text::_('COM_XBFILMS_CAPFIRSTNAME'),'lastname'=>Text::_('COM_XBFILMS_CAPLASTNAME'),
		'id'=>'id','sortdate'=>Text::_('COM_XBFILMS_CAPDATES'),'category_title'=>Text::_('COM_XBFILMS_CAPCATEGORY'),
		'published'=>Text::_('COM_XBFILMS_CAPPUBSTATE'),'ordering'=>Text::_('COM_XBFILMS_CAPORDERING'));

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbfilms.film');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_xbfilms&task=persons.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'xbpersonsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$noportrait = JURI::root()."media/com_xbfilms/images/noportrait.jpg";

$pelink = 'index.php?option=com_xbfilms&view=person&task=person.edit&id=';
$pvlink = 'index.php?option=com_xbfilms&view=person&task=person.edit&id='; //change this to view view when available
$celink = 'index.php?option=com_categories&task=category.edit&id=';
$cvlink = 'index.php?option=com_xbfilms&view=category&id=';
$telink = 'index.php?option=com_tags&view=tag&task=tag.edit&id=';
$tvlink = 'index.php?option=com_xbfilms&view=tag&id=';
$bplink = 'index.php?option=com_xbbooks&view=person&layout=edit&id=';

?>
<form action="index.php?option=com_xbfilms&view=persons" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. JText::_(($fnd==1)?'COM_XBFILMS_ONEPERSON':'COM_XBFILMS_MANYPEOPLE').' '.JText::_('COM_XBFILMS_FOUND');			
            ?>
		</p>
	</div>
	<div class="clearfix"></div>
	<?php
        // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>
	<?php $search = $this->searchTitle; ?>

	<?php if ($search) {
		echo '<p>Searched for <b>'; 
		if (stripos($search, 'i:') === 0) {
            echo trim(substr($search, 2)).'</b> '.JText::_('COM_XBFILMS_AS_PERSONID');
		} elseif ((stripos($search, 's:') === 0) || (stripos($search, 'b:') === 0)) {
            echo trim(substr($search, 2)).'</b> '.JText::_('COM_XBFILMS_AS_INBIOG');
        } else {
			echo trim($search).'</b> '.JText::_('COM_XBFILMS_AS_INNAMES');
		}
		echo '</p>';
	} ?> 
	<?php if ($this->state->get('filter.rolefilt')!='all') {
	    echo 'Filtered by role '.ucfirst($this->state->get('filter.rolefilt'));
	}
	?>
	<div class="pagination">
		<?php  echo $this->pagination->getPagesLinks(); ?>
		<br />
	    <?php echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	
	<table class="table table-striped table-hover" id="xbpersonsList">
		<thead>
			<tr>
				<th class="nowrap center hidden-phone" style="width:25px;">
					<?php echo JHtml::_('searchtools.sort', '', 'ordering', 
					    $listDirn, $listOrder, null, 'asc', 'COM_XBFILMS_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
    			<th class="hidden-phone" style="width:25px;">
    				<?php echo JHtml::_('grid.checkall'); ?>
    			</th>
    			<th class="nowrap center" style="width:55px">
					<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
    			</th>
    			<th class="center" style="width:80px">
    				<?php echo JText::_('COM_XBFILMS_CAPPORTRAIT') ;?>
    			</th>
    			<th >
					<?php echo JHtml::_('searchtools.sort', 'COM_XBFILMS_FIRSTNAME', 'firstname', $listDirn, $listOrder); ?>
					<?php echo JHtml::_('searchtools.sort', 'COM_XBFILMS_LASTNAME', 'lastname', $listDirn, $listOrder); ?>					
					<?php echo JHtml::_('searchtools.sort', 'COM_XBFILMS_CAPDATES', 'sortdate', $listDirn, $listOrder); ?>
    			</th>
    			<th>
    				<?php echo JText::_('COM_XBFILMS_CAPBIOG'); ?>
    			</th>
    			<th >
    				<?php echo JText::_('COM_XBFILMS_CAPFILMS') ;?>
    			</th>
    			<th class="hidden-tablet hidden-phone" style="width:15%;">
						<?php echo JHTML::_('searchtools.sort','COM_XBFILMS_CAPCATS','category_title',$listDirn,$listOrder ).' &amp; '.
						JText::_( 'Tags' ); ?>
					</th>
    			
    			<th class="nowrap hidden-phone" style="width:45px;">
					<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
    			</th>
    		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
    			$canEdit    = $user->authorise('core.edit', 'com_xbfilms.person.'.$item->id);
    			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
    			$canEditOwn = $user->authorise('core.edit.own', 'com_xbfilms.person.'.$item->id) && $item->created_by == $userId;
    			$canChange  = $user->authorise('core.edit.state', 'com_xbfilms.person.'.$item->id) && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="order nowrap center hidden-phone">
                        <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            } elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                            }
                        ?>
                        <span class="sortable-handler<?php echo $iconClass; ?>">
                        	<span class="icon-menu" aria-hidden="true"></span>
                        </span>
                        <?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                        <?php endif; ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'person.', true, 'cb'); ?>
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
						<img class="img-polaroid hasTooltip xbimgthumb" title="" 
							data-original-title="<?php echo $item->portrait;?>"
							src="<?php echo $item->portrait !='' ? JURI::root() .$item->portrait : $noportrait; ?>" 
							border="0" alt="" />						
					</td>
					<td>
						<p class="xbtitlelist">
							<?php if ($item->checked_out) {
							    $couname = JFactory::getUser($item->checked_out)->username;
							    echo JHtml::_('jgrid.checkedout', $i, JText::_('COM_XBFILMS_OPENEDBY').':,'.$couname, $item->checked_out_time, 'person.', $canCheckin); 
							} ?>
							
							<a href="<?php echo $pelink.$item->id; ?>" title="<?php echo JText::_('COM_XBFILMS_EDIT_PERSON'); ?>">
								<?php echo ($item->firstname=='')? '... ' : $item->firstname; ?>
								<?php echo ' '.$item->lastname; ?> 
							</a>
							<br />
							<span class="xbaliaslist"><i><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></i></span>
						</p>
						<p>
						<?php 
							if($item->year_born > 0) { echo '<i>'.JText::_('COM_XBFILMS_BORN').' </i>: '.$item->year_born;} 
							if($item->year_died > 0) { 
								echo '&nbsp;&nbsp;<i>'.JText::_('COM_XBFILMS_DIED').' </i>: '.$item->year_died;
							}
							if($item->nationality) { 
                        		echo '<br /><i>'.JText::_('COM_XBFILMS_CAPNATIONALITY').' </i>: '.$item->nationality;
                        	} ?>						
						</p>							
					</td>
					<td>						
						<p class="xb095">
							<?php if (!empty($item->summary)) : ?>
								<?php echo $item->summary; ?>
    						<?php else : ?>
    							<span class="xbnit">
    							<?php if (!empty($item->biography)) : ?>
    								<?php echo Text::_('COM_XBFILMS_BIOG_EXTRACT'); ?>: </span>
    								<?php echo XbfilmsGeneral::makeSummaryText($item->biography,200); ?>
    							<?php else : ?>
    								<?php echo Text::_('COM_XBFILMS_NO_SUMMARY_BIOG'); ?></span>
    							<?php endif; ?>
    						<?php endif; ?>
                        </p>
                        <?php if ((!empty($item->biography)) && (strlen(strip_tags($item->biography))>200)) : ?>
                        	<p class="xbnit xb09">   
                             <?php 
                             echo Text::_('COM_XBFILMS_CAPBIOG').' '.str_word_count(strip_tags($item->biography)).' '.Text::_('COM_XBBOOKS_WORDS'); 
                             ?>
							</p>
						<?php endif; ?>

						<?php if($item->ext_links_cnt >0 ) : ?>
							<p class="xbnit xb095">	
								<?php echo JText::_('COM_XBFILMS_FIELD_EXTLINK_LABEL').': '; 
	                            echo '<span class="xbnamelist">';
	                            echo $item->ext_links_list.'</span>'; ?>
	                    	</p>
						<?php endif; ?>
                    </td>
					<td>
						<?php if ($item->dircnt>0) { 
							echo '<span class="xbnit hasTooltip" data-original-title="'.$item->dirlist.'">';
						    echo JText::_('COM_XBFILMS_DIRECTOR_OF').' '.$item->dircnt.' ';
                            echo JText::_(($item->dircnt==1)?'COM_XBFILMS_ONEFILM':'COM_XBFILMS_MANYFILMS'); 
						    echo '</span><br />';
						}?> 
						<?php if ($item->prdcnt>0) { 
							echo '<span class="xbnit hasTooltip" data-original-title="'.$item->prdlist.'">';
							echo JText::_('COM_XBFILMS_PRODUCER_OF').' '.$item->prdcnt.' ';
                            echo JText::_(($item->prdcnt==1)?'COM_XBFILMS_ONEFILM':'COM_XBFILMS_MANYFILMS'); 
                            echo '</span><br />';
						}?>
						<?php if ($item->crewcnt>0) { 
							echo '<span class="xbnit hasTooltip" data-original-title="'.$item->crewlist.'">';
							echo JText::_('Crew on').' '.$item->crewcnt.' ';
                            echo JText::_(($item->crewcnt==1)?'COM_XBFILMS_ONEFILM':'COM_XBFILMS_MANYFILMS'); 
                            echo '</span><br />';
						}?>
						<?php if ($item->appcnt>0) { 
							echo '<span class="xbnit hasTooltip" data-original-title="'.$item->applist.'">';
							echo JText::_('Subject or cameo in').' '.$item->appcnt.' ';
							echo JText::_(($item->appcnt==1)?'COM_XBFILMS_ONEFILM':'COM_XBFILMS_MANYFILMS'); 
                            echo '</span><br />';
						}?>
						<?php if ($item->actcnt>0) { 
							echo '<span class="xbnit hasTooltip" data-original-title="'.$item->actlist.'">';
							echo JText::_('Actor in').' '.$item->actcnt.' ';
							echo JText::_(($item->actcnt==1)?'COM_XBFILMS_ONEFILM':'COM_XBFILMS_MANYFILMS'); 
                            echo '</span><br />';
						}?>
						<?php if ($item->bookcnt>0) {
							echo '<span class="xbnit">';
							echo JText::_('COM_XBFILMS_ALSO_WITH').' <a href="'.$bplink.$item->id.'">'.$item->bookcnt.' ';
							echo JText::_(($item->bookcnt==1)?'COM_XBFILMS_BOOK':'COM_XBFILMS_BOOKS');
							echo '</a></span>';
						}?>
					</td>
					<td>
						<p><a  class="label label-success" href="<?php echo $cvlink . $item->catid.'&extension=com_xbfilms'; ?>" 
							title="<?php echo JText::_( 'COM_XBFILMS_VIEW_CATEGORY' );?>::<?php echo $item->category_title; ?>">
								<?php echo $item->category_title; ?>
						</a></p>						
						
						<ul class="inline">
						<?php foreach ($item->tags as $t) : ?>
							<li><a href="<?php echo $tvlink.$t->id; ?>" class="label label-info">
								<?php echo $t->title; ?></a>
							</li>													
						<?php endforeach; ?>
						</ul>						    											
					</td>					
					<td align="center">
						<?php echo $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
        <?php // load the modal for displaying the batch options
            echo JHtml::_(
            'bootstrap.renderModal',
            'collapseModal',
            array(
                'title' => JText::_('COM_XBFILMS_BATCH_TITLE'),
                'footer' => $this->loadTemplate('batch_footer')
            ),
            $this->loadTemplate('batch_body')
        ); ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbfilmsHelper::credit();?></p>
