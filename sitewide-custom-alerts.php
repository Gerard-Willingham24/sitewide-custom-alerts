<?php
/**
 * Plugin Name: Site Alert Banner
 * Description: Display a customizable alert banner at the top or bottom of your site
 * Version: 1.0.0
 * Author: Gerard Willingham
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

class Site_Alert_Banner {
    
    private $option_name = 'site_alert_settings';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        
        add_action('wp_head', array($this, 'display_alert'));
        add_action('wp_footer', array($this, 'display_alert'));
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    public function add_settings_page() {
        add_options_page(
            'Site Alert Settings',
            'Site Alert',
            'manage_options',
            'site-alert-banner',
            array($this, 'render_settings_page')
        );
    }
    
    public function register_settings() {
        register_setting(
            'site_alert_settings_group',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
        
        add_settings_section(
            'site_alert_main_section',
            'Alert Configuration',
            array($this, 'section_callback'),
            'site-alert-banner'
        );
        
        add_settings_field(
            'alert_enabled',
            'Enable Alert',
            array($this, 'alert_enabled_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            'alert_type',
            'Alert Type',
            array($this, 'alert_type_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            'alert_content',
            'Alert Message',
            array($this, 'alert_content_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            'alert_position',
            'Alert Position',
            array($this, 'alert_position_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            // amazonq-ignore-next-line
            'alert_dismissible',
            'Dismissible',
            array($this, 'alert_dismissible_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            'alert_width',
            'Alert Width',
            array($this, 'alert_width_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            'alert_expiration',
            'Expiration Date',
            array($this, 'alert_expiration_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
        
        add_settings_field(
            'alert_fixed',
            'Fixed Position',
            array($this, 'alert_fixed_callback'),
            'site-alert-banner',
            'site_alert_main_section'
        );
    }
    
    public function sanitize_settings($input) {
        if (!check_admin_referer('site_alert_settings_nonce', 'site_alert_nonce')) {
            wp_die('Security check failed');
        }
        
        $sanitized = array();
        
        $sanitized['enabled'] = isset($input['enabled']) ? 1 : 0;
        $sanitized['content'] = wp_kses_post($input['content']);
        
        $allowed_types = array('info', 'success', 'warning', 'error');
        $sanitized['type'] = in_array($input['type'], $allowed_types) ? $input['type'] : 'info';
        
        $allowed_positions = array('top', 'bottom');
        $sanitized['position'] = in_array($input['position'], $allowed_positions) ? $input['position'] : 'top';
        
        $sanitized['dismissible'] = isset($input['dismissible']) ? 1 : 0;
        
        $allowed_widths = array('full', 'container');
        $sanitized['width'] = in_array($input['width'], $allowed_widths) ? $input['width'] : 'full';
        
        $sanitized['content_hash'] = wp_hash($sanitized['content']);
        
        $sanitized['expiration'] = !empty($input['expiration']) ? sanitize_text_field($input['expiration']) : '';
        $sanitized['expiration_time'] = !empty($input['expiration_time']) ? sanitize_text_field($input['expiration_time']) : '23:59';
        
        $sanitized['fixed'] = isset($input['fixed']) ? 1 : 0;
        
        return $sanitized;
    }
    
    public function section_callback() {
        echo '<p>Configure the site-wide alert banner that appears to all visitors.</p>';
    }
    
    public function alert_enabled_callback() {
        $options = get_option($this->option_name);
        $checked = isset($options['enabled']) && $options['enabled'] ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="<?php echo $this->option_name; ?>[enabled]" value="1" <?php echo $checked; ?>>
            Display alert on site
        </label>
        <?php
    }
    
    public function alert_type_callback() {
        $options = get_option($this->option_name);
        $type = isset($options['type']) ? $options['type'] : 'info';
        
        $types = array(
            'info' => 'Info (Blue)',
            'success' => 'Success (Green)',
            'warning' => 'Warning (Orange)',
            'error' => 'Error (Red)'
        );
        ?>
        <select name="<?php echo $this->option_name; ?>[type]">
            <?php foreach ($types as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">Choose the alert type to set the color scheme.</p>
        <?php
    }
    
    public function alert_content_callback() {
        $options = get_option($this->option_name);
        $content = isset($options['content']) ? $options['content'] : '';
        
        $editor_settings = array(
            'textarea_name' => $this->option_name . '[content]',
            'media_buttons' => true,
            'textarea_rows' => 10,
            'teeny' => false,
            'tinymce' => array(
                'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,|,bullist,numlist,|,link,unlink,|,undo,redo',
                'toolbar2' => ''
            )
        );
        
        wp_editor($content, 'site_alert_content', $editor_settings);
        ?>
        <p class="description">Create your alert message using the visual editor. You can add links, formatting, and more.</p>
        <?php
    }
    
    public function alert_position_callback() {
        $options = get_option($this->option_name);
        $position = isset($options['position']) ? $options['position'] : 'top';
        ?>
        <fieldset>
            <label>
                <input type="radio" name="<?php echo $this->option_name; ?>[position]" value="top" <?php checked($position, 'top'); ?>>
                Top of page
            </label><br>
            <label>
                <input type="radio" name="<?php echo $this->option_name; ?>[position]" value="bottom" <?php checked($position, 'bottom'); ?>>
                Bottom of page
            </label>
        </fieldset>
        <?php
    }
    
    public function alert_dismissible_callback() {
        $options = get_option($this->option_name);
        $checked = isset($options['dismissible']) && $options['dismissible'] ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="<?php echo $this->option_name; ?>[dismissible]" value="1" <?php echo $checked; ?>>
            Allow visitors to close the alert
        </label>
        <p class="description">If enabled, visitors can dismiss the alert. It will not show again until they clear their browser data or you update the alert content.</p>
        <?php
    }
    
    public function alert_width_callback() {
        $options = get_option($this->option_name);
        $width = isset($options['width']) ? $options['width'] : 'full';
        ?>
        <fieldset>
            <label>
                <input type="radio" name="<?php echo $this->option_name; ?>[width]" value="full" <?php checked($width, 'full'); ?>>
                Full width (edge to edge)
            </label><br>
            <label>
                <input type="radio" name="<?php echo $this->option_name; ?>[width]" value="container" <?php checked($width, 'container'); ?>>
                Container width (max-width: 1200px, centered)
            </label>
        </fieldset>
        <p class="description">Choose whether the alert spans the full page width or is contained within a maximum width.</p>
        <?php
    }
    
    public function alert_expiration_callback() {
        $options = get_option($this->option_name);
        $expiration = isset($options['expiration']) ? $options['expiration'] : '';
        $expiration_time = isset($options['expiration_time']) ? $options['expiration_time'] : '23:59';
        $timezone = wp_timezone_string();
        ?>
        <input type="date" name="<?php echo $this->option_name; ?>[expiration]" value="<?php echo esc_attr($expiration); ?>">
        <input type="time" name="<?php echo $this->option_name; ?>[expiration_time]" value="<?php echo esc_attr($expiration_time); ?>">
        <p class="description">Optional: Set expiration date and time (<?php echo esc_html($timezone); ?>). The alert will automatically hide after this time. Leave blank for no expiration.</p>
        <?php
    }
    
    public function alert_fixed_callback() {
        $options = get_option($this->option_name);
        $checked = isset($options['fixed']) && $options['fixed'] ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="<?php echo $this->option_name; ?>[fixed]" value="1" <?php echo $checked; ?>>
            Use fixed positioning (alert stays visible when scrolling)
        </label>
        <p class="description">When enabled, the alert will remain fixed on screen. When disabled, the alert will scroll with the page content.</p>
        <?php
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'site_alert_messages',
                'site_alert_message',
                'Settings Saved',
                'updated'
            );
        }

        settings_errors('site_alert_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('site_alert_settings_group');
                wp_nonce_field('site_alert_settings_nonce', 'site_alert_nonce');
                do_settings_sections('site-alert-banner');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }
    
    public function enqueue_frontend_assets() {
        $options = get_option($this->option_name);
        
        if (!isset($options['enabled']) || !$options['enabled'] || empty($options['content'])) {
            return;
        }
        
        wp_enqueue_style(
            'site-alert-frontend',
            plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'site-alert-frontend',
            plugin_dir_url(__FILE__) . 'assets/js/frontend.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_site-alert-banner') {
            return;
        }
        
        wp_enqueue_style(
            'site-alert-admin',
            plugin_dir_url(__FILE__) . 'assets/css/admin.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'site-alert-admin',
            plugin_dir_url(__FILE__) . 'assets/js/admin.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
    
    public function display_alert() {
        $options = get_option($this->option_name);
        
        if (!isset($options['enabled']) || !$options['enabled'] || empty($options['content'])) {
            return;
        }
        
        // Check if alert has expired
        if (!empty($options['expiration'])) {
            $expiration_time = isset($options['expiration_time']) ? $options['expiration_time'] : '23:59';
            $expiration_datetime = $options['expiration'] . ' ' . $expiration_time;
            $expiration_timestamp = wp_date('U', strtotime($expiration_datetime), wp_timezone());
            
            if ($expiration_timestamp < current_time('timestamp')) {
                return;
            }
        }
        
        $position = isset($options['position']) ? $options['position'] : 'top';
        $current_hook = current_filter();
        
        // Only display on correct hook based on position
        if (($position === 'top' && $current_hook !== 'wp_head') || 
            ($position === 'bottom' && $current_hook !== 'wp_footer')) {
            return;
        }
        
        $type = isset($options['type']) ? $options['type'] : 'info';
        $dismissible = isset($options['dismissible']) && $options['dismissible'];
        $width = isset($options['width']) ? $options['width'] : 'full';
        $fixed = isset($options['fixed']) && $options['fixed'];
        $content_hash = isset($options['content_hash']) ? $options['content_hash'] : '';
        
        $classes = array(
            'site-alert-banner',
            'alert-' . $type,
            'alert-' . $position,
            'alert-' . $width
        );
        
        if ($fixed) {
            $classes[] = 'alert-fixed';
        }
        
        if ($dismissible) {
            $classes[] = 'alert-dismissible';
        }
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" 
             data-alert-hash="<?php echo esc_attr($content_hash); ?>"
             style="display: none;">
            <div class="alert-content">
                <p><?php echo wp_kses_post($options['content']); ?></p>
                <?php if ($dismissible) : ?>
                    <button class="alert-close" aria-label="Close alert">&times;</button>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

new Site_Alert_Banner();
?>