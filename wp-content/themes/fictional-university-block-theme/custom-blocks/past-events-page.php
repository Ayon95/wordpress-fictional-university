<?php 
  page_banner(array(
    'title' => 'Past Events',
    'subtitle' => 'A recap of our past events.'
  ));
?>

<div class="container container--narrow page-section">
  <?php
    $today = date('Y-m-d H:i:s');
    $past_events_query = new WP_Query(array(
      'paged' => get_query_var('paged', 1),
      'post_type' => 'event',
      'meta_key' => 'event_date',
      'orderby' => 'meta_value',
      'order' => 'ASC',
      'meta_query' => array(
        array(
          'key' => 'event_date',
          'compare' => '<',
          'value' => $today,
          'type' => 'DATETIME'
        )
      )
    ));
  ?>
  <?php if ($past_events_query->have_posts()): while ($past_events_query->have_posts()):
    $past_events_query->the_post();
    get_template_part('template-parts/content-event');
  endwhile; endif; ?>
  <?php
    echo paginate_links(array(
      'total' => $past_events_query->max_num_pages
    ));
  ?>  
</div>