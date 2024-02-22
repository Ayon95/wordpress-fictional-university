<header class="site-header">
  <div class="container">
    <h1 class="school-logo-text float-left">
      <a href="<?php echo site_url(); ?>"><strong>Fictional</strong> University</a>
    </h1>
    <span class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
    <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
    <div class="site-header__menu group">
      <nav class="main-navigation">
        <?php
          wp_nav_menu(array(
            'theme_location' => 'header_menu_location'
          ));
        ?>
      </nav>
      <div class="site-header__util">
        <?php if (is_user_logged_in()): ?>
          <a href="<?php echo wp_logout_url(); ?>" class="btn btn--small btn--dark-orange float-left">
            Log Out
          </a>
        <?php else: ?>
          <a href="<?php echo wp_login_url(); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
          <a href="<?php echo wp_registration_url(); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>
        <?php endif; ?>
        <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
      </div>
    </div>
  </div>
</header>