<?php
/**
 * The template for displaying comments
 */

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area" style="margin-top: 50px;">

    <?php
    // You can start editing here -- including this comment!
    if ( have_comments() ) :
        ?>
        <h2 class="comments-title" style="font-size: 1.5rem; margin-bottom: 30px;">
            <?php
            $windmill_comment_count = get_comments_number();
            if ( '1' === $windmill_comment_count ) {
                printf(
                    /* translators: 1: title. */
                    esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'windmill-breeze' ),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf( 
                    /* translators: 1: comment count number, 2: title. */
                    esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $windmill_comment_count, 'comments title', 'windmill-breeze' ) ),
                    number_format_i18n( $windmill_comment_count ),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2><!-- .comments-title -->

        <ol class="comment-list" style="list-style: none; padding: 0;">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size'=> 60,
                )
            );
            ?>
        </ol><!-- .comment-list -->

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if ( ! comments_open() ) :
            ?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'windmill-breeze' ); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    comment_form(array(
        'class_submit' => 'submit btn-primary',
        'style' => 'margin-top: 30px;'
    ));
    ?>

</div><!-- #comments -->