<?php
class Zhanzhangb_Baidu_Utils {
    const MAX_RECORDS = 100;
    
    public function install() {
        if (!get_option('zhanzhangb_baidu_submit_number')) {
            update_option('zhanzhangb_baidu_submit_number', 0);
        }
        if (!get_option('zhanzhangb_baidu_submissions')) {
            update_option('zhanzhangb_baidu_submissions', []);
        }
        if (!get_option('zhanzhangb_baidu_custom_post_types')) {
            update_option('zhanzhangb_baidu_custom_post_types', []);
        }
        if (!get_option('zhanzhangb_baidu_set_time')) {
            update_option('zhanzhangb_baidu_set_time', []);
        }
    }

    public function get_option($key) {
        return get_option($key, '');
    }

    public function record_submission($post_id, $normal_success, $realtime_success) {
        $url = get_permalink($post_id);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        $date_key = $this->get_beijing_date_key();
        
        if ($normal_success) {
            $this->set_daily_submission($url, 'normal', $date_key);
        }
        if ($realtime_success) {
            $this->set_daily_submission($url, 'realtime', $date_key);
        }
    }

    private function set_daily_submission($url, $type, $date_key) {
        $hash = md5($url);
        $transient_key = "zh_{$type}_submitted_{$hash}_{$date_key}";
        
        $expiration = $this->get_beijing_seconds_until_midnight();
        set_transient($transient_key, 1, $expiration);
    }

    public function has_submitted_today($url, $type) {
        $hash = md5($url);
        $date_key = $this->get_beijing_date_key();
        $transient_key = "zh_{$type}_submitted_{$hash}_{$date_key}";
        
        return (bool) get_transient($transient_key);
    }

    public function get_beijing_date_key() {
        return gmdate('Ymd', time() + 8 * HOUR_IN_SECONDS);
    }

    public function get_beijing_seconds_until_midnight() {
        $current_time = time();
        $beijing_time = $current_time + 8 * HOUR_IN_SECONDS;
        $midnight = strtotime('tomorrow', $beijing_time) - 8 * HOUR_IN_SECONDS;
        return $midnight - $current_time;
    }

    public function update_submit_count($success_count) {
        $current = (int)get_option('zhanzhangb_baidu_submit_number', 0);
        update_option('zhanzhangb_baidu_submit_number', $current + $success_count);
    }
}