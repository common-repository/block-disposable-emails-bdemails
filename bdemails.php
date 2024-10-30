<?php
/*
Plugin Name: Block Disposable Emails - BDEmails
Plugin URI: https://wordpress.org/plugins/block-disposable-emails-bdemails/
Description: Stop users from registering to your service with disposable emails
Version: 1.2
Author: BDEmails
Author URI: https://bdemails.com/
License: GPLv2 or later

    BDEmails (email: contact@bdemails.com)
*/
// Check if we are running PHP5 or above, otherwise die with an error message.
function bdeb_php_version_checker()
{
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        deactivate_plugins(basename(__FILE__));
        wp_die("Sorry, but you can't run this plugin, it requires PHP 5 or higher.");
    }
}
register_activation_hook( __FILE__, 'bdeb_php_version_checker' );

add_action('admin_menu', 'bdeb_bdemails_menu');

function bdeb_bdemails_menu()
{
    add_options_page('BDEmails Options', 'BDEmails', 'manage_options', 'bdemails_admin_menu', 'bdeb_bdemails_options');
    add_action('admin_init', 'bdeb_register_settings');
}

function bdeb_section_text()
{
    $api_key = get_option('bdeb_options');
    if (!$api_key['bdeb_api_key']) {
      echo '<p>This plugin requires an API Key. You can get one from <a href="https://bdemails.com/account/sign-up" target="_blank">https://bdemails.com</a> for FREE.</p>';
      echo '<p>It is absolutely FREE and we are offering 50 API requests per hour (minimum).</p>';
      echo '<p>You can read more about this on our website: <a href="https://bdemails.com/" target="_blank">https://bdemails.com</a>.</p>';
    } else {
      echo '<p>Check a bunch of statistics about your API usage here: <a href="https://bdemails.com/console/" target="_blank">https://bdemails.com/console</a>.</p>';
      echo '<p>If you like our plugin please consider to <a href="https://bdemails.com/" target="_blank">donate</a>.</p>';
    }
}

function bdeb_status()
{
    // API Key Message
    $key     = get_option('bdeb_options');
    $request = 'https://bdemails.com/api/status/' . $key['bdeb_api_key'];

    if (!$key['bdeb_api_key']) {
        echo 'No API key entered so far. Please get one and insert it here.';
    } else {
        $response = wp_remote_get($request);
        if (is_array($response)) {
            $status = json_decode($response['body']);
            //print_r($status);
            if ($status->request_status == 'ok' && $status->api_status == 'ok') {
                echo 'Everything is fine! The API Key you entered is valid. <p>Currently (as of ' . $status->server_time . ' - server time) there are ' . number_format($status->credits) . ' credits. Your credits will renew every hour.</p>';
                if ($status->credits <= 0)
                    echo '<p><b>Warning:</b> All your credits are used up so far! You need to wait one hour to have more requests. If you think this service needs more than 50 requests per hour please contact us and let us know more about your service.</p>';
            } else {
                echo '<div id="message" class="error">You entered an API Key that doesn\' exist. Please get an API Key for FREE from <a href="https://bdemails.com">https://bdemails.com</a>.</div>';
            }
        } else
            echo 'No response from server. Please try later.';
    }
}

// API Key Input
function bdeb_api_string()
{
    $options = get_option('bdeb_options');
    echo "<input id='plugin_text_string' name='bdeb_options[bdeb_api_key]' size='42' type='text' value='{$options['bdeb_api_key']}' />";
}

// Blocked Domains Message
function bdeb_blocked_message()
{
    $options = get_option('bdeb_options');
    if (!$options['bdemails_blocked_domains_message']) {
      echo "<input id='bdeb_blocked_message' name='bdeb_options[bdemails_blocked_domains_message]' size='60' type='text' value='{$options['bdemails_blocked_domains_message']}' />";
      echo "  e.g.: 'Disposable emails are not allowed. Please try again.'<br />";
    } else {
      echo "<input id='bdeb_blocked_message' name='bdeb_options[bdemails_blocked_domains_message]' size='60' type='text' value='{$options['bdemails_blocked_domains_message']}' />";
    }
}

// No MX Domains Message
function bdeb_no_mx_message()
{
    $options = get_option('bdeb_options');
    if (!$options['bdemails_no_mx_domains_message']) {
      echo "<input id='bdeb_no_mx_message' name='bdeb_options[bdemails_no_mx_domains_message]' size='60' type='text' value='{$options['bdemails_no_mx_domains_message']}' />";
      echo "  e.g.: 'No MX Record found. Please try again.'<br />";
    } else {
      echo "<input id='bdeb_no_mx_message' name='bdeb_options[bdemails_no_mx_domains_message]' size='60' type='text' value='{$options['bdemails_no_mx_domains_message']}' />";
    }
}

// Free Email Service Providers Message
function bdeb_fesp_message()
{
    $options = get_option('bdeb_options');
    if (!$options['bdemails_fesp_domains_message']) {
      echo "<input id='bdeb_fesp_message' name='bdeb_options[bdemails_fesp_domains_message]' size='60' type='text' value='{$options['bdemails_fesp_domains_message']}' />";
      echo "  e.g.: 'This free email service provider is blocked. Please try again.'<br />";
    } else {
      echo "<input id='bdeb_fesp_message' name='bdeb_options[bdemails_fesp_domains_message]' size='60' type='text' value='{$options['bdemails_fesp_domains_message']}' />";
    }
}
?>

<?php
function bdeb_filter_string()
{
    $options = get_option('bdeb_options');
?>
<input type="radio" name="bdeb_options[bdeb_filter_options]" value="1" <?php
    echo ($options['bdeb_filter_options'] == 1 || empty($options['bdeb_filter_options']) ? 'checked' : '');
?>> All Email Interactions<br>
  <input type="radio" name="bdeb_options[bdeb_filter_options]" value="2" <?php
    echo ($options['bdeb_filter_options'] == 2 ? 'checked' : '');
?>> Only Comments<br>
<input type="radio" name="bdeb_options[bdeb_filter_options]" value="3" <?php
    echo ($options['bdeb_filter_options'] == 3 ? 'checked' : '');
?>> Only Registration<br><br>
<?php
}

function bdeb_register_settings()
{
    $key     = get_option('bdeb_options');
    $request = 'https://bdemails.com/api/status/' . $key['bdeb_api_key'];
    if ($key['bdeb_api_key']) {
        $response = wp_remote_get($request);
        if (is_array($response)) {
            $status = json_decode($response['body']);
            if ($status->request_status == 'ok' && $status->api_status == 'ok') {
              $block_no_mx = $status->block_no_mx;
              $block_fesp  = $status->block_fesp;
              $block_domain_message = true;
            }
            else {
              $block_no_mx = false;
              $block_fesp  = false;
              $block_domain_message = false;
            }
        }
    }
    register_setting('bdeb_options', 'bdeb_options');
    add_settings_section('plugin_status', 'Status of your API Key', 'bdeb_status', 'plugin');
    add_settings_section('plugin_main', 'Main Settings', 'bdeb_section_text', 'plugin');
    add_settings_field('plugin_text_string', 'BDEmails API Key:', 'bdeb_api_string', 'plugin', 'plugin_main');
    if ($block_domain_message) {
      add_settings_field('plugin_blocked_text', 'Blocked Domains Message:', 'bdeb_blocked_message', 'plugin', 'plugin_main');
    }
    if ($block_no_mx) {
      add_settings_field('plugin_no_mx_text', 'No MX Domains Message:', 'bdeb_no_mx_message', 'plugin', 'plugin_main');
    }
    if ($block_fesp) {
      add_settings_field('plugin_fesp_text', 'Free Email Service Providers Message:', 'bdeb_fesp_message', 'plugin', 'plugin_main');
    }
    add_settings_section('bdeb_api_string', 'Filter Settings', 'bdeb_filter_string', 'plugin');
}

// Add a settings link in the plugin listing
add_filter('bdeb_action_links', 'bdeb_action_links', 10, 2);

function bdeb_bdemails_options()
{
?>
<div>
<h2>Block Disposable Emails - BDEmails</h2>
Options relating to the BDEmails plugin.
<form action="options.php" method="post">
<?php
    settings_fields('bdeb_options');
?>
<?php
    do_settings_sections('plugin');
?>

<input name="Submit" type="submit" value="<?php
    esc_attr_e('Save Changes');
?>" />
</form></div>

<br />
<h3>Tell others about us</h6>
<a href="https://www.facebook.com/sharer/sharer.php\?u=bdemails.com" target="_blank">Share on Facebook</a><br />
<a href="https://twitter.com/intent/tweet\?text=Block disposable emails for FREE https://bdemails.com" target="_blank">Share on Twitter</a>
<?php
}

function bdeb_action_links($links, $file)
{
    static $this_plugin;
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
    if ($file == $this_plugin) {
        $settings_link = '<a href="options-general.php?page=bdemails_admin_menu">' . __('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

// Hooks
$options = get_option('bdeb_options');
// Check all emails integrations
if ($options['bdeb_filter_options'] == 1) {
    add_filter('is_email', 'bdeb_check_all_integrations');
}

// Check only comments 
if ($options['bdeb_filter_options'] == 2) {
 
    add_filter('preprocess_comment', 'bdeb_check_comments');
}

// Check only registrations
if ($options['bdeb_filter_options'] == 3) {
    add_filter('registration_errors', 'bdeb_check_registration', 10, 3);
}

// Check only registration form
function bdeb_check_registration($errors, $sanitized_user_login, $user_email)
{
    list(, $domain) = explode('@', $user_email);
    $key     = get_option('bdeb_options');
    $request = 'https://bdemails.com/api/txt/' . $key['bdeb_api_key'] . '/' . trim($domain);
 
    $response = wp_remote_get($request, array(
        'timeout' => 7
    ));

    if (is_array($response)) {
        // Get messages
        $options  = get_option('bdeb_options');
        $block_domain_message = $options['bdemails_blocked_domains_message'];
        $block_no_mx_message  = $options['bdemails_no_mx_domains_message'];
        $block_fesp_message   = $options['bdemails_fesp_domains_message'];

        $domain_status = json_decode($response['body']);
        if ($domain_status == 'blocked') {
          $errors->add('bdemails_error', __($block_domain_message, 'my_textdomain'));
        } elseif ($domain_status == 'domain_does_not_exist') {
          $errors->add('bdemails_error', __($block_domain_message, 'my_textdomain'));
        } elseif ($domain_status == 'not_a_valid_domain') {
          $errors->add('bdemails_error', __($block_domain_message, 'my_textdomain'));
        } elseif ($domain_status == 'no_mx') {
          $errors->add('bdemails_error', __($block_no_mx_message, 'my_textdomain'));
        } elseif ($domain_status == 'fesp') {
          $errors->add('bdemails_error', __($block_fesp_message, 'my_textdomain'));
        }
    } else {
      return $errors;
    }
    return $errors;
}

// Check only comments
function bdeb_check_comments($commentdata)
{
    list(, $domain) = explode('@', $commentdata['comment_author_email']);
    $key     = get_option('bdeb_options');
    $request = 'https://bdemails.com/api/txt/' . $key['bdeb_api_key'] . '/' . trim($domain);
 
    $response = wp_remote_get($request, array(
        'timeout' => 7
    ));

    if (is_array($response)) {
        // Get messages
        $options  = get_option('bdeb_options');
        $block_domain_message = $options['bdemails_blocked_domains_message'];
        $block_no_mx_message  = $options['bdemails_no_mx_domains_message'];
        $block_fesp_message   = $options['bdemails_fesp_domains_message'];

        $domain_status = json_decode($response['body']);
        if ($domain_status == 'blocked') {
          wp_die($block_domain_message);
        } elseif ($domain_status == 'domain_does_not_exist') {
          wp_die($block_domain_message);
        } elseif ($domain_status == 'not_a_valid_domain') {
          wp_die($block_domain_message);
        } elseif ($domain_status == 'no_mx') {
          wp_die($block_no_mx_message);
        } elseif ($domain_status == 'fesp') {
          wp_die($block_fesp_message);
        }
    } else {
      return $commentdata;
    }
    return $commentdata;
}

// Check comments and registrations
function bdeb_check_all_integrations($email)
{
    list(, $domain) = explode('@', $email);
    $key     = get_option('bdeb_options');
    $request = 'https://bdemails.com/api/txt/' . $key['bdeb_api_key'] . '/' . trim($domain);
 
    $response = wp_remote_get($request, array(
        'timeout' => 7
    ));
    if (is_array($response)) {
        // Get messages
        $options  = get_option('bdeb_options');
        $block_domain_message = $options['bdemails_blocked_domains_message'];
        $block_no_mx_message  = $options['bdemails_no_mx_domains_message'];
        $block_fesp_message   = $options['bdemails_fesp_domains_message'];

        $domain_status = json_decode($response['body']);
        if ($domain_status == 'blocked') {
          wp_die($block_domain_message . ' <br /><input action="action" onclick="window.history.go(-1); return false;" type="button" value="Back" />');
        } elseif ($domain_status == 'domain_does_not_exist') {
          wp_die($block_domain_message . ' <br /><input action="action" onclick="window.history.go(-1); return false;" type="button" value="Back" />');
        } elseif ($domain_status == 'not_a_valid_domain') {
          wp_die($block_domain_message . ' <br /><input action="action" onclick="window.history.go(-1); return false;" type="button" value="Back" />');
        } elseif ($domain_status == 'no_mx') {
          wp_die($block_no_mx_message . ' <br /><input action="action" onclick="window.history.go(-1); return false;" type="button" value="Back" />');
        } elseif ($domain_status == 'fesp') {
          wp_die($block_fesp_message . ' <br /><input action="action" onclick="window.history.go(-1); return false;" type="button" value="Back" />');
        }
    }
    return true;
}

// Show the message if the plugin is not activated with a valid API Key
function bdeb_show_activation_message($message, $errormsg = true)
{
    if ($errormsg) {
        echo '<div id="message" class="error">';
    } else {
        echo '<div id="message" class="updated fade">';
    }
 
    $message = '<h3>Warning from the Block Disposable Emails - BDEmails Plugin </h3><p>Please insert a valid API Key!</p><p>The plugin will not work correctly otherwise ...</p><p>You can get one from <a href="https://bdemails.com/account/sign-up" target="_blank">https://bdemails.com</a> for FREE.</p>';
    echo "<p><strong>$message</strong></p></div>";
}
$api_key = get_option('bdeb_options');
if (!$api_key['bdeb_api_key'])
    add_action('admin_notices', 'bdeb_show_activation_message');
?>
