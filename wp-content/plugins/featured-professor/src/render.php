<?php
  $professorId = intval($attributes['professorId']);
  $professor_query = new WP_Query(array(
    'post_type' => 'professor',
    'p' => $professorId
  ));

  if ($professor_query->have_posts()): while ($professor_query->have_posts()): $professor_query->the_post(); ?>
    <article <?php echo get_block_wrapper_attributes(array('class' => 'featured-professor')); ?>>
      <?php the_post_thumbnail('professor_portrait', array('class' => 'featured-professor__photo')); ?>
      <div class="featured-professor__text">
        <h3><?php the_title(); ?></h3>
        <p><?php echo wp_trim_words(get_the_content(), 30); ?></p>
        <?php 
          $related_programs = array_map(fn($program) => get_the_title($program), get_field('related_programs'));
          if (count($related_programs) > 0): ?>
            <p>Programs taught: <?php echo implode(', ', $related_programs); ?></p>
          <?php endif; ?>
        <a href="<?php the_permalink(); ?>">Learn more about <?php the_title(); ?> &raquo;</a>
      </div>
    </article>
  <?php endwhile; endif; wp_reset_postdata(); ?>

