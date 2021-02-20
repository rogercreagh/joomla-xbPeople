<?php
/*******
 * @package xbFilms
 * @filesource admin/views/persons/tmpl/default.php
 * @version 0.2.1 19th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2020
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
	$listOrder='lastname';
	$listDirn = 'ascending';
}
$orderNames = array('firstname'=>Text::_('COM_XBPEOPLE_CAPFIRSTNAME'),'lastname'=>Text::_('COM_XBPEOPLE_CAPLASTNAME'),
		'id'=>'id','sortdate'=>Text::_('COM_XBPEOPLE_CAPDATES'),'category_title'=>Text::_('COM_XBPEOPLE_CAPCATEGORY'),
		'published'=>Text::_('COM_XBPEOPLE_CAPSTATUS'),'a.ordering'=>Text::_('COM_XBPEOPLE_CAPORDERING'));

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbfilms.film');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_xbfilms&task=persons.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'xbpersonsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$noportrait = "media/com_xbpeople/images/noportrait.jpg";
$nofile = "media/com_xbpeople/images/nofile.jpg";

$pelink = 'index.php?option=com_xbpeople&view=person&task=person.edit&id=';
$celink = 'index.php?option=com_categories&task=category.edit&id=';
$telink = 'index.php?option=com_tags&view=tag&task=tag.edit&id=';
$bplink = 'index.php?option=com_xbbooks&view=persons';
$fplink = 'index.php?option=com_xbfilms&view=persons';

?>
<form action="index.php?option=com_xbpeople&view=persons" method="post" id="adminForm" name="adminForm">
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
			echo $fnd .' '. Text::_(($fnd==1)?'COM_XBPEOPLE_ONEPERSON':'COM_XBPEOPLE_MANYPEOPLE').' '.Text::_('COM_XBPEOPLE_FOUND');			
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
            echo trim(substr($search, 2)).'</b> '.Text::_('COM_XBPEOPLE_AS_PERSONID');
		} elseif ((stripos($search, 's:') === 0) || (stripos($search, 'b:') === 0)) {
            echo trim(substr($search, 2)).'</b> '.Text::_('COM_XBPEOPLE_AS_INBIOG');
        } else {
			echo trim($search).'</b> '.Text::_('COM_XBPEOPLE_AS_INNAMES');
		}
		echo '</p>';
	} ?> 
	<?php if ($this->state->get('filter.rolefilt')=='orphans') {
	    echo Text::_('COM_XBPEOPLE_ORPHAN_PEOPLE');
	}
	?>
	<div class="pagination">
		<?php  echo $this->pagination->getPagesLinks(); ?>
		<br />
	    <?php echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	
	<table class="table table-striped table-hover" id="xbpersonsList">
		<thead>
			<tr>
				<th class="nowrap center hidden-phone" style="width:25px;">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', 
					    $listDirn, $listOrder, null, 'asc', 'COM_XBPEOPLE_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
    			<th class="hidden-phone" style="width:25px;">
    				<?php echo HTMLHelper::_('grid.checkall'); ?>
    			</th>
    			<th class="nowrap center" style="width:55px">
					<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
    			</th>
    			<th class="center" style="width:80px">
    				<?php echo Text::_('COM_XBPEOPLE_CAPPORTRAIT') ;?>
    			</th>
    			<th >
					<?php echo HTMLHelper::_('searchtools.sort', 'COM_XBPEOPLE_CAPFIRSTNAME', 'firstname', $listDirn, $listOrder); ?>
					<?php echo HTMLHelper::_('searchtools.sort', 'COM_XBPEOPLE_CAPLASTNAME', 'lastname', $listDirn, $listOrder); ?>					
					<?php echo HTMLHelper::_('searchtools.sort', 'COM_XBPEOPLE_CAPDATES', 'sortdate', $listDirn, $listOrder); ?>
    			</th>
    			<th>
    				<?php echo Text::_('COM_XBPEOPLE_CAPBIOG'); ?>
    			</th>
    			<th >
    				<?php echo Text::_('COM_XBPEOPLE_BOOKFILMS') ;?>
    			</th>
    			<th class="hidden-tablet hidden-phone" style="width:15%;">
						<?php echo HTMLHelper::_('searchtools.sort','COM_XBPEOPLE_CAPCATS','category_title',$listDirn,$listOrder ).' &amp; '.
						Text::_( 'COM_XBPEOPLE_CAPTAGS' ); ?>
					</th>
    			
    			<th class="nowrap hidden-phone" style="width:45px;">
					<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
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
    			$canEdit    = $user->authorise('core.edit', 'com_xbpeople.person.'.$item->id);
    			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
    			$canEditOwn = $user->authorise('core.edit.own', 'com_xbpeople.person.'.$item->id) && $item->created_by == $userId;
    			$canChange  = $user->authorise('core.edit.state', 'com_xbpeople.person.'.$item->id) && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="order nowrap center hidden-phone">
                        <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            } elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
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
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'person.', true, 'cb'); ?>
							<?php if ($item->note!=''){ ?>
								<span class="btn btn-micro active hasTooltip" title="" 
									data-original-title="<?php echo '<b>'.Text::_( 'COM_XBPEOPLE_CAPNOTE' ) .'</b>: '. htmlentities($item->note); ?>">
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
							<?php 
    							$src = $item->portrait;
    							if (empty($src)) {
    							    $src = $noportrait;
    							} elseif (!file_exists(JPATH_ROOT.'/'.$src)) {
    							    $src = $nofile;
    							}
    							$src = JURI::root().$src;
							?>
							src="<?php echo $src; ?>"
							border="0" alt="" />						
					</td>
					<td>
						<p class="xbtitlelist">
							<?php if ($item->checked_out) {
							    $couname = Factory::getUser($item->checked_out)->username;
							    echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('COM_XBPEOPLE_OPENEDBY').':,'.$couname, $item->checked_out_time, 'person.', $canCheckin); 
							} ?>
							
							<a href="<?php echo $pelink.$item->id; ?>" title="<?php echo Text::_('COM_XBPEOPLE_EDIT_PERSON'); ?>">
								<?php echo ($item->firstname=='')? '... ' : $item->firstname; ?>
								<?php echo ' '.$item->lastname; ?> 
							</a>
							<br />
							<span class="xb08 xbnorm"><i><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></i></span>
						</p>
						<p>
						<?php 
							if($item->year_born > 0) { echo '<i>'.Text::_('COM_XBPEOPLE_BORN').' </i>: '.$item->year_born;} 
							if($item->year_died > 0) { 
								echo '&nbsp;&nbsp;<i>'.Text::_('COM_XBPEOPLE_DIED').' </i>: '.$item->year_died;
							}
							if($item->nationality) { 
                        		echo '<br /><i>'.Text::_('COM_XBPEOPLE_CAPNATIONALITY').' </i>: '.$item->nationality;
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
    								<?php echo Text::_('COM_XBPEOPLE_BIOG_EXTRACT'); ?>: </span>
    								<?php echo XbfilmsGeneral::makeSummaryText($item->biography,200); ?>
    							<?php else : ?>
    								<?php echo Text::_('COM_XBPEOPLE_NO_SUMMARY_BIOG'); ?></span>
    							<?php endif; ?>
    						<?php endif; ?>
                        </p>
                        <?php if ((!empty($item->biography)) && (strlen(strip_tags($item->biography))>200)) : ?>
                        	<p class="xbnit xb09">   
                             <?php 
                             echo Text::_('COM_XBPEOPLE_CAPBIOG').' '.str_word_count(strip_tags($item->biography)).' '.Text::_('COM_XBPEOPLE_WORDS'); 
                             ?>
							</p>
						<?php endif; ?>

						<?php if($item->ext_links_cnt >0 ) : ?>
							<p class="xbnit xb095">	
								<?php echo Text::_('COM_XBPEOPLE_FIELD_EXTLINK_LABEL').': '; 
	                            echo '<span class="xbnamelist">';
	                            echo $item->ext_links_list.'</span>'; ?>
	                    	</p>
						<?php endif; ?>
                    </td>
					<td>
						<?php if ($item->bookcnt>0) : ?> 
							<?php $tlist='';
							foreach ($item->blist as $bk) {
								$tlist .= $bk->title.' ('.$bk->role.')<br />';
							} ?>
						<div class="hasPopover" title data-original-title="Book Roles"
							data-content="<?php echo $tlist; ?>">
							<a href="<?php echo $bplink; ?>" >
							<?php echo Text::_('with').' '.$item->bookcnt.' ';
                            echo Text::_(($item->bookcnt==1)?'COM_XBPEOPLE_BOOKROLE':'COM_XBPEOPLE_BOOKROLES'); ?>
						    </a>
							</div>
							<br />
						<?php endif; ?> 
						<?php if ($item->filmcnt>0) : ?> 
							<?php $tlist='';
							foreach ($item->flist as $f) {
								$tlist .= $f->title.' ('.$f->role.')<br />';
							} ?>
						<div class="hasPopover" title data-original-title="Film Roles"
							data-content="<?php echo $tlist; ?>">
							<a href="<?php echo $fplink; ?>" >
							<?php echo Text::_('with').' '.$item->filmcnt.' ';
							echo Text::_(($item->filmcnt==1)?'COM_XBPEOPLE_FILMROLE':'COM_XBPEOPLE_FILMROLES'); ?>
							</a></div>
							<?php endif; ?>
					</td>
					<td>
						<p><a  class="label label-success" href="<?php echo $celink . $item->catid; ?>" 
							title="<?php echo Text::_( 'COM_XBPEOPLE_VIEW_CATEGORY' );?>::<?php echo $item->category_title; ?>">
								<?php echo $item->category_title; ?>
						</a></p>						
						
						<ul class="inline">
						<?php foreach ($item->filmtags as $t) : ?>
							<li><a href="<?php echo $tvlink.$t->id; ?>" class="label label-info">
								<?php echo $t->title; ?></a>
							</li>													
						<?php endforeach; ?>
						<?php foreach ($item->booktags as $t) : ?>
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
            echo HTMLHelper::_(
            'bootstrap.renderModal',
            'collapseModal',
            array(
                'title' => Text::_('COM_XBPEOPLE_BATCH_TITLE'),
                'footer' => $this->loadTemplate('batch_footer')
            ),
            $this->loadTemplate('batch_body')
        ); ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbpeopleHelper::credit();?></p>

