<?php
/*******
 * @package xbPeople
 * @filesource admin/views/group/tmpl/qnew.php
 * @version 1.0.2.8 14th January 2023
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
<form action="<?php echo Route::_('index.php?option=com_xbpeople&view=group&layout=qnew&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" style="margin:0;">
    <div class="row-fluid">
    	<div class="span9">
             <?php echo $this->form->renderField('title'); ?>
        </div>
		<div class="span3">
			<?php echo $this->form->renderField('state'); ?> 
		</div>
		<div class="span3">
			<?php echo $this->form->renderField('catid'); ?> 
		</div>
	</div>
    <div class="row-fluid">
    	<div class="span6">
			<?php echo $this->form->renderField('year_formed'); ?>
			<?php echo $this->form->renderField('year_disolved'); ?>
		</div>
    	<div class="span6">
    		<?php echo $this->form->renderField('summary'); ?>
    	</div>
    </div>
    <?php echo $this->form->renderField('ext_links'); ?>
    <input type="hidden" name="task" value="group.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
