    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
    <?php if ( ! function_exists( '_wp_render_title_tag' ) ) :?>
      <title><?php wp_title( '|' , true, 'right' ); ?></title>
    <?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <style type="text/css">
    body > section {
      background: #564777;
      text-align: left;
      min-height: 400px;
 /*     width: 100%;*/
      padding: 5%;
      color: #fff;
    }
    header ul.navbar-nav { width: 100%; text-align: center;}
    header .navbar-nav > li { float:none; display: inline-block; }
    </style>
    <?php wp_head(); ?>
