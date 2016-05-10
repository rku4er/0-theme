<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="form-group">
        <div class="input-group">
            <input class="search-field form-control" type="search" value="<?php echo get_search_query(); ?>" name="s" placeholder="<?php _e('Search', 'sage'); ?>" required>
            <span class="input-group-btn">
                <button type="submit" class="search-submit btn btn-primary-outline"><span class="fa fa-search"></span></button>
            </span>
        </div>
    </div>
</form>
