<?php
/*******
 * @package xbPeople
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 0.10.0.5 30th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

jimport('joomla.html.html.bootstrap');

$pelink='index.php?option=com_xbpeople&view=person&layout=edit&id=';
$chelink='index.php?option=com_xbpeople&view=character&layout=edit&id=';

?>
<form action="<?php echo Route::_('index.php?option=com_xbpeople&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
		<hr />
        <div class="xbinfopane">
        	<div class="row-fluid hidden-phone">
        	<?php echo HtmlHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
        		<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBPEOPLE_SYSINFO'), 'sysinfo','xbaccordion'); ?>
        			<p><b><?php echo Text::_( 'XBPEOPLE_COMPONENT' ); ?></b>
    					<br /><?php echo Text::_('XBCULTURE_VERSION').': '.$this->xmldata['version'].' '.
    						$this->xmldata['creationDate'];?>
                                  <br /><i></i>
                                  <?php  if (XbcultureHelper::penPont()) {
                                      echo Text::_('XBCULTURE_BEER_THANKS'); 
                                  } else {
                                      echo Text::_('XBCULTURE_BEER_LINK');
                                  }?>
                                  </i></p>
                                  <?php echo Text::_('XBCULTURE_OTHER_COMPS'); ?>
                                  <ul>
                              	<?php $coms = array('com_xbbooks','com_xbfilms','com_xbevents');
                              	foreach ($coms as $element) {
                              	    echo '<li>';
                                  	$ext = XbcultureHelper::getExtensionInfo($element);
                                  	if ($ext) {
                                  	    //todo add mouseover description
                                  	    echo $ext['name'].' v'.$ext['version'].' '.Text::_('XBCULTURE_INSTALLED');
                                  	    if (!$ext['enabled']) echo '<b><i>'.Text::_('XBCULTURE_NOT_ENABLED').'</i></b>';
                                  	} else {
                                  	    echo '<i>'.$element.' '.Text::_('XBCULTURE_NOT_INSTALLED').'</i>';
                                  	}
                                    echo '</li>';
                              	}
                              	
                              	?>
                              	</ul>
                                  <?php echo Text::_('XBCULTURE_MODULES'); ?>
                              	<ul>
                              	<?php $mods = array('mod_xbculture_list','mod_xbculture_randimg','mod_xbculture_recent');
                              	foreach ($mods as $element) {
                              	    echo '<li>';
                                  	$mod = XbcultureHelper::getExtensionInfo($element);
                                  	if ($mod) {
                                  	    echo $mod['name'].' v'.$mod['version'].' '.Text::_('XBCULTURE_INSTALLED');
                                  	    if (!$mod['enabled']) echo '<b><i>'.Text::_('XBCULTURE_NOT_ENABLED').'</i></b>';
                                  	} else {
                                  	    echo '<i>'.$element.' '.Text::_('XBCULTURE_NOT_INSTALLED').'</i>';
                                  	}
                                    echo '</li>';
                              	}                             	
                              	?>
                              	</ul>
                          	</p>
                          	<p><b><?php echo Text::_( 'XBCULTURE_CLIENT'); ?></b>
                              <br/><?php echo Text::_( 'XBCULTURE_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XBCULTURE_BROWSER').' '.$this->client['browser']; ?>
                         	</p>
    					<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
                      <?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBCULTURE_ABOUT'), 'about','xbaccordion'); ?>
                          <p><?php echo Text::_( 'XBPEOPLE_ABOUT_INFO' ); ?></p>
                      <?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
                      <?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBCULTURE_LICENSE'), 'license','xbaccordion'); ?>
                          <p><?php echo Text::_( 'XBCULTURE_LICENSE_GPL' ); ?>
                          	<br><?php echo Text::sprintf('XBCULTURE_LICENSE_INFO','xbPeople');?>
                              <br /><?php echo $this->xmldata['copyright']; ?>
                          </p>
                      <?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
				<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
        	</div>
        </div>
	</div>
	<div id="j-main-container" >
	<h3><?php echo Text::_( 'XBCULTURE_SUMMARY' ); ?></h3>
	<div class="row-fluid">
		<div class="span5">
			<div class="xbbox xbboxgrn">
				<div class="row-fluid"><div class="span12">
				<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>
					<span class="pull-right">
						<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_TOTAL'); ?>: </span>
						<span class="badge percnt xbmr20"><?php echo $this->totPeople;?></span>
					</span>	
				</h2>
				<p class="pull-right">
					<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_INBOOKS'); ?>: </span>
					<span class="badge <?php echo ($this->xbbooks_ok) ? 'badge-info' : ''?>"><?php echo $this->bookPeople;?></span>
					&nbsp;&nbsp;
					<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_INFILMS'); ?></span>
					<span class="badge <?php echo ($this->xbfilms_ok) ? 'badge-info' : ''?>"><?php echo $this->filmPeople;?></span>	
				</p>
				</div></div>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->perStates['published']; ?></span>
							<?php echo Text::_('XBCULTURE_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->perStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->perStates['unpublished']; ?></span>
							<?php echo Text::_('XBCULTURE_UNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->perStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->perStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="xbbox xbboxcyan">
				<div class="row-fluid"><div class="span12">
				<h2 class="xbtitle"><?php echo ucfirst(Text::_('XBCULTURE_CHARACTERS')); ?>
					<span class="pull-right">
						<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_TOTAL'); ?>: </span>
						<span class="badge chcnt xbmr20"><?php echo $this->totChars;?></span>
					</span>	
				</h2>
				<p class="pull-right">
					<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_INBOOKS'); ?>: </span>
					<span class="badge <?php echo ($this->xbbooks_ok) ? 'badge-info' : ''?>"><?php echo $this->bookChars;?></span>
					&nbsp;&nbsp;
					<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_INFILMS'); ?></span>
					<span class="badge <?php echo ($this->xbfilms_ok) ? 'badge-info' : ''?>"><?php echo $this->filmChars;?></span>	
				</p>
				</div></div>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->charStates['published']; ?></span>
							<?php echo Text::_('XBCULTURE_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->charStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->charStates['unpublished']; ?></span>
							<?php echo Text::_('XBCULTURE_UNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->charStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->charStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->charStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->charStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="xbbox xbboxgrey">
				<div class="row-fluid"><div class="span12">
					<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_NUM_ITEMS_TAGGED'); ?>
						<span class="pull-right">
							<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_TOTAL'); ?>: </span>
        					<span class="badge badge-info" style="border: blue solid 1px;"><?php echo ($this->tags['allper']  + $this->tags['allchar']) ; ?></span> 
						</span>
					</h2>
				</div></div>
 				<div class="row-striped">
 					<h4>Items tagged per type</h4>
					<div class="row-fluid">
						<div class="span4">
							<?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_INBOOKS'); ?>: <span class="percnt badge"><?php echo $this->tags['bookper']; ?></span>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_INFILMS'); ?>: <span class="percnt badge"><?php echo $this->tags['filmper']; ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span4">
							<?php echo Text::_('XBCULTURE_CHARACTERS_U'); ?>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_INFILMS'); ?>: <span class="chcnt badge"><?php echo $this->tags['bookchar']; ?></span>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_INFILMS'); ?>: <span class="chcnt badge"><?php echo $this->tags['filmchar']; ?>
						</div>
					</div>
				</div>
 				<hr style="margin: 8px 0;" />
 				<div class="row-striped">
 					<h4>Tags used per type</h4>
					<div class="row-fluid">
						<div class="span4">
							<?php echo Text::_('XBCULTURE_BOOKS_U'); ?>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_PEOPLE'); ?>: <span class="bkcnt badge"><?php echo $this->tags['bookpertags']; ?></span>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_CHARACTERS'); ?>: <span class="flmcnt badge"><?php echo $this->tags['filmpertags']; ?></span>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span4">
							<?php echo Text::_('XBCULTURE_FILMS_U'); ?>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_PEOPLE'); ?>: <span class="bkcnt badge"><?php echo $this->tags['bookchartags']; ?></span>
						</div>
						<div class="span4">
							<?php echo Text::_('XBCULTURE_CHARACTERS'); ?>: <span class="flmcnt badge"><?php echo $this->tags['filmchartags']; ?></span>
						</div>
					</div>
				</div>
 
 			</div>
		</div>
		<div class="span5">
			<div class="xbbox xbboxyell">
 				<h2 class="xbtitle">
					<span class="badge badge-info pull-right" style="border: blue solid 1px;">
						<?php echo $this->pcatStates['total']; ?></span> 
					<?php echo Text::_('XBPEOPLE_PEOPLE_CATS'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->pcatStates['published']; ?></span>
							<?php echo Text::_('XBCULTURE_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->pcatStates['unpublished']; ?></span>
							<?php echo Text::_('XBCULTURE_UNPUBLISHED'); ?>
						</div>
 					</div>
 					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->pcatStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->pcatStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
						</div>
					</div>
                 </div>
                 <h3 class="xbsubtitle">Counts per category<span class="xb09 xbnorm"> <i>(people:characters)</i></span></h3>
                 <div class="row-striped">
					<div class="row-fluid">
						    <?php echo $this->pcatlist; ?>
					</div>
				</div>
			</div>
						<?php if((!empty($this->orphanpeep)) || (!empty($this->orphanchar))) : ?>
			<div class="xbbox xbboxred">
				<h2 class="xbtitle">
					<?php echo Text::_('XBCULTURE_ORPHANS'); ?>
				</h2>
                <?php if(!empty($this->orphanpeep)) : ?>
				<div class="row-striped">
					<span class="badge badge-important pull-right"><?php echo count($this->orphanpeep); ?></span>
					<?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>
					<?php foreach($this->orphanpeep as $rev) {
						echo '<br /><a class="xbml10" href="'.$pelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
                <?php if(!empty($this->orphanchars)) : ?>
				<div class="row-striped">
					<
					<span class="badge badge-important pull-right"><?php echo count($this->orphanchars); ?></span>
					<?php echo Text::_('XBCULTURE_CHARACTERS_U'); ?>
					<?php foreach($this->orphanchars as $rev) {
						echo '<br /><a class="xbml10" href="'.$chelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
			</div>
			<?php  endif; ?>
			
		</div>
		<div class="span2">
			<div class="xbbox xbboxwht">
				<h4><?php echo Text::_('XBCULTURE_CONFIG_OPTIONS'); ?></h4>
				<p>
					<?php echo ($this->savedata) ? 'Data not deleted on unistall' : '<b>Uninstall deletes all people data</b>'; ?>
				</p>
        			<p>
            		<?php echo '<b>'.ucfirst(Text::_('XBCULTURE_CATEGORIES')).'</b><br />';
            		if (($this->show_cat==0) || 
            		    (($this->show_pcat==0) && ($this->show_ccat==0))) {
            		    echo '<i>'.Text::_('XBCULTURE_CATS_HIDDEN_ALL').'</i>';
            		} else {
            		    echo ($this->show_pcat) ? Text::_('XBCULTURE_SHOW_FOR') : Text::_('XBCULTURE_HIDE_FOR');
            		    echo ' '.Text::_('XBCULTURE_PEOPLE');
            		    echo '<br />';
            		    echo ($this->show_ccat) ? Text::_('XBCULTURE_SHOW_FOR') : Text::_('XBCULTURE_HIDE_FOR');
            		    echo Text::_('XBCULTURE_CHARACTERS');
            		}
            		?>
            		</p>
            		<p>
            		<?php echo '<b>'.ucfirst(Text::_('XBCULTURE_TAGS')).'</b><br />';
            		if (($this->show_tags==0) || 
            		    (($this->show_ptags==0) && ($this->show_ctags==0))) {
            		    echo '<i>'.Text::_('XBCULTURE_TAGS_HIDDEN_ALL').'</i>';
            		} else {
            		    echo Text::_('XBCULTURE_SHOW_FOR').' ';
            		    echo ($this->show_ptags) ? Text::_('XBCULTURE_PEOPLE').' ' : '';
            		    echo ($this->show_ctags) ? Text::_('XBCULTURE_CHARACTERS').' ' : '';
            		}
            		?>
            		</p>
            		<p>
            		<?php echo '<b>'.Text::_('XBCULTURE_ALLOW_SEARCH').': </b>';
            		    echo ($this->show_search==0)? Text::_('JNO') : Text::_('JYES'); ?>
            		</p>
            		<p>
            		<?php echo '<b>'.Text::_('XBCULTURE_HIDE_EMPTY_FIELDS').': </b>';
            		    echo ($this->hide_empty==0)? Text::_('JNO') : Text::_('JYES'); ?>
            		</p>    		        		
            		<p>
            		<?php echo '<b>'.Text::_('XBCULTURE_IMAGE_FOLDERS').'</b><br />';
    	        		echo Text::_('XBCULTURE_PORTRAITS').': <code>'.$this->portraits.'</code> ';
            		?>	
            		</p>
            		<p>
            		<?php echo '<b>'.Text::_('XBCULTURE_SHOW_PORTRAITS').'</b><br />'; 
    	        		echo Text::_('XBCULTURE_IN_LISTS').': ';
    	        		echo ($this->show_people_portraits==0)? Text::_('JNO') : Text::_('JYES');
    	        		echo '<br />';
    	        		echo Text::_('XBCULTURE_IN_PEOPLE').': ';
    	        		echo ($this->show_person_portrait==0)? Text::_('JNO') : Text::_('JYES');
            		?>	        		
            		</p>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo HtmlHelper::_('form.token'); ?>
	</div>
</form>

<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbpeople');?></p>
