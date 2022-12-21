<?php
/*******
 * @package xbPeople
 * @filesource admin/views/event/tmpl/modalnewp.php
 * @version 1.0.0.5 21st December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<script>
  jQuery('.iframe-full-height').on('load', function(){
    this.style.height=this.contentDocument.body.scrollHeight+20 +'px';
});
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
    	style="opacity:unset;line-height:unset;border:none;">&times;</button>
     <h4 class="modal-title">Preview Person</h4>
</div>
<div class="modal-body">
    <div style="margin:0 30px;">
		<iframe src="<?php echo JURI::root(); 
            ?>/index.php?option=com_xbpeople&view=person&layout=default&tmpl=component&id=
	       <?php echo JFactory::getApplication()->input->getInt('id'); ?>" 
			title="Preview Person" id="pv" class="iframe-full-height"></iframe>   
	</div>
</div>

