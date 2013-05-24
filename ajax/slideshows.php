<?php

// add ajax actions
add_action('wp_ajax_radslide_slideshows_populate', 'radslide_ajax_slideshows_populate');
add_action('wp_ajax_radslide_slideshows_add_form', 'radslide_ajax_slideshows_add_form');
add_action('wp_ajax_radslide_slideshows_add', 'radslide_ajax_slideshows_add');
add_action('wp_ajax_radslide_slideshows_settings', 'radslide_ajax_slideshows_settings');
add_action('wp_ajax_radslide_slideshows_settings_edit', 'radslide_ajax_slideshows_settings_edit');
add_action('wp_ajax_radslide_slideshows_delete', 'radslide_ajax_slideshows_delete');


function radslide_make_title($title){
  return "<h2>radSLIDE // $title</h2>";
}

// list of slideshows
function radslide_ajax_slideshows_populate() {
    global $wpdb;

    $table_name = radslide_helper_db_slideshow();
    $slide_table_name = radslide_helper_db_slide();
    $rows = $wpdb->get_results("SELECT id,name FROM $table_name ORDER BY name,id");

        echo radslide_make_title('Slideshows') . '
        <input type="button" id="radslide_add_showform" class="button-primary button-secondary add-slideshow" value="Add New Slideshow">
        <div class="clear"></div>';

        foreach($rows as $row) {
            $name = $row->name;
            if($name == '') {
                $name = '[Slideshow #' . $row->id . ']';
            }
            $slides = $wpdb->get_results("SELECT id,image_url FROM $slide_table_name WHERE slideshow_id = $row->id ORDER BY sort,id");
            $slide_div = '';
            foreach ($slides as $slide) {
                $slide_div .= '<div class="crop"><img src="' . $slide->image_url . '"></div>';
            }
            if($slide_div == '') {
                $slide_div = '<em>This slideshow does not contain any slides.</em>';
            }
            echo '<div class="slideshow-box slideshow-' . $row->id . '">
                <div class="title">' . $name . '</div>
                <div class="preview">' . $slide_div . '</div>
                ' . radslide_helper_ajax_loader("radslide_loading-" . $row->id) . '
                <div class="controls">
                <input type="button" class="button-secondary" id="radslide_manage-' . $row->id . '" value="Manage" />
                <input type="button" class="button-secondary" id="radslide_settings-' . $row->id . '" value="Settings" />
                <input type="button" class="button-secondary" id="radslide_delete-' . $row->id . '" value="Delete" />
                </div>
                <div class="codes">
                    <label for="page-embed">Page embed:</label><br>
                    <input name="page-embed" type="text" value="[[radslide ' . $row->id . ']]">
                </div>
                <div class="codes">
                    <label for="theme-embed">Theme embed:</label><br>
                    <input name="theme-embed" type="text" value="&lt;?php radslide(' . $row->id . '); ?&gt;">
                </div>
                <div class="clear"></div>
            </div>';
        }
    exit();
}

//Add Slideshow Page
function radslide_ajax_slideshows_add_form() {
        global $wpdb;
        
        $default_template = '<a href="[[LINK_URL]]"><img src="[[IMAGE_URL]]" alt="[[TITLE]]" /></a>
            <h3><a href="[[LINK_URL]]">[[TITLE]]</a></h3>
            <div class="blurb">[[DESCRIPTION]]</div>';
        $default_cycle_options = '{ timeout:2000, speed:500 }';
        echo radslide_make_title('New Slideshow');
    ?>
        
        <div id="radslide_add_form">
                <table>
                        <tr>
                                <td>Name</td>
                                <td><input type="text" id="radslide_add-name" value="" /></td>
                        </tr>
                        <tr>
                                <td style="width:120px;">Template<br/><span style="font-size:.8em; font-style:italic;">Note: Use [[TITLE]], [[DESCRIPTION]], [[LINK_URL]], [[IMAGE_URL]], [[SLIDE_ID]]</span></th>
                                <td><textarea style="width:500px;height:150px;" id="radslide_add-template"><?php echo($default_template); ?></textarea></td>
                        </tr>
                        <tr>
                                <td><a href="http://jquery.malsup.com/cycle/options.html" target="_blank">jQuery Cycle Options</a></td>
                                <td><textarea style="width:500px;height:100px;" id="radslide_add-cycle_options"><?php echo($default_cycle_options); ?></textarea></td>
                        </tr>
                </table>
                <p class="submit">
                        <input type="submit" class="button-secondary" id="radslide_add" value="<?php _e('Add Slideshow') ?>" />
                        <?php radslide_helper_ajax_loader("radslide_loading"); ?>
                </p>
        </div>
<?php
        exit();
}

// add a new slideshow
function radslide_ajax_slideshows_add() {
    global $wpdb;
    $row = array(
        'name' => $_POST['radslide_name'],
        'template' => $_POST['radslide_template'],
        'cycle_options' => $_POST['radslide_cycle_options'],
    );
    $wpdb->insert(radslide_helper_db_slideshow(), $row);
    exit();
}

// edit a slideshow's settings
function radslide_ajax_slideshows_settings() {
    global $wpdb;
    $slideshow_row = $wpdb->get_row("SELECT * FROM ".radslide_helper_db_slideshow()." WHERE id=".(int)($_POST['radslide_slideshow_id']));
?>
        <input type="hidden" id="radslide_slideshow_id" value="<?php echo($slideshow_row->id); ?>" />
        <?php echo radslide_make_title('Settings for ' . $slideshow_row->name);?>
        <input type="button" id="radslide_back_to_slideshows" class="button-secondary" value="Back to Slideshows" style="margin-bottom:10px;" />
        <?php radslide_helper_ajax_loader("radslide_back_to_slideshows_loading"); ?>
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" id="radslide-name" value="<?php echo(stripslashes($slideshow_row->name)); ?>" /></td>
                </tr>
                <tr>
                        <td style="width:120px;">Template<br/><span style="font-size:.8em; font-style:italic;">Note: Use [[TITLE]], [[DESCRIPTION]], [[LINK_URL]], [[IMAGE_URL]], [[SLIDE_ID]]</span></th>
                        <td><textarea style="width:650px;height:150px;" id="radslide-template"><?php echo(stripslashes($slideshow_row->template)); ?></textarea></td>
                </tr>
                <tr>
                        <td><a href="http://jquery.malsup.com/cycle/options.html" target="_blank">jQuery Cycle Options</a></td>
                        <td><textarea style="width:500px;height:100px;" id="radslide-cycle_options"><?php echo(stripslashes($slideshow_row->cycle_options)); ?></textarea></td>
                </tr>
        </table>
        <p class="submit">
                <input type="submit" class="button-secondary" id="radslide_edit" value="<?php _e('Edit Slideshow') ?>" />
                <?php radslide_helper_ajax_loader("radslide_loading"); ?>
        </p>
<?php
    exit();
}

// update a slideshow's settings
function radslide_ajax_slideshows_settings_edit() {
    global $wpdb;
    $row = array(
        'name' => $_POST['radslide_name'],
        'template' => $_POST['radslide_template'],
        'cycle_options' => $_POST['radslide_cycle_options'],
    );
    $wpdb->update(radslide_helper_db_slideshow(), $row, array('id'=>(int)($_POST['radslide_slideshow_id'])));
    exit();
}

// delete a slideshow
function radslide_ajax_slideshows_delete() {
    global $wpdb;
    $wpdb->query("DELETE FROM ".radslide_helper_db_slideshow()." WHERE id='".(int)($_POST['radslide_slideshow_id'])."'");
    $wpdb->query("DELETE FROM ".radslide_helper_db_slide()." WHERE slideshow_id='".(int)($_POST['radslide_slideshow_id'])."'");
    exit();
}

?>
