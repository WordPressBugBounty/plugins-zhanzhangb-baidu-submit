<?php
class Zhanzhangb_Baidu_Submit_API {
    private $logger;
    private $utils;
    private $normal_token;
    private $realtime_token;
    private $allow_recent_submit;

    public function __construct($logger, $utils) {
        $this->logger = $logger;
        $this->utils = $utils;
        $this->normal_token = $this->utils->get_option('zhanzhangb_baidu_token');
        $this->realtime_token = $this->utils->get_option('zhanzhangb_baidu_realtime_token');
        $this->allow_recent_submit = $this->utils->get_option('zhanzhangb_baidu_check');
    }

    public function handle_submit($post_id_or_post) {
        if (is_int($post_id_or_post)) {
            $post_id = $post_id_or_post;
            $post = get_post($post_id);
        } elseif (is_a($post_id_or_post, 'WP_Post')) {
            $post = $post_id_or_post;
            $post_id = $post->ID;
        } else {
            $this->logger->log("Invalid post parameter: " . print_r($post_id_or_post, true), 'error');
            return;
        }

        if (!$post || is_wp_error($post)) {
            $this->logger->log("Failed to retrieve post with ID: $post_id", 'error');
            return;
        }

        $url = get_permalink($post_id);

        if ($this->is_recent_submission($url)) {
            return;
        }

        if ($this->should_submit($post_id)) {
            $success_normal = false;
            $success_realtime = false;

            if ($this->normal_token && !$this->has_submitted_today($url, 'normal')) {
                $success_normal = $this->submit_normal($url);
            }

            if ($this->realtime_token && !$this->has_submitted_today($url, 'realtime')) {
                $success_realtime = $this->submit_realtime($url);
            }

            if ($success_normal || $success_realtime) {
                $this->utils->record_submission($post_id, $success_normal, $success_realtime);
                $this->set_submission_lock($url);
            }
        }
    }

    private function submit_normal($url) {
        $api_url = 'http://data.zz.baidu.com/urls?site=' . site_url() . '&token=' . $this->normal_token;
        
        $response = wp_remote_post($api_url, [
            'body' => $url,
            'headers' => ['Content-Type' => 'text/plain']
        ]);

        return $this->process_response($response, $url, '普通收录');
    }

    private function submit_realtime($url) {
        $api_url = 'http://data.zz.baidu.com/urls?site=' . site_url() . '&token=' . $this->realtime_token . '&type=fastcrawl';

        $response = wp_remote_post($api_url, [
            'body' => $url,
            'headers' => ['Content-Type' => 'text/plain']
        ]);

        return $this->process_response($response, $url, '快速抓取');
    }

    private function process_response($response, $url, $type) {
        if (is_wp_error($response)) {
            $this->logger->log("{$type}提交失败 - {$response->get_error_message()}", 'error', $url);
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['error'])) {
            $this->logger->log("{$type}提交失败 - {$body['message']}", 'error', $url);
            return false;
        } else {
            $this->utils->update_submit_count($body['success']);
            $this->logger->log("{$type}提交成功 - 当日剩余配额: {$body['remain']}", 'success', $url);
            return true;
        }
    }

    private function should_submit($post_id) {
        $url = get_permalink($post_id);
        $post_type = get_post_type($post_id);
        $selected_types = get_option('zhanzhangb_baidu_custom_post_types', []);

        if (!is_array($selected_types)) {
            $selected_types = [];
        }

        if ($post_type === 'post' || $post_type === 'page') {
            return true;
        }

        return in_array($post_type, $selected_types);
    }

    private function is_recent_submission($url) {
        $cache_key = 'zh_submit_' . md5($url);
        return (bool) wp_cache_get($cache_key, 'zhanzhangb_baidu_submit');
    }
    
    private function set_submission_lock($url) {
        $cache_key = 'zh_submit_' . md5($url);
        wp_cache_set($cache_key, 1, 'zhanzhangb_baidu_submit', 30);
    }

    private function has_submitted_today($url, $type) {
        $submissions = get_option('zhanzhangb_baidu_submissions', []);
        $hash = md5($url);
        
        if (isset($submissions[$hash])) {
            $submission = $submissions[$hash];
            if ((time() - $submission['timestamp']) < 86400) {
                if (($type === 'normal' && $submission['normal']) || ($type === 'realtime' && $submission['realtime'])) {
                    return true;
                }
            }
        }
        
        return false;
    }
}