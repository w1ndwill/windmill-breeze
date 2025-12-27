<?php

function windmill_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'windmill-breeze' ),
    ) );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
}
add_action( 'after_setup_theme', 'windmill_setup' );

function my_custom_theme_scripts() {
    // Enqueue Styles
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Meie+Script&family=Noto+Sans+SC:wght@300;400;500;700&display=swap', array(), null);
    
    // Use file modification time as version to bust cache
    $style_ver = filemtime(get_stylesheet_directory() . '/style.css');
    wp_enqueue_style('main-style', get_stylesheet_uri(), array(), $style_ver);

    // Enqueue Comments CSS
    wp_enqueue_style('comments-style', get_template_directory_uri() . '/assets/css/comments.css', array(), $style_ver);

    // Enqueue Mobile CSS
    wp_enqueue_style('mobile-style', get_template_directory_uri() . '/assets/css/mobile.css', array(), $style_ver);

    // Enqueue Hero CSS
    wp_enqueue_style('hero-style', get_template_directory_uri() . '/assets/css/hero.css', array(), $style_ver);

    // Enqueue Scripts
    $script_ver = filemtime(get_template_directory() . '/assets/js/script.js');
    wp_enqueue_script('main-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), $script_ver, true);

    // Localize script for AJAX
    wp_localize_script('main-script', 'windmill_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('windmill_auth_nonce'),
        'is_logged_in' => is_user_logged_in()
    ));
}
add_action('wp_enqueue_scripts', 'my_custom_theme_scripts');



// --- AJAX Login & Register Handlers ---

function windmill_ajax_login() {
    check_ajax_referer('windmill_auth_nonce', 'nonce');

    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon($info, false);

    if (is_wp_error($user_signon)) {
        wp_send_json_error(array('message' => '用户名或密码错误'));
    } else {
        wp_send_json_success(array('message' => '登录成功'));
    }
}
add_action('wp_ajax_nopriv_windmill_login', 'windmill_ajax_login');

function windmill_ajax_register() {
    check_ajax_referer('windmill_auth_nonce', 'nonce');

    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    if (username_exists($username) || email_exists($email)) {
        wp_send_json_error(array('message' => '用户名或邮箱已存在'));
        return;
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => $user_id->get_error_message()));
    } else {
        // Auto login after register
        $info = array();
        $info['user_login'] = $username;
        $info['user_password'] = $password;
        $info['remember'] = true;
        wp_signon($info, false);
        wp_send_json_success(array('message' => '注册成功'));
    }
}
add_action('wp_ajax_nopriv_windmill_register', 'windmill_ajax_register');

// --- User Profile Management ---

// 1. Custom Avatar Support
function windmill_get_avatar_url($url, $id_or_email, $args) {
    $user_id = false;
    
    if (is_numeric($id_or_email)) {
        $user_id = $id_or_email;
    } elseif (is_object($id_or_email)) {
        // Handle WP_Comment object
        if (!empty($id_or_email->user_id)) {
            $user_id = $id_or_email->user_id;
        } 
        // Handle WP_User object
        elseif (!empty($id_or_email->ID)) {
            $user_id = $id_or_email->ID;
        }
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if ($user) $user_id = $user->ID;
    }

    if ($user_id) {
        $custom_avatar = get_user_meta($user_id, 'windmill_custom_avatar', true);
        if ($custom_avatar) {
            return $custom_avatar;
        }
    }
    return $url;
}
add_filter('get_avatar_url', 'windmill_get_avatar_url', 10, 3);

// 2. AJAX Get Profile
function windmill_ajax_get_profile() {
    check_ajax_referer('windmill_auth_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error(array('message' => '未登录'));

    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    
    $data = array(
        'display_name' => $user->display_name,
        'email' => $user->user_email,
        'url' => $user->user_url,
        'hobbies' => get_user_meta($user_id, 'windmill_user_hobbies', true),
        'friend_links' => get_user_meta($user_id, 'windmill_user_friend_links', true),
        'description' => get_user_meta($user_id, 'description', true),
        'avatar' => get_avatar_url($user_id, array('size' => 200))
    );
    
    wp_send_json_success($data);
}
add_action('wp_ajax_windmill_get_profile', 'windmill_ajax_get_profile');

// 3. AJAX Update Profile
function windmill_ajax_update_profile() {
    check_ajax_referer('windmill_auth_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error(array('message' => '未登录'));

    $user_id = get_current_user_id();
    
    // Update Text Fields
    if (isset($_POST['display_name'])) {
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => sanitize_text_field($_POST['display_name']),
            'user_email' => sanitize_email($_POST['email']),
            'user_url' => esc_url_raw($_POST['url'])
        ));
        update_user_meta($user_id, 'description', sanitize_textarea_field($_POST['description']));
        update_user_meta($user_id, 'windmill_user_hobbies', sanitize_text_field($_POST['hobbies']));
        update_user_meta($user_id, 'windmill_user_friend_links', sanitize_textarea_field($_POST['friend_links']));
    }

    // Handle Avatar Upload
    if (!empty($_FILES['avatar']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('avatar', 0);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => '图片上传失败: ' . $attachment_id->get_error_message()));
        } else {
            $avatar_url = wp_get_attachment_url($attachment_id);
            update_user_meta($user_id, 'windmill_custom_avatar', $avatar_url);
        }
    }

    wp_send_json_success(array('message' => '资料更新成功'));
}
add_action('wp_ajax_windmill_update_profile', 'windmill_ajax_update_profile');

function my_custom_theme_customize_register($wp_customize) {
    // 1. Motto Section
    $wp_customize->add_section('motto_section', array(
        'title' => __('座右铭设置', 'my-custom-theme'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('motto_text', array(
        'default' => '种一棵树最好的时间是十年前，其次是现在。',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('motto_text', array(
        'label' => __('座右铭内容', 'my-custom-theme'),
        'section' => 'motto_section',
        'type' => 'textarea',
    ));

    // 2. Resume Section
    $wp_customize->add_section('resume_section', array(
        'title' => __('简历区域设置', 'my-custom-theme'),
        'priority' => 31,
    ));

    $wp_customize->add_setting('resume_intro', array(
        'default' => '拥有5年开发经验的前端工程师，热衷于创造优雅的用户体验。',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('resume_intro', array(
        'label' => __('个人简介', 'my-custom-theme'),
        'section' => 'resume_section',
        'type' => 'textarea',
    ));

    $wp_customize->add_setting('resume_file_url', array(
        'default' => '#',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'resume_file_url', array(
        'label' => __('上传简历 PDF', 'my-custom-theme'),
        'section' => 'resume_section',
        'settings' => 'resume_file_url',
    )));

    // 3. Portfolio Section (Simple 4 items)
    $wp_customize->add_section('portfolio_section', array(
        'title' => __('作品集设置', 'my-custom-theme'),
        'priority' => 32,
        'description' => '设置首页显示的4个小作品',
    ));

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("portfolio_{$i}_title", array('default' => "作品 {$i}", 'transport' => 'refresh'));
        $wp_customize->add_control("portfolio_{$i}_title", array('label' => "作品 {$i} 标题", 'section' => 'portfolio_section', 'type' => 'text'));

        $wp_customize->add_setting("portfolio_{$i}_desc", array('default' => "简短描述...", 'transport' => 'refresh'));
        $wp_customize->add_control("portfolio_{$i}_desc", array('label' => "作品 {$i} 描述", 'section' => 'portfolio_section', 'type' => 'text'));
        
        $wp_customize->add_setting("portfolio_{$i}_icon", array('default' => "🚀", 'transport' => 'refresh'));
        $wp_customize->add_control("portfolio_{$i}_icon", array('label' => "作品 {$i} 图标(Emoji)", 'section' => 'portfolio_section', 'type' => 'text'));
    }

    // 4. Social Links Section
    $wp_customize->add_section('social_section', array(
        'title' => __('社交链接设置', 'my-custom-theme'),
        'priority' => 33,
        'description' => '留空则不显示对应图标',
    ));

    $social_platforms = array(
        'github' => array('label' => 'GitHub', 'default' => 'https://github.com'),
        'bilibili' => array('label' => 'Bilibili', 'default' => ''),
        'zhihu' => array('label' => '知乎', 'default' => ''),
        'csdn' => array('label' => 'CSDN', 'default' => ''),
        'netease' => array('label' => '网易云音乐', 'default' => ''),
        'weibo' => array('label' => '微博', 'default' => ''),
    );

    foreach ($social_platforms as $id => $info) {
        $setting_id = 'social_' . $id;
        $wp_customize->add_setting($setting_id, array(
            'default' => $info['default'],
            'transport' => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control($setting_id, array(
            'label' => $info['label'],
            'section' => 'social_section',
            'type' => 'url',
        ));
    }
}
add_action('customize_register', 'my_custom_theme_customize_register');

// Add Title Tag Support
add_theme_support('title-tag');

// Disable WordPress Admin Bar for all users
add_filter('show_admin_bar', '__return_false');

// --- Custom Favicon (Emoji Support) ---

// 1. Add Customizer Setting for Favicon Emoji
function windmill_customize_favicon($wp_customize) {
    $wp_customize->add_section('favicon_section', array(
        'title' => __('浏览器标签图标', 'my-custom-theme'),
        'priority' => 20,
        'description' => '设置浏览器标签页显示的图标 (Emoji)',
    ));

    $wp_customize->add_setting('site_favicon_emoji', array(
        'default' => '🌸',
        'transport' => 'refresh',
        'sanitize_callback' => 'wp_filter_nohtml_kses', // Basic sanitization
    ));

    $wp_customize->add_control('site_favicon_emoji', array(
        'label' => '图标 Emoji',
        'section' => 'favicon_section',
        'type' => 'text',
        'description' => '输入一个 Emoji 表情作为网站图标 (例如: 🌸, 🚀, 🌙)',
    ));
}
add_action('customize_register', 'windmill_customize_favicon');

// 2. Output Favicon in Head
function windmill_output_favicon() {
    // If user has set a standard Site Icon in WordPress (Appearance -> Customize -> Site Identity), use that.
    if (has_site_icon()) {
        return; 
    }

    // Otherwise, use our custom Emoji Favicon
    $emoji = get_theme_mod('site_favicon_emoji', '🌸');
    
    // Create SVG data URI
    // Note: We use a simple SVG with the emoji inside a <text> element.
    // We need to URL encode the SVG content.
    $svg = '<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>' . $emoji . '</text></svg>';
    
    echo '<link rel="icon" href="data:image/svg+xml,' . $svg . '">';
}
add_action('wp_head', 'windmill_output_favicon');

// --- Portfolio Custom Post Type ---
function windmill_register_portfolio_cpt() {
    $labels = array(
        'name'               => '作品集',
        'singular_name'      => '作品',
        'menu_name'          => '作品集',
        'add_new'            => '添加新作品',
        'add_new_item'       => '添加新作品',
        'edit_item'          => '编辑作品',
        'new_item'           => '新作品',
        'view_item'          => '查看作品',
        'search_items'       => '搜索作品',
        'not_found'          => '未找到作品',
        'not_found_in_trash' => '回收站中未找到作品',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-art',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'             => array('slug' => 'portfolio'),
        'show_in_rest'        => true, // Enable Gutenberg editor
    );

    register_post_type('portfolio', $args);

    // 自动刷新伪静态规则 (修复 404 错误)
    if (get_option('windmill_flush_rewrite_rules_flag') !== 'yes') {
        flush_rewrite_rules();
        update_option('windmill_flush_rewrite_rules_flag', 'yes');
    }
}
add_action('init', 'windmill_register_portfolio_cpt');

// --- Fix for Mobile/LAN Access (Localhost vs IP) ---
// Automatically replace 'localhost' with the current IP address in asset URLs
// This fixes missing styles when accessing the site from iPhone/iPad on the same network.
function windmill_fix_local_assets($url) {
    // Check if we are in a web request
    if ( !isset($_SERVER['HTTP_HOST']) ) return $url;

    $current_host = $_SERVER['HTTP_HOST']; // e.g., 192.168.1.5
    
    // If the URL contains 'localhost' but the user is accessing via IP
    if ( strpos($url, 'localhost') !== false && strpos($current_host, 'localhost') === false ) {
        $url = str_replace('localhost', $current_host, $url);
    }

    // Fix Mixed Content: Force HTTPS if current request is HTTPS
    // Also force HTTPS if the site is accessed via a domain name (production environment)
    $is_production = strpos($current_host, 'localhost') === false && !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $current_host);
    
    if ( is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || $is_production ) {
        if ( strpos($url, 'http://') === 0 ) {
            $url = str_replace('http://', 'https://', $url);
        }
    }
    
    return $url;
}
add_filter('style_loader_src', 'windmill_fix_local_assets');
add_filter('script_loader_src', 'windmill_fix_local_assets');
add_filter('theme_file_uri', 'windmill_fix_local_assets');

// --- Force HTTPS for all asset and site URLs (prevents mixed content on https domains) ---
function windmill_force_https_urls($url) {
    if (empty($url)) return $url;

    // If request is https or current host looks like a domain (not localhost/IP), force https
    $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $is_production = $current_host && strpos($current_host, 'localhost') === false && !preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $current_host);

    if (is_ssl() || $is_production) {
        $url = preg_replace('#^http://#i', 'https://', $url);
    }
    return $url;
}

add_filter('content_url', 'windmill_force_https_urls');
add_filter('plugins_url', 'windmill_force_https_urls');
add_filter('includes_url', 'windmill_force_https_urls');
add_filter('site_url', 'windmill_force_https_urls');
add_filter('home_url', 'windmill_force_https_urls');
add_filter('theme_file_uri', 'windmill_force_https_urls');
add_filter('stylesheet_directory_uri', 'windmill_force_https_urls');
add_filter('template_directory_uri', 'windmill_force_https_urls');
add_filter('style_loader_src', 'windmill_force_https_urls');
add_filter('script_loader_src', 'windmill_force_https_urls');

// --- Strip accidental credential query strings like ?username=...&password=... ---
function windmill_strip_credential_query() {
    if (isset($_GET['username']) || isset($_GET['password'])) {
        $clean_url = remove_query_arg(array('username', 'password'));
        wp_safe_redirect($clean_url, 301);
        exit;
    }
}
add_action('template_redirect', 'windmill_strip_credential_query', 0);

// --- Auto Create Pages on Theme Activation ---
function windmill_auto_create_pages() {
    // 1. Create Blog Page
    // Check if page exists by slug 'blog'
    $blog_page = get_page_by_path('blog');
    
    if (!$blog_page) {
        // Check if it's in trash
        $blog_page = get_page_by_path('blog', OBJECT, 'page');
        if (!$blog_page) {
             $blog_page_id = wp_insert_post(array(
                'post_title'    => 'Blog',
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_name'     => 'blog',
                'page_template' => 'page-blog.php'
            ));
        }
    } else {
        // Ensure template is set correctly if page exists
        $current_template = get_post_meta($blog_page->ID, '_wp_page_template', true);
        if ($current_template !== 'page-blog.php') {
            update_post_meta($blog_page->ID, '_wp_page_template', 'page-blog.php');
        }
    }

    // 2. Create About Page
    $about_page = get_page_by_path('about');

    if (!$about_page) {
        $about_page_id = wp_insert_post(array(
            'post_title'    => 'About',
            'post_content'  => 'Welcome to my about page.',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'about',
            'page_template' => 'page-about.php'
        ));
    } else {
        $current_template = get_post_meta($about_page->ID, '_wp_page_template', true);
        if ($current_template !== 'page-about.php') {
            update_post_meta($about_page->ID, '_wp_page_template', 'page-about.php');
        }
    }
}
add_action('init', 'windmill_auto_create_pages');
