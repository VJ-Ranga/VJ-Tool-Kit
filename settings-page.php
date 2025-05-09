<?php
// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
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