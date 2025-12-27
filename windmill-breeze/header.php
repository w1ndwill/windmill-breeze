<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <script>
        // 立即执行主题检查，防止闪烁
        (function() {
            var savedTheme = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <?php 
    // Check if user is logged in OR if they have set the guest mode cookie
    $is_guest_mode = isset($_COOKIE['windmill_guest_mode']);
    if (!is_user_logged_in() && !$is_guest_mode) : 
    ?>
    <!-- 登录/注册 弹窗 -->
    <div class="login-overlay active" id="login-overlay">
        <div class="login-card">
            <div class="login-title">Welcome</div>
            <p style="color: var(--text-light); margin-bottom: 20px;">请登录以继续访问</p>
            
            <!-- 登录表单 -->
            <form class="login-form" id="login-form" method="POST">
                <input type="text" name="username" placeholder="用户名" required>
                <input type="password" name="password" placeholder="密码" required>
                <button type="submit" class="login-btn">登录</button>
                <div class="form-toggle-text">
                    还没有账号？ <span class="form-toggle-link" id="to-register">去注册</span>
                </div>
                <button type="button" class="guest-btn" id="guest-btn">我是访客，随便看看</button>
            </form>

            <!-- 注册表单 (默认隐藏) -->
            <form class="login-form hidden" id="register-form" method="POST">
                <input type="text" name="username" placeholder="设置用户名" required>
                <input type="email" name="email" placeholder="电子邮箱" required>
                <input type="password" name="password" placeholder="设置密码" required>
                <button type="submit" class="login-btn">注册</button>
                <div class="form-toggle-text">
                    已有账号？ <span class="form-toggle-link" id="to-login">去登录</span>
                </div>
                <button type="button" class="guest-btn" id="guest-btn-reg">我是访客，随便看看</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Login Overlay Structure (Hidden by default if guest mode is active, but available for JS to toggle) -->
    <?php if (!is_user_logged_in() && $is_guest_mode) : ?>
    <div class="login-overlay" id="login-overlay">
        <div class="login-card">
            <div class="login-title">Welcome</div>
            <p style="color: var(--text-light); margin-bottom: 20px;">请登录以继续访问</p>
            
            <!-- 登录表单 -->
            <form class="login-form" id="login-form" method="POST">
                <input type="text" name="username" placeholder="用户名" required>
                <input type="password" name="password" placeholder="密码" required>
                <button type="submit" class="login-btn">登录</button>
                <div class="form-toggle-text">
                    还没有账号？ <span class="form-toggle-link" id="to-register">去注册</span>
                </div>
                <button type="button" class="guest-btn" id="guest-btn">我是访客，随便看看</button>
            </form>

            <!-- 注册表单 (默认隐藏) -->
            <form class="login-form hidden" id="register-form" method="POST">
                <input type="text" name="username" placeholder="设置用户名" required>
                <input type="email" name="email" placeholder="电子邮箱" required>
                <input type="password" name="password" placeholder="设置密码" required>
                <button type="submit" class="login-btn">注册</button>
                <div class="form-toggle-text">
                    已有账号？ <span class="form-toggle-link" id="to-login">去登录</span>
                </div>
                <button type="button" class="guest-btn" id="guest-btn-reg">我是访客，随便看看</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (is_user_logged_in()) : ?>
    <!-- 个人资料弹窗 -->
    <div class="login-overlay" id="profile-overlay">
        <div class="login-card" style="width: 400px;">
            <div class="login-title">My Profile</div>
            <form class="login-form" id="profile-form" enctype="multipart/form-data">
                <!-- 头像上传预览 -->
                <div class="profile-avatar-upload">
                    <img src="" id="profile-avatar-preview" alt="Avatar">
                    <div class="avatar-overlay-icon">📷</div>
                    <input type="file" name="avatar" id="profile-avatar-input" accept="image/*">
                </div>

                <div style="text-align: left; margin-top: 20px;">
                    <label style="font-size: 0.9rem; color: var(--text-light);">昵称</label>
                    <input type="text" name="display_name" id="profile-name" required>
                    
                    <label style="font-size: 0.9rem; color: var(--text-light);">邮箱</label>
                    <input type="email" name="email" id="profile-email" required>

                    <label style="font-size: 0.9rem; color: var(--text-light);">我的博客</label>
                    <input type="url" name="url" id="profile-url" placeholder="https://example.com">

                    <label style="font-size: 0.9rem; color: var(--text-light);">爱好</label>
                    <input type="text" name="hobbies" id="profile-hobbies" placeholder="例如：摄影、编程、发呆">

                    <label style="font-size: 0.9rem; color: var(--text-light);">友链 (名称 | 链接)</label>
                    <textarea name="friend_links" id="profile-friend-links" rows="3" style="width: 100%; padding: 10px; border-radius: 12px; border: 2px solid #eee; font-family: inherit; margin: 5px 0 15px;" placeholder="我的朋友 | https://friend.com"></textarea>

                    <label style="font-size: 0.9rem; color: var(--text-light);">个人简介</label>
                    <textarea name="description" id="profile-desc" rows="3" style="width: 100%; padding: 10px; border-radius: 12px; border: 2px solid #eee; font-family: inherit; margin: 5px 0 15px;"></textarea>
                </div>

                <button type="submit" class="login-btn">保存修改</button>
                <button type="button" class="guest-btn" id="close-profile-btn">关闭</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- 自定义顶栏 -->
    <nav class="custom-navbar">
        <div class="nav-left">
            <a href="<?php echo home_url(); ?>" class="nav-brand">
                <!-- SVG Logo (Flower) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M4.93 19.07l1.41-1.41"/><path d="M17.66 6.34l1.41-1.41"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <ul class="nav-menu">
                <li><a href="<?php echo home_url(); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>">首页</a></li>
                <?php 
                // Get the Blog Page URL dynamically
                $blog_page = get_pages(array(
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'page-blog.php'
                ));
                $blog_url = (!empty($blog_page)) ? get_permalink($blog_page[0]->ID) : home_url('/blog');
                ?>
                <li><a href="<?php echo esc_url($blog_url); ?>" class="<?php echo (!is_front_page() && (is_page_template('page-blog.php') || (is_single() && get_post_type() == 'post'))) ? 'active' : ''; ?>">文章</a></li>
                <li><a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="<?php echo (is_post_type_archive('portfolio') || (is_single() && get_post_type() == 'portfolio')) ? 'active' : ''; ?>">作品</a></li>
                <?php 
                // Get the About Page URL dynamically
                $about_page = get_pages(array(
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'page-about.php'
                ));
                $about_url = (!empty($about_page)) ? get_permalink($about_page[0]->ID) : '#';
                ?>
                <li><a href="<?php echo $about_url; ?>" class="<?php echo is_page_template('page-about.php') ? 'active' : ''; ?>">关于</a></li>
            </ul>
        </div>
        <div class="nav-items" style="display: flex; align-items: center; gap: 15px;">
            
            <!-- Search Toggle -->
            <button id="search-toggle" class="theme-toggle" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>

            <!-- User Avatar Dropdown -->
            <div class="user-menu-container">
                <?php if (is_user_logged_in()) : 
                    $current_user = wp_get_current_user();
                    $avatar = get_avatar_url($current_user->ID);
                ?>
                    <div class="user-avatar-trigger">
                        <img src="<?php echo esc_url($avatar); ?>" alt="User Avatar" class="nav-user-avatar">
                        <div class="user-dropdown">
                            <div class="dropdown-header">
                                <strong><?php echo esc_html($current_user->display_name); ?></strong>
                            </div>
                            <a href="#" id="nav-profile-link-dropdown" class="dropdown-item">个人资料</a>
                            <a href="<?php echo wp_logout_url(home_url()); ?>" class="dropdown-item logout">注销</a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="user-avatar-trigger">
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iIzg4OCI+PHBhdGggZD0iTTEyIDEyYzIuMjEgMCA0LTEuNzkgNC00cy0xLjc5LTQtNC00LTQgMS43OS00IDQgMS43OSA0IDQgNHptMCAyYy0yLjY3IDAtOCAxLjM0LTggNHYyaDE2di0yYzAtMi42Ni01LjMzLTQtOC00eiIvPjwvc3ZnPg==" alt="Guest" class="nav-user-avatar default-avatar">
                        <div class="user-dropdown">
                            <a href="#" id="nav-login-btn-dropdown" class="dropdown-item">登录</a>
                            <a href="#" id="nav-register-btn-dropdown" class="dropdown-item">注册</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (current_user_can('manage_options')) : ?>
                <a href="<?php echo admin_url(); ?>" style="text-decoration: none; color: var(--text-main); font-weight: bold; font-size: 0.9rem; transition: color 0.3s;">后台管理</a>
            <?php endif; ?>

            <!-- 主题切换按钮 (移入顶栏) -->
            <button class="theme-toggle" id="theme-toggle" aria-label="切换主题">
                <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            </button>
        </div>
    </nav>

    <!-- 天气特效层 -->
    <div class="weather-effect-layer" id="weather-layer"></div>

    <!-- Search Overlay -->
    <div id="search-overlay" class="search-overlay">
        <button id="search-close" class="search-close">&times;</button>
        <div class="search-container">
            <?php get_search_form(); ?>
            <p style="margin-top: 20px; color: #fff; opacity: 0.8;">输入关键词并按回车搜索</p>
        </div>
    </div>
