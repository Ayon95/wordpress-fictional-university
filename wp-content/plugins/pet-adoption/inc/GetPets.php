<?php

/**
 * This class executes a SQL query to fetch pets based on URL query params  
*/ 
class GetPets {
  private string $table_name;
  private array $query_params;
  public array $pets;
  public int $total_count;

  function __construct() {
    global $wpdb;

    $this->table_name = $wpdb->prefix . 'pets';
    $this->query_params = $this->get_query_params();

    $where_text = $this->buildWhereText();

    $sql = "SELECT * FROM $this->table_name ";
    $sql .= $where_text;
    $sql .= 'LIMIT 20';

    $sql_count = "SELECT COUNT(*) FROM $this->table_name ";
    $sql_count .= $where_text;

    if (count($this->query_params) > 0) {
      $placeholder_values = array_values($this->query_params);
      $this->pets = $wpdb->get_results($wpdb->prepare($sql, $placeholder_values));
      $this->total_count = intval($wpdb->get_var($wpdb->prepare($sql_count, $placeholder_values)));
    } else {
      $this->pets = $wpdb->get_results($sql);
      $this->total_count = intval($wpdb->get_var($sql_count));
    }
  }

  function get_query_params() {
    $possible_query_params = array(
      'species', 
      'min_year', 
      'max_year', 
      'min_weight', 
      'max_weight', 
      'favorite_hobby', 
      'favorite_food', 
      'favorite_color'
    );

    $query_params = array();

    foreach ($possible_query_params as $param) {
      if (isset($_GET[$param])) {
        $query_params[$param] = sanitize_text_field($_GET[$param]);
      }
    }

    return $query_params;
  }

  /** 
   * Suppose, the query string is '?species=dog&min_weight=40'. The output will be
   * 'WHERE species = %s AND pet_weight >= %d' 
  */
  function buildWhereText() {
    $params_count = count($this->query_params);
    $where_text = '';

    if ($params_count === 0) return '';

    $where_text .= 'WHERE ';
    $params_added = 0;

    foreach ($this->query_params as $param => $value) {
      $where_text .= $this->get_where_condition($param);
      if ($params_added < $params_count - 1) {
        $where_text .= ' AND ';
      }
      $params_added++;
    }

    $where_text .= ' ';

    return $where_text;
  }

  function get_where_condition($param) {
    switch ($param) {
      case 'min_year':
        return 'birth_year >= %d';
      case 'max_year':
        return 'birth_year <= %d';
      case 'min_weight':
        return 'pet_weight >= %d';
      case 'max_weight':
        return 'pet_weight <= %d';
      default:
        return "$param = %s";
    }
  }
}