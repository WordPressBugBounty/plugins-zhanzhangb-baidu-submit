<?php
/**
Plugin Name: 自动提交百度收录插件
Plugin URI: https://www.zhanzhangb.cn/zhanzhangb-baidu-submit
Text Domain: zhanzhangb-baidu-submit
Description: 发布/更新文章、页面或自定义文章时，实时推送URL至百度搜索资源平台，支持普通收录与快速抓取提交。
Version: 1.9.0
Requires at least: 5.5
Requires PHP: 7.0
Author: 站长帮
Author URI: https://www.zhanzhangb.cn
License: GNU General Public License (GPL) version 3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Copyright (c) 2020-2025, 站长帮（zhanzhangb.cn）
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
/*
*BOOTSTRAP FILE
*/
defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'admin/class-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-submit-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-logger.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-utils.php';

class Zhanzhangb_Baidu_Submit {
    private $settings;
    private $submit_api;
    private $logger;
    private $utils;

    public function __construct() {
        $this->utils = new Zhanzhangb_Baidu_Utils();
        $this->logger = new Zhanzhangb_Baidu_Logger();
        $this->settings = new Zhanzhangb_Baidu_Settings($this->logger, $this->utils);
        $this->submit_api = new Zhanzhangb_Baidu_Submit_API($this->logger, $this->utils);

        register_activation_hook(__FILE__, [$this->utils, 'install']);
        add_action('init', [$this, 'register_hooks']);

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_plugin_actions']);

        add_filter('plugin_row_meta', [$this, 'add_custom_plugin_links_after_version'], 10, 2);
    }

    public function register_hooks() {
        add_action('publish_post', [$this->submit_api, 'handle_submit']);
        add_action('publish_page', [$this->submit_api, 'handle_submit']);

        $selected_types = get_option('zhanzhangb_baidu_custom_post_types', []);
        if (is_array($selected_types)) {
            foreach ($selected_types as $post_type) {
                add_action("publish_$post_type", [$this->submit_api, 'handle_submit']);
            }
        }
    }

    public function add_plugin_actions($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=zhanzhangb_baidu_submit') . '">设置</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function add_custom_plugin_links_after_version($meta, $file) {
        if ($file == plugin_basename(__FILE__)) {
            $link1 = '<a href="https://www.zhanzhangb.com" target="_blank">站长帮资源站</a>';
            $link2 = '<a href="https://www.zhanzhangb.cn/tutorials/" target="_blank">WordPress 教程</a>';

            $custom_links = ' ✶✶✶ 推荐：' . $link1 . ' & ' . $link2 . ' ✶✶✶ ';
            $meta[] = $custom_links;
        }
        return $meta;
    }
}

new Zhanzhangb_Baidu_Submit();