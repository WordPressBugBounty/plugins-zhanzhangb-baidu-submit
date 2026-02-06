<?php
class Zhanzhangb_Baidu_Logger {
    const MAX_LOGS = 30;
    const LOG_FILENAME = 'baidu-submit-logfile.log';

    private function get_log_file_path() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . DIRECTORY_SEPARATOR . self::LOG_FILENAME;
    }

    public function log($message, $type = 'info', $url = '') {
        $log_entry = sprintf(
            "[%s] [%s]%s %s",
            current_time('mysql'), 
            ($type === 'success') ? __("success", 'zhanzhangb-baidu-submit') : (($type === 'error') ? __("error", 'zhanzhangb-baidu-submit') : __("unknown", 'zhanzhangb-baidu-submit')),
            $url ? " [URL: {$url}]" : "",
            $message
        );

        try {
            $logs = $this->get_logs();
            
            if (count($logs) >= self::MAX_LOGS * 1.5) {
                $logs = array_slice($logs, -self::MAX_LOGS);
            }
            
            array_push($logs, $log_entry);
            $this->save_logs(array_slice($logs, -self::MAX_LOGS));
        } catch (Exception $e) {
            error_log(
                __('Baidu submission log recording failed:', 'zhanzhangb-baidu-submit') . ' ' . $e->getMessage()
            );
        }
    }

    public function display_logs() {
        echo '<div class="zhanzhangb-log-container">';
        echo '<h3>' . esc_html__('Submission log (latest 30):', 'zhanzhangb-baidu-submit') . '</h3>';
        
        $logs = $this->get_logs();
        if (empty($logs)) {
            echo '<p>' . esc_html__('No submission log yet', 'zhanzhangb-baidu-submit') . '</p>';
            return;
        }

        echo '<pre style="max-height:550px; overflow:auto; white-space:pre-wrap; word-wrap:break-word; background-color:#2c3338; color:#E0E0E0;padding:20px;">';
        foreach (array_reverse($logs) as $log) {
            echo esc_html($log) . "\n";
        }
        echo '</pre></div>';
    }

    private function get_logs() {
        $log_file_path = $this->get_log_file_path();
        if (!file_exists($log_file_path)) {
            return [];
        }
        
        try {
            $content = file_get_contents($log_file_path);
            return array_filter(
                explode(PHP_EOL, $content),
                function($line) {
                    return !empty(trim($line));
                }
            );
        } catch (Exception $e) {
            return [];
        }
    }

    private function save_logs($logs) {
        try {
            $log_file_path = $this->get_log_file_path();
            $log_dir = dirname($log_file_path);

            if (!is_dir($log_dir)) {
                wp_mkdir_p($log_dir);
            }

            $escaped_logs = array_map('esc_html', $logs);
            file_put_contents(
                $log_file_path,
                implode(PHP_EOL, $escaped_logs) . PHP_EOL,
                LOCK_EX
            );
        } catch (Exception $e) {
            error_log(
                __('Log file write failed:', 'zhanzhangb-baidu-submit') . ' ' . $e->getMessage()
            );
        }
    }
}