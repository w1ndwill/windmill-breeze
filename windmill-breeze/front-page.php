<?php 
get_header(); 

// Determine which user's info to show
// If logged in, show current user's info (so they can see their changes)
// If not, show the admin's info (User ID 1)
$current_user_id = get_current_user_id();
$display_user_id = $current_user_id ? $current_user_id : 1;

// Get Avatar URL
$avatar_url = get_avatar_url($display_user_id, array('size' => 240));

// Get Description (Intro)
// Prefer user meta 'description', fallback to site tagline
$user_desc = get_user_meta($display_user_id, 'description', true);
if (empty($user_desc)) {
    $user_desc = get_bloginfo('description');
}

// Get Display Name
$user_info = get_userdata($display_user_id);
$display_name = $user_info ? $user_info->display_name : get_bloginfo('name');
?>

<div class="container">
    
    <!-- 1. 头部 -->
    <header class="header-section">
        <!-- 头像区域 -->
        <div class="avatar-container">
            <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar" class="avatar-img">
        </div>
        <h1 class="cursive-name"><?php echo esc_html($display_name); ?></h1>
        <p class="site-intro"><?php echo esc_html($user_desc); ?></p>
        
        <!-- 社交链接区域 -->
        <div class="social-links">
            <?php 
            $social_platforms = array(
                'github' => '🐙 GitHub',
                'bilibili' => '📺 Bilibili',
                'zhihu' => '🧠 知乎',
                'csdn' => '💻 CSDN',
                'netease' => '🎵 网易云',
                'weibo' => '👁️ 微博'
            );
            
            foreach ($social_platforms as $id => $label) {
                $url = get_theme_mod('social_' . $id, '');
                if ($url) {
                    echo '<a href="' . esc_url($url) . '" target="_blank" class="social-link">' . $label . '</a>';
                }
            }
            ?>
        </div>
    </header>

    <!-- 2. 座右铭 -->
    <div class="card motto-card">
        <div class="motto-text">“ <?php echo get_theme_mod('motto_text', '种一棵树最好的时间是十年前，其次是现在。'); ?> ”</div>
    </div>

    <!-- 2.5 时钟 -->
    <div class="card clock-card">
        <div class="analog-clock">
            <div class="hand hour-hand" id="hour-hand"></div>
            <div class="hand minute-hand" id="minute-hand"></div>
            <div class="hand second-hand" id="second-hand"></div>
            <div class="center-dot"></div>
        </div>
    </div>

    <!-- 3. 天气 -->
    <div class="card weather-card" id="weather-card" style="cursor: pointer;" title="点击切换天气模拟">
        <div style="font-size: 3rem;" id="weather-icon">☀️</div>
        <div style="font-size: 1.5rem; font-weight: bold;" id="weather-temp">26°C</div>
        <div style="opacity: 0.8;" id="weather-desc">北京 · 晴</div>
        <div class="date-display" id="date" style="margin-top: 5px; font-size: 0.8rem;">2023年10月1日</div>
    </div>

    <!-- 4. 博客 -->
    <div class="card blog-card">
        <div class="card-title">
            <?php 
            $blog_page_id = get_option( 'page_for_posts' );
            $blog_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url('/blog');
            ?>
            <a href="<?php echo esc_url($blog_url); ?>" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                📝 最近更新 <span style="font-size: 0.8rem; opacity: 0.6; font-weight: normal;">(点击查看全部)</span>
            </a>
        </div>
        <ul class="blog-list">
            <?php 
            $recent_posts = new WP_Query(array('posts_per_page' => 4));
            if ($recent_posts->have_posts()) :
                while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <li>暂无文章</li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- 5. 简历 -->
    <div class="card resume-card">
        <div class="resume-content">
            <div>
                <div class="card-title">👨‍💻 关于我 / 简历</div>
                <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 15px;">
                    <?php echo get_theme_mod('resume_intro', '拥有5年开发经验的前端工程师，热衷于创造优雅的用户体验。'); ?>
                </p>
                <div class="tag-container">
                    <span class="tag">JavaScript</span>
                    <span class="tag">React</span>
                    <span class="tag">Node.js</span>
                    <span class="tag">UI Design</span>
                </div>
            </div>
            <div class="resume-actions" style="display: flex; gap: 10px; margin-top: 15px;">
                <a href="<?php echo get_theme_mod('resume_file_url', '#'); ?>" class="btn-resume" download>下载简历</a>
                <button type="button" class="btn-resume btn-preview" id="btn-resume-preview" data-url="<?php echo get_theme_mod('resume_file_url', '#'); ?>">在线预览</button>
            </div>
        </div>
    </div>

    <!-- 6. 作品集 -->
    <div class="portfolio-section">
        <div class="card-title" style="padding-left: 10px;">🚀 我的小作品</div>
        <div class="portfolio-grid">
            <?php for ($i = 1; $i <= 4; $i++) : ?>
                <div class="portfolio-item">
                    <span class="portfolio-icon"><?php echo get_theme_mod("portfolio_{$i}_icon", '🚀'); ?></span>
                    <strong><?php echo get_theme_mod("portfolio_{$i}_title", "作品 {$i}"); ?></strong>
                    <p style="font-size: 0.8rem; color: #666;"><?php echo get_theme_mod("portfolio_{$i}_desc", "简短描述..."); ?></p>
                </div>
            <?php endfor; ?>
        </div>
    </div>

</div>

<?php get_footer(); ?>