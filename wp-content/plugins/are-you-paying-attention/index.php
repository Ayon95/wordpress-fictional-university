<?php

/**
 * Plugin Name: Are You Paying Attention Quiz
 * Description: Give your readers a multiple choice question.
 * Version: 1.0
 * Author: Mushfiq
 */

//  Exit if accessed directly from the browser
if (!defined('ABSPATH')) exit;

class AreYouPayingAttention {
  function __construct() {
    add_action('init', array($this, 'add_custom_block'));
  }

  function add_custom_block() {
    wp_register_style('plugin_block_css', plugin_dir_url(__FILE__) . 'build/index.css');

    wp_register_script(
      'plugin_block_js',
      plugin_dir_url(__FILE__) . 'build/index.js',
      array('wp-blocks', 'wp-element', 'wp-editor'),
      '1.0',
      true
    );

    register_block_type('aypa-plugin/are-you-paying-attention', array(
      'editor_style_handles' => array('plugin_block_css'),
      'editor_script_handles' => array('plugin_block_js'),
      'render_callback' => array($this, 'custom_block_html')
    ));
  }

  function custom_block_html($block_attributes) {
    $this->load_frontend_output_assets();
    ob_start(); ?>
    <div class="aypa-frontend-root" data-hydrated='false' data-quiz='<?php echo base64_encode(wp_json_encode($block_attributes)); ?>'></div>
    <?php return ob_get_clean();
  }

  function load_frontend_output_assets() {
    wp_enqueue_style('plugin_block_frontend_css', plugin_dir_url(__FILE__) . 'build/frontend.css');
    wp_enqueue_script(
      'plugin_block_frontend_js',
      plugin_dir_url(__FILE__) . 'build/frontend.js',
      array('wp-element'),
      '1.0',
      true
    );
  }
}

$are_you_paying_attention = new AreYouPayingAttention();