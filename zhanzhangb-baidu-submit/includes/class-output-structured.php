<?php
class Zhanzhangb_Baidu_Structured {
    public static function output($set_time_options) {
        if (!is_singular()) return;

        global $post;
        $post_id = $post->ID;
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);

        $wp_timezone = wp_timezone();
        $pub_date = new DateTime(get_the_date('Y-m-d H:i:s', $post_id), $wp_timezone);
        $update_date = new DateTime(get_the_modified_date('Y-m-d H:i:s', $post_id), $wp_timezone);

        if (in_array('baidu', $set_time_options) && !is_front_page() && !is_home()) {
            self::output_baidu($title, $permalink, $post_id, $pub_date, $update_date);
        }

        if (in_array('toutiao', $set_time_options)) {
            self::output_bytedance($pub_date, $update_date);
        }
    }

    private static function output_baidu($title, $permalink, $post_id, $pub_date, $update_date) {
        $excerpt = self::get_best_excerpt($post_id);

        $image_url = '';
        if (has_post_thumbnail($post_id)) {
            $image_url = wp_get_attachment_url(get_post_thumbnail_id($post_id));
        }

        $data = [
            "@context" => "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
            "@id" => $permalink,
            "title" => $title,
            "images" => $image_url ? [$image_url] : [],
            "description" => $excerpt,
            "pubDate" => $pub_date->format('Y-m-d\TH:i:s'),
            "upDate" => $update_date->format('Y-m-d\TH:i:s')
        ];

        echo '<script type="application/ld+json">' 
             . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
             . '</script>';
    }

    private static function output_bytedance($pub_date, $update_date) {
        echo '<meta property="bytedance:published_time" content="' 
             . esc_attr($pub_date->format('Y-m-d\TH:i:sP')) 
             . '" />';
        echo '<meta property="bytedance:updated_time" content="' 
             . esc_attr($update_date->format('Y-m-d\TH:i:sP')) 
             . '" />';
    }

    private static function get_best_excerpt($post_id) {
        $excerpt = '';
        
        if (defined('WPSEO_VERSION')) {
            $excerpt = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        }
        
        if (empty($excerpt) && class_exists('RankMath')) {
            $excerpt = get_post_meta($post_id, 'rank_math_description', true);
        }
        
        if (empty($excerpt)) {
            $excerpt = get_the_excerpt($post_id);
        }
        
        return mb_substr(strip_tags($excerpt), 0, 210);
    }
}
