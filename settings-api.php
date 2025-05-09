<?php
/**
 * Handle the plugin settings using WordPress Settings API
 *
 * @link       https://vjranga.com/
 * @since      2.0.3
 *
 * @package    VJ_Tool_Kit
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display admin notices for task results
 */
function vj_toolkit_admin_notices() {
    $screen = get_current_screen();
    
    // Only show on our settings page
    if ($screen->id !== 'settings_page_vj-toolkit-settings') {
        return;
    }
    
    // Check for task results in transient
    $task_results_data = get_transient('vj_toolkit_task_results');
    if ($task_results_data) {
        $task_results = $task_results_data['results'];
        $has_errors = $task_results_data['has_errors'];
        
        // Display results
        if (!empty($task_results)) {
            echo '<div class="notice notice-' . ($has_errors ? 'warning' : 'success') . ' is-dismissible"><p>';
            foreach ($task_results as $task => $result) {
                echo '<strong>' . esc_html(ucfirst(str_replace('_', ' ', $task))) . ':</strong> ' . esc_html($result['message']) . '<br>';
            }
            echo '</p></div>';
        }
        
        // Clear the transient
        delete_transient('vj_toolkit_task_results');
    }
}
add_action('admin_notices', 'vj_toolkit_admin_notices');

/**
 * Process task execution
 */
function vj_toolkit_process_tasks() {
    // Check if the form was submitted
    if (!isset($_POST['execute_tasks']) || !isset($_POST['vj_toolkit_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['vj_toolkit_nonce'], 'vj_toolkit_actions')) {
        wp_die('Security check failed. Please try again.');
    }
    
    // Get existing options
    $options = get_option('vj_toolkit_options', []);
    
    // Update options based on form submission
    $tasks = [
        'delete_posts', 
        'delete_pages', 
        'delete_comments', 
        'delete_media', 
        'remove_inactive_themes', 
        'change_permalink_structure', 
        'delete_plugins', 
        'create_pages'
    ];
    
    // Update task checkboxes
    foreach ($tasks as $task) {
        $options[$task] = isset($_POST['vj_toolkit_tasks']) && in_array($task, $_POST['vj_toolkit_tasks']);
    }
    
    // Update page list
    if (isset($_POST['vj_toolkit_page_list'])) {
        $options['page_list'] = sanitize_textarea_field($_POST['vj_toolkit_page_list']);
    }
    
    // Save the updated options
    update_option('vj_toolkit_options', $options);
    
    $task_results = [];
    $has_errors = false;
    
    // Process each task if enabled
    if (isset($options['delete_posts']) && $options['delete_posts']) {
        try {
            $count = vj_toolkit_delete_posts();
            $task_results['delete_posts'] = [
                'success' => true,
                'message' => sprintf('%d posts deleted successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['delete_posts'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['delete_pages']) && $options['delete_pages']) {
        try {
            $count = vj_toolkit_delete_pages();
            $task_results['delete_pages'] = [
                'success' => true,
                'message' => sprintf('%d pages deleted successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['delete_pages'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['delete_comments']) && $options['delete_comments']) {
        try {
            $count = vj_toolkit_delete_comments();
            $task_results['delete_comments'] = [
                'success' => true,
                'message' => sprintf('%d comments deleted successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['delete_comments'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['delete_media']) && $options['delete_media']) {
        try {
            $count = vj_toolkit_delete_media();
            $task_results['delete_media'] = [
                'success' => true,
                'message' => sprintf('%d media items deleted successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['delete_media'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['remove_inactive_themes']) && $options['remove_inactive_themes']) {
        try {
            $count = vj_toolkit_remove_inactive_themes();
            $task_results['remove_inactive_themes'] = [
                'success' => true,
                'message' => sprintf('%d inactive themes removed successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['remove_inactive_themes'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['change_permalink_structure']) && $options['change_permalink_structure']) {
        try {
            vj_toolkit_change_permalink_structure();
            $task_results['change_permalink_structure'] = [
                'success' => true,
                'message' => 'Permalink structure changed to Post name.'
            ];
        } catch (Exception $e) {
            $task_results['change_permalink_structure'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['delete_plugins']) && $options['delete_plugins']) {
        try {
            $count = vj_toolkit_disable_and_delete_plugins();
            $task_results['delete_plugins'] = [
                'success' => true,
                'message' => sprintf('%d plugins disabled and deleted successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['delete_plugins'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    if (isset($options['create_pages']) && $options['create_pages'] && !empty($options['page_list'])) {
        try {
            $count = vj_toolkit_create_pages($options['page_list']);
            $task_results['create_pages'] = [
                'success' => true,
                'message' => sprintf('%d new pages created successfully.', $count)
            ];
        } catch (Exception $e) {
            $task_results['create_pages'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $has_errors = true;
        }
    }
    
    // Store results in transient to display notice after redirect
    set_transient('vj_toolkit_task_results', [
        'results' => $task_results,
        'has_errors' => $has_errors
    ], 60);
    
    // Redirect to avoid form resubmission
    wp_redirect(add_query_arg('settings-updated', 'true', admin_url('options-general.php?page=vj-toolkit-settings')));
    exit;
}
add_action('admin_init', 'vj_toolkit_process_tasks');

/**
 * Plugin settings page
 */
function vj_toolkit_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Get current options
    $options = get_option('vj_toolkit_options', []);
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('VJ Tool Kit Settings', 'vj-toolkit'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('vj_toolkit_actions', 'vj_toolkit_nonce'); ?>
            
            <div class="notice notice-warning">
                <p><strong><?php echo esc_html__('Warning:', 'vj-toolkit'); ?></strong> <?php echo esc_html__('The actions below are destructive and cannot be undone. Please use with caution.', 'vj-toolkit'); ?></p>
            </div>
            
            <h2><?php echo esc_html__('Select Tasks:', 'vj-toolkit'); ?></h2>
            <ul>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_posts" class="destructive-action" <?php checked(isset($options['delete_posts']) && $options['delete_posts']); ?>>
                        <?php echo esc_html__('Delete all posts', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_pages" class="destructive-action" <?php checked(isset($options['delete_pages']) && $options['delete_pages']); ?>>
                        <?php echo esc_html__('Delete all pages', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_comments" class="destructive-action" <?php checked(isset($options['delete_comments']) && $options['delete_comments']); ?>>
                        <?php echo esc_html__('Delete all comments', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_media" class="destructive-action" <?php checked(isset($options['delete_media']) && $options['delete_media']); ?>>
                        <?php echo esc_html__('Delete all media', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="remove_inactive_themes" class="destructive-action" <?php checked(isset($options['remove_inactive_themes']) && $options['remove_inactive_themes']); ?>>
                        <?php echo esc_html__('Remove all inactive themes', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="change_permalink_structure" <?php checked(isset($options['change_permalink_structure']) && $options['change_permalink_structure']); ?>>
                        <?php echo esc_html__('Change Permalink structure to Post name', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_plugins" class="destructive-action" <?php checked(isset($options['delete_plugins']) && $options['delete_plugins']); ?>>
                        <?php echo esc_html__('Disable and Delete all plugins (except this plugin)', 'vj-toolkit'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="vj_toolkit_tasks[]" value="create_pages" <?php checked(isset($options['create_pages']) && $options['create_pages']); ?>>
                        <?php echo esc_html__('Create new pages', 'vj-toolkit'); ?>
                    </label>
                </li>
            </ul>

            <h2><?php echo esc_html__('Create Pages:', 'vj-toolkit'); ?></h2>
            <p>
                <label for="vj_toolkit_page_list"><?php echo esc_html__('Page List (one page per line):', 'vj-toolkit'); ?></label><br>
                <textarea name="vj_toolkit_page_list" id="vj_toolkit_page_list" rows="5" cols="50"><?php echo isset($options['page_list']) ? esc_textarea($options['page_list']) : ''; ?></textarea>
            </p>
            <p>
                <em><?php echo esc_html__('Example list:', 'vj-toolkit'); ?></em><br>
                <?php echo esc_html__('Home', 'vj-toolkit'); ?><br>
                <?php echo esc_html__('About Us', 'vj-toolkit'); ?><br>
                <?php echo esc_html__('Contact Us', 'vj-toolkit'); ?>
            </p>

            <p>
                <?php 
                submit_button(
                    __('Execute Tasks', 'vj-toolkit'), 
                    'primary', 
                    'execute_tasks', 
                    true, 
                    array('data-confirm' => __('Are you sure you want to execute these tasks? This cannot be undone.', 'vj-toolkit'),
                         'id' => 'vj_toolkit_submit')
                ); 
                ?>
            </p>
        </form>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#vj_toolkit_submit').on('click', function(e) {
                    // Check if destructive actions are selected
                    var hasDestructiveAction = $('.destructive-action:checked').length > 0;
                    
                    if (hasDestructiveAction) {
                        if (!confirm($(this).data('confirm'))) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            });
        </script>
    </div>
    <?php
}