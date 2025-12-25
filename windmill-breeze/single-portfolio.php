<?php 
get_header(); 
?>

<div class="container">
    
    <?php while ( have_posts() ) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?> style="grid-column: span 12; padding: 40px;">
            
            <header class="entry-header" style="text-align: center; margin-bottom: 40px;">
                <div style="font-size: 0.9rem; color: var(--primary-color); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">Portfolio Project</div>
                <?php the_title( '<h1 class="entry-title" style="font-size: 2.5rem; margin-bottom: 15px;">', '</h1>' ); ?>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="post-thumbnail" style="width: 100%; max-height: 500px; overflow: hidden; border-radius: 16px; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <?php the_post_thumbnail('full', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content" style="font-size: 1.1rem; line-height: 1.8;">
                <?php the_content(); ?>
            </div>
            
            <!-- Project Meta / Links (Example: if you add custom fields later) -->
            <div class="project-meta" style="margin-top: 40px; padding: 20px; background: rgba(0,0,0,0.02); border-radius: 12px;">
                <p><strong>发布时间:</strong> <?php echo get_the_date(); ?></p>
                <!-- You can add custom fields here like 'Project URL', 'Client', etc. -->
            </div>

            <div class="entry-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; display: flex; justify-content: space-between;">
                <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="btn-back" style="text-decoration: none; color: var(--text-light);">&larr; 返回作品集</a>
            </div>

        </article>

    <?php endwhile; ?>

</div>

<?php get_footer(); ?>