<?php
/*******
 * @package xbPeople
 * @filesource admin/views/character/tmpl/edit.php
 * @version 0.9.6.f 9th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HtmlHelper::_('behavior.formvalidator');
HtmlHelper::_('behavior.keepalive');
HtmlHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HtmlHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HtmlHelper::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo Route::_('index.php?option=com_xbpeople&layout=edit&id=' . (int) $this->item->id); ?>"
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
            				src="<?php echo Uri::root() . $this->form->getValue('image');?>" />
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
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'links', Text::_('COM_XBPEOPLE_FILMS_BOOKS')); ?>
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
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
		<?php echo HtmlHelper::_('bootstrap.addTab', 'myTab', 'publishing', Text::_('XBCULTURE_PUBLISHING')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
	</div>
  </div>

    <input type="hidden" name="task" value="character.edit" />
    <?php echo HtmlHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbpeopleHelper::credit();?></p>
