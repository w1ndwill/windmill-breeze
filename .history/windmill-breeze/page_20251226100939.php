<?php get_header(); ?>

<div class="container">

    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?> style="grid-column: span 12; padding: 40px;">
            
            <header class="entry-header" style="margin-bottom: 30px; text-align: center;">
                <?php the_title( '<h1 class="entry-title" style="font-size: 2.5rem; margin-bottom: 10px;">', '</h1>' ); ?>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="post-thumbnail" style="margin-bottom: 30px; border-radius: 16px; overflow: hidden;">
                    <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: auto; display: block;')); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content" style="font-size: 1.1rem; line-height: 1.8;">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links">' . __( 'Pages:', 'windmill-breeze' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>

        </article>

    <?php endwhile; ?>

</div>

<?php get_footer(); ?>
