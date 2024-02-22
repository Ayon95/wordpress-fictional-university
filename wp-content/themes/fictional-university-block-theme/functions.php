<?php

// REST API

require_once get_theme_file_path('/includes/search-api-route.php');

function customize_rest_response() {
  register_rest_field('post', 'author_name', array(
    'get_callback' => fn() => get_the_author()
  ));
}

add_action('rest_api_init', 'customize_rest_response');

// Loading assets

function load_assets() {

  wp_register_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('font_awesome');

  wp_register_style('site_font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('site_font');

  wp_register_style('leaflet_css', '//unpkg.com/leaflet@1.8.0/dist/leaflet.css');
  wp_enqueue_style('leaflet_css');

  wp_register_script('leaflet_js', '//unpkg.com/leaflet@1.8.0/dist/leaflet.js', array(), '1.0', true);
  wp_enqueue_script('leaflet_js');

  wp_register_style('main_style', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('main_style');

  wp_register_style('extra_style', get_theme_file_uri('/build/index.css'));
  wp_enqueue_style('extra_style');
  
  wp_register_script('main_js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_script('main_js');

  wp_add_inline_script(
    'main_js', 
    'const dataFromWp = ' . json_encode(array(
      'siteUrl' => get_site_url()
    )) . ';', 
    'before'
  );
}

add_action('wp_enqueue_scripts', 'load_assets');

// Enabling theme support for features

function enable_features() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('editor-styles');
  add_editor_style(array(
    'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i',
    'build/style-index.css',
    'build/index.css'
  ));
}

add_action('after_setup_theme', 'enable_features');

function add_custom_image_sizes() {
  add_image_size('page_banner', 1500, 350, true);

  add_image_size('professor_landscape', 400, 260, true);
  add_image_size('professor_portrait', 480, 650, true);
}

add_action('after_setup_theme', 'add_custom_image_sizes');

// Navigation Menu

function add_nav_menu_locations () {
  register_nav_menus(array(
    'header_menu_location' => esc_html__('Header Menu Location'),
    'footer_menu_location_one' => esc_html__('Footer Menu Location One'),
    'footer_menu_location_two' => esc_html__('Footer Menu Location Two')
  ));
}

add_action('after_setup_theme', 'add_nav_menu_locations');

// Add a CSS class to various menu items to show active link styles
function header_current_menu_item_class($classes, $menu_item, $args) {
  $post_type = get_post_type();

  if ($args->theme_location === 'header_menu_location') {
    if ($menu_item->title === 'Blog' && $post_type === 'post') {
      $classes[] = 'current-menu-item';
    } else if ($menu_item->title === 'Events' && ($post_type === 'event' || is_page('past-events'))) {
      $classes[] = 'current-menu-item';
    } else if ($menu_item->title === 'Programs' && $post_type === 'program') {
      $classes[] = 'current-menu-item';
    } else if ($menu_item->title === 'Campuses' && $post_type === 'campus') {
      $classes[] = 'current-menu-item';
    }
  }

  return $classes;
}

add_filter('nav_menu_css_class', 'header_current_menu_item_class', 10, 3);

// Modify events archive page query

function modify_event_archive_query($query) {
  // is_main_query check -> to avoid modifying a custom query accidentally
  if (!is_admin() && $query->is_main_query() && is_post_type_archive('event')) {
    $today = date('Y-m-d H:i:s');

    // Show upcoming events (event date is greater than or equal to today's date)

    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value');
    $query->set('order', 'ASC');
    $query->set('meta_query', array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'DATETIME'
      )
    ));
  }
}

add_action('pre_get_posts', 'modify_event_archive_query');

function modify_program_archive_query($query) {
  if (!is_admin() && $query->is_main_query() && is_post_type_archive('program')) {
    $query->set('posts_per_page', -1);
    $query->set('orderby', 'title');
    $query->set('order', 'ASC');
  }
}

add_action('pre_get_posts', 'modify_program_archive_query');

function modify_campus_archive_query($query) {
 if (!is_admin() && $query->is_main_query() && is_post_type_archive('campus')) {
  $query->set('post_per_page', -1);
 } 
}

add_action('pre_get_posts', 'modify_campus_archive_query');

function page_banner($args = array()) {
  if (!isset($args['title'])) {
    $args['title'] = get_the_title();
  }

  if (!isset($args['subtitle'])) {
    $banner_subtitle = get_field('banner_subtitle');
    $args['subtitle'] = $banner_subtitle ? $banner_subtitle : '';
  }

  if (!isset($args['image'])) {
    $banner_image = get_field('banner_image');
    $fallback_image_url = get_theme_file_uri('/images/ocean.jpg');
    // In case of an archive page or the blog listing page (home), we don't want to use the image (if any) of the first post as the banner image of the entire page
    $args['image'] = $banner_image && !is_archive() && !is_home() ? $banner_image['sizes']['page_banner'] : $fallback_image_url;
  }


  ?>
    <div class="page-banner">
      <div
        class="page-banner__bg-image"
        style="background-image: url(<?php echo $args['image'] ?>)">
      </div>
      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
        <?php
          if ($args['subtitle']): ?>
            <div class="page-banner__intro">
              <p><?php echo $args['subtitle']; ?></p>
            </div>
          <?php endif;?>
      </div>
    </div>
  <?php 
}

// Redirect logged-in subscribers to the home page

function redirect_subscribers_to_home() {
  $current_user = wp_get_current_user();

  if (count($current_user->roles) === 1 && $current_user->roles[0] === 'subscriber') {
    wp_safe_redirect(site_url('/'));
    exit;
  }
}

add_action('admin_init', 'redirect_subscribers_to_home');

function hide_admin_bar_for_subscribers() {
  $current_user = wp_get_current_user();

  if (count($current_user->roles) === 1 && $current_user->roles[0] === 'subscriber') {
    show_admin_bar(false);
  }
}

add_action('wp_loaded', 'hide_admin_bar_for_subscribers');

// Customize Login page

function load_login_custom_styles() {
  wp_register_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('font_awesome');

  wp_register_style('site_font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('site_font');

  wp_register_style('main_style', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('main_style');

  wp_register_style('extra_style', get_theme_file_uri('/build/index.css'));
  wp_enqueue_style('extra_style');
}

add_action('login_enqueue_scripts', 'load_login_custom_styles');

function change_login_page_logo_url() {
  // The logo should link to the home page
  return esc_url(site_url('/'));
}

add_filter('login_headerurl', 'change_login_page_logo_url');

function change_login_header_link_text() {
  return get_bloginfo('name');
}

add_filter('login_headertext', 'change_login_header_link_text');

// Custom blocks

class JSXBlock {
  private string $block_name;
  private bool $use_render_callback;
  private mixed $data_for_js;

  function __construct(string $block_name, bool $use_render_callback = false, mixed $data_for_js = null) {
    $this->block_name = $block_name;
    $this->use_render_callback = $use_render_callback;
    $this->data_for_js = $data_for_js;

    add_action('init', array($this, 'add_jsx_block'));
  }

  function add_jsx_block() { 
    wp_register_script(
      "{$this->block_name}_block_js", 
      get_theme_file_uri("/build/{$this->block_name}.js"), 
      array('wp-blocks', 'wp-editor'), 
      '1.0', 
      true
    );

    if ($this->data_for_js) {
      wp_add_inline_script(
        "{$this->block_name}_block_js", 
        'let dataFromWp = ' . json_encode($this->data_for_js) . ';',
        'before'
      );
    }


    $args = array(
      'editor_script_handles' => array("{$this->block_name}_block_js")
    );

    if ($this->use_render_callback) {
      $args['render_callback'] = array($this, 'render_callback');
    }

    register_block_type("fictional-university/{$this->block_name}", $args);
  }

  function render_callback($block_attributes, $block_content) {
    ob_start();
    require get_theme_file_path("/custom-blocks/{$this->block_name}.php");
    return ob_get_clean();
  }
}

new JSXBlock('banner', true);
new JSXBlock('generic-heading');
new JSXBlock('generic-button');
new JSXBlock('slideshow', true);
new JSXBlock('slide', true, array('theme_images_folder_path' => get_theme_file_uri('/images')));

// Placeholder blocks will not have any JSX, so no JSX processing by @wordpress/scripts is required

class PlaceholderBlock {
  private string $block_name;

  function __construct(string $block_name) {
    $this->block_name = $block_name;
    add_action('init', array($this, 'add_placeholder_block'));
  }

  function add_placeholder_block() {
    wp_register_script(
      "{$this->block_name}_block_js",
      get_theme_file_uri("/custom-blocks/{$this->block_name}.js"),
      array('wp-blocks', 'wp-editor'), 
      '1.0', 
      true
    );

    $args = array(
      'editor_script_handles' => array("{$this->block_name}_block_js"),
      'render_callback' => array($this, 'render_callback')
    );

    register_block_type("fictional-university/{$this->block_name}", $args);
  }

  function render_callback() {
    ob_start();
    require get_theme_file_path("/custom-blocks/{$this->block_name}.php");
    return ob_get_clean();
  }
}

new PlaceholderBlock('header');
new PlaceholderBlock('footer');
new PlaceholderBlock('events-and-blogs');
new PlaceholderBlock('single-post');
new PlaceholderBlock('page');
new PlaceholderBlock('blog-index');
new PlaceholderBlock('program-archive');
new PlaceholderBlock('single-program');
new PlaceholderBlock('single-professor');
new PlaceholderBlock('event-archive');
new PlaceholderBlock('single-event');
new PlaceholderBlock('past-events-page');
new PlaceholderBlock('campus-archive');
new PlaceholderBlock('single-campus');

// Utilities

function pretty_print($value) {
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}