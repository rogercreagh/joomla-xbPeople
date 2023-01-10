<?php
/*******
 * @package xbPeople
 * @filesource admin/views/group/tmpl/edit.php
 * @version 1.0.2.3 9th January 2023
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

//set styles for quick person modal
$document = Factory::getDocument();
$style = '.controls .btn-group > .btn  {min-width: unset;padding:3px 12px 4px;}';
$document->addStyleDeclaration($style);
?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbqpmodal .modal-body {height:330px;} 
    .xbqpmodal .modal-body iframe { height:300px;}
</style>
<form action="<?php echo Route::_('index.php?option=com_xbpeople&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span9">
     		<div class="row-fluid form-horizontal">
        		<div class="pull-left" >
             		<?php echo $this->form->renderField('title'); ?>
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
         <div class="pull-right span3">
    		<?php $src = $this->form->getValue('picture');
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
    	     				<?php echo $this->form->renderField('year_formed'); ?>
        	 				<?php echo $this->form->renderField('year_disolved'); ?>
						</fieldset>
					</div>
					<div class="span8">
 						<?php echo $this->form->renderField('ext_links'); ?>					
   					</div>
        		</div>
          		<div class="row-fluid">
					<div class="span12">
						<fieldset class="form-vertical">
							<div style="max-width:1200px;"><?php echo $this->form->renderField('description'); ?></div>
						</fieldset>
					</div>        		
        		</div>		
			</div>
			<div class="span3">
 				<fieldset class="form-vertical">
           			<?php echo $this->form->renderField('image'); ?>
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
				<div class="row-fluid">
					<div class="span9">
            			<?php echo $this->form->renderField('grouppersonlist'); ?>
					</div>
					<div class="span3 xbbox xbboxwht">
             			<h4><?php echo Text::_('XBCULTURE_QUICK_P_ADD');?></h4>
            			<p class="xbnote"><?php echo Text::_('XBCULTURE_QUICK_P_NOTE');?></p> 
    					<a class="btn btn-small" data-toggle="modal" 
    						href="index.php?option=com_xbpeople&view=group&layout=modalnewp&tmpl=component" 
    						data-target="#ajax-modal"><i class="icon-new">
    						</i><?php echo Text::_('XBCULTURE_ADD_NEW_P');?></a>
             		</div>					
				</div>
			</fieldset>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		<?php if($this->xbevents_ok) : ?>
    		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'elinks', ucfirst(Text::_('XBCULTURE_EVENTS'))); ?>
    			<h3>Group Events</h3>
    			<fieldset class="form-vertical">
        			<?php echo $this->form->renderField('groupeventlist'); ?>
    			</fieldset>
    		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>
		<?php if($this->xbbooks_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'blinks', ucfirst(Text::_('XBCULTURE_BOOKS'))); ?>
    			<h3>Books</h3>
    			<fieldset class="form-vertical">
        			<?php echo $this->form->renderField('groupbooklist'); ?>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>
		<?php if($this->xbfilms_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'flinks', ucfirst(Text::_('XBCULTURE_FILMS'))); ?>
    			<h3>Films</h3>
    			<fieldset class="form-vertical">
        			<?php echo $this->form->renderField('groupfilmlist'); ?>
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
    jQuery('#ajax-pvmodal').on('show', function () {
        // Load view vith AJAX
        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#'+jQuery(this).attr('id')+'"]').attr('href'));
    })
    jQuery('#ajax-pvmodal').on('hidden', function () {
     //document.location.reload(true);
     //Joomla.submitbutton('group.apply');
    })
});
</script>
<!-- quick person modal window -->
<div class="modal fade xbqpmodal" id="ajax-modal" style="max-width:1000px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>


