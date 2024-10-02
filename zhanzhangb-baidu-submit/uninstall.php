<?php
if(!defined("WP_UNINSTALL_PLUGIN")) exit();

delete_option('zhanzhangb_baidu_realtime_number');
delete_option('zhanzhangb_baidu_realtime_date');
delete_option('zhanzhangb_baidu_submit_number');
delete_option('zhanzhangb_baidu_realtime_token');
delete_option('zhanzhangb_baidu_token');
delete_option('zhanzhangb_baidu_push');
delete_option('zhanzhangb_baidu_today_number');
?>