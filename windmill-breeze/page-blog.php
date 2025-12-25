<?php 
/*
Template Name: Blog Page
*/
get_header(); 
?>

<div class="container">
    
    <!-- Blog Header -->
    <div class="header-section" style="grid-column: span 12; padding: 20px 0; text-align: center;">
        <h1 class="cursive-name" style="font-size: 2.5rem; font-family: 'Meie Script', cursive; color: var(--primary-color);">Blog Posts</h1>
        <p class="site-intro" style="color: var(--text-light);">分享技术与生活</p>
    </div>

    <?php 
    // Custom Query for Blog Page Template
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'paged' => $paged
    );
    $blog_query = new WP_Query( $args );

    if ( $blog_query->have_posts() ) : ?>
        <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
            
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
        <div class="pagination-container" style="grid-column: span 12; display: flex; justify-content: center; margin-top: 40px; margin-bottom: 40px;">
            <?php
            echo paginate_links( array(
                'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'total'        => $blog_query->max_num_pages,
                'current'      => max( 1, get_query_var( 'paged' ) ),
                'format'       => '?paged=%#%',
                'show_all'     => false,
                'type'         => 'plain',
                'end_size'     => 2,
                'mid_size'     => 1,
                'prev_next'    => true,
                'prev_text'    => '<span class="page-numbers">上一页</span>',
                'next_text'    => '<span class="page-numbers">下一页</span>',
                'add_args'     => false,
                'add_fragment' => '',
            ) );
            ?>
        </div>
        <?php wp_reset_postdata(); ?>

    <?php else : ?>

        <div class="card" style="grid-column: span 12; text-align: center; padding: 40px;">
            <h2>暂无文章</h2>
            <p>这里还没有发布任何内容。</p>
        </div>

    <?php endif; ?>

</div>

<?php get_footer(); ?>