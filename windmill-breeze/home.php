<?php 
get_header(); 
?>

<div class="container">
    
    <!-- Blog Header -->
    <div class="header-section" style="padding: 20px 0;">
        <h1 class="cursive-name" style="font-size: 2.5rem;">Blog Posts</h1>
        <p class="site-intro">分享技术与生活</p>
    </div>

    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('card blog-post-card'); ?>>
                
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail" style="height: 200px; overflow: hidden; border-radius: 12px; margin-bottom: 15px;">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;')); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <header class="entry-header">
                    <?php the_title( sprintf( '<h2 class="entry-title" style="font-size: 1.5rem; margin-bottom: 10px;"><a href="%s" rel="bookmark" style="text-decoration: none; color: inherit;">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                    
                    <div class="entry-meta" style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 15px;">
                        <span class="posted-on">📅 <?php echo get_the_date(); ?></span>
                        <span class="byline"> 👤 <?php the_author(); ?></span>
                        <span class="cat-links"> 📂 <?php the_category( ', ' ); ?></span>
                    </div>
                </header>

                <div class="entry-summary" style="color: var(--text-main); opacity: 0.9;">
                    <?php the_excerpt(); ?>
                </div>

                <div class="entry-footer" style="margin-top: auto; padding-top: 15px;">
                    <a href="<?php the_permalink(); ?>" class="read-more-btn" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">阅读全文 &rarr;</a>
                </div>

            </article>

        <?php endwhile; ?>

        <!-- Pagination -->
        <div class="pagination-container" style="grid-column: span 12; display: flex; justify-content: center; margin-top: 20px;">
            <?php
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( '&larr; Previous', 'textdomain' ),
                'next_text' => __( 'Next &rarr;', 'textdomain' ),
            ) );
            ?>
        </div>

    <?php else : ?>

        <div class="card" style="grid-column: span 12; text-align: center; padding: 40px;">
            <h2>暂无文章</h2>
            <p>这里还没有发布任何内容。</p>
        </div>

    <?php endif; ?>

</div>

<?php get_footer(); ?>