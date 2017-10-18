<?php
/**
 * The template for displaying the header of a single post
 * In loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-header-inner">
    <?php
    do_action( '__before_regular_heading_title' );
    ?>
    <?php

    if ( get_the_title() ) :

    ?>
    <h1 class="entry-title"><?php the_title() ?></h1>
    <?php

    endif;

    if ( czr_fn_is_registered_or_possible('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
        czr_fn_edit_button( array( 'link'  => $edit_post_link ) );

    // This hook is used to render the following elements(ordered by priorities) :
    // singular thumbnail
    do_action( '__after_regular_heading_title' );
    ?>
    <div class="header-bottom">
      <div class="post-info">
        <?php

          if ( $has_meta = czr_fn_is_registered_or_possible('post_metas') ) :

        ?>
          <span class="entry-meta">
        <?php
            if ( $author = czr_fn_get_property( 'author', 'post_metas' ) )
              echo $author;

            if ( $date = czr_fn_get_property( 'publication_date', 'post_metas') )
              if ( $author ) : ?><span class="v-separator">|</span><?php endif; echo $date;

            if ( $up_date = czr_fn_get_property( 'update_date', 'post_metas') )  {
              if ( $date ) : ?><span class="v-separator">-</span><?php
              elseif( $author ) : ?><span class="v-separator">|</span><?php
              endif;

              echo $up_date;

            }
            if ( $attachment_image_info = czr_fn_get_property( 'attachment_image_info', 'post_metas' ) ) :
              if ( $date || $update || $author ) : 
                ?><span class="v-separator">-</span><?php ;
              endif;
              echo $attachment_image_info;
            endif;
              
        ?>
          </span>
        <?php endif ?>
      </div>
    </div>
  </div>
</header>