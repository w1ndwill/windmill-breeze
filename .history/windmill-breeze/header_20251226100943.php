<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <?php if (!is_user_logged_in()) : ?>
    <!-- 登录/注册 弹窗 -->
    <div class="login-overlay active" id="login-overlay">
        <div class="login-card">
            <div class="login-title">Welcome</div>
            <p style="color: var(--text-light); margin-bottom: 20px;">请登录以继续访问</p>
            
            <!-- 登录表单 -->
            <form class="login-form" id="login-form">
                <input type="text" name="username" placeholder="用户名" required>
                <input type="password" name="password" placeholder="密码" required>
                <button type="submit" class="login-btn">登录</button>
                <div class="form-toggle-text">
                    还没有账号？ <span class="form-toggle-link" id="to-register">去注册</span>
                </div>
                <button type="button" class="guest-btn" id="guest-btn">我是访客，随便看看</button>
            </form>

            <!-- 注册表单 (默认隐藏) -->
            <form class="login-form hidden" id="register-form">
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
            <a href="<?php echo home_url(); ?>" class="nav-brand">🌸</a>
            <?php 
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-menu',
                    'fallback_cb'    => false,
                ) );
            } else {
                // Fallback if no menu is assigned
                ?>
                <ul class="nav-menu">
                    <li><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li><a href="<?php echo admin_url('nav-menus.php'); ?>">Assign Menu</a></li>
                </ul>
                <?php
            }
            ?>
        </div>
        <div class="nav-items" style="display: flex; align-items: center; gap: 15px;">
            
            <!-- Search Toggle -->
            <button id="search-toggle" class="theme-toggle" aria-label="Search">🔍</button>

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
                🌙
            </button>
        </div>
    </nav>

    <!-- 天气特效层 -->
    <div class="weather-effect-layer" id="weather-layer"></div>
