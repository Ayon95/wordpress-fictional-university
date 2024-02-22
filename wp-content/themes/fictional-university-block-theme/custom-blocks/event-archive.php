<?php 
  page_banner(array(
    'title' => 'All Events',
    'subtitle' => 'See what is going on in our world.'
  ));
?>

<div class="container container--narrow page-section">
  <?php if (have_posts()): while (have_posts()):
    the_post();
    get_template_part('template-parts/content-event');
  endwhile; endif; ?>
  <?php echo paginate_links(); ?>
  <p>
    Looking for a recap of past events? Check out our <a href="<?php echo site_url('/past-events') ?>">past events archive.</a>
  </p>  
</div>