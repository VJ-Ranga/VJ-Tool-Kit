<?php
// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('VJ Tool Kit Settings', 'vj-toolkit'); ?></h1>
    <form method="post">
        <?php wp_nonce_field('vj_toolkit_actions', 'vj_toolkit_nonce'); ?>

        <div class="notice notice-warning">
            <p><strong><?php echo esc_html__('Warning:', 'vj-toolkit'); ?></strong> <?php echo esc_html__('The actions below are destructive and cannot be undone. Please use with caution.', 'vj-toolkit'); ?></p>
        </div>

        <div class="vj-tabs">
            <div class="vj-tab-buttons">
                <button type="button" class="vj-tab-btn active" data-tab="cleanup"><?php echo esc_html__('Cleanup Tasks', 'vj-toolkit'); ?></button>
                <button type="button" class="vj-tab-btn" data-tab="setup"><?php echo esc_html__('Site Setup Tasks', 'vj-toolkit'); ?></button>
            </div>

            <div id="tab-cleanup" class="vj-tab-content active">
                <h2><?php echo esc_html__('Select Cleanup Tasks:', 'vj-toolkit'); ?></h2>
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_posts" class="destructive-action">
                            <?php echo esc_html__('Delete all posts', 'vj-toolkit'); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_pages" class="destructive-action">
                            <?php echo esc_html__('Delete all pages', 'vj-toolkit'); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_comments" class="destructive-action">
                            <?php echo esc_html__('Delete all comments', 'vj-toolkit'); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_media" class="destructive-action">
                            <?php echo esc_html__('Delete all media', 'vj-toolkit'); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="remove_inactive_themes" class="destructive-action">
                            <?php echo esc_html__('Remove all inactive themes', 'vj-toolkit'); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="delete_plugins" class="destructive-action">
                            <?php echo esc_html__('Disable and Delete all plugins (except this plugin)', 'vj-toolkit'); ?>
                        </label>
                    </li>
                </ul>
            </div>

            <div id="tab-setup" class="vj-tab-content">
                <h2><?php echo esc_html__('Site Setup Tasks:', 'vj-toolkit'); ?></h2>
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="change_permalink_structure">
                            <?php echo esc_html__('Change Permalink structure to Post name', 'vj-toolkit'); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="vj_toolkit_tasks[]" value="create_pages">
                            <?php echo esc_html__('Create new pages', 'vj-toolkit'); ?>
                        </label>
                    </li>
                </ul>

                <h3><?php echo esc_html__('Create Pages:', 'vj-toolkit'); ?></h3>
                <p>
                    <label for="vj_toolkit_page_list"><?php echo esc_html__('Page List (one page per line):', 'vj-toolkit'); ?></label><br>
                    <textarea name="vj_toolkit_page_list" id="vj_toolkit_page_list" rows="5" cols="50"><?php echo isset($_POST['vj_toolkit_page_list']) ? esc_textarea($_POST['vj_toolkit_page_list']) : ''; ?></textarea>
                </p>
                <p>
                    <em><?php echo esc_html__('Example list:', 'vj-toolkit'); ?></em><br>
                    <?php echo esc_html__('Home', 'vj-toolkit'); ?><br>
                    <?php echo esc_html__('About Us', 'vj-toolkit'); ?><br>
                    <?php echo esc_html__('Contact Us', 'vj-toolkit'); ?>
                </p>
            </div>
        </div>

        <p>
            <?php 
            submit_button(
                __('Execute Tasks', 'vj-toolkit'), 
                'primary', 
                'vj_toolkit_submit', 
                true, 
                array('data-confirm' => __('Are you sure you want to execute these tasks? This cannot be undone.', 'vj-toolkit'))
            ); 
            ?>
        </p>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#vj_toolkit_submit').on('click', function(e) {
            var hasDestructiveAction = $('.destructive-action:checked').length > 0;
            if (hasDestructiveAction && !confirm($(this).data('confirm'))) {
                e.preventDefault();
                return false;
            }
        });

        $('.vj-tab-btn').on('click', function() {
            $('.vj-tab-btn').removeClass('active');
            $(this).addClass('active');
            var tab = $(this).data('tab');
            $('.vj-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });
    });
</script>
