<?php

function related_posts_html(int $professor_id) {
  $posts_where_professor_mentioned_query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
        'field' => 'featured_professor',
        'compare' => '=',
        'value' => $professor_id
      )
    )
  ));

  ob_start();

  if ($posts_where_professor_mentioned_query->have_posts()): ?>
    <p><?php the_title(); ?> is mentioned in the following posts:</p>
    <ul>
      <?php while ($posts_where_professor_mentioned_query->have_posts()): 
        $posts_where_professor_mentioned_query->the_post(); ?>
        <li>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
      <?php endwhile; ?>
    </ul>
    
  <?php endif; wp_reset_postdata();

  return ob_get_clean();
}