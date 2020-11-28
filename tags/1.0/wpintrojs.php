<?php

/*
  Plugin Name: WP Intro.JS Tours
  Plugin URI:  https://www.cfuze.com/wpintrojs.html
  Description: Include <a href="https://introjs.com" target="_blank">Intro.JS</a> Tours, Step-by-Step guides, walkthroughs and hints into any WordPress site.
  Author: CFuze
  Version: 1.0
  Copyright: 2020 CFuze
  Author URI: https://www.cfuze.com/
  Text Domain: wpintrojs
  License:     GPL2

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
 */
defined('ABSPATH') or die( 'Unauthorized action!' );

/* Register Hooks */
register_activation_hook(__FILE__, 'wpintrojs_activation');
register_deactivation_hook(__FILE__, 'wpintrojs_deactivation');
register_uninstall_hook(__FILE__, 'wpintrojs_uninstall');

/* Add Actions */

add_action('wp_enqueue_scripts', 'wpintrojs_enqueue_script');
add_action('admin_enqueue_scripts', 'wpintrojs_enqueue_script' );
add_action('admin_menu', 'wpintrojs_menu_page');
add_action("wp_ajax_wpintrojs_tour_complete", "wpintrojs_tour_complete");
add_action("wp_ajax_nopriv_wpintrojs_tour_complete", "wpintrojs_ajax_no_login");

/* Add Filters */
add_filter( 'the_content', 'wpintrojs_inject_code', 1 );

/* Add Shortcodes */
add_shortcode('wpintrojs_tour', 'wpintrojs_shortcode_tour');
add_shortcode('wpintrojs_hint', 'wpintrojs_shortcode_hint');

/* Shortcode Functions */
if ( !function_exists( 'wpintrojs_shortcode_tour' )) {
    function wpintrojs_shortcode_tour($atts = [], $content = null)
    {
        return '<a href="#" onclick="wpIntroJs_StartTour()">Start Tour</a>';
    }
}

if ( !function_exists( 'wpintrojs_shortcode_hint' )) {
    function wpintrojs_shortcode_hint($atts = [], $content = null)
    {
        return '<a href="#" onclick="wpIntroJs_ShowHints()">Show Hints</a>';
    }
}

/* Filter Functions */
if ( !function_exists( 'wpintrojs_inject_code' )) {
    function wpintrojs_inject_code($content)
    {
        if (is_singular() && in_the_loop() && is_main_query()) {
            $code = '';
            require(dirname( __FILE__ ).'/components/code_generator.php');
            return $content.$code;
        }
        return $content;
    }
}


/* Hook Functions */
if ( !function_exists( 'wpintrojs_activation' )) {
    function wpintrojs_activation()
    {
        wpintrojs_install_tables();
    }
}

if ( !function_exists( 'wpintrojs_deactivation' )) {
    function wpintrojs_deactivation()
    {
        remove_shortcode('wpintrojs_tour');
        remove_shortcode('wpintrojs_hint');
    }
}

/* Action Functions */
if ( !function_exists( 'wpintrojs_ajax_no_login' )) {
    function wpintrojs_ajax_no_login()
    {
        wp_die("Please login.");
    }
}

if ( !function_exists( 'wpintrojs_enqueue_script' )) {
    function wpintrojs_enqueue_script()
    {
        wp_register_style('intro_js', plugins_url('css/introjs.min.css', __FILE__));
        wp_enqueue_style('intro_js');
        wp_register_style('jquery_ui_css',  plugins_url('css/jquery-ui.css', __FILE__));
        wp_enqueue_style('jquery_ui_css');
        wp_register_style('wpintrojs_main', plugins_url('css/main.css', __FILE__));
        wp_enqueue_style('wpintrojs_main');
        wp_register_script('intro_js', plugins_url('js/intro.min.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_script('intro_js');
        $nonce = wp_create_nonce("wpintrojs_nonce");
        wp_register_script('wpintrojs_main', plugins_url('js/main.js', __FILE__), null,'1.0',true);
        wp_localize_script('wpintrojs_main', 'wpintrojsScript', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'nonce'=>$nonce,));
        wp_enqueue_script('wpintrojs_main');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-draggable');
    }
}


if ( !function_exists( 'wpintrojs_menu_page' )) {
    function wpintrojs_menu_page()
    {
        add_menu_page('Intro.Js Tours', 'Intro.Js Tours', 'manage_options', 'wpintrojs_tour', 'wpintrojs_menu_page_display', 'dashicons-welcome-view-site', 22);
    }
}

if ( !function_exists( 'wpintrojs_menu_page_display' )) {
    function wpintrojs_menu_page_display()
    {
        $tourTable = '';
        require_once dirname(__FILE__) . '/components/tours_class.php';
        echo '<div class="wrap"><h2>' . get_bloginfo('name') . ' Tour Management</h2>';
        if (isset($_POST['frm_tour_addnew']) || (isset($_GET['action']) && $_GET['action'] == 'edit')) {
            wpintrojs_addEditTour();
        } elseif (isset($_GET['action']) && $_GET['action']=='steps'){
            include(dirname( __FILE__ ).'/components/steps.php');
            echo '<p>To change the order, simply drag and drop the rows above after the initial save.</p>';
        } elseif (isset($_GET['action']) && $_GET['action']=='hints'){
            include(dirname( __FILE__ ).'/components/hints.php');
            echo '<p>To change the order, simply drag and drop the rows above after the initial save.</p>';
        } else {
            $tourTable->search_box('search', 'search_id');
            echo '<form id="wpintrojs-tour-table-form" method="post">';
            $tourTable->prepare_items();
            $tourTable->display();
            echo '<h3>Short Codes and Initiation Code</h3>';
            echo '<p>To include a link to start the tour or hint, use <span style="font-weight: bold">[wpintrojs_tour]</span> or <span style="font-weight: bold">[wpintrojs_hint]</span>.  To programatically call the tour or hint, simply invoke <span style="font-weight: bold">wpIntroJs_StartTour()</span> or <span style="font-weight: bold">wpIntroJs_ShowHints()</span>.</p>';
            echo '<h3>Additional Options / Instructions</h3>';
            echo '<p>Please visit the Intro.JS <a href="https://introjs.com/docs/" target="_blank">Documentation</a> to expand functionaility beyond what is provided within the plugin.  Simply add Javascript in your footer of the page, referencing the `intro` variable.</p>';
        }
        echo '</form></div>';
        echo '<h3>Thank you!</h3>';
        echo '<p>If you like this plugin, please:';
        echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="ZDQUMPMKBP738" />
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>';
        echo ' to help fund further development and <a href="mailto:cfuze@cfuze.com">support.</a>';
        echo '<p class="copyright">Copyright &copy; 2020'.(date("Y")>2020?' - '.date("Y"):'').' <a href="https://www.cfuze.com">CFuze</a>';
    }
}

if ( !function_exists( 'wpintrojs_tour_complete' )) {
    function wpintrojs_tour_complete(){
        update_user_meta(get_current_user_id(), 'wpintrojs_tour_complete', $_POST['page_id']);
        $return = array('status'  => 'success');
        wp_send_json($return);
    }
}

if ( !function_exists( 'wpintrojs_install_tables' )) {
    function wpintrojs_install_tables()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        add_option('wpintrojs_db_version', '1.0');
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . 'wpintrojs_tours';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	  page_id BIGINT(20) UNSIGNED,
	  tour_name varchar(50) DEFAULT NULL,
	  tour_description text DEFAULT NULL,
	  tour_steps JSON DEFAULT NULL,	
	  tour_hints JSON DEFAULT NULL,
	  tour_auto_start int(1) DEFAULT 1,
	  tour_complete int(1) DEFAULT 1,	 	  
	  exitOnEsc int(1) DEFAULT 1,
	  exitOnOverlayClick  int(1) DEFAULT 1,
      showStepNumbers int(1) DEFAULT 1,
      keyboardNavigation int(1) DEFAULT 1,
      showButtons int(1) DEFAULT 1,
      showBullets int(1) DEFAULT 1,
      showProgress int(1) DEFAULT 1,
      disableInteraction int(1) DEFAULT 1,
      hidePrev int(1) DEFAULT 1,
      hideNext int(1) DEFAULT 1,
      scrollToElement int(1) DEFAULT 1,
      scrollTo varchar(50) DEFAULT 'element',
      hintAnimation int(1) DEFAULT 1,
      nextLabel varchar(50) DEFAULT 'Next',
      prevLabel varchar(50) DEFAULT 'Prev',
      skipLabel varchar(50) DEFAULT 'Skip',
      doneLabel varchar(50) DEFAULT 'Done',
      tooltipPosition varchar(50) DEFAULT 'bottom',
      tooltipClass varchar(50) DEFAULT NULL,
      highlightClass varchar(50) DEFAULT NULL,
      scrollPadding int(2) DEFAULT 30,
      overlayOpacity decimal(3,2) DEFAULT 0.8,
      hintLabel varchar(50) DEFAULT 'Got it',
      hintPosition varchar(50) DEFAULT 'top-middle',      
        FOREIGN KEY (page_id)
        REFERENCES " . $wpdb->prefix . "posts(ID)
        ON DELETE CASCADE	  
	) ENGINE=InnoDB $charset_collate;";
        dbDelta($sql);
    }
}

if ( !function_exists( 'wpintrojs_uninstall' )) {
    function wpintrojs_uninstall()
    {
        global $wpdb;
        delete_option('wpintrojs_db_version');
        $table_name = $wpdb->prefix . 'wpintrojs_tours';
        $sql = "DROP TABLE $table_name";
        $wpdb->query($sql);
    }
}