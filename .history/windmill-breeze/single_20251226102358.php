<?php 
get_header(); 
?>

<div class="container">
    
    <?php while ( have_posts() ) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?> style="grid-column: span 12; padding: 40px;">
            
            <header class="entry-header" style="text-align: center; margin-bottom: 30px;">
                <?php the_title( '<h1 class="entry-title" style="font-size: 2.5rem; margin-bottom: 15px;">', '</h1>' ); ?>
                
                <div class="entry-meta" style="font-size: 0.9rem; color: var(--text-light);">
                    <span class="posted-on">📅 <?php echo get_the_date(); ?></span>
                    <span class="byline" style="margin-left: 15px;"> 👤 <?php the_author(); ?></span>
                    <span class="cat-links" style="margin-left: 15px;"> 📂 <?php the_category( ', ' ); ?></span>
                </div>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="post-thumbnail" style="width: 100%; max-height: 400px; overflow: hidden; border-radius: 16px; margin-bottom: 30px;">
                    <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content" style="font-size: 1.1rem; line-height: 1.8;">
                <?php the_content(); ?>
            </div>

            <div class="entry-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                <?php
                // Previous/Next Post Navigation
                the_post_navigation( array(
                    'prev_text' => '<span class="nav-subtitle">' . __( '上一篇:', 'textdomain' ) . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . __( '下一篇:', 'textdomain' ) . '</span> <span class="nav-title">%title</span>',
                ) );
                ?>
                
                <!-- Comments Section -->
                <?php 
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>

        </article>

    <?php endwhile; ?>

</div>

<?php get_footer(); ?>