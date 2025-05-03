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
 * Register plugin settings, sections and fields
 */
function vj_toolkit_register_settings() {
    // Register a setting group
    register_setting(
        'vj_toolkit_settings_group',    // Option group
        'vj_toolkit_options',           // Option name
        'vj_toolkit_sanitize_options'   // Sanitize callback
    );

    // Register a settings section
    add_settings_section(
        'vj_toolkit_tasks_section',        // ID
        esc_html__('Select Tasks:', 'vj-toolkit'),  // Title
        'vj_toolkit_tasks_section_cb',    // Callback
        'vj-toolkit-settings'             // Page
    );

    // Register settings fields
    add_settings_field(
        'delete_posts',                 // ID
        esc_html__('Delete all posts', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'delete_posts',
            'class' => 'destructive-action'
        ]
    );

    add_settings_field(
        'delete_pages',                 // ID
        esc_html__('Delete all pages', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'delete_pages',
            'class' => 'destructive-action'
        ]
    );

    add_settings_field(
        'delete_comments',              // ID
        esc_html__('Delete all comments', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'delete_comments',
            'class' => 'destructive-action'
        ]
    );

    add_settings_field(
        'delete_media',                 // ID
        esc_html__('Delete all media', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'delete_media',
            'class' => 'destructive-action'
        ]
    );

    add_settings_field(
        'remove_inactive_themes',       // ID
        esc_html__('Remove all inactive themes', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'remove_inactive_themes',
            'class' => 'destructive-action'
        ]
    );

    add_settings_field(
        'change_permalink_structure',   // ID
        esc_html__('Change Permalink structure to Post name', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'change_permalink_structure',
            'class' => ''
        ]
    );

    add_settings_field(
        'delete_plugins',               // ID
        esc_html__('Disable and Delete all plugins (except this plugin)', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'delete_plugins',
            'class' => 'destructive-action'
        ]
    );

    add_settings_field(
        'create_pages',                 // ID
        esc_html__('Create new pages', 'vj-toolkit'),   // Title
        'vj_toolkit_checkbox_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_tasks_section',     // Section
        [                               // Args
            'name' => 'create_pages',
            'class' => ''
        ]
    );

    // Add a section for page creation
    add_settings_section(
        'vj_toolkit_pages_section',        // ID
        esc_html__('Create Pages:', 'vj-toolkit'),  // Title
        'vj_toolkit_pages_section_cb',    // Callback
        'vj-toolkit-settings'             // Page
    );

    // Add field for page list
    add_settings_field(
        'page_list',                    // ID
        esc_html__('Page List (one page per line):', 'vj-toolkit'),   // Title
        'vj_toolkit_textarea_field_cb', // Callback
        'vj-toolkit-settings',          // Page
        'vj_toolkit_pages_section',     // Section
        [                               // Args
            'name' => 'page_list',
            'rows' => 5,
            'cols' => 50
        ]
    );
}
add_action('admin_init', 'vj_toolkit_register_settings');

/**
 * Sanitize the options
 *
 * @param array $input The input array to sanitize.
 * @return array Sanitized array
 */
function vj_toolkit_sanitize_options($input) {
    $output = [];
    
    // Sanitize task checkboxes
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
    
    foreach ($tasks as $task) {
        if (isset($input[$task])) {
            $output[$task] = true;
        } else {
            $output[$task] = false;
        }
    }
    
    // Sanitize page list
    if (isset($input['page_list'])) {
        $output['page_list'] = sanitize_textarea_field($input['page_list']);
    } else {
        $output['page_list'] = '';
    }
    
    return $output;
}

/**
 * Callback for the tasks section description
 */
function vj_toolkit_tasks_section_cb() {
    echo '<div class="notice notice-warning">';
    echo '<p><strong>' . esc_html__('Warning:', 'vj-toolkit') . '</strong> ' . esc_html__('The actions below are destructive and cannot be undone. Please use with caution.', 'vj-toolkit') . '</p>';
    echo '</div>';
}

/**
 * Callback for the pages section description
 */
function vj_toolkit_pages_section_cb() {
    echo '<p>';
    echo '<em>' . esc_html__('Example list:', 'vj-toolkit') . '</em><br>';
    echo esc_html__('Home', 'vj-toolkit') . '<br>';
    echo esc_html__('About Us', 'vj-toolkit') . '<br>';
    echo esc_html__('Contact Us', 'vj-toolkit');
    echo '</p>';
}

/**
 * Callback for checkbox fields
 */
function vj_toolkit_checkbox_field_cb($args) {
    $options = get_option('vj_toolkit_options', []);
    $name = $args['name'];
    $class = $args['class'];
    
    $checked = isset($options[$name]) && $options[$name] ? 'checked' : '';
    
    echo '<label>';
    echo '<input type="checkbox" name="vj_toolkit_options[' . esc_attr($name) . ']" value="1" class="' . esc_attr($class) . '" ' . $checked . '>';
    echo '</label>';
}

/**
 * Callback for textarea fields
 */
function vj_toolkit_textarea_field_cb($args) {
    $options = get_option('vj_toolkit_options', []);
    $name = $args['name'];
    $rows = $args['rows'];
    $cols = $args['cols'];
    
    $value = isset($options[$name]) ? $options[$name] : '';
    
    echo '<textarea name="vj_toolkit_options[' . esc_attr($name) . ']" id="vj_toolkit_' . esc_attr($name) . '" rows="' . esc_attr($rows) . '" cols="' . esc_attr($cols) . '">' . esc_textarea($value) . '</textarea>';
}

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
    
    $options = get_option('vj_toolkit_options', []);
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
 * Plugin settings page
 */
function vj_toolkit_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Get the active tab from the $_GET parameter
    $default_tab = 'cleanup';
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('VJ Tool Kit Settings', 'vj-toolkit'); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=vj-toolkit-settings&tab=cleanup" class="nav-tab <?php echo $active_tab == 'cleanup' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Cleanup Tools', 'vj-toolkit'); ?>
            </a>
            <a href="?page=vj-toolkit-settings&tab=setup" class="nav-tab <?php echo $active_tab == 'setup' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Site Setup', 'vj-toolkit'); ?>
            </a>
        </h2>
        
        <form method="post" action="options.php">
            <?php
            // Output security fields
            settings_fields('vj_toolkit_settings_group');
            
            // Output setting sections and fields
            do_settings_sections('vj-toolkit-settings');
            
            // Submit button
            submit_button(esc_html__('Save Settings', 'vj-toolkit'));
            ?>
        </form>
        
        <form method="post" action="">
            <?php wp_nonce_field('vj_toolkit_actions', 'vj_toolkit_nonce'); ?>
            <p>
                <?php 
                submit_button(
                    esc_html__('Execute Tasks', 'vj-toolkit'), 
                    'primary', 
                    'execute_tasks', 
                    true, 
                    [
                        'data-confirm' => esc_html__('Are you sure you want to execute these tasks? This cannot be undone.', 'vj-toolkit'),
                        'id' => 'vj_toolkit_submit'
                    ]
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