<?php
/*******
 * @package xbPeople for all xbCulture extensions
 * @filesource site/layouts/xbculture/modalpvlayout.php
 * @version 1.0.3.9 12th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

$show = 'x'.strtoupper(trim($displayData['show']));
if ($show == 'x') $show = 'xPGCFEBOIE';

?>
<script>
jQuery(document).ready(function(){
//for preview modals
    // Load view vith AJAX
<?php if(strpos($show,'P')) : ?>
    jQuery('#ajax-ppvmodal').on('show', function () {
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=person&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'G')) : ?>
    jQuery('#ajax-gpvmodal').on('show', function () {
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=group&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'C')) : ?>
    jQuery('#ajax-cpvmodal').on('show', function () {
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=character&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'B')) : ?>
    jQuery('#ajax-bpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbbooks&view=book&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'E')) : ?>
    jQuery('#ajax-bpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbevents&view=event&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'F')) : ?>
    jQuery('#ajax-fpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbfilms&view=film&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'O')) : ?>
    jQuery('#ajax-rpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbbooks&view=bookreview&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; 
if (strpos($show,'I')) : ?>
    jQuery('#ajax-rpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbfilms&view=filmreview&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif;
if (strpos($show,'V')) : ?>
    jQuery('#ajax-rpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbevents&view=eventreview&layout=default&tmpl=component&id='+window.pvid);
    })
<?php endif; ?>
jQuery('#ajax-bpvmodal,#ajax-ppvmodal,#ajax-gpvmodal,#ajax-cpvmodal,#ajax-epvmodal,#ajax-fpvmodal,#ajax-rpvmodal').on('hidden', function () {
    // cleanup the modal-content that was loaded
		jQuery(this).find(".modal-content").html("");
    })    
});
// fix multiple backdrops
jQuery(document).bind('DOMNodeInserted', function(e) {
    var element = e.target;
    if (jQuery(element).hasClass('modal-backdrop')) {
         if (jQuery(".modal-backdrop").length > 1) {
           jQuery(".modal-backdrop").not(':last').remove();
       }
	}    
});
</script>
<!-- preview modal windows -->
<?php if (strpos($show,'P')) : ?>
<div class="modal fade xbpvmodal" id="ajax-ppvmodal" style="max-width:800px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Person</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; 
if (strpos($show,'G')) : ?>
<div class="modal fade xbpvmodal" id="ajax-gpvmodal" style="max-width:800px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Group</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; 
if (strpos($show,'C')) : ?>
<div class="modal fade xbpvmodal" id="ajax-cpvmodal" style="max-width:800px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Character</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; 
if (strpos($show,'B')) : ?>
<div class="modal fade xbpvmodal" id="ajax-bpvmodal" style="max-width:1000px">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
        	style="opacity:unset;line-height:unset;border:none;">&times;</button>
         <h4 class="modal-title" style="margin:5px;">Preview Book</h4>
    </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; 
if (strpos($show,'E')) : ?>
<div class="modal fade xbpvmodal" id="ajax-epvmodal" style="max-width:1000px">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
        	style="opacity:unset;line-height:unset;border:none;">&times;</button>
         <h4 class="modal-title" style="margin:5px;">Preview Event</h4>
    </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; 
if (strpos($show,'F')) : ?>
<div class="modal fade xbpvmodal" id="ajax-fpvmodal" style="max-width:1000px">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
        	style="opacity:unset;line-height:unset;border:none;">&times;</button>
         <h4 class="modal-title" style="margin:5px;">Preview Film</h4>
    </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; 
if ((strpos($show,'O')) || (strpos($show,'I')) || (strpos($show,'V')) ): ?>
<div class="modal fade xbpvmodal" id="ajax-rpvmodal" style="max-width:900px">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Review</h4>
        </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<?php endif; ?>
