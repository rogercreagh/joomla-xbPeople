<?php
/*******
 * @package xbPeople
 * @filesource admin/views/group/tmpl/edit.php
 * @version 1.0.3.3 31st January 2023
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

HtmlHelper::_('behavior.tabState');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

//set styles for quick person modal
$document = Factory::getDocument();
$style = '.controls .btn-group > .btn  {min-width: unset;padding:3px 12px 4px;}';
$document->addStyleDeclaration($style);
	
//     .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
?>
<style type="text/css" media="screen">
	.xbpvmodal .modal-content {padding:15px;max-height:calc(100vh - 190px); overflow:scroll; }
    .xbqpmodal .modal-body {height:340px;} 
    .xbqpmodal .modal-body iframe { height:310px;}
</style>
<form action="<?php echo Route::_('index.php?option=com_xbpeople&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span10">
     		<div class="row-fluid">
       			<div class="span11 form-vertical" >
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
                 <div class="span1 form-vertical">
                   <?php echo $this->form->renderField('id'); ?>
                </div>
			</div>
    		<div class="row-fluid form-vertical">
                <div class="span4">
                   <?php echo $this->form->renderField('summary'); ?>
                </div>
              	<div class="span8">
        			<div class="xbmw1200 xbcentre">
        				<?php echo $this->form->renderField('ext_links'); ?>
        			</div>
              	</div>
         	</div>
        </div>
        <div class="span2">
    		<?php if($this->form->getValue('picture')) : ?>
    			<div class="control-group">
    				<img class="img-polaroid hidden-phone" style="max-width:100%;" 
        				src="<?php echo Uri::root() . $this->form->getValue('picture');?>" />
    			</div>
    		<?php else : ?>
    			<div class="xbbox xbboxwht xbnit xbtc"><?php echo Text::_('XBCULTURE_NO_PICTURE'); ?></div>
    		<?php endif; ?>
        </div>
    </div>
    
    <div class="row-fluid">
      	<div class="span12">
			<?php echo HtmlHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

    		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('XBCULTURE_GENERAL')); ?>
    		<div class="row-fluid">
    			<div class="span9">
    				<div class="row-fluid">
    					<fieldset class="form-vertical">
    						<div class="span6">
        	     				<?php echo $this->form->renderField('year_formed'); ?>
        	     			</div>
        	     			<div class="span6">
            	 				<?php echo $this->form->renderField('year_disolved'); ?>
        	     			</div>
    					</fieldset>
    				</div>
    				<fieldset class="form-vertical">
     					<div class="xbmw1000 xbcentre">
                         	<?php echo $this->form->renderField('description'); ?>
            			</div>
    				</fieldset>
    			</div>
    			<div class="span3">
     				<fieldset class="form-vertical">
               			<?php echo $this->form->renderField('picture'); ?>
     				</fieldset>
    					<?php if ($this->grouptaggroup_parent) : ?>
    						<h4>Group Tags</h4>
     						<?php  $this->form->setFieldAttribute('tags','label',Text::_('XBCULTURE_ALLTAGS'));
     						    $this->form->setFieldAttribute('tags','description',Text::_('XBCULTURE_ALLTAGS_DESC'));						    
     						    $this->form->setFieldAttribute('grouptaggroup','label',$this->taggroupinfo[$this->grouptaggroup]['title']);
     						    $this->form->setFieldAttribute('grouptaggroup','description',$this->taggroupinfo[$this->grouptaggroup]['description']);
     						    echo $this->form->renderField('grouptaggroup'); 
    						endif; ?>
     				<h4><?php echo Text::_('XBCULTURE_STATUS_CATS_TAGS'); ?></h4> 				
    				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
    			</div>
			</div>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'members', ucfirst(Text::_('XBCULTURE_MEMBERS'))); ?>
			<h3>Group Members</h3>
			<fieldset class="form-vertical">
				<div class="xbmw1400 xbcentre">
            		<?php echo $this->form->renderField('grouppersonlist'); ?>
				</div>
				<div class="xbbox xbboxwht" style="margin:0 auto 30px; width:350px;" >
         			<h4><?php echo Text::_('XBCULTURE_QUICK_P_ADD');?></h4>
        			<p class="xbnote"><?php echo Text::_('XBCULTURE_QUICK_P_NOTE');?></p> 
					<a class="btn btn-small" data-toggle="modal" 
						href="index.php?option=com_xbpeople&view=group&layout=modalnewp&tmpl=component" 
						data-target="#ajax-modal">
        			<!-- 
        			 <a href="" data-toggle="modal" class="btn btn-small" data-target="#ajax-qnpmodal" >
        			 -->
						<i class="icon-new">
						</i><?php echo Text::_('XBCULTURE_ADD_NEW_P');?></a>
         		</div>					
			</fieldset>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		<?php if($this->xbbooks_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'blinks', ucfirst(Text::_('XBCULTURE_BOOKS'))); ?>
    			<h3>Books</h3>
    			<fieldset class="form-vertical">
    				<div class="xbmw1100 xbcentre">
        				<?php echo $this->form->renderField('groupbooklist'); ?>
        			</div>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>
		<?php if($this->xbevents_ok) : ?>
    		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'elinks', ucfirst(Text::_('XBCULTURE_EVENTS'))); ?>
    			<h3>Group Events</h3>
    			<fieldset class="form-vertical">
    				<div class="xbmw1100 xbcentre">
        				<?php echo $this->form->renderField('groupeventlist'); ?>
        			</div>
    			</fieldset>
    		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>
		<?php if($this->xbfilms_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'flinks', ucfirst(Text::_('XBCULTURE_FILMS'))); ?>
    			<h3>Films</h3>
    			<fieldset class="form-vertical">
    				<div class="xbmw1100 xbcentre">
        				<?php echo $this->form->renderField('groupfilmlist'); ?>
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

    <input type="hidden" name="task" value="group.edit" />
    <?php echo HtmlHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbpeople');?></p>
<script>
jQuery(document).ready(function(){
//for quick person modal
    jQuery('#ajax-modal').on('show', function () {
        // Load view vith AJAX
        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#'+jQuery(this).attr('id')+'"]').attr('href'));
    })
    jQuery('#ajax-modal').on('hidden', function () {
     //document.location.reload(true);
     Joomla.submitbutton('group.apply');
    })
//for preview modal
    jQuery('#ajax-gpvmodal').on('show', function () {
        // Load view vith AJAX
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=group&layout=default&tmpl=component&id='+window.pvid);
    })
    jQuery('#ajax-gpvmodal').on('hidden', function () {
     document.location.reload(true);
    })
});
</script>
<div class="modal fade xbpvmodal" id="ajax-gpvmodal" style="max-width:1000px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Group</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<!-- quick person modal window 
<div class="modal fade xbpvmodal" id="ajax-qnpmodal" style="max-width:1100px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Quick New Person Form</h4>
        </div>
        <div class="modal-content">
         </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary"  onclick="document.getElementById('newp').contentWindow.Joomla.submitbutton('person.save');" data-dismiss="modal">Save &amp; Close</button>
        </div>
        
    </div>
</div>
-->
<div class="modal fade xbqpmodal" id="ajax-modal" style="max-width:1100px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>


