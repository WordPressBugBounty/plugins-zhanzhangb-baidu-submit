<?php
/**
Plugin Name: 自动提交百度收录插件
Plugin URI: https://www.zhanzhangb.cn/zhanzhangb-baidu-submit
Text Domain: zhanzhangb-baidu-submit
Description: 发布/更新文章或页面时，实时推送URL至百度搜索资源平台，支持普通收录与快速收录提交。
Version: 1.8.3
Requires at least: 5.2
Requires PHP: 7.0
Author: 站长帮
Author URI: https://www.zhanzhangb.cn
License: GNU General Public License (GPL) version 3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Copyright (c) 2020-2024, 站长帮（zhanzhangb.cn）
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
/*
*BOOTSTRAP FILE
*/
defined( 'ABSPATH' ) || exit;
if (!class_exists('Zhanzhangb_Baidu_Submit')){
class Zhanzhangb_Baidu_Submit{
    function __construct(){
        register_activation_hook( __FILE__, array( $this,'zhanzhangb_baidu_submit_install') );
		add_action( 'publish_page', array( $this, 'baidu_submit_post' ), 1 );
        add_action( 'publish_post', array( $this, 'baidu_submit_post' ), 1 );
        if( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'zhanzhangb_baidu_submit_menu' ));
            add_action( 'admin_init', array( $this, 'settings_init' ) );
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ));
        }
    }
    function zhanzhangb_baidu_submit_install() {
        if ( intval(get_option('zhanzhangb_baidu_submit_number')) > 0){
            return;
        }
        update_option('zhanzhangb_baidu_submit_number',0);
    }
    function add_action_links ( $links ) {
     $mylinks = array(
     '<a href="' . admin_url( 'options-general.php?page=zhanzhangb_baidu_submit' ) . '">' . __('Settings') . '</a>'
     );
    return array_merge( $links, $mylinks );
    }
    function zhanzhangb_baidu_submit_menu() {
        if( is_admin() ) {
            add_options_page(
                '站长帮 - 自动提交百度收录设置',
                '站长帮 - 自动提交百度收录设置',
                'manage_options',
                'zhanzhangb_baidu_submit',
                array( $this, 'zhanzhangb_baidu_submit_options' )
            );
        }
    }
    function settings_init(){
        add_settings_section(
            'zhanzhangb_baidu_set',
            __( '百度推送设置', 'zhanzhangb-baidu-submit' ),
            array( $this, 'zhanzhangb_baidu_settings_title' ),
            'zhanzhangb_baidu_settings'
        );
        add_settings_field(
            'zhanzhangb_baidu_token', //id
            __( '普通收录提交密钥（token）：', 'zhanzhangb-baidu-submit' ), 
            array( $this, 'token_setting_cb' ), 
            'zhanzhangb_baidu_settings', 
            'zhanzhangb_baidu_set',
            'SecretId',
            array( 'label_for' => 'zhanzhangb_baidu_token' ) 
        );
        add_settings_field(
            'zhanzhangb_baidu_realtime_token', //id
            __( '快速收录提交密钥（token）：', 'zhanzhangb-baidu-submit' ), 
            array( $this, 'realtime_token_setting_cb' ), 
            'zhanzhangb_baidu_settings', 
            'zhanzhangb_baidu_set',
            'SecretId',
            array( 'label_for' => 'zhanzhangb_baidu_realtime_token' ) 
        );
        add_settings_field(
            'zhanzhangb_baidu_check', //id
            __( '允许24小时内重复提交：', 'zhanzhangb-baidu-submit' ), 
            array( $this, 'saving_checkbox_cb' ), 
            'zhanzhangb_baidu_settings', 
            'zhanzhangb_baidu_set',
            'zhanzhangb_baidu_check'
        );
        register_setting( 'zhanzhangb_baidu_settings', 'zhanzhangb_baidu_token', 'sanitize_text_field' );
        register_setting( 'zhanzhangb_baidu_settings', 'zhanzhangb_baidu_realtime_token', 'sanitize_text_field' );
        register_setting( 'zhanzhangb_baidu_settings', 'zhanzhangb_baidu_check', 'intval' );
    }
    function token_setting_cb(){
        $zhanzhangb_baidu_token = get_option('zhanzhangb_baidu_token');
        echo '<input maxlength="16" size="16" type="text" required="required" pattern="[A-z0-9]{16}" name="zhanzhangb_baidu_token" value="'. esc_attr( $zhanzhangb_baidu_token ) .'" />';
        if (empty($zhanzhangb_baidu_token)){
            echo esc_html__('*必填','zhanzhangb-baidu-submit');
        }
    }
    function realtime_token_setting_cb(){
        $zhanzhangb_baidu_realtime_token = get_option('zhanzhangb_baidu_realtime_token');
        echo '<input maxlength="16" size="16" type="text" pattern="[A-z0-9]{16}" name="zhanzhangb_baidu_realtime_token" value="'. esc_attr( $zhanzhangb_baidu_realtime_token ) .'" />';
        if (empty($zhanzhangb_baidu_realtime_token)){
            echo esc_html__(' 如空，则不开启快速收录提交','zhanzhangb-baidu-submit');
        }
    }
    function saving_checkbox_cb(){
        $zhanzhangb_baidu_check = get_option('zhanzhangb_baidu_check');
        echo '<input type="checkbox" id="zhanzhangb_baidu_check" name="zhanzhangb_baidu_check" value="1" ' . checked( true, $zhanzhangb_baidu_check, false ) . ' />';
		echo esc_html__(' 不建议勾选，默认同一个URL在24小时内仅提交一次。PS：实践证明频繁提交不会加快收录。','zhanzhangb-baidu-submit');
    }
    function zhanzhangb_baidu_settings_title($args){
        echo '<a href="';
        echo esc_url( 'https://ziyuan.baidu.com/linksubmit/index' );
        echo '" target="_blank"><span>';
        echo esc_html__( '获取百度普通收录提交token ', 'zhanzhangb-baidu-submit' );
        echo '</span></a><br />';
        echo '<a href="';
        echo esc_url( 'https://ziyuan.baidu.com/dailysubmit/index' );
        echo '" target="_blank"><span>';
        echo esc_html__( '获取快速收录提交token ', 'zhanzhangb-baidu-submit' );
        echo '</span></a>';
    }
    function zhanzhangb_baidu_submit_options() {
        if ( !current_user_can( 'manage_options' ) ){
            wp_die( __( 'Sorry, you are not allowed to manage options for this site.' ) );
        }
        echo '<div class="zhanzhangb_baidu">';
        echo '<form method="post" action="options.php">';
        echo '<h1>'.esc_html__( '自动提交百度收录 - 设置', 'zhanzhangb-baidu-submit' ).'</h1>';
        echo esc_html__( '插件技术支持：', 'zhanzhangb-baidu-submit' ).'<a href="'.esc_url('https://www.zhanzhangb.cn/zhanzhangb-baidu-submit/').'" target="_blank">'.esc_html__('站长帮官网', 'zhanzhangb-baidu-submit' ).'</a> ，'.esc_html__(' 如觉得插件还不错，请给个', 'zhanzhangb-baidu-submit' ).'<a href="'.esc_url('https://wordpress.org/support/plugin/zhanzhangb-baidu-submit/reviews/#new-post').'" target="_blank">'.esc_html__('五星好评！', 'zhanzhangb-baidu-submit' ).'</a><br />';
        echo esc_html__( '其它资源推荐：', 'zhanzhangb-baidu-submit' ).'<a href="'.esc_url('https://www.zhanzhangb.com/plugins').'" target="_blank">'.esc_html__( '热门插件下载', 'zhanzhangb-baidu-submit' ).'</a>，<a href="'.esc_url('https://www.zhanzhangb.com/themes').'" target="_blank">'.esc_html__( '热门主题下载', 'zhanzhangb-baidu-submit' ).'</a><br />';
        echo '<hr />';
        settings_fields( 'zhanzhangb_baidu_settings' );
        do_settings_sections( 'zhanzhangb_baidu_settings' );
        submit_button();
        echo '<hr />';
        if (get_option('zhanzhangb_baidu_token') !== false){
            echo '<span style="color:#009933">' . esc_html__('普通收录提交功能：已开启','zhanzhangb-baidu-submit') . '</span><br />';
        }else {
            echo '<span style="color:#FF0000">' . esc_html__( '普通收录提交功能：未开启，请正确设置推送密钥（token值）', 'zhanzhangb-baidu-submit' ) . '</span><br />';
        }
        echo esc_html__('累计提交成功：','zhanzhangb-baidu-submit') . get_option('zhanzhangb_baidu_submit_number') . esc_html__('条','zhanzhangb-baidu-submit').'<br />';
        echo '<hr />';
        if (get_option('zhanzhangb_baidu_realtime_token')){
            echo '<span style="color:#009933">' . esc_html__('快速收录提交功能已开启','zhanzhangb-baidu-submit') . '</span><br />';
        }else {
            echo '<span style="color:#FF0000">' . esc_html__( '快速收录提交功能未开启，如您已获得快速收录提交权限请正确设置token', 'zhanzhangb-baidu-submit' ) . '</span><br />';
        }
        echo '</form></div>';
		echo '<hr />';
		echo '<div>';
		echo '<h3>' . esc_html__('提交日志(最多显示20条)：', 'zhanzhangb-baidu-submit') . '</h3>';
		$logFile = WP_CONTENT_DIR . '/baidu-submit-logfile.log';
		if (file_exists($logFile)) {
			$logContent = file_get_contents($logFile);
			$maxFileSize = 1024 * 20;
            if (strlen($logContent) > $maxFileSize) {
                $logContent = esc_html__('日志文件载入错误，请手动检查文件是否受损或被恶意篡改。日志文件路径：', 'zhanzhangb-baidu-submit') . $logFile;
            }
			$logLines = explode("\n", $logContent);
			$logLines = array_reverse($logLines);
			echo '<pre>' . esc_html(implode("\n", $logLines)) . '</pre>';
		} else {
			echo '<p>' . esc_html__('暂无API提交日志。', 'zhanzhangb-baidu-submit') . '</p>';
		}
		echo '</div>';
    }
    public function baidu_submit_post( $ID ) {
        $zhanzhangb_postid = $ID;
        $WEB_TOKEN = get_option('zhanzhangb_baidu_token');
        if (!empty($WEB_TOKEN)) {
            $realtimeToken = get_option('zhanzhangb_baidu_realtime_token');
            if ( get_option('zhanzhangb_baidu_check') || !$this->has_post_been_submitted($zhanzhangb_postid) ) {
                $this->record_submission_time($zhanzhangb_postid);
                $this->submit_normal($zhanzhangb_postid, $WEB_TOKEN);
                if (!empty($realtimeToken)) {
                    $this->submit_realtime($zhanzhangb_postid, $WEB_TOKEN);
                }
            }
        }
    }
    private function has_post_been_submitted($post_id) {
        $submitted_posts = get_option('zhanzhangb_baidu_today_number', array());
        $submission_time = isset($submitted_posts[$post_id]) ? $submitted_posts[$post_id] : 0;
        return $submission_time > strtotime('-24 hours');
    }
    private function record_submission_time($post_id) {
        $submitted_posts = get_option('zhanzhangb_baidu_today_number', array());
        $submitted_posts[$post_id] = time();
        $submitted_posts = array_slice($submitted_posts, -30, 30, true);
        update_option('zhanzhangb_baidu_today_number', $submitted_posts);
    }
    private function process_response($response, $urls, $type) {
        $wp_timezone = get_option('timezone_string');
        date_default_timezone_set($wp_timezone);
        if (is_wp_error($response)) {
            $logMessage = $type . '提交失败，错误码: ' . $response->get_error_code() . ', 错误提示: ' . $response->get_error_message() . '。提交URL：' . $urls[0];
        } else {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($data['success'])) {
                $successCount = $data['success'];
                $remainQuota = $data['remain'];
                $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $type . '收录提交成功' . $successCount . '条URL，今日可提交额度还余' . $remainQuota . '条。提交URL：' . $urls[0];
                update_option('zhanzhangb_baidu_submit_number',intval(get_option('zhanzhangb_baidu_submit_number')) + intval($successCount));
            } elseif (isset($data['error'])) {
                $errorCode = $data['error'];
                $errorMessage = $data['message'];
                $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $type . '收录提交失败，错误码: ' . $errorCode . ', 错误提示: ' . $errorMessage . '。提交URL：' . $urls[0];
            }
        }
        define('LOG_FILE', WP_CONTENT_DIR . '/baidu-submit-logfile.log');
        if (!file_exists(LOG_FILE)) {
            touch(LOG_FILE);
        }
		$maxLogs = 19;
		$currentLogs = file(LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (count($currentLogs) >= $maxLogs) {
			$excessLogs = count($currentLogs) - $maxLogs;
			array_splice($currentLogs, 0, $excessLogs);
		}
		file_put_contents(LOG_FILE, implode(PHP_EOL, $currentLogs) . PHP_EOL . $logMessage, LOCK_EX);
    }
    private function submit_normal($post_id, $token) {
        $WEB_DOMAIN = site_url();
        $urls = array(get_permalink($post_id));
        $normalApi = 'http://data.zz.baidu.com/urls?site=' . $WEB_DOMAIN . '&token=' . $token;
        $normalArgs = array(
            'body' => implode("\n", $urls),
            'headers' => array('Content-Type' => 'text/plain'),
        );
        $normalResponse = wp_remote_post($normalApi, $normalArgs);
        $this->process_response($normalResponse, $urls, '普通');
    }
	private function submit_realtime($post_id, $token) {
		$WEB_DOMAIN = site_url();
		$urls = array(get_permalink($post_id));
		$realtimeApi = 'http://data.zz.baidu.com/urls?site=' . $WEB_DOMAIN . '&token=' . $token . '&type=daily';
		$realtimeArgs = array(
			'body' => implode("\n", $urls),
			'headers' => array('Content-Type' => 'text/plain'),
		);
		$realtimeResponse = wp_remote_post($realtimeApi, $realtimeArgs);
		$this->process_response($realtimeResponse, $urls, '快速');
    }
}
}
new Zhanzhangb_Baidu_Submit()
?>