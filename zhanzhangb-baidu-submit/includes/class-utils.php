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
    }

    public function get_option($key) {
        return get_option($key, '');
    }

    public function record_submission($post_id, $normal_success, $realtime_success) {
        $url = get_permalink($post_id);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        $submissions = get_option('zhanzhangb_baidu_submissions', []);
        $hash = md5($url);
        
        if (!isset($submissions[$hash])) {
            $submissions[$hash] = [
                'url' => $url,
                'timestamp' => time(),
                'normal' => false,
                'realtime' => false,
                'count' => 0
            ];
        }

        $submissions[$hash]['count']++;
        if ($normal_success) {
            $submissions[$hash]['normal'] = true;
        }
        if ($realtime_success) {
            $submissions[$hash]['realtime'] = true;
        }

        update_option('zhanzhangb_baidu_submissions', 
            array_slice($submissions, -self::MAX_RECORDS, self::MAX_RECORDS, true)
        );
    }

    public function is_recently_submitted($url) {
        $submissions = get_option('zhanzhangb_baidu_submissions', []);
        $hash = md5($url);
        
        return isset($submissions[$hash]) && 
               (time() - $submissions[$hash]['timestamp']) < 86400 &&
               $submissions[$hash]['count'] > 0;
    }

    public function update_submit_count($success_count) {
        $current = (int)get_option('zhanzhangb_baidu_submit_number', 0);
        update_option('zhanzhangb_baidu_submit_number', $current + $success_count);
    }
}