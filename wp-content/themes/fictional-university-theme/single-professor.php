<?php get_header(); ?>

<?php if (have_posts()): while (have_posts()):
  the_post();
  page_banner(); ?>

  <div class="container container--narrow page-section">
    <div class="generic-content">
      <?php if (get_the_post_thumbnail(null, 'professor_portrait')): ?>
        <div class="row group">
        <div class="one-third">
          <?php the_post_thumbnail('professor_portrait'); ?>
        </div>
        <div class="two-thirds">
          <?php
            professor_like_box(get_the_ID());
            the_content();
          ?>
        </div>
      </div> 
      <?php else:
        professor_like_box(get_the_ID());
        the_content();  
      endif; ?> 
    </div>
    <?php
    $related_programs = get_field('related_programs');

    if ($related_programs): ?>
      <h2 class="headline headline--medium">Subjects Taught</h2>
      <ul class="link-list min-list">
        <?php foreach($related_programs as $program): ?>
          <li>
            <a href="<?php echo get_the_permalink($program); ?>">
              <?php echo get_the_title($program); ?>
            </a>
          </li>
      </ul>
    <?php endforeach; endif; ?>
  </div>
<?php endwhile; endif ?>

<?php get_footer(); ?>