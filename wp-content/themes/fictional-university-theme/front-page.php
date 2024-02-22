<?php get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/library-hero.jpg') ?>)"></div>
  <div class="page-banner__content container t-center c-white">
    <h1 class="headline headline--large">Welcome!</h1>
    <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
    <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
    <a href="<?php echo get_post_type_archive_link('program') ?>" class="btn btn--large btn--blue">Find Your Major</a>
  </div>
</div>

<div class="full-width-split group">
  <div class="full-width-split__one">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>

      <?php
        $today = date('Y-m-d H:i:s');
        // Get upcoming events (events whose dates are greater than or equal to today's date)
        $upcoming_events_query = new WP_Query(array(
          'posts_per_page' => 2,
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
            )
          )
        ));

        if ($upcoming_events_query->have_posts()):
          while ($upcoming_events_query->have_posts()):
            $upcoming_events_query->the_post();
            get_template_part('template-parts/content-event');
        endwhile; endif; wp_reset_postdata(); ?>

      <p class="t-center no-margin">
        <a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn--blue">View All Events</a>
      </p>
    </div>
  </div>
  <div class="full-width-split__two">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">From Our Blog</h2>

      <?php
        $query = new WP_Query(array(
          'posts_per_page' => 2
        ));

        if ($query->have_posts()):
          while ($query->have_posts()):
            $query->the_post(); ?>

            <article class="event-summary">
              <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>">
                <span class="event-summary__month"><?php the_time('M'); ?></span>
                <span class="event-summary__day"><?php the_time('d'); ?></span>
              </a>
              <div class="event-summary__content">
                <h5 class="event-summary__title headline headline--tiny">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h5>
                <p>
                  <?php echo has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 14); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Read more</a>
                </p>
              </div>
            </article>
        <?php endwhile; endif; wp_reset_postdata(); ?>

      <p class="t-center no-margin">
        <a href="<?php echo site_url('/blog'); ?>" class="btn btn--yellow">View All Blog Posts</a>
      </p>
    </div>
  </div>
</div>

<?php
  $slides_query = new WP_Query(array(
    'post_type' => 'slide',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
        'key' => 'page_slug',
        'compare' => '=',
        'value' => 'home'
      )
    )
  )); ?>

<?php if ($slides_query->have_posts()): ?>
  <div class="hero-slider">
    <div data-glide-el="track" class="glide__track">
      <div class="glide__slides">
        <?php while ($slides_query->have_posts()): $slides_query->the_post(); ?>
          <div class="hero-slider__slide" style="background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'page_banner'); ?>)">
            <div class="hero-slider__interior container">
              <div class="hero-slider__overlay">
                <h2 class="headline headline--medium t-center"><?php the_title(); ?></h2>
                <?php
                  $slide_subtitle = get_field('subtitle');
                  $slide_button_text = get_field('button_text');
                ?>
                <?php if ($slide_subtitle): ?>
                  <p class="t-center"><?php echo $slide_subtitle; ?></p>
                <?php endif; ?>
                <?php if ($slide_button_text): ?>
                  <p class="t-center no-margin">
                    <a href="<?php echo get_field('button_link_path'); ?>" class="btn btn--blue"><?php echo $slide_button_text; ?></a>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
      <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]"></div>
    </div>
  </div>
<?php endif; ?>

<?php get_footer(); ?>