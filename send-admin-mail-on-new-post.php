<?php
/**
 * Plugin Name:Send admin mail on new post
 * Plugin URI: https://www.php-globe.nl/wordpress_plugins/send-admin-mail-on-new-post
 * Description: Send an email to admin when a new post is published
 * Version: 1.0.4
 * Requires at least: 5.5
 * Tested up to: 5.5.1
 * Requires PHP: 7.0
 * Text Domain: samonp
 * Author: Johan Wiegel @ PHP-GLOBE
 * Author URI: https://www.php-globe.nl
 * Since: 20201013
 */

add_action('transition_post_status', 'samonp_send_admin_email', 10, 3);

function samonp_send_admin_email($new_status, $old_status, $post)
{
    if ('post' === $post->post_type && in_array($new_status, array('publish', 'future'), true) && !in_array($old_status, array('publish', 'future'), true))
    {
        if (!get_post_meta($post->ID, 'emailed_to_admin', true))
        {
            $sEmail = get_option( 'admin_email');

            $sSubject = __('New post on','samonp');
            $sSubject .= ' '.get_option('blogname');

            $sMsg = get_the_author_meta( 'user_nicename' , $post->post_author ).' ';
            $sMsg .= __('posted a new post with the title ','samonp').':';
            $sMsg .= get_the_title( $post->ID ).PHP_EOL.PHP_EOL;
            $sMsg .= __('Click the link to view the post','samonp').' ';
            $sMsg .= get_post_permalink( $post->ID );
            wp_mail( $sEmail, $sSubject, $sMsg);


            // Flag email as having been sent now.
            update_post_meta($post->ID, 'emailed_to_admin', time());
        }
    }
}