<?php

function radslide_helper_ajax_loader($id) {
  $image_url = get_option('siteurl').'/wp-content/plugins/radslide/images/ajax-loader.gif';
  echo '<img src="'.$image_url.'" id="'.$id.'" style="display:none" />';
}

function radslide_helper_db_slideshow() {
	global $wpdb;
  return $wpdb->prefix.'radslide_slideshow';
}

function radslide_helper_db_slide() {
	global $wpdb;
  return $wpdb->prefix.'radslide_slide';
}

// add jquery to head, if needed
function radslide_head() {
	global $wpdb;
        ?><script type="text/javascript">jQuery(window).load(function() { 
                  jQuery(function(){<?php
	          $table_name = radslide_helper_db_slideshow();
	          $slideshow_rows = $wpdb->get_results("SELECT * FROM $table_name");
	          foreach($slideshow_rows as $slideshow_row) { ?>
                    jQuery("#radslide-<?php echo($slideshow_row->id) ?>").cycle(<?php echo(stripslashes($slideshow_row->cycle_options)); ?>); <?php
	          }
              ?>})
        });</script>	<?php
}

// media api scripts and styles
function radslide_media_api_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', WP_PLUGIN_URL.'/radslide/image_selector.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
}
function radslide_media_api_styles() {
	wp_enqueue_style('thickbox');
}

function radslide_rd_credit() {
	echo '<div class="credit">&copy; radSLIDE was originally developed by <a href="http://github.com/micahflee/" target="_blank">Micah Lee</a> and is maintained by <a href="http://radicaldesigns.org/" target="_blank" style="text-decoration: none;"><span style="font-size: 12px; font-family: sans-serif;color:#000000;">radical</span><span style="color: #fb6e27; letter-spacing: 1px;font-size:10px;  font-family: sans-serif;font-weight: bold;">DESIGNS</span></a>. It has been happily released under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GLP2 License</a>.</div>';
}

?>
