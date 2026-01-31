<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Claude_AI_Summarizer
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all plugin options
delete_option('claude_api_key');
delete_option('claude_model');
delete_option('claude_summary_length');
delete_option('claude_auto_button');
delete_option('claude_button_position');
delete_option('claude_github_repo');
delete_option('claude_auto_update');
delete_option('claude_last_update_check');

// Delete all post meta (summaries)
global $wpdb;
$wpdb->query(
    "DELETE FROM {$wpdb->postmeta} 
     WHERE meta_key IN ('_claude_summary', '_claude_summary_time')"
);
