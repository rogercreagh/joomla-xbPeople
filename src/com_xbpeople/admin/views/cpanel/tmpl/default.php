<?php
/*******
 * @package xbPeople
 * @filesource admin/views/cpanel/tmpl/default.php
 * @version 0.3.0 19th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

jimport('joomla.html.html.bootstrap');

$pelink='index.php?option=com_xbpeople&view=person&layout=edit&id=';
$chelink='index.php?option=com_xbpeople&view=character&layout=edit&id=';

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbpeople&view=cpanel'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="<?php echo ($this->client['mobile']? 'span3' : 'span2'); ?>">
		<?php echo $this->sidebar; ?>
		<p> </p>
		<div class="row-fluid hidden-phone">
        	<?php echo JHtml::_('bootstrap.startAccordion', 'slide-cpanel', array('active' => 'sysinfo')); ?>
        		<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', Text::_('COM_XBPEOPLE_SYSINFO'), 'sysinfo'); ?>
        			<p><b><?php echo Text::_( 'COM_XBPEOPLE_COMPONENT' ); ?></b>
						<br /><?php echo Text::_('XBCULTURE_VERSION').': '.$this->xmldata['version'].'<br/>'.
							$this->xmldata['creationDate'];?>
						<br /><?php if ($this->xbbooks_ok) {
						          echo Text::_('XBCULTURE_BOOKSOK') ;
						      }?>
						<br /><?php if ($this->xbfilms_ok) {
						          echo Text::_('XBCULTURE_FILMSOK') ;
						      }?>
						<br /><?php if ($this->xbgigs_ok) {
						          echo Text::_('XBCULTURE_GIGSOK') ;
						      }?>
						      
					</p>
					<p><b><?php echo Text::_( 'XBCULTURE_CAPCLIENT' ); ?></b>
						<br/><?php echo $this->client['platform'].'<br/>'.$this->client['browser']; ?>
					</p>
        		<?php echo JHtml::_('bootstrap.endSlide'); ?>
        		<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', Text::_('COM_XBPEOPLE_ABOUT'), 'about'); ?>
        			<p><?php echo Text::_('COM_XBPEOPLE_ABOUT_INFO' ).Text::_('XBCULTURE_JVER' ).Text::_('COM_XBPEOPLE_ABOUT_LINKS' ); ?></p>
        		<?php echo JHtml::_('bootstrap.endSlide'); ?>
        		<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBCULTURE_LICENSE'), 'license'); ?>
        			<p><?php echo Text::_( 'XBCULTURE_LICENSE_INFO' ); ?>
        				<br /><?php echo $this->xmldata['copyright']; ?>
        			</p>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
		</div>
	</div>
</div>
<div class="<?php echo ($this->client['mobile']? 'span9' : 'span10'); ?>">
<h4><?php echo Text::_( 'XBCULTURE_CAPSUMMARY' ); ?></h4>
	<div class="row-fluid">
		<div class="span6">
			<div class="xbbox xbboxgrn">
				<div class="row-fluid"><div class="span12">
				<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_CAPPEOPLE'); ?>
					<span class="pull-right">
						<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_CAPTOTAL'); ?>: </span>
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
							<?php echo Text::_('XBCULTURE_CAPPUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->perStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->perStates['unpublished']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPUNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->perStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->perStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="xbbox xbboxcyan">
				<div class="row-fluid"><div class="span12">
				<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_CAPCHARACTERS'); ?>
					<span class="pull-right">
						<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_CAPTOTAL'); ?>: </span>
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
							<?php echo Text::_('XBCULTURE_CAPPUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->charStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->charStates['unpublished']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPUNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->charStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->charStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->charStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->charStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php if((!empty($this->orphanpeep)) || (!empty($this->orphanchar))) : ?>
			<div class="xbbox xbboxred">
				<h2 class="xbtitle">
					<?php echo Text::_('XBCULTURE_CAPORPHANS'); ?>
				</h2>
                <?php if(!empty($this->orphanpeep)) : ?>
				<div class="row-striped">
					<span class="badge badge-important pull-right"><?php echo count($this->orphanpeep); ?></span>
					<?php echo Text::_('XBCULTURE_CAPPEOPLE'); ?>
					<?php foreach($this->orphanpeep as $rev) {
						echo '<br /><a class="xbml10" href="'.$pelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
                <?php if(!empty($this->orphanchars)) : ?>
				<div class="row-striped">
					<span class="badge badge-important pull-right"><?php echo count($this->orphanchars); ?></span>
					<?php echo Text::_('XBCULTURE_CAPCHARACTERS'); ?>
					<?php foreach($this->orphanchars as $rev) {
						echo '<br /><a class="xbml10" href="'.$chelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
			</div>
			<?php  endif; ?>
		</div>
		<div class="span6">
			<div class="xbbox xbboxyell">
 				<h2 class="xbtitle">
					<span class="badge badge-info pull-right">
						<?php echo $this->pcatStates['total']; ?></span> 
					<?php echo Text::_('People Categories'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->pcatStates['published']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPPUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->pcatStates['unpublished']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPUNPUBLISHED'); ?>
						</div>
 					</div>
 					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->pcatStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->pcatStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
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
			<div class="xbbox xbboxgrey">
				<h2 class="xbtitle">
					<span class="badge badge-info pull-right"><?php echo ($this->tags['tagcnts']['percnt']  + $this->tags['tagcnts']['charcnt']) ; ?></span> 
					<?php echo Text::_('Tagged Items'); ?>
				</h2>
				<div class="row-striped">
                    <div class="row-fluid">
                      <?php echo 'People: ';
						echo '<span class="percnt badge pull-right">'.$this->tags['tagcnts']['percnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'Characters: ';
						echo '<span class="revcnt badge pull-right">'.$this->tags['tagcnts']['charcnt'].'</span>'; ?>
                    </div>  
                 </div>
				 <h2 class="xbtitle">Tag counts <span class="xb09 xbnorm"><i>(people:chars)</i></span></h2>
              <div class="row-fluid">
                 <div class="row-striped">
					<div class="row-fluid">
						<?php echo $this->taglist; ?>
                   </div>
                 </div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
		</div>
		<div class="span6">
		</div>
	</div>
</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="clearfix"></div>
<p><?php echo XbpeopleHelper::credit();?></p>
