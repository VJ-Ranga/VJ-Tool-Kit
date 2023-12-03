<?php
/**
 * Plugin Name: VJ Tool Kit
 * Plugin URI: https://cloudycode.net/
 * Description: A versatile plugin for WordPress, simplifying website cleaning and optimization. Easily delete posts, pages, comments, media, and inactive themes. Manage permalink structure, plugins, and create new pages effortlessly.
 * Version: 2.0.2
 * Author: VJRanga
 * Author URI: https://vjranga.com/
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Donate link: https://www.buymeacoffee.com/vjranga
 * Tested up to: 6.2.2
 * Requires PHP: 7.4
 */

// Add plugin name under the Settings menu
function vj_toolkit_add_settings_menu() {
    add_options_page('VJ Tool Kit Settings', 'VJ Tool Kit', 'manage_options', 'vj-toolkit-settings', 'vj_toolkit_settings_page');
}
add_action('admin_menu', 'vj_toolkit_add_settings_menu');

// Plugin settings page
function vj_toolkit_settings_page() {
    if (isset($_POST['vj_toolkit_submit'])) {
        if (isset($_POST['vj_toolkit_tasks'])) {
            $tasks = $_POST['vj_toolkit_tasks'];
            foreach ($tasks as $task) {
                if ($task === 'delete_posts') {
                    vj_toolkit_delete_posts();
                } elseif ($task === 'delete_pages') {
                    vj_toolkit_delete_pages();
                } elseif ($task === 'delete_comments') {
                    vj_toolkit_delete_comments();
                } elseif ($task === 'delete_media') {
                    vj_toolkit_delete_media();
                } elseif ($task === 'remove_inactive_themes') {
                    vj_toolkit_remove_inactive_themes();
                } elseif ($task === 'change_permalink_structure') {
                    vj_toolkit_change_permalink_structure();
                } elseif ($task === 'delete_plugins') {
                    vj_toolkit_disable_and_delete_plugins();
                } elseif ($task === 'create_pages') {
                    $page_list = isset($_POST['vj_toolkit_page_list']) ? $_POST['vj_toolkit_page_list'] : '';
                    vj_toolkit_create_pages($page_list);
                }
            }
            echo '<div class="notice notice-success"><p>Selected tasks executed successfully.</p></div>';
        } else {
            echo '<div class="notice notice-warning"><p>No tasks selected.</p></div>';
        }
    }

    // Include the HTML template
    include_once plugin_dir_path(__FILE__) . 'settings-page.html';
}

// Task: Delete all posts
function vj_toolkit_delete_posts() {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $posts = get_posts($args);
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Task: Delete all pages
function vj_toolkit_delete_pages() {
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $pages = get_posts($args);
    foreach ($pages as $page) {
        wp_delete_post($page->ID, true);
    }
}

// Task: Delete all plugins (except this plugin)
function vj_toolkit_disable_and_delete_plugins() {
    $plugins = get_plugins();
    $this_plugin = plugin_basename(__FILE__);

    foreach ($plugins as $plugin_path => $plugin) {
        if ($plugin_path !== $this_plugin) {
            deactivate_plugins($plugin_path);
            delete_plugins(array($plugin_path));
        }
    }
}

// Task: Delete all comments
function vj_toolkit_delete_comments() {
    $comments = get_comments(array('status' => 'all'));
    foreach ($comments as $comment) {
        wp_delete_comment($comment->comment_ID, true);
    }
}

// Task: Delete all media
function vj_toolkit_delete_media() {
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
    ));

    foreach ($attachments as $attachment) {
        wp_delete_attachment($attachment->ID, true);
    }
}

// Task: Remove all inactive themes
function vj_toolkit_remove_inactive_themes() {
    $themes = wp_get_themes(array('errors' => null, 'allowed' => null, 'blog_id' => 0));
    $active_theme = get_stylesheet();

    foreach ($themes as $theme_slug => $theme) {
        if ($theme_slug !== $active_theme && !is_child_theme($theme_slug)) {
            delete_theme($theme_slug);
        }
    }
}

// Task: Change permalink structure to Post name
function vj_toolkit_change_permalink_structure() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules();
}

// Task: Create new pages
function vj_toolkit_create_pages($page_list) {
    $pages = explode("\n", $page_list);
    $pages = array_map('trim', $pages);
    $pages = array_filter($pages);

    foreach ($pages as $page_title) {
        $new_page = array(
            'post_title' => $page_title,
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
        );
        wp_insert_post($new_page);
    }
}
