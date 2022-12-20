<?php
/*******
 * @package xbPeople
 * @filesource admin/views/character/tmpl/qnew.php
 * @version 1.0.0.5 18th December 2022
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
    	<div class="span9">
             <?php echo $this->form->renderField('name'); ?>
        </div>
		<div class="span3">
			<?php echo $this->form->renderField('state'); ?> 
		</div>
    </div>
    <?php echo $this->form->renderField('ext_links'); ?>
    <input type="hidden" name="task" value="group.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
