<?php
  if (!isset($block_attributes['imageUrl'])) {
    $block_attributes['imageUrl'] = get_theme_file_uri('/images/library-hero.jpg');
  }
?>

<div class="page-banner">
  <div
    class="page-banner__bg-image"
    style="background-image: url('<?php echo $block_attributes['imageUrl']; ?>');"
  ></div>
  <div class="page-banner__content container t-center c-white">
    <?php echo $block_content; ?>
  </div>
</div>