<?php
/**
 * Plugin Name: 自动提交百度收录插件
 * Plugin URI: https://www.zhanzhangb.cn/zhanzhangb-baidu-submit
 * Text Domain: zhanzhangb-baidu-submit
 * Description: 发布/更新文章、页面或自定义文章时，实时推送URL至百度搜索资源平台，支持普通收录与快速抓取提交。
 * Version: 1.9.1
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * Author: 站长帮
 * Author URI: https://www.zhanzhangb.cn
 * License: GNU General Public License Version 2 (GPL v2)
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Copyright (c) 2020-2025, 站长帮（zhanzhangb.cn）
/*
 * BOOTSTRAP FILE
 */
defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'admin/class-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-submit-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-logger.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-utils.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-output-structured.php';

class Zhanzhangb_Baidu_Submit {
    private $settings;
    private $submit_api;
    private $logger;
    private $utils;
    private $set_time_options;

    public function __construct() {
        $this->utils = new Zhanzhangb_Baidu_Utils();
        $this->logger = new Zhanzhangb_Baidu_Logger();
        $this->settings = new Zhanzhangb_Baidu_Settings($this->logger, $this->utils);
        $this->submit_api = new Zhanzhangb_Baidu_Submit_API($this->logger, $this->utils);

        $this->set_time_options = get_option('zhanzhangb_baidu_set_time', []);

        register_activation_hook(__FILE__, [$this->utils, 'install']);
        add_action('init', [$this, 'register_hooks']);

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_plugin_actions']);

        add_filter('plugin_row_meta', [$this, 'add_custom_plugin_links_after_version'], 10, 2);

        add_action('init', [$this, 'add_time_factor_hook']);
    }

    public function register_hooks() {
        if (defined('WP_IMPORTING') && WP_IMPORTING) return;

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
            $meta[] = ' ✶✶✶ 推荐：' . $link1 . ' & ' . $link2 . ' ✶✶✶ ';
        }
        return $meta;
    }

    public function add_time_factor_hook() {
        if (!is_user_logged_in() && !empty($this->set_time_options)) {
            add_action('wp_head', [$this, 'output_structured_data']);
        }
    }

    public function output_structured_data() {
        Zhanzhangb_Baidu_Structured::output($this->set_time_options);
    }
}

new Zhanzhangb_Baidu_Submit();