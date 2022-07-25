<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/tmpl/edit.php
 * @version 0.9.9.3 25th July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
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

?>
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
                   <?php echo $this->form->renderField('id'); ?>
              </div>
              <div class="span4">
                   <?php echo $this->form->renderField('summary'); ?>
              </div>
          	</div>
          </div>
         <div class="pull-right span3">
    		<?php if($this->form->getValue('portrait')) : ?>
    			<div class="control-group">
    				<img class="img-polaroid hidden-phone" style="max-height:200px;min-width:24px;" 
        				src="<?php echo Uri::root() . $this->form->getValue('portrait');?>" />
    			</div>
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
					<div class="span5">
						<fieldset class="form-vertical">
    	     				<?php echo $this->form->renderField('year_born'); ?>
        	 				<?php echo $this->form->renderField('year_died'); ?>
						</fieldset>
					</div>
					<div class="span7">
         					<?php echo $this->form->renderField('nationality'); ?>
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
				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo HtmlHelper::_('bootstrap.endTab'); ?>
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
