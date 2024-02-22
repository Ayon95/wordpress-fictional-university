<?php
/**
 * Plugin Name: Featured Professor Block Type
 * Description: Displays a preview of a featured professor
 * Version: 1.0
 * Author: Mushfiq
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'inc/related_posts_html.php';

class FeaturedProfessor {
  function __construct() {
    add_action('init', array($this, 'init_custom_block'));
    add_action('rest_api_init', array($this, 'api_route_get_professor'));
    add_filter('the_content', array($this, 'add_related_posts'));
  }

  function init_custom_block() {
    register_meta('post', 'featured_professor', array(
      'type' => 'number',
      'show_in_rest' => true,
      'single' => false
    ));

    register_block_type_from_metadata(__DIR__ . '/build');
  }

  function add_related_posts($content) {
    if (is_singular('professor') && is_main_query() && in_the_loop()) {
      return $content . related_posts_html(get_the_ID());
    }

    return $content;
  }

  function api_route_get_professor() {
    register_rest_route('fp-plugin/v1', '/professor/(?P<id>\d+)', array(
      'method' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_professor'),
      'permission_callback' => '__return_true'
    ));
  }

  function get_professor(WP_REST_Request $request) {
    $professor_id = intval($request->get_url_params()['id']);
    $professor = get_post($professor_id);

    if (!$professor || $professor->post_type !== 'professor') {
      return rest_ensure_response(
        new WP_REST_Response(array('message' => 'A professor with that ID does not exist'), 404)
      );
    }

    return rest_ensure_response(array(
      'id' => $professor->ID,
      'title' => get_the_title($professor_id),
      'content' => wp_trim_words(get_the_content(null, null, $professor_id), 30),
      'related_programs' => array_map(fn($program) => $program->post_title, get_field('related_programs', $professor_id)),
      'url' => get_the_permalink($professor_id),
      'image' => array(
        'url' => get_the_post_thumbnail_url($professor_id, 'professor_portrait'),
        'caption' => get_the_post_thumbnail_caption($professor_id)
      )
    ));
  }
}

$featured_professor = new FeaturedProfessor();

