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
delete_option('zhanzhangb_baidu_set_time');

$log_file_old = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'baidu-submit-logfile.log';
if (file_exists($log_file_old)) {
    @unlink($log_file_old);
}

$zhanzhangb_baidu_upload_dir = wp_upload_dir();
$zhanzhangb_baidu_log_file = $zhanzhangb_baidu_upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'baidu-submit-logfile.log';
if (file_exists($zhanzhangb_baidu_log_file)) {
    @unlink($zhanzhangb_baidu_log_file);
}
