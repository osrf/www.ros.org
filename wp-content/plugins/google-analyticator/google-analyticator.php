<?php
/*
 * Plugin Name: Google Analyticator
 * Version: 6.4.9.7
 * Plugin URI: http://www.videousermanuals.com/google-analyticator/?utm_campaign=analyticator&utm_medium=plugin&utm_source=readme-txt
 * Description: Adds the necessary JavaScript code to enable <a href="http://www.google.com/analytics/">Google's Analytics</a>. After enabling this plugin you need to authenticate with Google, then select your domain and you're set.
 * Author: SumoMe
 * Author URI: http://www.sumome.com/?src=wp_readme
 * Text Domain: google-analyticator
 */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('GOOGLE_ANALYTICATOR_VERSION', '6.4.9');

define('GOOGLE_ANALYTICATOR_CLIENTID', '1007949979410.apps.googleusercontent.com');
define('GOOGLE_ANALYTICATOR_CLIENTSECRET', 'q06U41XDXtzaXD14E-KO1hti'); //don't worry - this don't need to be secret in our case
define('GOOGLE_ANALYTICATOR_REDIRECT', 'urn:ietf:wg:oauth:2.0:oob');
define('GOOGLE_ANALYTICATOR_SCOPE', 'https://www.googleapis.com/auth/analytics');//.readonly

// Constants for enabled/disabled state
define("ga_enabled", "enabled");
define("ga_disabled", "disabled");

// Defaults, etc.
define("key_ga_uid", "ga_uid");
define("key_ga_status", "ga_status");
define("key_ga_disable_gasites", "ga_disable_gasites");
define("key_ga_analytic_snippet", "ga_analytic_snippet");
define("key_ga_admin", "ga_admin_status");
define("key_ga_admin_disable", "ga_admin_disable");
define("key_ga_admin_disable_DimentionIndex", "ga_admin_disable_DimentionIndex");
define("key_ga_remarketing", 'ga_enable_remarketing');
define("key_ga_track_login", "key_ga_track_login");
define("key_ga_show_ad", "key_ga_show_ad");
define("key_ga_admin_role", "ga_admin_role");
define("key_ga_dashboard_role", "ga_dashboard_role");
define("key_ga_adsense", "ga_adsense");
define("key_ga_extra", "ga_extra");
define("key_ga_extra_after", "ga_extra_after");
define("key_ga_event", "ga_event");
define("key_ga_outbound", "ga_outbound");
define("key_ga_outbound_prefix", "ga_outbound_prefix");
define("key_ga_enhanced_link_attr", "ga_enhanced_link_attr");
define("key_ga_downloads", "ga_downloads");
define("key_ga_downloads_prefix", "ga_downloads_prefix");
define("key_ga_widgets", "ga_widgets");
define("key_ga_annon", "ga_annon");

define("ga_uid_default", "UA-XXXXXXXX-X");
define("ga_google_token_default", "");
define("ga_disable_gasites_default", ga_disabled);
define("ga_analytic_snippet_default", ga_disabled);
define("ga_status_default", ga_disabled);
define("ga_admin_default", ga_enabled);
define("ga_admin_disable_DimentionIndex_default", "");
define("ga_admin_disable_default", 'remove');
define("ga_adsense_default", "");
define("ga_extra_default", "");
define("ga_extra_after_default", "");
define("ga_event_default", ga_enabled);
define("ga_outbound_default", ga_enabled);
define("ga_outbound_prefix_default", 'outgoing');
define("ga_enhanced_link_attr_default", ga_disabled);
define("ga_downloads_default", "");
define("ga_downloads_prefix_default", "download");
define("ga_widgets_default", ga_enabled);

// Create the default key and status
add_option( 'ga_version', GOOGLE_ANALYTICATOR_VERSION );
add_option(key_ga_status, ga_status_default, '');
add_option(key_ga_disable_gasites, ga_disable_gasites_default, '');
add_option(key_ga_analytic_snippet, ga_analytic_snippet_default, '');
add_option(key_ga_uid, ga_uid_default, '');
add_option(key_ga_admin, ga_admin_default, '');
add_option(key_ga_admin_disable_DimentionIndex, ga_admin_disable_DimentionIndex_default, '');
add_option(key_ga_admin_disable, ga_admin_disable_default, '');
add_option(key_ga_admin_role, array('administrator'), '');
add_option(key_ga_dashboard_role, array('administrator'), '');
add_option(key_ga_show_ad, '1' );
add_option(key_ga_adsense, ga_adsense_default, '');
add_option(key_ga_extra, ga_extra_default, '');
add_option(key_ga_extra_after, ga_extra_after_default, '');
add_option(key_ga_event, ga_event_default, '');
add_option(key_ga_outbound, ga_outbound_default, '');
add_option(key_ga_outbound_prefix, ga_outbound_prefix_default, '');
add_option(key_ga_enhanced_link_attr, ga_enhanced_link_attr_default, '');
add_option(key_ga_downloads, ga_downloads_default, '');
add_option(key_ga_downloads_prefix, ga_downloads_prefix_default, '');
add_option(key_ga_widgets, ga_widgets_default, '');
add_option(key_ga_annon, false );
add_option('ga_defaults', 'yes' );
add_option('ga_google_token', '', '');

 $useAuth = ( get_option( 'ga_google_token' ) == '' ? false : true );
 
 
# Check if we have a version of WordPress greater than 2.8
if ( function_exists('register_widget') ) {

	# Check if widgets are enabled and the auth has been set!
	if ( get_option(key_ga_widgets) == 'enabled'  && $useAuth ) {

		# Include Google Analytics Stats widget
		require_once('google-analytics-stats-widget.php');

		# Include the Google Analytics Summary widget
		require_once('google-analytics-summary-widget.php');
		$google_analytics_summary = new GoogleAnalyticsSummary();

	}

}

// Create a option page for settings
add_action('admin_init', 'ga_admin_init');

add_action('init', "ganalyticator_stats_init");
add_action('admin_menu', 'add_ga_option_page');

function  ganalyticator_stats_init(){
	require_once('class.analytics.stats.php');
	do_action("gapro_init");
}
// Initialize the options
function ga_admin_init() {

	ga_get_active_addons();

	# Load the localization information
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain('google-analyticator', 'wp-content/plugins/' . $plugin_dir . '/localizations', $plugin_dir . '/localizations');

}

# Add the core Google Analytics script, with a high priority to ensure last script for async tracking
add_action('wp_head', 'add_google_analytics',99);

// ONly track WP login if requested.
if( get_option( key_ga_track_login ) )
    add_action('login_head', 'add_google_analytics', 99);

# Initialize outbound link tracking
add_action('init', 'ga_outgoing_links');
// Include the Google Experiment page


// Hook in the options page function
function add_ga_option_page() {


	if(ga_get_active_addons() == false){
		//$plugin_page = add_options_page(__('Google Analyticator Settings', 'google-analyticator'), 'Google Analytics', 'manage_options', basename(__FILE__), 'ga_settings_page');
		//add_action('load-'.$plugin_page, 'ga_pre_load' );
	}

	$activate_page = add_submenu_page( null, 'Activation', 'Google Analytics', 'manage_options', 'ga_activate' , 'ga_activate');
	$reset_page = add_submenu_page(null, 'Reset', 'Reset', 'activate_plugins', 'ga_reset', 'ga_reset' );
	add_action('load-'.$reset_page, 'ga_do_reset' );

}

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'ga_filter_plugin_actions');

function ga_pre_load()
{

	add_action('admin_footer', 'add_ga_admin_footer');

    if( isset( $_POST['key_ga_google_token'] ) ):

        check_admin_referer('google-analyticator-update_settings');

        // Nolonger defaults
        update_option('ga_defaults', 'no');

        // Update GA Token
        update_option('ga_google_token', $_POST['key_ga_google_token']);

    endif;

    if( get_option('ga_defaults') == 'yes' ):

        wp_redirect( admin_url('options-general.php?page=ga_activate') );
        exit;

    endif;

    /** Action to trancate Analyticator db cache   **/
    if(isset($_GET['pageaction']) && $_GET['pageaction'] == 'ga_clear_cache'){
	    global $wpdb;
			delete_transient('google_stats_uniques' );
			delete_transient('ga_admin_stats_widget');
			$wpdb->query( "delete from $wpdb->options where `option_name` like 'google_stats_visitsGraph_%'"); 
		    $wpdb->query( "delete from $wpdb->options where `option_name` like '%ga_admin_dashboard%'");   
    }
}

function ga_activate()
{

if (! function_exists('curl_init')) {
  print('Google PHP API Client requires the CURL PHP extension');
  return;
}

if (! function_exists('json_decode')) {
  print('Google PHP API Client requires the JSON PHP extension');
  return;
}

if (! function_exists('http_build_query')) {
  print('Google PHP API Client requires http_build_query()');
  return;
}
$url = http_build_query( array(
                                'next' =>ga_analyticator_setting_url(),
                                'scope' => GOOGLE_ANALYTICATOR_SCOPE,
                                'response_type'=>'code',
                                'redirect_uri'=>GOOGLE_ANALYTICATOR_REDIRECT,
                                'client_id'=>GOOGLE_ANALYTICATOR_CLIENTID
                                )
                        );

    ?>

<div class="wrap">
  <p><strong>Google Authentication Code </strong> </p>
  <p>You need to sign in to Google and grant this plugin access to your Google Analytics account</p>
  <p> <a
                onclick="window.open('https://accounts.google.com/o/oauth2/auth?<?php echo $url ?>', 'activate','width=700, height=600, menubar=0, status=0, location=0, toolbar=0')"
                target="_blank"
                href="javascript:void(0);"> Click Here </a> - <small> Or <a target="_blank" href="https://accounts.google.com/o/oauth2/auth?<?php echo $url ?>">here</a> if you have popups blocked</small> </p>
  <div  id="key">
    <p>Enter your Google Authentication Code in this box. This code will be used to get an Authentication Token so you can access your website stats.</p>
    <form method="post" action="<?php echo ga_analyticator_setting_url();?>">
      <?php wp_nonce_field('google-analyticator-update_settings'); ?>
      <input type="text" name="key_ga_google_token" value="" style="width:450px;"/>
      <input type="submit"  value="Save &amp; Continue" />
    </form>
  </div>
  <br />
  <br />
  <br />
  <hr />
  <br />
  <p><strong>I Don't Want To Authenticate Through Google </strong> </p>
  <p>If you don't want to authenticate through Google and only use the tracking capability of the plugin (<strong><u>not the dashboard functionality</u></strong>), you can do this by clicking the button below. </p>
  <p>You will be asked on the next page to manually enter your Google Analytics UID.</p>
  <form method="post" action="<?php echo ga_analyticator_setting_url();?>">
    <input type="hidden" name="key_ga_google_token" value="" />
    <?php wp_nonce_field('google-analyticator-update_settings'); ?>
    <input type="submit"  value="Continue Without Authentication" />
  </form>
</div>
<?php
}

// Add settings option
function ga_filter_plugin_actions($links) {
	$new_links = array();

	$new_links[] = '<a href="' . ga_analyticator_setting_url() .'">' . __('Settings', 'google-analyticator') . '</a>';
        $new_links[] = '<a href="' . wp_nonce_url( admin_url('options-general.php?page=ga_reset'), 'ga-reset' ) .'">'. __('Reset', 'google-analyticator') . '</a>';

	return array_merge($new_links, $links);
}

function ga_do_reset()
{
	global $wpdb;
	// Check to make sure referer is same as host.
	check_admin_referer( 'ga-reset' );

    // Delete all GA options.
    delete_option(key_ga_status);
    delete_option(key_ga_disable_gasites);
    delete_option(key_ga_analytic_snippet);
    delete_option(key_ga_uid);
    delete_option(key_ga_admin);
    delete_option(key_ga_admin_disable);
    delete_option(key_ga_admin_role);
    delete_option(key_ga_dashboard_role);
    delete_option(key_ga_adsense);
    delete_option(key_ga_extra);
    delete_option(key_ga_extra_after);
    delete_option(key_ga_event);
    delete_option(key_ga_outbound);
    delete_option(key_ga_outbound_prefix);
    delete_option(key_ga_enhanced_link_attr);
    delete_option(key_ga_downloads);
    delete_option(key_ga_downloads_prefix);
    delete_option(key_ga_widgets);
    delete_option(key_ga_annon);
    delete_option('ga_defaults');
    delete_option('ga_google_token');
    delete_option('ga_google_authtoken');
    delete_option('ga_profileid');
    delete_transient('ga_admin_stats_widget');

    // Need to remove cached items from GA widgets 
    $wpdb->query( "delete from $wpdb->options where `option_name` like 'google_stats_visitsGraph_%'");

    wp_redirect( admin_url( 'options-general.php?page=ga_activate' ) );
    exit;
}

function ga_reset(){ /* Wont ever run. */ }

function ga_settings_page(){
	ga_options_page();
}
function ga_options_page() {
	add_thickbox();

	if (array_key_exists('ga_analyticator_global_notification', $_GET) && $_GET['ga_analyticator_global_notification'] == 0) {
        update_option('ga_analyticator_global_notification', 0);
	}

	// If we are a postback, store the options
	if (isset($_POST['info_update'])) {
		
		# Verify nonce
		check_admin_referer('google-analyticator-update_settings');

                update_option('ga_defaults', 'no');
				
				if(isset( $_POST['ga_domain_names'])){
					// Get our domains array, and match the UID to the value
					$domains = stripslashes( $_POST['ga_domain_names'] );
					$all_domains = unserialize( $domains );
					update_option( 'ga_domain_name', $all_domains[ $_POST[key_ga_uid] ] );
				}
        
        // Update the status
		$ga_status = wp_filter_kses( $_POST[key_ga_status] );
		if (($ga_status != ga_enabled) && ($ga_status != ga_disabled))
			$ga_status = ga_status_default;
		update_option(key_ga_status, $ga_status);

		// Update Hiding UID (if set)
                if( isset( $_POST[key_ga_disable_gasites] ) ) {
                    
                    $ga_disable_gasites = wp_filter_kses( $_POST[key_ga_disable_gasites] );
	
                    if (!$ga_disable_gasites)
						$ga_disable_gasites = ga_disable_gasites_default;
	
                    update_option(key_ga_disable_gasites, $ga_disable_gasites);
                }
		
		// Update the Analytic Snippet
		//define("key_ga_analytic_snippet", "ga_analytic_snippet", true);
		$ga_analytic_snippet = wp_filter_kses( $_POST[key_ga_analytic_snippet] );
		if (($ga_analytic_snippet != ga_enabled) && ($ga_analytic_snippet != ga_disabled))
			$ga_analytic_snippet = ga_analytic_snippet;
		update_option(key_ga_analytic_snippet, $ga_analytic_snippet);
		
		// Update the UID
		$ga_uid = wp_filter_kses( $_POST[key_ga_uid] );
		if ($ga_uid == '')
			$ga_uid = ga_uid_default;
		update_option(key_ga_uid, $ga_uid);

		// Update the admin logging
		$ga_admin = wp_filter_kses( $_POST[key_ga_admin] );
		if (($ga_admin != ga_enabled) && ($ga_admin != ga_disabled))
			$ga_admin = ga_admin_default;
		update_option(key_ga_admin, wp_filter_kses( $ga_admin ) );

		// Update the Dimension Index
		$ga_admin_disable_DimentionIndex = $_POST[key_ga_admin_disable_DimentionIndex];
		if ($ga_admin_disable_DimentionIndex == '')
			$ga_admin_disable_DimentionIndex = ga_admin_disable_DimentionIndex_default;
			
		update_option(key_ga_admin_disable_DimentionIndex, sanitize_text_field( $ga_admin_disable_DimentionIndex ) );
		
		// Update the admin disable setting
		$ga_admin_disable = wp_filter_kses( $_POST[key_ga_admin_disable] );
		if ( $ga_admin_disable == '' )
			$ga_admin_disable = ga_admin_disable_default;
		update_option(key_ga_admin_disable, wp_filter_kses( $ga_admin_disable) );

		// Update the admin level
		if ( array_key_exists(key_ga_admin_role, $_POST) ) {
			$ga_admin_role = $_POST[key_ga_admin_role];
		} else {
			$ga_admin_role = "";
		}
		update_option(key_ga_admin_role, $ga_admin_role);

		// Update the dashboard level
		if ( array_key_exists(key_ga_dashboard_role, $_POST) ) {
			$ga_dashboard_role = $_POST[key_ga_dashboard_role];
		} else {
			$ga_dashboard_role = "";
		}

		update_option(key_ga_dashboard_role, $ga_dashboard_role );

		// Update the extra tracking code
		$ga_extra = $_POST[key_ga_extra];
		update_option(key_ga_extra, wp_filter_kses( $ga_extra ) );

		// Update the extra after tracking code
		$ga_extra_after = $_POST[key_ga_extra_after];
		update_option(key_ga_extra_after, wp_filter_kses( $ga_extra_after ));

		// Update the adsense key
		$ga_adsense = $_POST[key_ga_adsense];
		update_option(key_ga_adsense, sanitize_text_field( $ga_adsense ) );

		// Update the event tracking
		$ga_event = $_POST[key_ga_event];
		if (($ga_event != ga_enabled) && ($ga_event != ga_disabled))
			$ga_event = ga_event_default;
			update_option(key_ga_event, wp_filter_kses ( $ga_event ) );

		// Update the outbound tracking
		$ga_outbound = $_POST[key_ga_outbound];
		if (($ga_outbound != ga_enabled) && ($ga_outbound != ga_disabled))
			$ga_outbound = ga_outbound_default;
			update_option(key_ga_outbound, wp_filter_kses( $ga_outbound ) );

		// Update the outbound prefix
		$ga_outbound_prefix = $_POST[key_ga_outbound_prefix];
		if ($ga_outbound_prefix == '')
			$ga_outbound_prefix = ga_outbound_prefix_default;
			update_option(key_ga_outbound_prefix, sanitize_text_field( $ga_outbound_prefix) );

			// Update the download tracking code
			$ga_downloads = $_POST[key_ga_downloads];
			update_option(key_ga_downloads, sanitize_text_field( $ga_downloads ) );

		// Update the Enhanced Link Attribution
		$ga_enhanced_link_attr = $_POST[key_ga_enhanced_link_attr];
		if ($ga_enhanced_link_attr == '')
			$ga_enhanced_link_attr = ga_enhanced_link_attr_default;
			update_option(key_ga_enhanced_link_attr, wp_filter_kses( $ga_enhanced_link_attr) );
			
		// Update the download prefix
		$ga_downloads_prefix = $_POST[key_ga_downloads_prefix];
		if ($ga_downloads_prefix == '')
			$ga_downloads_prefix = ga_downloads_prefix_default;
			update_option(key_ga_downloads_prefix, sanitize_text_field( $ga_downloads_prefix) );

		// Update the widgets option
		$ga_widgets = $_POST[key_ga_widgets];
		if (($ga_widgets != ga_enabled) && ($ga_widgets != ga_disabled))

				$ga_widgets = ga_widgets_default;
				update_option(key_ga_widgets, wp_filter_kses( $ga_widgets ) );
		
						// Update the widgets option
				update_option(key_ga_annon, wp_filter_kses( $_POST[key_ga_annon] ) );
                
                // Update enable remarketing
                update_option(key_ga_remarketing, wp_filter_kses( $_POST[key_ga_remarketing] ) );

                // Update key_ga_hide_ad
                update_option(key_ga_show_ad, wp_filter_kses( $_POST[key_ga_show_ad] ) );
                // Update enable tracking login
                update_option(key_ga_track_login, wp_filter_kses( $_POST[key_ga_track_login] ) );           
		
		do_action("ga_experiment_setting_save");
		
		// Give an updated message
		echo "<div class='updated fade'><p><strong>" . __('Google Analyticator settings saved.', 'google-analyticator') . "</strong></p></div>";
	}
        // Are we using the auth system?
        $useAuth = ( get_option( 'ga_google_token' ) == '' ? false : true );
		// Output the options page
		
	?>
<div class="wrap">
	<?php if (get_option('ga_analyticator_global_notification') == 1): ?>
		<style type="text/css">
            #ga_analyticator_global_notification a.button:active {vertical-align:baseline;}
	    </style>
	    <div id="ga_analyticator_global_notification" class="updated" style="border:3px solid #317A96;position:relative;background:##3c9cc2;background-color:#3c9cc2;color:#ffffff;height:70px;">
	        <a class="notice-dismiss" href="<?php echo admin_url('admin.php?page=google-analyticator&ga_analyticator_global_notification=0'); ?>" style="right:10px;top:0;"></a>
	        <p style="font-size:16px;line-height:50px;">
	                <?php _e('Grow your site faster!'); ?> &nbsp;<a style="background-color: #6267BE;border-color: #3C3F76;" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox button button-primary">Get SumoMe WordPress Plugin</a>
	        </p>
	    </div>
	<?php endif ?>
  <form method="post" action="<?php echo ga_analyticator_setting_url();?>">
    <?php
			# Add a nonce
			wp_nonce_field('google-analyticator-update_settings');
			?>
    <?php if (get_option(key_ga_status) == ga_disabled) { ?>
    <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
      <?php _e('Google Analytics integration is currently <strong>DISABLED</strong>.', 'google-analyticator'); ?>
    </div>
    <?php } ?>
    <?php if ((get_option(key_ga_uid) == "XX-XXXXX-X") && (get_option(key_ga_status) != ga_disabled)) { ?>
    <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
      <?php _e('Google Analytics integration is currently enabled, but you did not enter a UID. Tracking will not occur.', 'google-analyticator'); ?>
    </div>
    <?php } ?>
    <div id="vumga-container" style="position:relative;">
    <?php
$addons = get_option("gapro_addons");
if(!$addons){?>
    <div id="vumga-sidebar" style="position: absolute; top: 40px; right: 0; width: 250px; border: 1px solid #ccc; padding: 20px; background:#FFFFFF"> 
		<h3 style="text-align:center">Support us</h3>
		<p>1- Check out our <a target="_blank" href="https://wordpress.org/plugins/sumome">SumoMe plugin</a></p>
		<p>2- <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/google-analyticator">Leave a :) Review</a></p>	
		<p>3- Have a great day!</p>
		<h3 style="text-align:center">Show off your analytics</h3>
		<p>Use short code <b>[analytics]</b> anywhere on your site to show your analytics publicly.</p>
		<p>Use short code <b>[analytics-counter]</b> anywhere on your site to display the page view counter widget.</p>
	</div>
    <?php }?>
    <div style="margin-right: 320px;">
    <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
      <tr>
        <td colspan="2" style="padding-left:0"><h3>
            <?php _e('Basic Settings', 'google-analyticator'); ?>
          </h3></td>
      </tr>
      
      <tr>
        <th width="35%" valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_status ?>">
            <?php _e('Google Analytics logging is', 'google-analyticator'); ?>:</label>
        </th>
        <td>
		<?php
						echo "<select name='".key_ga_status."' id='".key_ga_status."'>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_status) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_status) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
       
       </td>
      </tr>
      <tr id="ga_ajax_accounts">
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_uid; ?>"><?php _e('Analytics Account', 'google-analyticator'); ?>:</label>
        </th>
        <td>
		
		<?php if (get_option(key_ga_disable_gasites) == ga_disabled){?>
		<?php

                        if( $useAuth ):
                            
                            $uids = ga_get_analytics_accounts();

                            echo "<select name='".key_ga_uid."'> ";

                            $hasSelected = false; // Will be set to true once a match is found. Cant echo selected twice.

	                            foreach($uids as $id=>$domain):

	                                echo '<option value="'.$id.'"';
	                                // If set in DB.
	                                if( get_option(key_ga_uid) == $id ) { $hasSelected=true; echo ' selected="selected"'; }
	                                // Else if the domain matches the current domain & nothing set in DB.
	                                elseif( ( $_SERVER['HTTP_HOST'] == $domain ) && ( ! $hasSelected ) ) { $hasSelected=true; echo ' selected="selected"'; }
	                                echo '>'.$domain.'</option>';

	                            endforeach;
	                            
                            echo '</select>';

                            // Need a copy of the array, so we can store the domain name too (for visual purposes)
                            echo '<input type="hidden" name="ga_domain_names" value=\'' . serialize( $uids ) . '\' />';
                            
                        else:

                            echo '<input type="text" name="'.key_ga_uid.'" value="'. get_option( key_ga_uid ) .'" />';

                        endif;
                        ?><br />
                        <input type="checkbox" name="<?php echo key_ga_disable_gasites?>" id="<?php echo key_ga_disable_gasites?>"<?php if(get_option(key_ga_disable_gasites) == ga_enabled){?> checked="checked"<?php }?> /> <?php _e('Hide Google Analytics UID after saving', 'google-analyticator'); ?>
         	<?php }else{
			?><?php echo get_option( 'ga_domain_name' ); ?> - To change this, you must <a href="<?php echo admin_url('/options-general.php?page=ga_reset'); ?>">deauthorize and reset the plugin</a>
			 <input type="hidden" name="<?php echo key_ga_disable_gasites?>" value="<?php echo ga_enabled?>" /><input type="hidden" name="<?php echo key_ga_uid?>" value="<?php echo get_option(key_ga_uid)?>" />
			<?php
			}?>               
         </td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_analytic_snippet ?>">
            <?php _e('Tracking Code', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_analytic_snippet."' id='".key_ga_analytic_snippet."'>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_analytic_snippet) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Universal (analytics.js)', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_analytic_snippet) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Traditional (ga.js)', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
					  <p  class="setting-description">
						<?php _e('If you are using Universal Analytics make sure you have changed your account to a Universal Analytics property in Google Analytics. Read more about Universal Analytics <a href="https://support.google.com/analytics/answer/2817075?hl=en" target="_blank">here</a>.', 'google-analyticator'); ?>
					  </p>
						</td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_widgets; ?>">
            <?php _e('Support us', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_show_ad."' id='".key_ga_show_ad."'>\n";

						echo "<option value='1'";
						if(get_option(key_ga_show_ad) == '1')
							echo " selected='selected'";
						echo ">" . __('Yes', 'google-analyticator') . "</option>\n";

						echo "<option value='0' ";
						if(get_option(key_ga_show_ad) == '0')
							echo" selected='selected'";
						echo ">" . __('No', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
          <p  class="setting-description">
            <?php _e('Show our link on the admin dashboard. Pretty please.', 'google-analyticator'); ?>
          </p></td>
      </tr>
	  <tr>
			<td><b>View your Dashboard</b></td>
			<td><a href="../wp-admin/index.php">Click here</a></td>
	  </tr>
	<tr>
		<td><input type="submit" class="button button-primary" name="info_update" value="<?php _e('Save Changes', 'google-analyticator'); ?>" /></td>
    </tr>
      <tr>
        <td colspan="2" style="padding-left:0"><h3>
            <?php _e('Tracking Settings', 'google-analyticator'); ?>
          </h3></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label>
            <?php _e('Anonymize IP Addresses', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_annon."' id='".key_ga_annon."'>\n";

						echo "<option value='0'";
						if(get_option(key_ga_annon) == false )
							echo " selected='selected'";
						echo ">" . __('No', 'google-analyticator') . "</option>\n";

						echo "<option value='1'";
						if(get_option(key_ga_annon) == true)
							echo" selected='selected'";
						echo ">" . __('Yes', 'google-analyticator') . "</option>\n";

						echo "</select>\n";

						?>
          <p  class="setting-description">
            <?php _e('By selecting "Yes", This tells Google Analytics to anonymize the information sent by the tracker objects by removing the last octet of the IP address prior to its storage. Note that this will slightly reduce the accuracy of geographic reporting.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_admin ?>">
            <?php _e('Track all logged in WordPress users', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_admin."' id='".key_ga_admin."'>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_admin) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Yes', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_admin) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('No', 'google-analyticator') . "</option>\n";

						echo "</select>\n";

						?>
          <p  class="setting-description">
            <?php _e('Selecting "no" to this option will prevent logged in WordPress users from showing up on your Google Analytics reports. This setting will prevent yourself or other users from showing up in your Analytics reports. Use the next setting to determine what user groups to exclude.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_admin_role ?>">
            <?php _e('User roles to not track', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						global $wp_roles;
						$roles = $wp_roles->get_names();
						$selected_roles = get_option(key_ga_admin_role);
						if ( !is_array($selected_roles) ) $selected_roles = array();

						# Loop through the roles
						foreach ( $roles AS $role => $name ) {
							echo '<input type="checkbox" value="' . $role . '" name="' . key_ga_admin_role . '[]"';
							if ( in_array($role, $selected_roles) )
								echo " checked='checked'";
							$name_pos = strpos($name, '|');
							$name = ( $name_pos ) ? substr($name, 0, $name_pos) : $name;
							echo ' /> ' . _x($name, 'User role') . '<br />';
						}
						?>
          <p  class="setting-description">
            <?php _e('Specifies the user roles to not include in your WordPress Analytics report. If a user is logged into WordPress with one of these roles, they will not show up in your Analytics report.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_admin_disable ?>">
            <?php _e('Method to prevent tracking', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_admin_disable."' id='".key_ga_admin_disable."'>\n";

						echo "<option value='remove'";
						if(get_option(key_ga_admin_disable) == 'remove')
							echo " selected='selected'";
						echo ">" . __('Remove', 'google-analyticator') . "</option>\n";

						echo "<option value='admin'";
						if(get_option(key_ga_admin_disable) == 'admin')
							echo" selected='selected'";
						echo ">" . __('Use \'admin\' variable', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
          <span class="ga_admin_disable_DimentionIndex_span"> <?php _e('Dimension Index', 'google-analyticator'); ?>:
          <input type="text" name="<?php echo key_ga_admin_disable_DimentionIndex?>" style="width:50px;" value="<?php echo get_option(key_ga_admin_disable_DimentionIndex)?>" class="<?php echo key_ga_admin_disable_DimentionIndex?>" id="<?php echo key_ga_admin_disable_DimentionIndex?>" />
          </span>
          <p  class="setting-description">
            <?php _e('Selecting the "Remove" option will physically remove the tracking code from logged in users. Selecting the "Use \'admin\' variable" option will assign a variable called \'admin\' to logged in users. This option will allow Google Analytics\' site overlay feature to work, but you will have to manually configure Google Analytics to exclude tracking from pageviews with the \'admin\' variable.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label>
            <?php _e('Enable Remarketing, Demographics and Interests reports', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_remarketing."' id='".key_ga_remarketing."'>\n";

                        echo "<option value='0'";
						if(get_option(key_ga_remarketing) == '0' )
							echo" selected='selected'";
						echo ">" . __('No', 'google-analyticator') . "</option>\n";

                                                
						echo "<option value='1'";
						if(get_option(key_ga_remarketing) == '1' )
							echo " selected='selected'";
						echo ">" . __('Yes', 'google-analyticator') . "</option>\n";

						
						echo "</select>\n";

						?>
          <p  class="setting-description">
            <?php _e( 'In order to use remarketing, <a href="https://support.google.com/analytics/answer/2611270" target="_blank">please make sure you complete this checklist from Google</a>', 'google-analyticator'); ?>
          </p>
          <p  class="setting-description">
            <?php _e( 'To use remarketing, <a href="https://support.google.com/analytics/answer/2884495" target="_blank">Edit permission</a> is required', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label><?php _e('Track WordPress Login Page', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_track_login."' id='".key_ga_track_login."'>\n";

                        echo "<option value='1'";
						if(get_option(key_ga_track_login) == '1' )
							echo " selected='selected'";
						echo ">" . __('Yes', 'google-analyticator') . "</option>\n";

                                                echo "<option value='0'";
						if(get_option(key_ga_track_login) == '0' )
							echo" selected='selected'";
						echo ">" . __('No', 'google-analyticator') . "</option>\n";

						echo "</select>\n";

						?>
          <p  class="setting-description">
            <?php _e( 'This will track all access to wp-login.php', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <td colspan="2" style="padding-left:0"><h3><?php _e('Link Tracking Settings', 'google-analyticator'); ?></h3></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_outbound ?>">
            <?php _e('Outbound link tracking', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_outbound."' id='".key_ga_outbound."'>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_outbound) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_outbound) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
          <p  class="setting-description">
            <?php _e('Disabling this option will turn off the tracking of outbound links. It\'s recommended not to disable this option unless you\'re a privacy advocate (now why would you be using Google Analytics in the first place?) or it\'s causing some kind of weird issue.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_event ?>">
            <?php _e('Event tracking', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_event."' id='".key_ga_event."'>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_event) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_event) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
          <p  class="setting-description">
            <?php _e('Enabling this option will treat outbound links and downloads as events instead of pageviews. Since the introduction of <a href="https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide">event tracking in Analytics</a>, this is the recommended way to track these types of actions. Only disable this option if you must use the old pageview tracking method.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_enhanced_link_attr ?>">
            <?php _e('Enhanced Link Attribution', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<select name='".key_ga_enhanced_link_attr."' id='".key_ga_enhanced_link_attr."'>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_enhanced_link_attr) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_enhanced_link_attr) == ga_disabled )
							echo " selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
          <p  class="setting-description">
            <?php _e('You can tag your pages to implement an enhanced link-tracking functionality by enabling this option. <a href="https://support.google.com/analytics/answer/2558867?hl=en" target="_blank">learn more</a>', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_downloads; ?>"><?php _e('Download extensions to track', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_downloads."' ";
						echo "id='".key_ga_downloads."' ";
						echo "value='".wp_filter_kses(get_option(key_ga_downloads))."' />\n";
						?>
          <p  class="setting-description">
            <?php _e('Enter any extensions of files you would like to be tracked as a download. For example to track all MP3s and PDFs enter <strong>mp3,pdf</strong>. <em>Outbound link tracking must be enabled for downloads to be tracked.</em>', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_outbound_prefix; ?>">
            <?php _e('Prefix external links with', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_outbound_prefix."' ";
						echo "id='".key_ga_outbound_prefix."' ";
						echo "value='".  stripslashes( wp_filter_kses(get_option(key_ga_outbound_prefix)))."' />\n";
						?>
          <p  class="setting-description">
            <?php _e('Enter a name for the section tracked external links will appear under. This option has no effect if event tracking is enabled.', 'google-analyticator'); ?>
            </em></p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_downloads_prefix; ?>">
            <?php _e('Prefix download links with', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_downloads_prefix."' ";
						echo "id='".key_ga_downloads_prefix."' ";
						echo "value='".stripslashes(wp_filter_kses ( get_option(key_ga_downloads_prefix) ))."' />\n";
						?>
          <p  class="setting-description">
            <?php _e('Enter a name for the section tracked download links will appear under. This option has no effect if event tracking is enabled.', 'google-analyticator'); ?>
            </em></p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_adsense; ?>">
            <?php _e('Google Adsense ID', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_adsense."' ";
						echo "id='".key_ga_adsense."' ";
						echo "value='".get_option(key_ga_adsense)."' />\n";
						?>
          <p  class="setting-description">
            <?php _e('Enter your Google Adsense ID assigned by Google Analytics in this box. This enables Analytics tracking of Adsense information if your Adsense and Analytics accounts are linked.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <td colspan="2" style="padding-left:0"><h3><?php _e('Additional Tracking Code', 'google-analyticator'); ?></h3></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_extra; ?>">
            <?php _e('Additional tracking code', 'google-analyticator'); ?>
            <br />(<?php _e('before tracker initialization', 'google-analyticator'); ?>):</label>
        </th>
        <td><?php
						echo "<textarea cols='50' rows='8' ";
						echo "name='".key_ga_extra."' ";
						echo "id='".key_ga_extra."'>";
						echo stripslashes(get_option(key_ga_extra))."</textarea>\n";
						?>
          <p  class="setting-description">
            <?php _e('Enter any additional lines of tracking code that you would like to include in the Google Analytics tracking script. The code in this section will be displayed <strong>before</strong> the Google Analytics tracker is initialized.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_extra_after; ?>">
            <?php _e('Additional tracking code', 'google-analyticator'); ?>
            <br />
            (<?php _e('after tracker initialization', 'google-analyticator'); ?>):</label>
        </th>
        <td><?php
						echo "<textarea cols='50' rows='8' ";
						echo "name='".key_ga_extra_after."' ";
						echo "id='".key_ga_extra_after."'>";
						echo stripslashes(get_option(key_ga_extra_after))."</textarea>\n";
						?>
          <p  class="setting-description">
            <?php _e('Enter any additional lines of tracking code that you would like to include in the Google Analytics tracking script. The code in this section will be displayed <strong>after</strong> the Google Analytics tracker is initialized.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr>
        <td colspan="2" style="padding-left:0"><h3><?php _e('Admin Dashboard Widgets', 'google-analyticator'); ?></h3>
          <?php if(!$useAuth): ?>
          <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
            <?php _e('You have not authenticated with Google - you cannot use dashboard widgets! Reset the plugin to authenticate..', 'google-analyticator'); ?>
          </div>
          <?php endif;?></td>
      </tr>
      <tr<?php if(!$useAuth){echo ' style="display:none"';}?>>
        <th valign="top" style="padding-top: 10px;"><label for="<?php echo key_ga_widgets; ?>">
            <?php _e('Include widgets', 'google-analyticator'); ?>:</label></th>
        <td><?php
						echo "<select name='".key_ga_widgets."' id='".key_ga_widgets."'>\n";

						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_widgets) == ga_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'google-analyticator') . "</option>\n";

						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_widgets) == ga_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'google-analyticator') . "</option>\n";

						echo "</select>\n";
						?>
          <p  class="setting-description">
            <?php _e('Disabling this option will completely remove the Dashboard Summary widget and the theme Stats widget. Use this option if you would prefer to not see the widgets.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <tr<?php if(!$useAuth){echo ' style="display:none"';}?>>
        <th valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ga_dashboard_role ?>">
            <?php _e('User roles that can see the dashboard widget', 'google-analyticator'); ?>:</label>
        </th>
        <td><?php
						global $wp_roles;
						$roles = $wp_roles->get_names();
						$selected_roles = get_option(key_ga_dashboard_role);
						if ( !is_array($selected_roles) ) $selected_roles = array();

						# Loop through the roles
						foreach ( $roles AS $role => $name ) {
							echo '<input type="checkbox" value="' . $role . '" name="' . key_ga_dashboard_role . '[]"';
							if ( in_array($role, $selected_roles) )
								echo " checked='checked'";
							$name_pos = strpos($name, '|');
							$name = ( $name_pos ) ? substr($name, 0, $name_pos) : $name;
							echo ' /> ' . _x($name, 'User role') . '<br />';
						}
						?>
          <p  class="setting-description">
            <?php _e('Specifies the user roles that can see the dashboard widget. If a user is not in one of these role groups, they will not see the dashboard widget.', 'google-analyticator'); ?>
          </p></td>
      </tr>
      <?php do_action("ga_experiment_setting_view");?>
    </table>
    <p class="submit">
      <input type="submit" class="button button-primary" name="info_update" value="<?php _e('Save Changes', 'google-analyticator'); ?>" />
    </p>
    <a href="<?php echo ga_analyticator_setting_url(). '&pageaction=ga_clear_cache' ?>"><?php _e('Clear Analyticator Cache', 'google-analyticator'); ?></a> |  <a href="<?php echo admin_url('/options-general.php?page=ga_reset'); ?>">
    <?php _e('Deauthorize &amp; Reset Google Analyticator.', 'google-analyticator'); ?></a>   
  </form>
</div>
</div>
<!-- end wrap -->
</div>
<!-- end vumga-container -->
<?php
}

function ga_sort_account_list($a, $b) {
	return strcmp($a['title'],$b['title']);
}

/**
 * Checks if the WordPress API is a valid method for selecting an account
 *
 * @return a list of accounts if available, false if none available
 **/
function ga_get_analytics_accounts()
{
	$accounts = array();


	# Create a new Gdata call
	if ( isset($_POST['token']) && $_POST['token'] != '' )
		$stats = new GoogleAnalyticsStats($_POST['token']);
	elseif ( trim(get_option('ga_google_token')) != '' )
		$stats = new GoogleAnalyticsStats();
	else
		return false;

	# Check if Google sucessfully logged in
	if ( ! $stats->checkLogin() )
		return false;

	# Get a list of accounts
	$accounts = $stats->getAllProfiles();

        natcasesort ($accounts);

	# Return the account array if there are accounts
	if ( count($accounts) > 0 )
		return $accounts;
	else
		return false;
}

/**
 * Add http_build_query if it doesn't exist already
 **/
if ( !function_exists('http_build_query') ) {
	function http_build_query($params, $key = null)
	{
		$ret = array();

		foreach( (array) $params as $name => $val ) {
			$name = urlencode($name);

			if ( $key !== null )
				$name = $key . "[" . $name . "]";

			if ( is_array($val) || is_object($val) )
				$ret[] = http_build_query($val, $name);
			elseif ($val !== null)
				$ret[] = $name . "=" . urlencode($val);
		}

		return implode("&", $ret);
	}
}

/**
 * Echos out the core Analytics tracking code
 **/
function add_google_analytics()
{
	# Fetch variables used in the tracking code
	$uid = stripslashes(get_option(key_ga_uid));
	$extra = stripslashes(get_option(key_ga_extra));
	$extra_after = stripslashes(get_option(key_ga_extra_after));
	$extensions = str_replace (",", "|", get_option(key_ga_downloads));

	# Determine if the GA is enabled and contains a valid UID
	if ( ( get_option(key_ga_status) != ga_disabled ) && ( $uid != "XX-XXXXX-X" ) )
	{
	
		# Determine if the user is an admin, and should see the tracking code
		
		#first check if its tracking is enabled but current user is not in the list
		# Or check if tracking is enabled but its in the list but the method is not remove
		if ( (get_option(key_ga_admin) == ga_enabled  &&  !ga_current_user_is(get_option(key_ga_admin_role) ) ) || 
			 (get_option(key_ga_admin) == ga_enabled && ga_current_user_is(get_option(key_ga_admin_role) ) && get_option(key_ga_admin_disable) != 'remove' ) 
			 || !is_user_logged_in())
		{
			# Disable the tracking code on the post preview page
			if ( !function_exists("is_preview") || ( function_exists("is_preview") && !is_preview() ) )
			{
				# Add the notice that Google Analyticator tracking is enabled
				echo "<!-- Google Analytics Tracking by Google Analyticator " . GOOGLE_ANALYTICATOR_VERSION . ": http://www.videousermanuals.com/google-analyticator/ -->\n";

				# Add the Adsense data if specified
				if ( get_option(key_ga_adsense) != '' )
					echo '<script type="text/javascript">window.google_analytics_uacct = "' . get_option(key_ga_adsense) . "\";</script>\n";

				# Include the file types to track
				$extensions = explode(',', stripslashes(get_option(key_ga_downloads)));
				$ext = "";
				foreach ( $extensions AS $extension )
					$ext .= "'$extension',";
					$ext = substr($ext, 0, -1);

					# Include the link tracking prefixes
					$outbound_prefix = stripslashes(get_option(key_ga_outbound_prefix));
					$downloads_prefix = stripslashes(get_option(key_ga_downloads_prefix));
					$event_tracking = get_option(key_ga_event);
					$need_to_annon = get_option(key_ga_annon);
					$jsanalytic_snippet = get_option(key_ga_analytic_snippet);
				?>
<script type="text/javascript">
    var analyticsFileTypes = [<?php echo strtolower($ext); ?>];
<?php if ( $event_tracking != 'enabled' ) { ?>
    var analyticsOutboundPrefix = '/<?php echo $outbound_prefix; ?>/';
    var analyticsDownloadsPrefix = '/<?php echo $downloads_prefix; ?>/';
<?php } ?>
    var analyticsSnippet = '<?php echo $jsanalytic_snippet; ?>';
    var analyticsEventTracking = '<?php echo $event_tracking; ?>';
</script>
<?php
	# Add the first part of the core tracking code
?>
<script type="text/javascript">
<?php if($jsanalytic_snippet == ga_enabled){?>
	var _gaq = _gaq || [];
<?php if(get_option(key_ga_enhanced_link_attr) == ga_enabled): ?>
	var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';
	_gaq.push(['_require', 'inpage_linkid', pluginUrl]);
<?php endif; ?>  
	_gaq.push(['_setAccount', '<?php echo $uid; ?>']);
    _gaq.push(['_addDevId', 'i9k95']); // Google Analyticator App ID with Google
<?php if ($need_to_annon == '1' ): ?>
    _gaq.push(['_gat._anonymizeIp']);
<?php endif; ?>
<?php

    # Add any tracking code before the trackPageview
    do_action('google_analyticator_extra_js_before');
    if ( '' != $extra )
            echo "	$extra\n";

    # Add the track pageview function
    echo "	_gaq.push(['_trackPageview']);\n";

    # Disable page tracking if admin is logged in
    if ( ( get_option(key_ga_admin_disable) == "admin" ) && ( ga_current_user_is(get_option(key_ga_admin_role)) ) )
            echo "	_gaq.push(['_setCustomVar', 'admin']);\n";

    # Add any tracking code after the trackPageview
    do_action('google_analyticator_extra_js_after');
    if ( '' != $extra_after )
            echo "	$extra_after\n";

    # Add the final section of the tracking code
    ?>

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		<?php if( get_option( key_ga_remarketing ) ) : ?>
                ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
                <?php else: ?>
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		<?php endif; ?>
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
<?php
}else{
	?>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', '<?php echo $uid; ?>', 'auto');
<?php if(get_option(key_ga_enhanced_link_attr) == ga_enabled): ?>
	ga('require', 'linkid', 'linkid.js');
<?php endif; ?>
<?php if( get_option( key_ga_remarketing ) ) : ?>
	ga('require', 'displayfeatures');
<?php endif; ?>
<?php if ($need_to_annon == '1' ): ?>

	ga('set', 'anonymizeIp', true);
<?php endif; ?> 
<?php
    # Add any tracking code before the trackPageview
    do_action('google_analyticator_extra_js_before');
    if ( '' != $extra ) echo "	$extra\n";
?>
<?php
	$dimentionKeyVal = get_option(key_ga_admin_disable_DimentionIndex);
    if ( ( get_option(key_ga_admin_disable) == "admin" ) && ( ga_current_user_is(get_option(key_ga_admin_role)) ) && $dimentionKeyVal )
      echo "	ga('set', 'dimension". $dimentionKeyVal ."', 'admin');\n";
?>
	ga('send', 'pageview');
<?php		
    # Add any tracking code after the trackPageview
    do_action('google_analyticator_extra_js_after');
    if ( '' != $extra_after )
      echo "	$extra_after\n";
}?>
</script>
<?php
			}
		} else {
			# Add the notice that Google Analyticator tracking is enabled
			echo "<!-- Google Analytics Tracking by Google Analyticator " . GOOGLE_ANALYTICATOR_VERSION . ": http://wordpress.org/plugins/google-analyticator/ -->\n";
			echo "	<!-- " . __('Tracking code is hidden, since the settings specify not to track admins. Tracking is occurring for non-admins.', 'google-analyticator') . " -->\n";
		}
	}
}

/**
 * Adds outbound link tracking to Google Analyticator
 **/
function ga_outgoing_links()
{
	# Fetch the UID
	$uid = stripslashes(get_option(key_ga_uid));

	# If GA is enabled and has a valid key
	if (  (get_option(key_ga_status) != ga_disabled ) && ( $uid != "XX-XXXXX-X" ) )
	{
		# If outbound tracking is enabled
		if ( get_option(key_ga_outbound) == ga_enabled )
		{
			# If this is not an admin page
			if ( !is_admin() )
			{
				# Display page tracking if user is not an admin
				if ( ( get_option(key_ga_admin) == ga_enabled || !ga_current_user_is(get_option(key_ga_admin_role)) ) && get_option(key_ga_admin_disable) == 'remove' || get_option(key_ga_admin_disable) != 'remove' )
				{
					add_action('wp_print_scripts', 'ga_external_tracking_js',99999);
				}
			}
		}
	}
}

/**
 * Adds the scripts required for outbound link tracking
 **/
function ga_external_tracking_js()
{
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_script('ga-external-tracking', plugins_url("/google-analyticator/external-tracking{$suffix}.js"), array('jquery'), GOOGLE_ANALYTICATOR_VERSION);
}

/**
 * Determines if a specific user fits a role
 **/
function ga_current_user_is($roles)
{
	if ( !$roles ) return false;

	global $current_user;
	get_currentuserinfo();
	$user_id = intval( $current_user->ID );

	if ( !$user_id ) {
		return false;
	}
	$user = new WP_User($user_id); // $user->roles

	foreach ( $roles as $role )
		if ( in_array($role, $user->roles) ) return true;

	return false;
}

function ga_analyticator_setting_url(){
	return ( ga_get_active_addons() == false) ? admin_url("admin.php?page=google-analyticator") : admin_url("admin.php?page=ga-pro-experiment.php");
}

function ga_get_active_addons(){
	
	$gapro_addons =	get_option("gapro_addons");	
	if($gapro_addons && is_array($gapro_addons)){
		return $gapro_addons;
	}else{
		return false;	
	}
}
function add_ga_admin_footer(){
		echo '<script type="text/javascript">';
			echo 'jQuery(document).ready(function(){
					jQuery("#ga_analytic_snippet").change(function(){
						if(jQuery("#ga_analytic_snippet option:selected").val() == "disabled")
							jQuery(".newtrackingnote").show();
						else
							jQuery(".newtrackingnote").hide();
						
						jQuery("#ga_admin_disable").trigger("change");
					});
					
					jQuery("#ga_admin_disable").change(function(){
						if((jQuery("#ga_analytic_snippet option:selected").val() == "disabled") && (jQuery("#ga_admin_disable option:selected").val() == "admin")){
							jQuery(".ga_admin_disable_DimentionIndex_span").show();
						}else
						{
							jQuery(".ga_admin_disable_DimentionIndex_span").hide();	
						}
					});
					
					jQuery( "#ga_analytic_snippet" ).trigger("change");
					jQuery("#ga_admin_disable").trigger("change");
			})';
		echo '</script>';
}

/**
 * Shortcode
 */

//[analytics]
function ga_analyticator_shortcode( $atts ) {
	# Include the Google Analytics Summary widget
	require_once('google-analytics-summary-widget.php');

	ob_start();

	$google_analytics_summary = new GoogleAnalyticsSummary(TRUE);
	// Wrap it with these divs to mimic the admin dashboard widget structure.
	echo '<div id="google-analytics-summary"><div class="inside">';
	$google_analytics_summary->widget();
	echo '</div></div>';

	return ob_get_clean();
}

//[analytics-counter]
function ga_analyticator_counter_shortcode( $atts ) {
	# Include Google Analytics Stats widget
	require_once('google-analytics-stats-widget.php');
	ob_start();

	$google_analytics_widget = new GoogleStatsWidget(TRUE);

	return ob_get_clean();
}

add_shortcode( 'analytics', 'ga_analyticator_shortcode' );
add_shortcode( 'analytics-counter', 'ga_analyticator_counter_shortcode' );

add_option('ga_analyticator_global_notification', 1);

function ga_analyticator_global_notice() {
	if (in_array(substr(basename($_SERVER['REQUEST_URI']), 0, 11), array('plugins.php', 'index.php')) && get_option('ga_analyticator_global_notification') == 1) {
		?>
			<style type="text/css">
				#ga_analyticator_global_notification a.button:active {vertical-align:baseline;}
			</style>
			<div class="updated" id="ga_analyticator_global_notification" style="border:3px solid #317A96;position:relative;background:##3c9cc2;background-color:#3c9cc2;color:#ffffff;height:70px;">
				<a class="notice-dismiss" href="<?php echo admin_url('admin.php?page=google-analyticator&ga_analyticator_global_notification=0'); ?>" style="right:165px;top:0;"></a>
				<a href="<?php echo admin_url('admin.php?page=google-analyticator&ga_analyticator_global_notification=0'); ?>" style="position:absolute;top:9px;right:15px;color:#ffffff;">Dismiss and go to settings</a>
				<p style="font-size:16px;line-height:50px;">
					<?php _e('Grow your site faster!'); ?> &nbsp;<a style="background-color: #6267BE;border-color: #3C3F76;" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox button button-primary">Get SumoMe WordPress Plugin</a>
				</p>
	        </div>
		<?php
	}
}
//add_action( 'admin_notices', 'ga_analyticator_global_notice' );

function ga_analyticator_deactivate() {
	delete_option('ga_analyticator_global_notification');
}
register_deactivation_hook( __FILE__, 'ga_analyticator_deactivate' );

function ga_analyticator_other_plugins_page() {
	include(plugin_dir_path( __FILE__ ).'/other_plugins.php');
}

function ga_analyticator_top_level_menu() {
	$menu_page = add_menu_page( 'Google Analytics', 'Google Analytics', 'manage_options', 'google-analyticator', 'ga_settings_page', 'dashicons-chart-line');
	add_action('load-'.$menu_page, 'ga_pre_load' );
	add_submenu_page( 'google-analyticator', 'Other Plugins', 'Other Plugins', 'manage_options', 'google-analyticator-other-plugins', 'ga_analyticator_other_plugins_page');

	$activate_page = add_submenu_page( null, 'Activation', 'Google Analytics', 'manage_options', 'ga_activate' , 'ga_activate');
	$reset_page = add_submenu_page(null, 'Reset', 'Reset', 'activate_plugins', 'ga_reset', 'ga_reset' );
   	add_action('load-'.$reset_page, 'ga_do_reset' );
}

add_action( 'admin_menu', 'ga_analyticator_top_level_menu' );
