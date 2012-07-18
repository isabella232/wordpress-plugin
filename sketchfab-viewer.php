<?php
/*
Plugin Name: Sketchfab Viewer
Plugin URI: sketchfab.com
Description: Display Sketchfab models to wordpress.
Version: 0.2
Author: Sketchfab
Author URI: sketchfab.com
License: A "Slug" license name e.g. GPL2
*/

// v0.3 : Better prompt window
// v0.2 : Added options (width and height)
// v0.1 : Simple shortcode and button

  // Create shortcode handler for Sketchfab
  // [sketchfab id=xxx]
  function addSketchfab($atts, $content = null) {
    extract(shortcode_atts(array( "id" => '' ), $atts));
    return '<iframe frameborder="0" height="'.get_settings('sketchfab-height').'" 
            width="'.get_settings('sketchfab-width').'" 
            webkitallowfullscreen="true" mozallowfullscreen="true" 
            src="http://skfb.ly/embed/'.$id.'"></iframe>';
  }
  add_shortcode('sketchfab', 'addSketchfab');

  // Add Sketchfab button to MCE
  
  function add_sketchfab_button() {
    if( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
      return;
    
    if( get_user_option('rich_editing') == 'true') {
      add_filter('mce_external_plugins', 'add_sketchfab_tinymce_plugin');
      add_filter('mce_buttons', 'register_sketchfab_button');
     }
  }

  function register_sketchfab_button($buttons) {
    array_push($buttons, "|", "sketchfabEmbed");
    return $buttons;
  }

  function add_sketchfab_tinymce_plugin($plugin_array) {
    $dir = '/wp-content/plugins/sketchfab-viewer';
    $url = get_bloginfo('wpurl');
    $plugin_array['sketchfabEmbed'] = $url.$dir.'/custom/editor_plugin.js';
    return $plugin_array;
  }
  add_action('init', 'add_sketchfab_button');

  // Add settings menu to Wordpress

  if ( is_admin() ){ // admin actions
    add_action( 'admin_menu', 'sketchfab_create_menu' );
  } else {
    // non-admin enqueues, actions, and filters
  }

  function sketchfab_create_menu() {
    // Create top-level menu
    add_menu_page('Sketchfab Plugin Settings', 'Sketchfab Settings', 'administrator',
      __FILE__, 'sketchfab_settings_page', plugins_url('/img/sketchfab-menu-icon.png', __FILE__));
  
    // Call register settings function
    add_action( 'admin_init', 'register_settings' );
  }

  function register_settings() { // whitelist options
    register_setting( 'settings-group', 'sketchfab-width' );
    register_setting( 'settings-group', 'sketchfab-height' );
  }

  // Page displayed as the settings page
  function sketchfab_settings_page() {
?>
  <div class="wrap">
  <h2>Sketchfab Viewer</h2>

  <form method="post" action="options.php">
    <?php settings_fields( 'settings-group' ); ?>
    
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Width</th>
        <td><input type="text" name="sketchfab-width" value="<?php echo get_option('sketchfab-width'); ?>" /> px</td>
      </tr>
      <tr valign="top">
        <th scope="row">Height</th>
        <td><input type="text" name="sketchfab-height" value="<?php echo get_option('sketchfab-height'); ?>" /> px</td>
      </tr>
    </table>
    
    <?php submit_button(); ?>
  </form> 
</div>

<?php } ?>