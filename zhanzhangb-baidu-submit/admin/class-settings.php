<?php
class Zhanzhangb_Baidu_Settings {
    private $logger;
    private $utils;

    public function __construct($logger, $utils) {
        $this->logger = $logger;
        $this->utils = $utils;
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'init_settings']);
    }

    public function add_menu() {
        add_options_page(
            '站长帮 - 自动提交百度收录设置',
            '站长帮 - 自动提交百度收录设置',
            'manage_options',
            'zhanzhangb_baidu_submit',
            [$this, 'render_settings_page']
        );
    }

    public function init_settings() {
        register_setting('zhanzhangb_baidu_settings', 'zhanzhangb_baidu_token', 'sanitize_text_field');
        register_setting('zhanzhangb_baidu_settings', 'zhanzhangb_baidu_realtime_token', 'sanitize_text_field');
        register_setting('zhanzhangb_baidu_settings', 'zhanzhangb_baidu_check', 'intval');
        register_setting('zhanzhangb_baidu_settings', 'zhanzhangb_baidu_custom_post_types', 'array_map_recursive');

        add_settings_section(
            'zhanzhangb_baidu_set',
            __('百度推送设置', 'zhanzhangb-baidu-submit'),
            [$this, 'render_settings_title'],
            'zhanzhangb_baidu_settings'
        );

        add_settings_field(
            'zhanzhangb_baidu_token',
            __('普通收录提交密钥（token）：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_token_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set',
            ['label_for' => 'zhanzhangb_baidu_token']
        );

        add_settings_field(
            'zhanzhangb_baidu_realtime_token',
            __('快速抓取提交密钥（token）：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_realtime_token_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set',
            ['label_for' => 'zhanzhangb_baidu_realtime_token']
        );

        add_settings_field(
            'zhanzhangb_baidu_check',
            __('允许24小时内重复提交：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_checkbox_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set'
        );

        add_settings_section(
            'zhanzhangb_baidu_custom_post_types',
            __('自定义文章类型设置', 'zhanzhangb-baidu-submit'),
            [$this, 'render_custom_post_types_title'],
            'zhanzhangb_baidu_settings'
        );

        add_settings_field(
            'zhanzhangb_baidu_custom_post_types',
            __('选择自定义文章类型：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_custom_post_types_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_custom_post_types'
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Sorry, you are not allowed to manage options for this site.'));
        }
        ?>
        <div class="zhanzhangb_baidu">
            <form method="post" action="options.php">
                <h1><?php esc_html_e('自动提交百度收录 - 设置', 'zhanzhangb-baidu-submit'); ?></h1>
                <hr>
                <?php settings_fields('zhanzhangb_baidu_settings'); ?>
                <?php do_settings_sections('zhanzhangb_baidu_settings'); ?>
                <?php submit_button(); ?>
                <hr>
                <div class="status-section">
                    <h3><?php esc_html_e('提交状态', 'zhanzhangb-baidu-submit'); ?></h3>
                    <p>
                        <?php if(get_option('zhanzhangb_baidu_token')) : ?>
                            <span style="color:#009933">✓</span>
                            <?php esc_html_e('普通收录提交功能：已开启', 'zhanzhangb-baidu-submit'); ?>
                        <?php else : ?>
                            <span style="color:#FF0000">✗</span>
                            <?php
                                echo sprintf(
                                    esc_html__('普通收录提交功能：未开启，请正确设置token。%s', 'zhanzhangb-baidu-submit'),
                                    '<a href="https://ziyuan.baidu.com/linksubmit/index" target="_blank" rel="noopener noreferrer">' . esc_html__('获取普通收录token', 'zhanzhangb-baidu-submit') . '</a>'
                                );
                            ?>
                        <?php endif; ?>
                        <br>
                    </p>
                    <p>
                        <?php if(get_option('zhanzhangb_baidu_realtime_token')) : ?>
                            <span style="color:#009933">✓</span>
                            <?php esc_html_e('快速抓取提交功能：已开启', 'zhanzhangb-baidu-submit'); ?>
                        <?php else : ?>
                            <span style="color:#FF0000">✗</span>
                            <?php
                                echo sprintf(
                                    esc_html__('快速抓取提交功能：未开启，请正确设置token，如空则不启用。%s', 'zhanzhangb-baidu-submit'),
                                    '<a href="https://ziyuan.baidu.com/fastcrawl/index" target="_blank" rel="noopener noreferrer">' . esc_html__('获取快速抓取token', 'zhanzhangb-baidu-submit') . '</a>'
                                );
                            ?>
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php esc_html_e('累计提交成功：', 'zhanzhangb-baidu-submit'); ?>
                        <?php echo absint(get_option('zhanzhangb_baidu_submit_number', 0)); ?>
                        <?php esc_html_e('条', 'zhanzhangb-baidu-submit'); ?>
                    </p>
                </div>
                <hr>
                <?php $this->logger->display_logs(); ?>
            </form>
        </div>
        <?php
    }

    public function render_settings_title() {
        $links = [
            [
                'text' => esc_html__('插件作者：', 'zhanzhangb-baidu-submit'),
                'url'  => 'https://www.zhanzhangb.cn/zhanzhangb-baidu-submit/',
                'label' => esc_html__('站长帮', 'zhanzhangb-baidu-submit'),
            ],
            [
                'text' => esc_html__('插件还不错？请给个', 'zhanzhangb-baidu-submit'),
                'url'  => 'https://wordpress.org/support/plugin/zhanzhangb-baidu-submit/reviews/#new-post',
                'label' => esc_html__('五星好评！', 'zhanzhangb-baidu-submit'),
            ],
            [
                'text' => esc_html__('★ 强烈推荐：', 'zhanzhangb-baidu-submit'),
                'url'  => 'https://www.zhanzhangb.com/plugins',
                'label' => esc_html__('精品插件下载', 'zhanzhangb-baidu-submit'),
            ],
            [
                'url'  => 'https://www.zhanzhangb.com/themes',
                'label' => esc_html__('精品主题下载', 'zhanzhangb-baidu-submit'),
            ],
            [
                'url'  => 'https://www.zhanzhangb.cn/tutorials',
                'label' => esc_html__('WordPress 教程', 'zhanzhangb-baidu-submit'),
            ]
        ];

        $html = '<p>' . implode(' | ', array_map(function($link) {
            return (isset($link['text']) ? $link['text'] : '') . 
                   '<a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['label']) . '</a>';
        }, $links)) . '</p>';
        echo wp_kses_post($html);
    }

    public function render_token_field() {
        $token = get_option('zhanzhangb_baidu_token');
        echo '<input id="zhanzhangb_baidu_token" maxlength="16" size="16" type="text" required pattern="[A-Za-z0-9]{16}" 
               name="zhanzhangb_baidu_token" value="' . esc_attr($token) . '">';
        if (empty($token)) echo '<span class="description">*必填</span>';
    }

    public function render_realtime_token_field() {
        $token = get_option('zhanzhangb_baidu_realtime_token');
        echo '<input id="zhanzhangb_baidu_realtime_token" maxlength="16" size="16" type="text" pattern="[A-Za-z0-9]{16}" 
               name="zhanzhangb_baidu_realtime_token" value="' . esc_attr($token) . '">';
    }

    public function render_checkbox_field() {
        $checked = get_option('zhanzhangb_baidu_check') ? 'checked' : '';
        echo '<input type="checkbox" name="zhanzhangb_baidu_check" value="1" ' . $checked . '>';
        echo '<span class="description">不建议勾选，默认同一个URL在24小时内仅提交一次。实践证明频繁提交不会加快收录。</span>';
    }

    public function render_custom_post_types_title() {
        echo '<p>' . esc_html__('请选择需要提交的自定义文章类型。默认情况下，只有标准的文章和页面会被提交。', 'zhanzhangb-baidu-submit') . '</p>';
    }

    public function render_custom_post_types_field() {
        $selected_types = get_option('zhanzhangb_baidu_custom_post_types', []);
        if (!is_array($selected_types)) {
            $selected_types = [];
        }
        $post_types = get_post_types(['public' => true, '_builtin' => false]);

        if (empty($post_types)) {
            echo '<p>' . esc_html__('没有可用的自定义文章类型。', 'zhanzhangb-baidu-submit') . '</p>';
            return;
        }

        echo '<ul>';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type, $selected_types) ? 'checked' : '';
            echo '<li>';
            echo '<label>';
            echo '<input type="checkbox" name="zhanzhangb_baidu_custom_post_types[]" value="' . esc_attr($post_type) . '" ' . $checked . '>';
            echo '&nbsp;' . esc_html(get_post_type_object($post_type)->labels->name);
            echo '</label>';
            echo '</li>';
        }
        echo '</ul>';
    }
}