<?php 
get_header(); 
?>

<div class="container">
    
    <!-- Portfolio Header -->
    <div class="header-section" style="padding: 20px 0;">
        <h1 class="cursive-name" style="font-size: 2.5rem;">我的作品集</h1>
        <p class="site-intro">创意与代码的结晶</p>
    </div>

    <?php if ( have_posts() ) : ?>
        
        <div class="portfolio-grid-container">
            <?php while ( have_posts() ) : the_post(); ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('card portfolio-card'); ?>>
                    
                    <a href="<?php the_permalink(); ?>" class="portfolio-thumbnail-link">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'portfolio-thumbnail')); ?>
                        <?php else : ?>
                            <div class="portfolio-placeholder">🎨</div>
                        <?php endif; ?>
                    </a>

                    <div class="portfolio-content">
                        <h2 class="entry-title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="btn-view-project">查看详情</a>
                    </div>

                </article>

            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <?php
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( '&larr; Previous', 'textdomain' ),
                'next_text' => __( 'Next &rarr;', 'textdomain' ),
            ) );
            ?>
        </div>

    <?php else : ?>

        <div class="card portfolio-empty">
            <div class="portfolio-empty-icon">🎨</div>
            <h2>暂无作品</h2>
            <p>正在整理优秀的作品，敬请期待...</p>
        </div>

    <?php endif; ?>

</div>

<?php get_footer(); ?>