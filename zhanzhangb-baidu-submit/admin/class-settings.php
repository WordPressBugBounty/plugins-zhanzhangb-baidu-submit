<?php
// admin/class-settings.php
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
        register_setting('zhanzhangb_baidu_settings', 'zhanzhangb_baidu_set_time', 'array_map_recursive');

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
            'zhanzhangb_baidu_custom_post_types',
            __('选择需提交的自定义文章类型：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_custom_post_types_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set'
        );

        add_settings_field(
            'zhanzhangb_baidu_check',
            __('允许24小时内重复提交：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_checkbox_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set'
        );

        add_settings_field(
            'zhanzhangb_baidu_set_time',
            __('输出时间因子结构化数据：', 'zhanzhangb-baidu-submit'),
            [$this, 'render_time_factor_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set'
        );
    }

public function render_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Sorry, you are not allowed to manage options for this site.'));
    }
    ?>
    <style>
    #setting-error-settings_updated {max-width: 860px;}
    .zhanzhangb_baidu {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        max-width: 950px;
        margin-top: 10px;
        margin-left: 5px;
        width: calc(100% - 40px);
    }

        .zhanzhangb_baidu h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .zhanzhangb_baidu hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 20px 0;
        }

        .zhanzhangb_baidu .form-table {
            width: 100%;
            border-collapse: collapse;
        }

        .zhanzhangb_baidu .form-table th {
            text-align: left;
            padding: 10px;
            background-color: #f1f1f1;
            border-bottom: 1px solid #ddd;
            width: 250px;
        }

        .zhanzhangb_baidu .form-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .zhanzhangb_baidu input[type="text"],
        .zhanzhangb_baidu input[type="checkbox"] {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .zhanzhangb_baidu .button-primary {
            background-color: #0073aa;
            border-color: #0073aa;
            color: #fff;
            text-shadow: none;
            box-shadow: none;
            transition: background-color 0.3s ease;
        }

        .zhanzhangb_baidu .button-primary:hover {
            background-color: #005177;
            border-color: #005177;
        }

        .zhanzhangb_baidu .status-section {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .zhanzhangb_baidu .status-section h3 {
            margin-top: 0;
            font-size: 18px;
            color: #0073aa;
        }

        .zhanzhangb_baidu .status-section p {
            margin: 5px 0;
            font-size: 14px;
        }

        .zhanzhangb_baidu .dashicons {
            vertical-align: middle;
            margin-right: 5px;
        }

        .zhanzhangb_baidu .dashicons-editor-help {
            color: #0073aa;
            cursor: pointer;
        }

        .zhanzhangb_baidu .dashicons-editor-help:hover {
            color: #005177;
        }
    </style>
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
                                '<a href="https://ziyuan.baidu.com/linksubmit/index" target="_blank" rel="noopener noreferrer">' . esc_html__('获取普通收录 token', 'zhanzhangb-baidu-submit') . '</a>'
                            );
                        ?>
                    <?php endif; ?>
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
                                '<a href="https://ziyuan.baidu.com/fastcrawl/index" target="_blank" rel="noopener noreferrer">' . esc_html__('获取快速抓取 token', 'zhanzhangb-baidu-submit') . '</a>'
                            );
                        ?>
                    <?php endif; ?>
                </p>
                <p>
                    <span style="color:#009933">☑</span>
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

    public function render_custom_post_types_field() {
        $selected_types = get_option('zhanzhangb_baidu_custom_post_types', []);
        if (!is_array($selected_types)) {
            $selected_types = [];
        }
        
        $post_types = get_post_types(['public' => true, '_builtin' => false]);
        
        if (empty($post_types)) {
            echo '<p>' . esc_html__('无可用的自定义文章类型，默认提交 WordPress 标准的文章(post)和页面(page)。', 'zhanzhangb-baidu-submit') . '</p>';
            return;
        }
        $tooltip = esc_html__('默认情况下只有 WordPress 标准的文章(post)和页面(page)会被提交。', 'zhanzhangb-baidu-submit');

        ob_start();
        ?>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <span class="dashicons dashicons-editor-help" style="cursor: pointer;" title="<?php echo $tooltip ?>"></span>
            <?php foreach ($post_types as $post_type) : 
                $checked = in_array($post_type, $selected_types) ? 'checked' : '';
                $label = esc_html(get_post_type_object($post_type)->labels->name);
            ?>
                <label style="flex: 0 0 auto; display: flex; align-items: center; gap: 5px; line-height: 1.3;">
                    <input type="checkbox" name="zhanzhangb_baidu_custom_post_types[]" 
                           value="<?php echo esc_attr($post_type); ?>" 
                           <?php echo $checked; ?> 
                           style="margin: 0;">
                    <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
        echo ob_get_clean();
    }
    
    public function render_checkbox_field() {
        $checked = get_option('zhanzhangb_baidu_check') ? 'checked' : '';
        $tooltip = esc_html__('默认同一个URL在24小时内仅能成功提交一次（失败可重试），实践证明频繁提交不会加快收录。', 'zhanzhangb-baidu-submit');
        echo '<span class="dashicons dashicons-editor-help" style="cursor: pointer;" title="' . $tooltip . '"></span>';
        echo '&nbsp;&nbsp;<input type="checkbox" name="zhanzhangb_baidu_check" value="1" ' . $checked . '>';
        echo '<span class="description">不建议勾选。</span>';
    }
    
    public function render_time_factor_field() {
        $set_time = get_option('zhanzhangb_baidu_set_time', []);
        if (!is_array($set_time)) {
            $set_time = [];
        }
    
        $tooltip = esc_html__('非登录状态下，将在页面头部 <head> 输出时间因子数据（百度落地页结构化数据或头条搜索支持的 meta 标签），以符合搜索落地页时间因子规范。', 'zhanzhangb-baidu-submit');

        echo '<div style="display: flex; align-items: center; gap: 10px;">';
        echo '<span class="dashicons dashicons-editor-help" style="cursor: pointer;" title="' . $tooltip . '"></span>';
        echo '<label>';
        echo '<input type="checkbox" name="zhanzhangb_baidu_set_time[]" value="baidu" ' . checked(in_array('baidu', $set_time), true, false) . '>';
        echo '&nbsp;' . sprintf(
            esc_html__('百度时间因子 %s', 'zhanzhangb-baidu-submit'),
            sprintf(
                '<a href="%s" target="_blank">（%s）</a>',
                'https://ziyuan.baidu.com/college/articleinfo?id=2210',
                esc_html__('结构化数据', 'zhanzhangb-baidu-submit')
            )
        );
        echo '</label>';
        echo '<label>';
        echo '<input type="checkbox" name="zhanzhangb_baidu_set_time[]" value="toutiao" ' . checked(in_array('toutiao', $set_time), true, false) . '>';
        echo '&nbsp;' . esc_html__('头条搜索时间因子（meta）', 'zhanzhangb-baidu-submit');
        echo '</label>';
        echo '</div>';
    }
}