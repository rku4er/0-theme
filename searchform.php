<?php $form_id = uniqid('search-'); ?>

<form
  role="search"
  method="get"
  class="search-form"
  action="<?php echo esc_url(home_url('/')); ?>">


  <label
    for="<?php echo $form_id; ?>"
    type="button">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon">
      <use xlink:href="#magnifier"></use>
    </svg>
  </label>

  <input
    class="form-control"
    type="search"
    value="<?php echo get_search_query(); ?>"
    id="<?php echo $form_id; ?>"
    name="s"
    placeholder="<?php _e('Search', 'sage'); ?>"
    required />

</form>
