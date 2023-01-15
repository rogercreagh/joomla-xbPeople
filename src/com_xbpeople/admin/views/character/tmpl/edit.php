<?php
/*******
 * @package xbPeople
 * @filesource admin/views/character/tmpl/edit.php
 * @version 1.0.2.9 15th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HtmlHelper::_('behavior.tabState');
HtmlHelper::_('behavior.formvalidator');
HtmlHelper::_('behavior.keepalive');
HtmlHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HtmlHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HtmlHelper::_('formbehavior.chosen', 'select');

?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
</style>
<form action="<?php echo Route::_('index.php?option=com_xbpeople&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span10">
          	<div class="row-fluid form-vertical">
        		<div class="span11">
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
        		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
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
    		<?php if($this->form->getValue('image')) : ?>
    			<div class="control-group">
    				<img class="img-polaroid hidden-phone" style="max-width:100%;" 
        				src="<?php echo Uri::root() . $this->form->getValue('image');?>" />
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
				<fieldset class="adminform form-vertical">
 					<div class="xbmw1000 xbcentre">
                     	<?php echo $this->form->renderField('description'); ?>
        			</div>
    			</fieldset>
			</div>
    		<div class="span3">
    			<fieldset class="form-vertical">
           			<?php echo $this->form->renderField('image'); ?>
    			</fieldset>
    				<?php if ($this->chartaggroup_parent) : ?>
    			        <h4>Character Tags</h4>
    					<?php  $this->form->setFieldAttribute('tags','label',Text::_('XBCULTURE_ALLTAGS'));
    					    $this->form->setFieldAttribute('tags','description',Text::_('XBCULTURE_ALLTAGS_DESC'));						    
    					    $this->form->setFieldAttribute('chartaggroup','label',$this->taggroupinfo[$this->chartaggroup_parent]['title']);
    					    $this->form->setFieldAttribute('chartaggroup','description',$this->taggroupinfo[$this->chartaggroup_parent]['description']);
    					    echo $this->form->renderField('chartaggroup'); 
    					endif; ?>
    			<h4><?php echo Text::_('XBCULTURE_STATUS_CATS_TAGS'); ?></h4> 				
    			<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
    		</div>
    	</div>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		<?php if($this->xbbooks_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'blinks', ucfirst(Text::_('XBCULTURE_BOOKS'))); ?>
    			<h3><?php echo Text::_('XBCULTURE_BOOKS_U'); ?></h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="xbmw1100 xbcentre">
		               		<?php echo $this->form->renderField('bookcharlist'); ?>
				        </div>
			        </div>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>
		<?php if($this->xbevents_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'elinks', Text::_('XBCULTURE_EVENTS')); ?>
    			<h3><?php echo Text::_('XBCULTURE_EVENTS'); ?></h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="xbmw1100 xbcentre">
		               		<?php echo $this->form->renderField('eventcharlist'); ?>
				        </div>
			        </div>
    			</fieldset>
			<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
    	<?php endif; ?>
		<?php if($this->xbfilms_ok) : ?>
			<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'flinks', ucfirst(Text::_('XBCULTURE_FILMS'))); ?>
    			<h3><?php echo Text::_('XBCULTURE_FILMS_U'); ?></h3>
    			<fieldset class="form-vertical">
                    <div class="row-fluid">
						<div class="xbmw1100 xbcentre">
                       		<?php echo $this->form->renderField('filmcharlist'); ?>
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

    <input type="hidden" name="task" value="character.edit" />
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


