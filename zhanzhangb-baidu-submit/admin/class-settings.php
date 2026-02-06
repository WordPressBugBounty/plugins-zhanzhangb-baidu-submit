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
			__('Zhanzhangb Indexing Submission for Baidu Settings', 'zhanzhangb-baidu-submit'),
			__('Zhanzhangb Indexing Submission for Baidu', 'zhanzhangb-baidu-submit'),
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
            __('Baidu Submission Settings', 'zhanzhangb-baidu-submit'),
            [$this, 'render_settings_title'],
            'zhanzhangb_baidu_settings'
        );

        add_settings_field(
            'zhanzhangb_baidu_token',
            __('Regular Submission Token (token):', 'zhanzhangb-baidu-submit'),
            [$this, 'render_token_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set',
            ['label_for' => 'zhanzhangb_baidu_token']
        );

        add_settings_field(
            'zhanzhangb_baidu_realtime_token',
            __('Fast Crawl Submission Token (token):', 'zhanzhangb-baidu-submit'),
            [$this, 'render_realtime_token_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set',
            ['label_for' => 'zhanzhangb_baidu_realtime_token']
        );
		
        add_settings_field(
            'zhanzhangb_baidu_custom_post_types',
            __('Select Custom Post Types to Submit:', 'zhanzhangb-baidu-submit'),
            [$this, 'render_custom_post_types_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set'
        );

        add_settings_field(
            'zhanzhangb_baidu_check',
            __('Allow duplicate submissions within the same day:', 'zhanzhangb-baidu-submit'),
            [$this, 'render_checkbox_field'],
            'zhanzhangb_baidu_settings',
            'zhanzhangb_baidu_set'
        );

        add_settings_field(
            'zhanzhangb_baidu_set_time',
            __('Output Time Factor Structured Data:', 'zhanzhangb-baidu-submit'),
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
            <h1><?php esc_html_e('Auto Submit to Baidu - Settings', 'zhanzhangb-baidu-submit'); ?></h1>
            <hr>
            <?php settings_fields('zhanzhangb_baidu_settings'); ?>
            <?php do_settings_sections('zhanzhangb_baidu_settings'); ?>
            <?php submit_button(); ?>
            
            <hr>
            
            <div class="status-section">
                <h3><?php esc_html_e('Submission Status', 'zhanzhangb-baidu-submit'); ?></h3>
                
                <p>
                    <?php if(get_option('zhanzhangb_baidu_token')) : ?>
                        <span style="color:#009933">✓</span>
                        <?php esc_html_e('Regular submission function: Enabled', 'zhanzhangb-baidu-submit'); ?>
                    <?php else : ?>
                        <span style="color:#FF0000">✗</span>
                        <?php
                            echo sprintf(
                                esc_html__('Regular submission function: Disabled, please set token correctly. %s', 'zhanzhangb-baidu-submit'),
                                '<a href="https://ziyuan.baidu.com/linksubmit/index" target="_blank" rel="noopener noreferrer">' . esc_html__('Get regular submission token', 'zhanzhangb-baidu-submit') . '</a>'
                            );
                        ?>
                    <?php endif; ?>
                </p>

                <p>
                    <?php if(get_option('zhanzhangb_baidu_realtime_token')) : ?>
                        <span style="color:#009933">✓</span>
                        <?php esc_html_e('Fast crawl submission function: Enabled', 'zhanzhangb-baidu-submit'); ?>
                    <?php else : ?>
                        <span style="color:#FF0000">✗</span>
                        <?php
                            echo sprintf(
                                esc_html__('Fast crawl submission function: Disabled, please set token correctly, leave empty to disable. %s', 'zhanzhangb-baidu-submit'),
                                '<a href="https://ziyuan.baidu.com/fastcrawl/index" target="_blank" rel="noopener noreferrer">' . esc_html__('Get fast crawl token', 'zhanzhangb-baidu-submit') . '</a>'
                            );
                        ?>
                    <?php endif; ?>
                </p>
                <p>
                    <span style="color:#009933">&#9745;</span>
                    <?php esc_html_e('Total successful submissions:', 'zhanzhangb-baidu-submit'); ?>
                    <?php echo absint(get_option('zhanzhangb_baidu_submit_number', 0)); ?>
                    <?php esc_html_e('items', 'zhanzhangb-baidu-submit'); ?>
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
				'text' => esc_html__('Plugin author:', 'zhanzhangb-baidu-submit'),
				'url'  => 'https://www.zhanzhangb.cn/zhanzhangb-baidu-submit/',
				'label' => esc_html__('Zhanzhangb', 'zhanzhangb-baidu-submit'),
			],
			[
				'text' => esc_html__('Like this plugin? Please give it a', 'zhanzhangb-baidu-submit'),
				'url'  => 'https://wordpress.org/support/plugin/zhanzhangb-baidu-submit/reviews/#new-post',
				'label' => esc_html__('5-star rating!', 'zhanzhangb-baidu-submit'),
			],
			[
				'text' => esc_html__('★ Highly recommended:', 'zhanzhangb-baidu-submit'),
				'url'  => 'https://zy.zhanzhangb.cn/plugins/',
				'label' => esc_html__('Premium Plugins Download', 'zhanzhangb-baidu-submit'),
			],
			[
				'url'  => 'https://zy.zhanzhangb.cn/themes/',
				'label' => esc_html__('Premium Themes Download', 'zhanzhangb-baidu-submit'),
			],
			[
				'url'  => 'https://www.zhanzhangb.cn/tutorials/',
				'label' => esc_html__('WordPress Tutorials', 'zhanzhangb-baidu-submit'),
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
        if (empty($token)) echo '<span class="description">*Required</span>';
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
			echo '<p>' . esc_html__('No custom post types available, default submission includes WordPress standard posts and pages.', 'zhanzhangb-baidu-submit') . '</p>';
			return;
		}

		ob_start();
		?>
		<div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
		    <span class="dashicons dashicons-editor-help" style="cursor: pointer;" title="<?php echo esc_attr(esc_html__('By default, only WordPress standard posts and pages will be submitted.', 'zhanzhangb-baidu-submit')); ?>"></span>
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
        $tooltip = esc_html__('By default, the same URL can only be successfully submitted once within 1 day (failed submissions can be retried). Practice shows that frequent submissions do not speed up indexing.', 'zhanzhangb-baidu-submit');
        echo '<span class="dashicons dashicons-editor-help" style="cursor: pointer;" title="' . esc_attr($tooltip) . '"></span>';
        echo '&nbsp;&nbsp;<input type="checkbox" name="zhanzhangb_baidu_check" value="1" ' . $checked . '>';
        echo '<span class="description">' . esc_html__('Not recommended to check.', 'zhanzhangb-baidu-submit') . '</span>';
    }
	
    public function render_time_factor_field() {
        $set_time = get_option('zhanzhangb_baidu_set_time', []);
        if (!is_array($set_time)) {
            $set_time = [];
        }
    
        $tooltip = esc_html__('For non-logged-in users, time factor data (Baidu landing page structured data or Toutiao search supported meta tags) will be output in the page header <head> to comply with search landing page time factor specifications.', 'zhanzhangb-baidu-submit');

        echo '<div style="display: flex; align-items: center; gap: 10px;">';
        echo '<span class="dashicons dashicons-editor-help" style="cursor: pointer;" title="' . esc_attr($tooltip) . '"></span>';
        echo '<label>';
        echo '<input type="checkbox" name="zhanzhangb_baidu_set_time[]" value="baidu" ' . checked(in_array('baidu', $set_time), true, false) . '>';
        echo '&nbsp;' . sprintf(
            esc_html__('Baidu Time Factor %s', 'zhanzhangb-baidu-submit'),
            sprintf(
                '<a href="%s" target="_blank">（%s）</a>',
                'https://ziyuan.baidu.com/college/articleinfo?id=2210',
                esc_html__('Structured Data', 'zhanzhangb-baidu-submit')
            )
        );
        echo '</label>';
        echo '<label>';
        echo '<input type="checkbox" name="zhanzhangb_baidu_set_time[]" value="toutiao" ' . checked(in_array('toutiao', $set_time), true, false) . '>';
        echo '&nbsp;' . esc_html__('Toutiao Search Time Factor (meta)', 'zhanzhangb-baidu-submit');
        echo '</label>';
        echo '</div>';
    }
}