<?php

function register_search_route() {
  register_rest_route('university/v1', 'search', array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'get_search_results',
    'permission_callback' => '__return_true'
  ));
}

// This function is meant to be used as a callback for 'posts_where' filter
// It changes the default search behavior (search in title, content, and excerpt) to search only in the title of a post 
function search_by_title_only(string $where, WP_Query $query) {
  global $wpdb;
  $search_term = $query->get('search_in_title');

  if ($search_term) {
    $where = $where . ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
  }

  return $where;
}

function get_search_results(WP_REST_Request $request) {
  add_filter('posts_where', 'search_by_title_only', 10, 2);

  $query = new WP_Query(array(
    'post_type' => array('post', 'page', 'professor', 'program', 'event', 'campus'),
    'posts_per_page' => -1,
    'search_in_title' => sanitize_text_field($request->get_param('term'))
  ));

  remove_filter('posts_where', 'search_by_title_only', 10, 2);

  $results = array(
    'general_info' => array(),
    'professor' => array(),
    'program' => array(),
    'event' => array(),
    'campus' => array(),
  );

  while ($query->have_posts()) {
    $query->the_post();

    $post_type = get_post_type();
    $result = array(
      'id' => get_the_ID(),
      'post_type' => $post_type,
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
    );

    if ($post_type === 'post') {
      $result['author'] = get_the_author();
    }

    if ($post_type === 'professor') {
      $result['image_url'] = get_the_post_thumbnail_url(0, 'professor_landscape');
      $result['image_caption'] = get_the_post_thumbnail_caption(0);
    }

    if ($post_type === 'event') {
      $event_date = DateTime::createFromFormat('d/m/Y', get_field('event_date'));
      $result['month'] = $event_date->format('M');
      $result['day'] = $event_date->format('d');
      $result['excerpt'] = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 14);
    }

    // Include related campuses of a program
    if ($post_type === 'program') {
      $related_campuses = get_field('related_campuses');

      if (count($related_campuses) > 0) {
        foreach($related_campuses as $campus) {
          array_push(
            $results['campus'], 
            array(
              'id' => $campus->ID,
              'post_type' => 'campus',
              'title' => get_the_title($campus),
              'permalink' => get_the_permalink($campus),
            )
          );
        }
      }
    }

    if ($post_type === 'post' || $post_type === 'page') {
      array_push($results['general_info'], $result);
    } else {
      array_push($results[$post_type], $result);
    }
  }

  wp_reset_postdata();

  if (count($results['program']) > 0) {
    // The search term can result in multiple programs
    // In that case, we want to get professors and events related to any of those programs

    $program_relationship_meta_query = array('relation' => 'OR');

    foreach($results['program'] as $program) {
      array_push(
        $program_relationship_meta_query, 
        array(
          'key' => 'related_programs',
          'compare' => 'LIKE',
          'value' => '"' . $program['id']
        )
      );
    }

    // Gets professors and events with related programs, e.g., if we search for 'biology', it will look for professors that teach biology
    $program_relationship_query = new WP_Query(array(
      'posts_per_page' => -1,
      'post_type' => array('professor', 'event'),
      'meta_query' => $program_relationship_meta_query
    ));

    while ($program_relationship_query->have_posts()) {
      $program_relationship_query->the_post();

      $post_type = get_post_type();
      $result = array(
        'id' => get_the_ID(),
        'post_type' => $post_type,
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
      );

      if ($post_type === 'professor') {
        $result['image_url'] = get_the_post_thumbnail_url(0, 'professor_landscape');
        $result['image_caption'] = get_the_post_thumbnail_caption(0);
      }

      if ($post_type === 'event') {
        $event_date = DateTime::createFromFormat('d/m/Y', get_field('event_date'));
        $result['month'] = $event_date->format('M');
        $result['day'] = $event_date->format('d');
        $result['excerpt'] = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 14);
      }

      array_push($results[$post_type], $result);

    }

    wp_reset_postdata();

    // Remove any duplicates and disregard numeric keys (convert to a regular array)
    $results['professor'] = array_values(array_unique($results['professor'], SORT_REGULAR));
    $results['event'] = array_values(array_unique($results['event'], SORT_REGULAR));
  }

  return rest_ensure_response($results);
}

add_action('rest_api_init', 'register_search_route');