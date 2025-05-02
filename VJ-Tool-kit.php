<?php
/**
 * Plugin Name: VJ Tool Kit
 * Plugin URI: https://cloudycode.net/
 * Description: A versatile plugin for WordPress, simplifying website cleaning and optimization. Easily delete posts, pages, comments, media, and inactive themes. Manage permalink structure, plugins, and create new pages effortlessly.
 * Version: 2.0.3
 * Author: VJRanga
 * Author URI: https://vjranga.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Donate link: https://www.buymeacoffee.com/vjranga
 * Tested up to: 6.2.2
 * Requires PHP: 7.4
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue admin styles
function vj_toolkit_admin_styles($hook) {
    if ($hook != 'settings_page_vj-toolkit-settings') {
        return;
    }
    
    wp_enqueue_style('vj-toolkit-admin-style', plugin_dir_url(__FILE__) . 'settings-page.css', array(), '2.0.3');
    wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'vj_toolkit_admin_styles');

// Add plugin name under the Settings menu
function vj_toolkit_add_settings_menu() {
    add_options_page('VJ Tool Kit Settings', 'VJ Tool Kit', 'manage_options', 'vj-toolkit-settings', 'vj_toolkit_settings_page');
}
add_action('admin_menu', 'vj_toolkit_add_settings_menu');

// Plugin settings page
function vj_toolkit_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    $task_results = array();
    $has_errors = false;
    
    // Process form submission
    if (isset($_POST['vj_toolkit_submit'])) {
        // Verify nonce
        if (!isset($_POST['vj_toolkit_nonce']) || !wp_verify_nonce($_POST['vj_toolkit_nonce'], 'vj_toolkit_actions')) {
            wp_die('Security check failed. Please try again.');
        }
        
        if (isset($_POST['vj_toolkit_tasks']) && is_array($_POST['vj_toolkit_tasks'])) {
            $tasks = $_POST['vj_toolkit_tasks'];
            
            foreach ($tasks as $task) {
                try {
                    switch ($task) {
                        case 'delete_posts':
                            $count = vj_toolkit_delete_posts();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d posts deleted successfully.', $count)
                            );
                            break;
                            
                        case 'delete_pages':
                            $count = vj_toolkit_delete_pages();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d pages deleted successfully.', $count)
                            );
                            break;
                            
                        case 'delete_comments':
                            $count = vj_toolkit_delete_comments();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d comments deleted successfully.', $count)
                            );
                            break;
                            
                        case 'delete_media':
                            $count = vj_toolkit_delete_media();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d media items deleted successfully.', $count)
                            );
                            break;
                            
                        case 'remove_inactive_themes':
                            $count = vj_toolkit_remove_inactive_themes();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d inactive themes removed successfully.', $count)
                            );
                            break;
                            
                        case 'change_permalink_structure':
                            vj_toolkit_change_permalink_structure();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => 'Permalink structure changed to Post name.'
                            );
                            break;
                            
                        case 'delete_plugins':
                            $count = vj_toolkit_disable_and_delete_plugins();
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d plugins disabled and deleted successfully.', $count)
                            );
                            break;
                            
                        case 'create_pages':
                            $page_list = isset($_POST['vj_toolkit_page_list']) ? sanitize_textarea_field($_POST['vj_toolkit_page_list']) : '';
                            $count = vj_toolkit_create_pages($page_list);
                            $task_results[$task] = array(
                                'success' => true,
                                'message' => sprintf('%d new pages created successfully.', $count)
                            );
                            break;
                            
                        default:
                            $task_results[$task] = array(
                                'success' => false,
                                'message' => 'Unknown task.'
                            );
                            $has_errors = true;
                            break;
                    }
                } catch (Exception $e) {
                    $task_results[$task] = array(
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    );
                    $has_errors = true;
                }
            }
            
            // Display results
            if (!empty($task_results)) {
                echo '<div class="notice notice-' . ($has_errors ? 'warning' : 'success') . ' is-dismissible"><p>';
                foreach ($task_results as $task => $result) {
                    echo '<strong>' . esc_html(ucfirst(str_replace('_', ' ', $task))) . ':</strong> ' . esc_html($result['message']) . '<br>';
                }
                echo '</p></div>';
            }
        } else {
            echo '<div class="notice notice-warning is-dismissible"><p>No tasks selected.</p></div>';
        }
    }

    // Include the PHP template instead of HTML
    require_once plugin_dir_path(__FILE__) . 'settings-page.php';
}

// Task: Delete all posts
function vj_toolkit_delete_posts() {
    if (!current_user_can('delete_posts')) {
        throw new Exception('You do not have permission to delete posts.');
    }
    
    $count = 0;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $posts = get_posts($args);
    
    foreach ($posts as $post) {
        if (wp_delete_post($post->ID, true)) {
            $count++;
        }
    }
    
    return $count;
}

// Task: Delete all pages
function vj_toolkit_delete_pages() {
    if (!current_user_can('delete_pages')) {
        throw new Exception('You do not have permission to delete pages.');
    }
    
    $count = 0;
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $pages = get_posts($args);
    
    foreach ($pages as $page) {
        if (wp_delete_post($page->ID, true)) {
            $count++;
        }
    }
    
    return $count;
}

// Task: Delete all plugins (except this plugin)
function vj_toolkit_disable_and_delete_plugins() {
    if (!current_user_can('delete_plugins')) {
        throw new Exception('You do not have permission to delete plugins.');
    }
    
    $count = 0;
    $plugins = get_plugins();
    $this_plugin = plugin_basename(__FILE__);

    foreach ($plugins as $plugin_path => $plugin) {
        if ($plugin_path !== $this_plugin) {
            if (is_plugin_active($plugin_path)) {
                deactivate_plugins($plugin_path);
            }
            
            if (delete_plugins(array($plugin_path))) {
                $count++;
            }
        }
    }
    
    return $count;
}

// Task: Delete all comments
function vj_toolkit_delete_comments() {
    if (!current_user_can('moderate_comments')) {
        throw new Exception('You do not have permission to delete comments.');
    }
    
    $count = 0;
    $comments = get_comments(array('status' => 'all'));
    
    foreach ($comments as $comment) {
        if (wp_delete_comment($comment->comment_ID, true)) {
            $count++;
        }
    }
    
    return $count;
}

// Task: Delete all media
function vj_toolkit_delete_media() {
    if (!current_user_can('delete_posts')) {
        throw new Exception('You do not have permission to delete media.');
    }
    
    $count = 0;
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
    ));

    foreach ($attachments as $attachment) {
        if (wp_delete_attachment($attachment->ID, true)) {
            $count++;
        }
    }
    
    return $count;
}

// Task: Remove all inactive themes
function vj_toolkit_remove_inactive_themes() {
    if (!current_user_can('delete_themes')) {
        throw new Exception('You do not have permission to delete themes.');
    }
    
    $count = 0;
    $themes = wp_get_themes(array('errors' => null, 'allowed' => null, 'blog_id' => 0));
    $active_theme = get_stylesheet();
    
    // Get the parent theme if the current theme is a child theme
    $parent_theme = '';
    $current_theme = wp_get_theme();
    if ($current_theme->parent()) {
        $parent_theme = $current_theme->parent()->get_stylesheet();
    }
    
    foreach ($themes as $theme_slug => $theme) {
        // Skip the active theme and its parent theme
        if ($theme_slug !== $active_theme && $theme_slug !== $parent_theme) {
            $result = delete_theme($theme_slug);
            if (!is_wp_error($result)) {
                $count++;
            }
        }
    }
    
    return $count;
}

// Task: Change permalink structure to Post name
function vj_toolkit_change_permalink_structure() {
    if (!current_user_can('manage_options')) {
        throw new Exception('You do not have permission to change permalink structure.');
    }
    
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules();
    
    return true;
}

// Task: Create new pages
function vj_toolkit_create_pages($page_list) {
    if (!current_user_can('publish_pages')) {
        throw new Exception('You do not have permission to create pages.');
    }
    
    $count = 0;
    $pages = explode("\n", $page_list);
    $pages = array_map('trim', $pages);
    $pages = array_filter($pages);

    foreach ($pages as $page_title) {
        $new_page = array(
            'post_title' => sanitize_text_field($page_title),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
        );
        
        $page_id = wp_insert_post($new_page);
        if ($page_id && !is_wp_error($page_id)) {
            $count++;
        }
    }
    
    return $count;
}
