<?php

/**
 * Plugin Name: Pet Adoption
 * Description: View and manage pets for adoption in a table format
 * Version: 1.0
 * Author: Mushfiq
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'inc/generate_pet.php';

class PetAdoption {
  private string $charset_collate;
  private string $table_name;

  function __construct()
  {
    global $wpdb;
    
    // Get the database character set and collation (what set of characters can be stored in a column and how those characters are stored and compared)
    $this->charset_collate = $wpdb->get_charset_collate();
    $this->table_name = $wpdb->prefix . 'pets';

    add_action('activate_pet-adoption/pet-adoption.php', array($this, 'create_db_table'));
    
    // Uncomment the line below to populate the database table
    // add_action('admin_head', array($this, 'populate_db_table'));

    add_action('wp_enqueue_scripts', array($this, 'load_assets'));
    add_filter('template_include', array($this, 'load_template'));
  }

  function create_db_table() {
    $sql = "CREATE TABLE $this->table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      birth_year SMALLINT(5) NOT NULL DEFAULT 0,
      pet_weight SMALLINT(5) NOT NULL DEFAULT 0,
      pet_name VARCHAR(60) NOT NULL DEFAULT '',
      favorite_food VARCHAR(60) NOT NULL DEFAULT '',
      favorite_hobby VARCHAR(60) NOT NULL DEFAULT '',
      favorite_color VARCHAR(60) NOT NULL DEFAULT '',
      species VARCHAR(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  function load_assets() {
    if (is_page('pet-adoption')) {
      wp_enqueue_style('pet_adoption_css', plugin_dir_url(__FILE__) . 'pet-adoption.css');
    }
  }

  function load_template($template_path) {
    if (is_page('pet-adoption')) {
      return plugin_dir_path(__FILE__) . 'inc/template-pets.php';
    }

    return $template_path;
  }

  function populate_db_table() {
    $number_of_pets = 100000;
    $sql = "INSERT INTO $this->table_name (`species`, `birth_year`, `pet_weight`, `favorite_food`, `favorite_hobby`, `favorite_color`, `pet_name`) VALUES ";

    for ($i = 0; $i < $number_of_pets; $i++) {
      $pet = generate_pet();
      $sql .= "('{$pet['species']}', '{$pet['birth_year']}', '{$pet['pet_weight']}', '{$pet['favorite_food']}', '{$pet['favorite_hobby']}', '{$pet['favorite_color']}', '{$pet['pet_name']}')";

      // Don't add comma when it is the last pet in the insert statement
      if ($i !== $number_of_pets - 1) {
        $sql .= ', ';
      }
    }

    /*
    Never use $wpdb->query() directly like this without using $wpdb->prepare() in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming from my pet generator function so I
    know they are not malicious.
    */
    global $wpdb;
    $wpdb->query($sql);
  }  
}

$pet_adoption = new PetAdoption();