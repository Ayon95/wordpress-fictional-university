<?php
/**
 * Plugin Name: Word Filter Plugin
 * Description: Replaces a list of words with a replacement string of your choice
 * Version: 1.0.0
 * Author: Mushfiq
 * Author URI: https://mdmushfiqrahman.com
*/

if (!defined('ABSPATH')) exit;

class WordFilterPlugin {
  private const MENU_PAGE_SLUG = 'word-filter';
  private const OPTIONS_PAGE_SLUG = self::MENU_PAGE_SLUG . '-options';
  private const OPTIONS_PAGE_FIELD_GROUP = 'wf_options_page_group';

  function __construct() {
    add_action('admin_menu', array($this, 'plugin_menu'));
    add_action('admin_init', array($this, 'options_page_settings'));

    add_filter('the_content', array($this, 'filter_content'));
  }

  function plugin_menu() {
    $main_page_hook_suffix = add_menu_page(
      'Words To Filter',
      'Word Filter',
      'manage_options',
      self::MENU_PAGE_SLUG,
      array($this, 'menu_page_html'),
      'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+',
      100
    );

    // By default, WordPress adds a submenu page which is the same as the menu page
    // By explicitly defining that submenu page, we can change its menu title
    add_submenu_page(
      self::MENU_PAGE_SLUG,
      'Words To Filter',
      'Words List',
      'manage_options',
      self::MENU_PAGE_SLUG,
      array($this, 'menu_page_html')
    );

    add_submenu_page(
      self::MENU_PAGE_SLUG,
      'Word Filter Options',
      'Options',
      'manage_options',
      self::OPTIONS_PAGE_SLUG,
      array($this, 'options_submenu_page_html')
    );

    add_action("load-{$main_page_hook_suffix}", array($this, 'main_page_assets'));
  }

  function main_page_assets() {
    wp_enqueue_style('word_filter_main_page_css', plugin_dir_url(__FILE__) . 'styles.css');
  }

  function menu_page_html() {?>
    <div class="wrap">
      <h1>Word Filter</h1>
      <?php if (isset($_POST['submitted']) && $_POST['submitted'] === 'true') $this->handle_form_submit(); ?>
      <form method="post" class="word-filter__form">
        <?php wp_nonce_field('save_filter_words', 'wf_filter_list_nonce'); ?>
        <input type="hidden" name="submitted" value="true">
        <div class="word-filter__form-group">
          <label for="wf_words_to_filter">
            A <strong>comma-separated</strong> list of words to filter from your site's content
          </label>
          <textarea name="wf_words_to_filter" id="wf_words_to_filter" placeholder="bad,mean,awful,horrible"><?php echo esc_textarea(get_option('wf_words_to_filter')); ?></textarea>
        </div>  
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
      </form>
    </div>
  <?php }

  function handle_form_submit() {
    $is_valid_nonce = wp_verify_nonce($_POST['wf_filter_list_nonce'], 'save_filter_words');
    if (current_user_can('manage_options') && $is_valid_nonce) {
      update_option('wf_words_to_filter', sanitize_text_field($_POST['wf_words_to_filter'])); ?>

      <div class="updated">
        <p>Your filtered words have been saved.</p>
      </div>
    <?php } else { ?>
      <div class="error">
        <p>Sorry, you do not have permission to perform this action.</p>
      </div>
    <?php }
  }

  function filter_content($content) {
    if (!get_option('wf_words_to_filter')) return $content;

    $words_to_filter = explode(',', get_option('wf_words_to_filter'));
    $trimmed_words = array_map('trim', $words_to_filter);
    $replacement_text = esc_html(get_option('wf_replacement_text', '****'));
    $filtered_content = str_ireplace($trimmed_words, $replacement_text, $content);

    return $filtered_content;
  }

  function options_submenu_page_html() {?>
    <div class="wrap">
      <h1>Word Filter Options</h1>
      <?php settings_errors(); ?>
      <form action="options.php" method="post">
        <?php
          settings_fields(self::OPTIONS_PAGE_FIELD_GROUP);
          do_settings_sections(self::OPTIONS_PAGE_SLUG);
          submit_button();
        ?>
      </form>
    </div>
  <?php }

  function options_page_settings() {
    add_settings_section('options_page_fields_section', null, null, self::OPTIONS_PAGE_SLUG);

    register_setting(self::OPTIONS_PAGE_FIELD_GROUP, 'wf_replacement_text', array(
      'sanitize_callback' => 'sanitize_text_field'
    ));

    add_settings_field(
      'wf_replacement_text', 
      'Replacement Text', 
      array($this, 'replacement_text_html'),
      self::OPTIONS_PAGE_SLUG,
      'options_page_fields_section'
    );
  }

  function replacement_text_html() {?>
    <input type="text" name="wf_replacement_text" value="<?php echo esc_attr(get_option('wf_replacement_text', '****')) ?>">
    <p class="description">Leave blank to simply remove the filtered words.</p>
  <?php }
}

$word_filter_plugin = new WordFilterPlugin();