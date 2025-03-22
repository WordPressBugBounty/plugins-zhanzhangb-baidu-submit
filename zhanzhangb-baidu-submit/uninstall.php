<?php
if (!defined("WP_UNINSTALL_PLUGIN")) exit();
delete_option('zhanzhangb_baidu_realtime_number');
delete_option('zhanzhangb_baidu_realtime_date');
delete_option('zhanzhangb_baidu_submit_number');
delete_option('zhanzhangb_baidu_realtime_token');
delete_option('zhanzhangb_baidu_token');
delete_option('zhanzhangb_baidu_push');
delete_option('zhanzhangb_baidu_check');
delete_option('zhanzhangb_baidu_custom_post_types');
delete_option('zhanzhangb_baidu_submissions');
$log_file = WP_CONTENT_DIR . '/baidu-submit-logfile.log';
if (file_exists($log_file)) {
    @unlink($log_file);
}