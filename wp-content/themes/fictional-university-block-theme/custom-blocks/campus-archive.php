<?php
  page_banner(array(
    'title' => 'Our Campuses',
    'subtitle' => 'We have several conveniently located campuses.'
  ));
?>

<div class="container container--narrow page-section">
  <div class="acf-map">
    <?php if (have_posts()): while (have_posts()):
      the_post();
      $campus_location = get_field('map_location')['markers'][0];
    ?>
    <div
      class="marker" 
      data-latitude='<?php echo $campus_location['lat'] ?>'
      data-longitude='<?php echo $campus_location['lng'] ?>'
    >
      <h3>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
      </h3>
      <address><?php echo $campus_location['label']; ?></address>
    </div>
    <?php endwhile; endif; ?>
  </div>
</div>