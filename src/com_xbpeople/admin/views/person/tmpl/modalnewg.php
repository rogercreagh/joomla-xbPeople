<?php
/*******
 * @package xbPeople
 * @filesource admin/views/event/tmpl/modalnewp.php
 * @version 1.0.0.5 19th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
     <h4 class="modal-title">Quick New Group</h4>
     

</div>
<div class="modal-body">
    <div style="margin:0 30px;">
		<iframe src="index.php?option=com_xbpeople&view=group&layout=qnew&tmpl=component" title="Quick Group Form" id="newg"></iframe>
	</div>    
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary"  onclick="document.getElementById('newg').contentWindow.Joomla.submitbutton('group.save');" data-dismiss="modal">Save &amp; Close</button>
</div>

