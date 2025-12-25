<?php
/*
Template Name: About Page
*/
get_header();
?>

<div class="container about-page">
    
    <!-- 1. Hero / Motto Section -->
    <div class="about-hero" style="text-align: center; padding: 80px 20px; background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-main) 100%); border-radius: 24px; margin-bottom: 50px; box-shadow: var(--card-shadow);">
        <h1 class="page-title" style="font-size: 3.5rem; margin-bottom: 25px; font-family: 'Meie Script', cursive; color: var(--primary-color);"><?php the_title(); ?></h1>
        <div class="motto-container" style="position: relative; display: inline-block;">
            <span style="font-size: 3rem; position: absolute; top: -20px; left: -30px; color: var(--primary-color); opacity: 0.2;">“</span>
            <p class="motto" style="font-size: 1.4rem; color: var(--text-light); font-style: italic; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                <?php echo get_theme_mod('motto_text', '种一棵树最好的时间是十年前，其次是现在。'); ?>
            </p>
            <span style="font-size: 3rem; position: absolute; bottom: -40px; right: -30px; color: var(--primary-color); opacity: 0.2;">”</span>
        </div>
    </div>

    <!-- 2. Main Content (Editable via WP Admin) -->
    <div class="about-content card" style="padding: 50px; margin-bottom: 50px; border-radius: 24px;">
        <?php
        while ( have_posts() ) : the_post();
            ?>
            <div class="entry-content" style="font-size: 1.1rem; line-height: 1.9; color: var(--text-main);">
                <?php the_content(); ?>
            </div>
            <?php
        endwhile;
        ?>
    </div>

    <!-- 3. Resume / Profile Section (From Customizer) -->
    <div class="resume-section card" style="padding: 50px; border-radius: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
        <div class="resume-text">
            <h2 style="margin-bottom: 25px; font-size: 2rem; border-left: 5px solid var(--primary-color); padding-left: 15px;">个人简介</h2>
            <p style="line-height: 1.8; color: var(--text-light); font-size: 1.1rem; margin-bottom: 30px;">
                <?php echo nl2br(get_theme_mod('resume_intro', '拥有5年开发经验的前端工程师，热衷于创造优雅的用户体验。')); ?>
            </p>
            
            <div class="resume-actions" style="display: flex; gap: 20px;">
                <?php
                $resume_url = get_theme_mod('resume_file_url');
                if ($resume_url && $resume_url !== '#') : ?>
                    <a href="<?php echo esc_url($resume_url); ?>" class="btn btn-primary" target="_blank" style="display: inline-flex; align-items: center; gap: 10px; padding: 12px 30px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 50px; font-weight: bold; transition: transform 0.2s;">
                        <span>📄</span> 下载简历
                    </a>
                <?php endif; ?>
                
                <!-- Social Links -->
                <div class="social-links" style="display: flex; gap: 15px; align-items: center;">
                    <?php
                    $socials = array('github', 'bilibili', 'zhihu', 'weibo');
                    foreach ($socials as $social) {
                        $url = get_theme_mod('social_' . $social);
                        if ($url) {
                            echo '<a href="' . esc_url($url) . '" target="_blank" style="font-size: 1.5rem; color: var(--text-light); transition: color 0.2s;">' . ucfirst($social) . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="resume-image" style="position: relative;">
             <?php if (has_post_thumbnail()) : ?>
                <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); transform: rotate(2deg);">
                    <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: auto; display: block;')); ?>
                </div>
             <?php else: ?>
                <div style="background: #eee; height: 300px; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #999;">
                    (在页面编辑器中设置特色图片)
                </div>
             <?php endif; ?>
        </div>
    </div>

</div>

<style>
    /* Simple responsive fix */
    @media (max-width: 768px) {
        .resume-section {
            grid-template-columns: 1fr !important;
            text-align: center;
        }
        .resume-text h2 {
            border-left: none !important;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
            padding-bottom: 10px;
        }
        .resume-actions {
            justify-content: center;
            flex-direction: column;
        }
        .social-links {
            justify-content: center;
        }
    }
</style>

<?php get_footer(); ?>