<?php
require_once plugin_dir_path(__FILE__) . 'GetPets.php';
$get_pets = new GetPets();
get_header();
?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Pet Adoption</h1>
    <div class="page-banner__intro">
      <p>Providing forever homes one search at a time.</p>
    </div>
  </div>  
</div>

<div class="container container--narrow page-section">
  <?php $pets_count = count($get_pets->pets); ?>
  <p>
    This page took <strong><?php echo timer_stop();?></strong> seconds to prepare. 
    Found <strong><?php echo $get_pets->total_count; ?></strong> results (showing the first <?php echo count($get_pets->pets) ?>).
  </p>

  <?php 
    $pets = $get_pets->pets;
  ?>

  <table class="pet-adoption-table">
    <tr>
      <th>Name</th>
      <th>Species</th>
      <th>Weight</th>
      <th>Birth Year</th>
      <th>Hobby</th>
      <th>Favorite Color</th>
      <th>Favorite Food</th>
    </tr>
    <?php foreach ($pets as $pet): ?>
      <tr>
        <td><?php echo $pet->pet_name; ?></td>
        <td><?php echo $pet->species; ?></td>
        <td><?php echo $pet->pet_weight; ?></td>
        <td><?php echo $pet->birth_year; ?></td>
        <td><?php echo $pet->favorite_hobby; ?></td>
        <td><?php echo $pet->favorite_color; ?></td>
        <td><?php echo $pet->favorite_food; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  
</div>

<?php get_footer(); ?>