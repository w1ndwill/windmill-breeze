<?php get_header(); ?>

<div class="container">
    <div class="card" style="grid-column: span 12; text-align: center; padding: 60px 20px;">
        <h1 style="font-size: 6rem; margin: 0; color: var(--primary-color);">404</h1>
        <h2 style="margin-top: 0;">Oops! Page Not Found</h2>
        <p style="color: var(--text-light); margin-bottom: 30px;">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        
        <a href="<?php echo home_url(); ?>" class="btn-resume" style="display: inline-block; text-decoration: none;">Back to Home</a>
    </div>
</div>

<?php get_footer(); ?>
