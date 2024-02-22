<?php if (!is_user_logged_in()) {
  wp_redirect(esc_url(site_url('/')));
  exit;
} ?>

<?php get_header(); ?>

<?php if (have_posts()): while (have_posts()):
  the_post();
  page_banner(); ?>

  <div class="container container--narrow page-section">
    <div class="create-note">
      <h2 class="headline headline--medium">Create new note</h2>
      <form>
        <div class="create-note__form-group">
          <label for="title">Title</label>
          <input class="new-note-title" type="text" id="title">
        </div>
        <div class="create-note__form-group">
          <label for="body">Body</label>
          <textarea class="new-note-body"id="body" placeholder="Write your notes here"></textarea>
        </div>
        <button type="submit" class="submit-note">Create Note</button>
      </form>
    </div>
    <ul class="min-list link-list" id="my-notes">
      <?php
        $notes_query = new WP_Query(array(
          'post_type' => 'note',
          'posts_per_page' => -1,
          'author' => get_current_user_id()
        ));

        if ($notes_query->have_posts()): while ($notes_query->have_posts()): $notes_query->the_post(); ?>
          <li data-note-id="<?php the_ID(); ?>">
            <input readonly class="note-title-field" type="text" value="<?php echo esc_attr(get_the_title()); ?>">
            <button class="edit-note" data-editable="false">
              <i class="fa fa-pencil" aria-hidden="true"></i>
              Edit
            </button>
            <button class="delete-note">
              <i class="fa fa-trash-o" aria-hidden="true"></i>
              Delete
            </button>
            <textarea readonly class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>
            <button class="update-note btn btn--blue btn--small">
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
              Save
            </button>
          </li>
      <?php endwhile; endif; wp_reset_postdata(); ?>
    </ul>
  </div>
<?php endwhile; endif; ?>

<?php get_footer(); ?>