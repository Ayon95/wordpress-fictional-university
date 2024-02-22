<?php

function create_event_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Events',
      'singular_name' => 'Event',
      'all_items' => 'All Events',
      'add_new' => 'Add New Event',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'search_items' => 'Search Events'
    ), 
    'public' => true,
    'has_archive' => true,
    'show_in_rest' => true,
    'capability_type' => 'event',
    'map_meta_cap' => true,
    'menu_icon' => 'dashicons-calendar',
    'rewrite' => array(
      'slug' => 'events'
    ),
    'supports' => array('title', 'editor', 'excerpt')
  );

  register_post_type('event', $args);
}

function create_program_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Programs',
      'singular_name' => 'Program',
      'all_items' => 'All Programs',
      'add_new' => 'Add New Program',
      'add_new_item' => 'Add New Program',
      'edit_item' => 'Edit Program',
      'search_items' => 'Search Programs'
    ), 
    'public' => true,
    'has_archive' => true,
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-awards',
    'rewrite' => array(
      'slug' => 'programs'
    )
  );

  register_post_type('program', $args);
}

function create_professor_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Professors',
      'singular_name' => 'Professor',
      'all_items' => 'All Professors',
      'add_new' => 'Add New Professor',
      'add_new_item' => 'Add New Professor',
      'edit_item' => 'Edit Professor',
      'search_items' => 'Search Professors'
    ), 
    'public' => true,
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-welcome-learn-more',
    'supports' => array('title', 'editor', 'thumbnail'),
  );

  register_post_type('professor', $args);
}

function create_campus_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Campuses',
      'singular_name' => 'Campus',
      'all_items' => 'All Campuses',
      'add_new' => 'Add New Campus',
      'add_new_item' => 'Add New Campus',
      'edit_item' => 'Edit Campus',
      'search_items' => 'Search Campuses'
    ), 
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'capability_type' => array('campus', 'campuses'),
    'map_meta_cap' => true,
    'menu_icon' => 'dashicons-location-alt',
    'rewrite' => array(
      'slug' => 'campuses'
    )
  );

  register_post_type('campus', $args);
}

function create_note_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Notes',
      'singular_name' => 'Note',
      'all_items' => 'All Notes',
      'add_new' => 'Add New Note',
      'add_new_item' => 'Add New Note',
      'edit_item' => 'Edit Note',
      'search_items' => 'Search Note'
    ),
    'show_in_rest' => true,
    // Notes should be private and specific to each registered user
    'public' => false,
    // Show notes in the admin dashboard
    'show_ui' => true,
    'menu_icon' => 'dashicons-welcome-write-blog',
    'capability_type' => 'note',
    'map_meta_cap' => true
  );

  register_post_type('note', $args);
}

// Create like post type for professor like count

function create_like_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Likes',
      'singular_name' => 'Like',
      'all_items' => 'All Likes',
      'add_new' => 'Add New Like',
      'add_new_item' => 'Add New Like',
      'edit_item' => 'Edit Like',
      'search_items' => 'Search Like'
    ),
    'public' => false,
    'show_ui' => true,
    'supports' => array('title'),
    'menu_icon' => 'dashicons-heart',
    'capability_type' => 'note',
    'map_meta_cap' => true
  );

  register_post_type('like', $args);
}

// Custom post type for slideshow slides

function create_slide_post_type() {
  $args = array(
    'labels' => array(
      'name' => 'Slides',
      'singular_name' => 'Slide',
      'all_items' => 'All Slides',
      'add_new' => 'Add New Slide',
      'add_new_item' => 'Add New Slide',
      'edit_item' => 'Edit Slide',
      'search_items' => 'Search Slide'
    ),
    'public' => true,
    'supports' => array('title', 'thumbnail'),
    'menu_icon' => 'dashicons-format-video',
  );

  register_post_type('slide', $args);
}

add_action('init', 'create_event_post_type');
add_action('init', 'create_program_post_type');
add_action('init', 'create_professor_post_type');
add_action('init', 'create_campus_post_type');
add_action('init', 'create_note_post_type');
add_action('init', 'create_like_post_type');
add_action('init', 'create_slide_post_type');