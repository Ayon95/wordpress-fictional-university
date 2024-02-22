<?php

  // Use the default image if it exists
  if (!empty($block_attributes['defaultImageFilename'])) {
    $block_attributes['imageUrl'] = get_theme_file_uri("/images/" . $block_attributes['defaultImageFilename']);
  }

  if (empty($block_attributes['imageUrl'])) {
    $block_attributes['imageUrl'] = get_theme_file_uri('/images/library-hero.jpg');
  }
?>

<div
  class="hero-slider__slide"
  style="background-image: url('<?php echo $block_attributes['imageUrl']; ?>');"
>
  <div class="hero-slider__interior container">
    <div class="hero-slider__overlay t-center">
      <?php echo $block_content; ?>
    </div>
  </div>
</div>