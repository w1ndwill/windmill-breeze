<?php
/*
Template Name: Guestbook
*/
get_header(); 
?>

<div class="container">
    
    <!-- Guestbook Header (Matching Portfolio Style) -->
    <div class="header-section" style="padding: 20px 0; grid-column: span 12;">
        <h1 class="cursive-name" style="font-size: 3rem;">留言板</h1>
        <p class="site-intro">欢迎留下你的足迹，与我分享你的故事</p>
    </div>

    <div class="card guestbook-card" style="grid-column: span 12; padding: 40px;">
        
        <div class="guestbook-intro" style="text-align: center; margin-bottom: 40px; color: var(--text-light);">
            <p>这里是我的小天地，也是你的树洞。</p>
            <p>无论是技术交流、生活感悟，还是简单的问候，都欢迎写下来。</p>
        </div>

        <!-- Comments Section -->
        <?php 
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
        ?>
    </div>

</div>

<?php get_footer(); ?>
