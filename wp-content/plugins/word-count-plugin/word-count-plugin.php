<?php
/**
 * Plugin Name: Word Count Plugin
 * Description: Shows post statistics such as word count, character count, and estimated time to finish reading a post
 * Version: 1.0.0
 * Author: Mushfiq
 * Author URI: https://mdmushfiqrahman.com
 * Text Domain: wcp_domain
 * Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

class WordCountPlugin {
  private const SETTINGS_PAGE_SLUG = 'word-count-settings';
  private const SETTINGS_FIELD_GROUP = 'word_count_plugin';
  private const TEXT_DOMAIN = 'wcp_domain';
  private const FIELDS = array(
    'display_location' => array(
      'slug' => 'wcp_display_location',
      'default_value' => '0'
    ),
    'title' => array(
      'slug' => 'wcp_title',
      'default_value' => 'Post Statistics'
    ),
    'show_word_count' => array(
      'slug' => 'wcp_show_word_count',
      'default_value' => '1'
    ),
    'show_char_count' => array(
      'slug' => 'wcp_show_char_count',
      'default_value' => '1'
    ),
    'show_read_time' => array(
      'slug' => 'wcp_show_read_time',
      'default_value' => '1'
    ),
  );

  function __construct() {
    add_action('admin_menu', array($this, 'settings_page'));
    add_action('admin_init', array($this, 'settings'));

    add_filter('the_content', array($this, 'process_content'));

    add_action('init', array($this, 'languages'));
  }

  function languages() {
    load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
  }

  function process_content($content) {
    if (!is_main_query() || !is_single() || get_post_type() !== 'post') return $content; 

    if ($this->should_show_word_count() || $this->should_show_char_count() || $this->should_show_read_time()) {
      $display_location = get_option(self::FIELDS['display_location']['slug'], self::FIELDS['display_location']['default_value']);

      if ($display_location === '0') {
        return $this->statistics_html($content) . $content;
      }

      return $content . $this->statistics_html($content);
    }

    return $content;
  }

  function statistics_html($content) {
    $title = esc_html(get_option(self::FIELDS['title']['slug'], self::FIELDS['title']['default_value']));
    $html = "<div class='wcp-post-statistics'><h2>{$title}</h2>";

    if ($this->should_show_word_count() || $this->should_show_read_time()) {
      $word_count = str_word_count(strip_tags($content));

      if ($this->should_show_word_count()) {
        $html .= '<p>' . esc_html__('This post has', self::TEXT_DOMAIN) . ' ' . $word_count . ' ' . esc_html__('words', self::TEXT_DOMAIN) . '.</p>';
      }

      if ($this->should_show_read_time()) {
        // Considering that an adult person can read 225 words per minute on average
        $read_time = round($word_count / 225);
  
        $html .= $read_time < 1
          ? "<p>This post will take less than a minute to read.</p>"
          : "<p>This post will take about {$read_time} minute(s) to read.</p>";
      }

    }

    if ($this->should_show_char_count()) {
      $char_count = mb_strlen(strip_tags($content), 'utf8');
      $html .= "<p>This post has {$char_count} characters.</p>";
    }
    
    return $html . '</div>';
  }

  function settings_page() {
    add_options_page(
      'Word Count Settings',
      __('Word Count', self::TEXT_DOMAIN), 
      'manage_options', 
      self::SETTINGS_PAGE_SLUG, 
      array($this, 'settings_page_html')
    );
  }

  function settings_page_html() { ?>
    <div class="wrap">
      <h1>Word Count Settings</h1>
      <p>This is the settings page for Word Count Plugin.</p>
      <form action="options.php" method="post">
        <?php
          settings_fields(self::SETTINGS_FIELD_GROUP);
          do_settings_sections(self::SETTINGS_PAGE_SLUG);
          submit_button();
        ?>
      </form>
    </div>
  <?php }

  function settings() {
    add_settings_section('wcp_fields_section', null, null, self::SETTINGS_PAGE_SLUG);

    // Field 1

    add_settings_field(
      self::FIELDS['display_location']['slug'],
      'Display Location',
      array($this, 'display_location_html'),
      self::SETTINGS_PAGE_SLUG,
      'wcp_fields_section',
    );

    register_setting(self::SETTINGS_FIELD_GROUP, self::FIELDS['display_location']['slug'], array(
      'sanitize_callback' => array($this, 'sanitize_display_location'),
      'default' => self::FIELDS['display_location']['default_value']
    ));

    // Field 2

    add_settings_field(
      self::FIELDS['title']['slug'],
      'Title',
      array($this, 'title_html'),
      self::SETTINGS_PAGE_SLUG,
      'wcp_fields_section'
    );

    register_setting(self::SETTINGS_FIELD_GROUP, self::FIELDS['title']['slug'], array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => self::FIELDS['title']['default_value']
    ));

    // Field 3

    add_settings_field(
      self::FIELDS['show_word_count']['slug'],
      'Word Count',
      array($this, 'checkbox_field_html'),
      self::SETTINGS_PAGE_SLUG,
      'wcp_fields_section',
      array(
        'field_slug' => self::FIELDS['show_word_count']['slug'], 
        'value' => self::FIELDS['show_word_count']['default_value']
      )
    );

    register_setting(self::SETTINGS_FIELD_GROUP, self::FIELDS['show_word_count']['slug'], array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => self::FIELDS['show_word_count']['default_value']
    ));

    // Field 4

    add_settings_field(
      self::FIELDS['show_char_count']['slug'],
      'Character Count',
      array($this, 'checkbox_field_html'),
      self::SETTINGS_PAGE_SLUG,
      'wcp_fields_section',
      array(
        'field_slug' => self::FIELDS['show_char_count']['slug'], 
        'value' => self::FIELDS['show_char_count']['default_value']
      )
    );

    register_setting(self::SETTINGS_FIELD_GROUP, self::FIELDS['show_char_count']['slug'], array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => self::FIELDS['show_char_count']['default_value']
    ));

    // Field 5

    add_settings_field(
      self::FIELDS['show_read_time']['slug'],
      'Read Time',
      array($this, 'checkbox_field_html'),
      self::SETTINGS_PAGE_SLUG,
      'wcp_fields_section',
      array(
        'field_slug' => self::FIELDS['show_read_time']['slug'], 
        'value' => self::FIELDS['show_read_time']['default_value']
      )
    );

    register_setting(self::SETTINGS_FIELD_GROUP, self::FIELDS['show_read_time']['slug'], array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => self::FIELDS['show_read_time']['default_value']
    ));
  }

  function sanitize_display_location($value) {
    $field_slug = self::FIELDS['display_location']['slug'];

    if ($value !== '0' && $value !== '1') {
      add_settings_error($field_slug, $field_slug . '_error', 'Display location must be either beginning or end');
      return get_option($field_slug);
    }

    return $value;
  }

  function display_location_html() {
    $field_slug = self::FIELDS['display_location']['slug'];
    ?>
    <select name="<?php echo $field_slug ?>">
      <option value="0" <?php selected(get_option($field_slug), '0'); ?>>
        Beginning of post
      </option>
      <option value="1" <?php selected(get_option($field_slug), '1'); ?>>
        End of post
      </option>
    </select>
  <?php }

  function title_html() {
    $field_slug = self::FIELDS['title']['slug'];
    ?>
    <input type="text" name="<?php echo $field_slug ?>" value="<?php echo esc_attr(get_option($field_slug)); ?>">
  <?php }

  function checkbox_field_html($args) {
    if (!isset($args['value'])) $args['value'] = '1'; ?>

    <input 
      type="checkbox"
      name="<?php echo $args['field_slug'] ?>" 
      value="<?php echo $args['value'] ?>" 
      <?php checked(get_option($args['field_slug']), $args['value']); ?>
    >
  <?php }

  function should_show_word_count() {
    return get_option(self::FIELDS['show_word_count']['slug'], self::FIELDS['show_word_count']['default_value']);
  }

  function should_show_char_count() {
    return get_option(self::FIELDS['show_char_count']['slug'], self::FIELDS['show_char_count']['default_value']);
  }

  function should_show_read_time() {
    return get_option(self::FIELDS['show_read_time']['slug'], self::FIELDS['show_read_time']['default_value']);
  }
}

$word_count_plugin = new WordCountPlugin();