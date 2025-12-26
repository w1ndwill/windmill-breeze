<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; gap: 10px;">
    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'windmill-breeze' ); ?>" value="<?php echo get_search_query(); ?>" name="s" style="flex-grow: 1; padding: 10px; border: 2px solid #eee; border-radius: 8px;">
    <button type="submit" class="search-submit" style="background: var(--primary-color); color: white; border: none; padding: 0 20px; border-radius: 8px; cursor: pointer;">Search</button>
</form>
