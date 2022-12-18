<?php
/*******
 * @package xbPeople
 * @filesource admin/views/person/tmpl/qnew.php
 * @version 1.0.0.4 18th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

?>
<div class="xbml20 xbmr20">
<form action="<?php echo Route::_('index.php?option=com_xbpeople&layout=qnew&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" style="margin:0;">
    <div class="row-fluid">
    	<div class="span12">
    		<div class="row-fluid form-vertical">
        		<div class="pull-left" >
             		<?php echo $this->form->renderField('firstname'); ?>
        		</div>
        		<div class="pull-left xbml15">
            		<?php echo $this->form->renderField('lastname'); ?>
        		</div>
           	</div>
        </div>
    </div>
    <div class="row-fluid">
    	<div class="span4">
			<?php echo $this->form->renderField('state'); ?> 
		</div>
		<div class="span4">
			<?php echo $this->form->renderField('year_born'); ?>
			<?php echo $this->form->renderField('year_died'); ?>
		</div>
		<div class="span4">
			<?php echo $this->form->renderField('nationality'); ?>
		</div>
    </div>
    <?php echo $this->form->renderField('ext_links'); ?>
    <input type="hidden" name="task" value="person.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
