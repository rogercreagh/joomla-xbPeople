<?php
/*******
 * @package xbPeople
 * @filesource admin/views/character/tmpl/edit.php
 * @version 0.4.1 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbpeople&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span9">
      		<div class="row-fluid form-vertical">
        		<div class="pull-left" >
             		<?php echo $this->form->renderField('name'); ?>
        		</div>
			</div>
			<div class="row-fluid form-vertical">
               <div class="span8">
                   <?php echo $this->form->renderField('alias'); ?> 
              </div>
              <div class="span4">
                   <?php echo $this->form->renderField('id'); ?>
                   <?php echo $this->form->renderField('summary'); ?>
              </div>
          </div>
      </div>
           <div class="pull-right span3">
        		<?php if($this->form->getValue('image')){?>
        			<div class="control-group">
        				<img class="img-polaroid hidden-phone" style="max-height:200px;min-width:24px;" 
            				src="<?php echo JUri::root() . $this->form->getValue('image');?>" />
        			</div>
        		<?php } ?>
            </div>  
    </div>
    <div class="row-fluid">
      <div class="span12">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_XBFILMS_FIELDSET_GENERAL')); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform form-vertical">
					<div class="row-fluid">
    					<div class="span12">
                 			<?php echo $this->form->renderField('description'); ?>
    					</div>
					</div>
				</fieldset>
			</div>
			<div class="span3">
 				<fieldset class="form-vertical">
           			<?php echo $this->form->renderField('image'); ?>
 				</fieldset>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'links', JText::_('Films and Books')); ?>
			<div class="row-fluid">
			<?php if($this->xbfilms_ok) : ?>
        		<div class="span6">
        			<h3>Films</h3>
        			<fieldset class="form-vertical">
                   		<?php echo $this->form->renderField('filmcharlist'); ?>
        			</fieldset>
        		</div>
        	<?php endif; ?>
			<?php if($this->xbbooks_ok) : ?>
       			<div class="span6">
        			<h3>Books</h3>
        			<fieldset class="form-vertical">
                   		<?php echo $this->form->renderField('bookcharlist'); ?>
        			</fieldset>
        		</div>
        	<?php endif; ?>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_XBFILMS_FIELDSET_PUBLISHING')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	</div>
  </div>

    <input type="hidden" name="task" value="character.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbpeopleHelper::credit();?></p>
