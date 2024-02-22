<?php page_banner(); ?>

<div class="container container--narrow page-section">
  <?php $parent_id = wp_get_post_parent_id(get_the_ID()); ?>
  <?php if ($parent_id): ?>
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a class="metabox__blog-home-link" href="<?php the_permalink($parent_id); ?>">
          <i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($parent_id); ?>
        </a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
  <?php endif; ?>

  <?php
    $child_pages = get_pages(array(
      'child_of' => get_the_ID()
    ));

    // Show the sidebar with child page links if the current page is a child page or a parent page with child pages
    if ($parent_id || $child_pages): ?>
      <div class="page-links">
        <h2 class="page-links__title">
          <a href="<?php the_permalink($parent_id) ?>"><?php echo get_the_title($parent_id); ?></a>
        </h2>
        <ul class="min-list">
          <?php
            $args = array(
              'title_li' => '',
              'child_of' => $parent_id ? $parent_id : get_the_ID(),
              'sort_column' => 'menu_order'
            );
            wp_list_pages($args);
          ?>
        </ul>
      </div>
    <?php endif; ?>

  <div class="generic-content">
    <?php the_content(); ?>
  </div>
</div>