<?php
/*
Plugin Name: WPPM Login Security Notification
Plugin URI: http://pradeepmaurya.in/
Description: Sends an email to the administrator each time When a user logs in successfully or fails to connect.This email information including IP adress and user agent and some usefull information.
Version: 1.0
Author: Pradeep Maurya
Author URI: http://pradeepmaurya.in/about-us
Domain Path: /languages

Copyright 2018 pradeep Maurya

*/


/*******************************************************************
 *
 * CORE FUNCTIONS
 *
 *******************************************************************/

if(!function_exists('wppm_neo_get_ip')){
  function wppm_neo_get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      //check ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      //to check ip is pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }
}



/*******************************************************************
 *
 * CORE FUNCTIONS
 *
 *******************************************************************/

/**
 * Log for each log in
 *
 * @param str $user_user_login
 * @param object $user
 */
function wppm_log_wp_user_login( $user_user_login, $user ) {
  
  // init var
  $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? esc_html($_SERVER['HTTP_USER_AGENT']) : '');
  $referrer = (isset($_SERVER['HTTP_REFERER'])      ? esc_html($_SERVER['HTTP_REFERER']) : '');
  
  
  //===============================================
  // Send email
  //===============================================
  $admin_email = get_bloginfo('admin_email');
  $site_info = sprintf('%1$s (%2$s)', get_bloginfo('name'), get_bloginfo('wpurl'));
  
  // generate email core
  $header = 'From: "'.$admin_email.'" <'.$admin_email.'>'. "\r\n";
  $header .= "Content-type: text/html; charset: ".get_bloginfo('charset')."\r\n";
  $email_subject = sprintf(__('Login of the user %1$s on the website %2$s', 'wppm'), $user->user_login, $site_info);
  
  $body_message = sprintf(__('Hello a user has logged in on the website %1$s. Here are the details of this access:', 'wppm'),$site_info).'<br />'."\n";
  $body_message .= sprintf(__('User: %1$s', 'wppm'),          $user->user_login).'<br />'."\n";
  $body_message .= sprintf(__('User email: %1$s', 'wppm'),    $user->user_email).'<br />'."\n";
  $body_message .= sprintf(__('Date: %1$s', 'wppm'),          date_i18n('Y-m-d H:i:s')).'<br />'."\n";
  $body_message .= sprintf(__('IP: %1$s', 'wppm'),            wppm_neo_get_ip()).'<br />'."\n";
  $body_message .= sprintf(__('User agent: %1$s', 'wppm'),    $user_agent).'<br />'."\n";
  $body_message .= sprintf(__('HTTP referrer: %1$s', 'wppm'), $referrer).'<br />'."\n";

  // send email
  wp_mail($admin_email, $email_subject, $body_message, $header);
  
}
add_action( 'wp_login', 'wppm_log_wp_user_login', '60', 2 );


/**
 * Redirect user to the login form if the login failed
 *
 * @param str $username
 */
function wppm_log_wp_user_login_fail( $username ) {
  
  // init var
  $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? esc_html($_SERVER['HTTP_USER_AGENT']) : '');
  $referrer = (isset($_SERVER['HTTP_REFERER'])      ? esc_html($_SERVER['HTTP_REFERER']) : '');
  
  
  //===============================================
  // Send email
  //===============================================
  $admin_email = get_bloginfo('admin_email');
  $site_info = sprintf(__('%1$s (%2$s)', 'wppm'), get_bloginfo('name'), get_bloginfo('wpurl'));

  // generate email core
  $header = 'From: "'.$admin_email.'" <'.$admin_email.'>'. "\r\n";
  $header .= "Content-type: text/html; charset: ".get_bloginfo('charset')."\r\n";
  $email_subject = sprintf(__('/!\ Error : login failed on %1$s', 'wppm'), $site_info);

  $body_message = sprintf(__('Hello, someone just failed to log in on %1$s. Here are the details:', 'wppm'),$site_info).'<br />'."\n";
  $body_message .= sprintf(__('Login: %1$s', 'wppm'), $username).'<br />'."\n";
  $body_message .= sprintf(__('Date: %1$s', 'wppm'),          date_i18n('Y-m-d H:i:s')).'<br />'."\n";
  $body_message .= sprintf(__('IP: %1$s', 'wppm'),            wppm_neo_get_ip()).'<br />'."\n";
  $body_message .= sprintf(__('User agent: %1$s', 'wppm'),    $user_agent).'<br />'."\n";
  $body_message .= sprintf(__('HTTP referrer: %1$s', 'wppm'), $referrer).'<br />'."\n";

  // send email
  wp_mail($admin_email, $email_subject, $body_message, $header);
  
}
add_action( 'wp_login_failed', 'wppm_log_wp_user_login_fail' );


/**
 * Additional links on the plugin page
 * 
 * @param array $links
 * @param str $file
 */
function wppm_plugin_row_meta($links, $file) {
  if ($file == plugin_basename(__FILE__)) {
    $links[] = '<a href="https://www.paypal.me/pradeeprkt">' . __('Donate','wppm') . '</a>';
  }
  return $links;
}
add_filter('plugin_row_meta', 'wppm_plugin_row_meta',10,2);
