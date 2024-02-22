<?php

function register_like_routes() {
  register_rest_route('university/v1', 'like', array(
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'create_like',
    'permission_callback' => '__return_true'
  ));

  register_rest_route('university/v1', 'like/(?P<id>\d+)', array(
    'methods' => WP_REST_Server::DELETABLE,
    'callback' => 'delete_like',
    'permission_callback' => '__return_true'
  ));
}

function create_like(WP_REST_Request $request) {
  if (!is_user_logged_in()) {
    return rest_ensure_response(
      new WP_REST_Response(array('message' => 'Only logged in users can create a like.'), 401)
    );
  }

  $professor_id = intval(sanitize_text_field($request->get_param('professor_id')));

  // A user cannot like a professor that they have already liked, or a post that is not of professor type

  if (get_post_type($professor_id) !== 'professor') {
    return rest_ensure_response(
      new WP_REST_Response(array('message' => 'Invalid ID provided.'), 400)
    );
  }

  $like_exists_query = new WP_Query(array(
    'post_type' => 'like',
    'author' => get_current_user_id(),
    'meta_query' => array(
      array(
        'key' => 'liked_professor_id',
        'compare' => '=',
        'value' => $professor_id
      )
    )
  ));

  if ($like_exists_query->found_posts > 0) {
    return rest_ensure_response(
      new WP_REST_Response(array('message' => 'You have already liked this professor.'), 400)
    );
  }

  $post_arr = array(
    'post_type' => 'like',
    'post_status' => 'publish',
    'meta_input' => array(
      'liked_professor_id' => sanitize_text_field($request->get_param('professor_id'))
    )
  );

  $post_id = wp_insert_post($post_arr);

  return rest_ensure_response(array('id' => $post_id));
}

function delete_like(WP_REST_Request $request) {
  $like_id = intval(sanitize_text_field($request->get_param('id')));

  if (get_post_type($like_id) !== 'like') {
    return rest_ensure_response(
      new WP_REST_Response(array('message' => 'Invalid ID provided.'), 400)
    );
  }

  if (!is_user_logged_in()) {
    return rest_ensure_response(
      new WP_REST_Response(array('message' => 'Only logged in users can delete a like.'), 401)
    );
  }

  // Only the user who created the like can delete it
  if (get_current_user_id() !== intval(get_post_field('post_author', $like_id))) {
    return rest_ensure_response(
      new WP_REST_Response(array('message' => 'You do not have permission to do that.'), 401)
    );
  }

  wp_delete_post($like_id, true);

  return rest_ensure_response(array('message' => 'Like deleted successfully!'));
}

add_action('rest_api_init', 'register_like_routes');