<?php 
  get_header();

  page_banner(array(
    'title' => is_category() ? single_cat_title('', false) : (is_author() ? 'Posts by ' . get_the_author() : get_the_archive_title()),
    'subtitle' => get_the_archive_description()
  ));
?>

<div class="container container--narrow page-section">
  <?php if (have_posts()): while (have_posts()): the_post(); ?>
    <article class="post-item">
      <h2 class="headline headline--medium headline--post-title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
      </h2>
      <div class="metabox">
        <p>
          Posted by <?php the_author_posts_link(); ?> on <?php echo get_the_date(); ?> in <?php echo get_the_category_list(', '); ?>
        </p>
      </div>
      <div class="generic-content">
        <?php the_excerpt(); ?>
        <p>
          <a href="<?php the_permalink() ?>" class="btn btn--blue">Continue reading</a>
        </p>
      </div>
    </article>
  <?php endwhile; endif; ?>
  <?php echo paginate_links(); ?>  
</div>

<?php get_footer(); ?>