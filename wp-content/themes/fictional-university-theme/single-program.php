<?php get_header(); ?>

<?php if (have_posts()): while (have_posts()):
  the_post();
  page_banner(); ?>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>">
          <i class="fa fa-home" aria-hidden="true"></i> All Programs
        </a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>

    <div class="generic-content m-bottom-large">
      <?php the_content(); ?>
    </div>

    <?php
      $related_professors = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'professor',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => array(
          array(
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '"' . get_the_ID() . '"'
          )
        )
      ));

      if ($related_professors->have_posts()): ?>
      <section class="m-bottom-2xl">
        <h2 class="headline headline--medium">
          <?php the_title(); ?> Professors
        </h2>
        <ul class="professor-cards">
          <?php while ($related_professors->have_posts()):
            $related_professors->the_post(); ?>
            <li class="professor-card__list-item">
              <a class="professor-card" href="<?php the_permalink(); ?>">
                <img class="professor-card__image" src="<?php the_post_thumbnail_url('professor_landscape'); ?>" alt="<?php the_post_thumbnail_caption(); ?>">
                <span class="professor-card__name"><?php the_title(); ?></span>
              </a>
            </li>
          <?php endwhile; ?>
        </ul>
      </section>
      <?php endif;
      
      wp_reset_postdata();

      $today = date('Y-m-d H:i:s');
      $related_upcoming_events_query = new WP_Query(array(
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
          array(
            'key' => 'event_date',
            'compare' => '>=',
            'value' => $today,
            'type' => 'DATETIME'
          ),
          array(
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '"' . get_the_ID() . '"'
          )
        )
      ));

      if ($related_upcoming_events_query->have_posts()): ?>
        <section class="m-bottom-2xl">
          <h2 class="headline headline--medium">Upcoming <?php the_title(); ?> Events</h2>
          <?php while ($related_upcoming_events_query->have_posts()):
            $related_upcoming_events_query->the_post();
            get_template_part('template-parts/content-event'); ?>    
        </section>
      <?php endwhile; endif;
      
      wp_reset_postdata();

      $related_campuses = get_field('related_campuses');

      if ($related_campuses): ?>
        <h2 class="headline headline--medium">Available At These Campuses</h2>
        <ul class="min-list link-list">
          <?php foreach($related_campuses as $campus): ?>
            <li>
              <a href="<?php the_permalink($campus) ?>"><?php echo get_the_title($campus); ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
  </div>
<?php endwhile; endif ?>

<?php get_footer(); ?>