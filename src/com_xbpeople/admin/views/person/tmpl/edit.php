<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/tmpl/edit.php
 * @version 1.0.0.5 21st December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

//set styles to remove control button min width and set padding 
$document = Factory::getDocument();
$style = '.controls .btn-group > .btn  {min-width: unset;padding:3px 12px 4px;}';
$document->addStyleDeclaration($style);
        
?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbqgmodal .modal-body {height:270px;} 
    .xbqgmodal .modal-body iframe { height:240px;}
</style>
<form action="<?php echo Route::_('index.php?option=com_xbpeople&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span9">
    		<div class="row-fluid form-vertical">
        		<div class="pull-left" >
             		<?php echo $this->form->renderField('firstname'); ?>
        		</div>
        		<div class="pull-left xbml15">
            		<?php echo $this->form->renderField('lastname'); ?>
        		</div>
           	</div>
			<div class="row-fluid form-vertical">
            	<div class="span4">
                	<?php echo $this->form->renderField('alias'); ?> 
            	</div>
            	<div class="span4">
                	<?php echo $this->form->renderField('summary'); ?>
				</div>
				<div class="span2 offset1">
                	<?php echo $this->form->renderField('id'); ?>
				</div>
          	</div>
          </div>
         <div class="pull-right span3">
    		<?php $src = $this->form->getValue('portrait');
    		    if($src != '') {
                    if (!file_exists(JPATH_ROOT.'/'.$src)) {
                        $src = 'media/com_xbpeople/images/nofile.jpg'; //
                    } ?>
					<div class="control-group">
    					<img class="img-polaroid hidden-phone" style="max-height:200px;min-width:24px;" 
        				src="<?php echo Uri::root().$src;?>" />
    				</div>
    		<?php } ?>
        </div>
    </div>
    
    <div class="row-fluid">
      <div class="span12">
		<?php echo HtmlHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('XBCULTURE_GENERAL')); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid">
					<div class="span4">
						<fieldset class="form-vertical">
    	     				<?php echo $this->form->renderField('year_born'); ?>
        	 				<?php echo $this->form->renderField('year_died'); ?>
         					<?php echo $this->form->renderField('nationality'); ?>
						</fieldset>
					</div>
					<div class="span8">
 							<?php echo $this->form->renderField('ext_links'); ?>					
   					</div>
        		</div>
          		<div class="row-fluid">
					<div class="span12">
						<fieldset class="form-horizontal">
							<div style="max-width:1200px;"><?php echo $this->form->renderField('biography'); ?></div>
						</fieldset>
					</div>        		
        		</div>		
			</div>
			<div class="span3">
 				<fieldset class="form-vertical">
           			<?php echo $this->form->renderField('portrait'); ?>
 				</fieldset>
					<?php if ($this->peeptaggroup_parent) : ?>
						<h4>Person Tags</h4>
 						<?php  $this->form->setFieldAttribute('tags','label',Text::_('XBCULTURE_ALLTAGS'));
 						    $this->form->setFieldAttribute('tags','description',Text::_('XBCULTURE_ALLTAGS_DESC'));						    
 						    $this->form->setFieldAttribute('peeptaggroup','label',$this->taggroupinfo[$this->peeptaggroup_parent]['title']);
 						    $this->form->setFieldAttribute('peeptaggroup','description',$this->taggroupinfo[$this->peeptaggroup_parent]['description']);
 						    echo $this->form->renderField('peeptaggroup'); 
						endif; ?>
 				<h4><?php echo Text::_('XBCULTURE_STATUS_CATS_TAGS'); ?></h4> 				
				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		
		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'groups', Text::_('XBCULTURE_GROUPS')); ?>
			<h3><?php Text::_('XBCULTURE_GROUPS'); ?></h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="span9">
                			<?php echo $this->form->renderField('persongrouplist'); ?>
                		</div>
                		<div class="span3 xbbox xbboxwht">
                 			<h4><?php echo Text::_('XBCULTURE_QUICK_G_ADD');?></h4>
                			<p class="xbnote"><?php echo Text::_('XBCULTURE_QUICK_G_NOTE');?></p> 
        					<a class="btn btn-small" data-toggle="modal" 
        						href="index.php?option=com_xbpeople&view=person&layout=modalnewg&tmpl=component" 
        						data-target="#ajax-qgmodal"><i class="icon-new">
        						</i><?php echo Text::_('XBCULTURE_ADD_NEW_G');?></a>
                 		</div>
			        </div>
    			</fieldset>			
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		
		<?php if($this->xbevents_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'elinks', ucfirst(Text::_('XBCULTURE_EVENTS'))); ?>
    			<h3>Events</h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="span6">
                			<?php echo $this->form->renderField('eventpersonlist'); ?>
                		</div>
			        </div>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>

		<?php if($this->xbbooks_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'blinks', ucfirst(Text::_('XBCULTURE_BOOKS'))); ?>
    			<h3>Books</h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="span6">
                			<?php echo $this->form->renderField('bookauthorlist'); ?>
                			<?php echo $this->form->renderField('bookeditorlist'); ?>
						</div>
                      	<div class="span6">
            				<?php echo $this->form->renderField('bookmenlist'); ?>
            				<?php echo $this->form->renderField('bookotherlist'); ?>
				        </div>
			        </div>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>

		<?php if($this->xbfilms_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'flinks', ucfirst(Text::_('XBCULTURE_FILMS'))); ?>
    			<h3>Films</h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="span6">
            				<?php echo $this->form->renderField('filmdirectorlist'); ?>
            				<?php echo $this->form->renderField('filmproducerlist'); ?>
           					<?php echo $this->form->renderField('filmcrewlist'); ?>
						</div>
                      	<div class="span6">
           					<?php echo $this->form->renderField('filmactorlist'); ?>
           					<?php echo $this->form->renderField('filmappearslist'); ?>
				        </div>
			        </div>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
       	<?php endif; ?>
		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'publishing', Text::_('XBCULTURE_PUBLISHING')); ?>
    		<div class="row-fluid form-horizontal-desktop">
    			<div class="span6">
    				<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
    			</div>
    			<div class="span6">
    				<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
    			</div>
    		</div>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
	</div>
  </div>

    <input type="hidden" name="task" value="person.edit" />
    <?php echo HtmlHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbpeople');?></p>
<script>
jQuery(document).ready(function(){
//for preview modal
    jQuery('#ajax-pvmodal').on('show', function () {
        // Load view vith AJAX
        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#'+jQuery(this).attr('id')+'"]').attr('href'));
    })
    jQuery('#ajax-pvmodal').on('hidden', function () {
     //document.location.reload(true);
     //Joomla.submitbutton('group.apply');
    })
//for quickgroup modal
     jQuery('#ajax-qgmodal').on('show', function () {
        // Load view vith AJAX
        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#'+jQuery(this).attr('id')+'"]').attr('href'));
    })
    jQuery('#ajax-qgmodal').on('hidden', function () {
     //document.location.reload(true);
     Joomla.submitbutton('person.apply');
    })    
});
</script>
<!-- preview modal window -->
<div class="modal fade xbpvmodal" id="ajax-pvmodal" style="max-width:80%">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<!-- quickgroup modal window -->
<div class="modal fade xbqgmodal" id="ajax-qgmodal" style="max-width:80%">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>


