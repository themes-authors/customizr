<?php
/**
* Defines the customizer setting map
* On live context, used to generate the default option values
*
*
*/


/**
* Defines sections, settings and function of customizer and return and array
* Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
*
* @package Customizr
* @since Customizr 3.0
*/
function czr_fn_get_customizer_map( $get_default = null,  $what = null ) {

  if ( ! empty( CZR___::$customizer_map ) ) {
    $_customizer_map = CZR___::$customizer_map;
  }
  else {
    //POPULATE THE MAP WITH DEFAULT CUSTOMIZR SETTINGS
    add_filter( 'czr_add_panel_map'           , 'czr_fn_popul_panels_map');
    add_filter( 'czr_remove_section_map'      , 'czr_fn_popul_remove_section_map');
    //theme switcher's enabled when user opened the customizer from the theme's page
    add_filter( 'czr_remove_section_map'      , 'czr_fn_set_theme_switcher_visibility');
    add_filter( 'czr_add_section_map'         , 'czr_fn_popul_section_map');
    //add controls to the map
    add_filter( 'czr_add_setting_control_map' , 'czr_fn_popul_setting_control_map', 10, 2 );


    //FILTER SPECIFIC SETTING-CONTROL MAPS
    //ADDS SETTING / CONTROLS TO THE RELEVANT SECTIONS
    add_filter( 'czr_fn_front_page_option_map', 'czr_fn_generates_featured_pages');

    //POST LIST
    //classical grid
    add_filter( 'czr_fn_post_list_option_map', 'czr_fn_classical_grid_option_map' , 20, 2 );
    //alternate
    add_filter( 'czr_fn_post_list_option_map', 'czr_fn_alternate_option_map' , 20, 2 );
    //archive titles
    add_filter( 'czr_fn_post_list_option_map', 'czr_fn_archive_titles_option_map' , 20, 2 );

    //CACHE THE GLOBAL CUSTOMIZER MAP
    $_customizer_map = array_merge(
      array( 'add_panel'           => apply_filters( 'czr_add_panel_map', array() ) ),
      array( 'remove_section'      => apply_filters( 'czr_remove_section_map', array() ) ),
      array( 'add_section'         => apply_filters( 'czr_add_section_map', array() ) ),
      array( 'add_setting_control' => apply_filters( 'czr_add_setting_control_map', array(), $get_default ) )
    );
    CZR___::$customizer_map = $_customizer_map;
  }
  if ( is_null($what) ) {
    return apply_filters( 'czr_customizer_map', $_customizer_map );
  }

  $_to_return = $_customizer_map;
  switch ( $what ) {
      case 'add_panel':
        $_to_return = $_customizer_map['add_panel'];
      break;
      case 'remove_section':
        $_to_return = $_customizer_map['remove_section'];
      break;
      case 'add_section':
        $_to_return = $_customizer_map['add_section'];
      break;
      case 'add_setting_control':
        $_to_return = $_customizer_map['add_setting_control'];
      break;
  }
  return $_to_return;
}



/**
* Populate the control map
* hook : 'czr_fn_add_setting_control_map'
* => loops on a callback list, each callback is a section setting group
* @return array()
*
* @package Customizr
* @since Customizr 3.3+
*/
function czr_fn_popul_setting_control_map( $_map, $get_default = null ) {
  $_new_map = array();
  $_settings_sections = array(
    //GLOBAL SETTINGS
    'czr_fn_logo_favicon_option_map',
    'czr_fn_skin_option_map',
    'czr_fn_fonts_option_map',
    'czr_fn_social_option_map',
    //'czr_fn_icons_option_map', //Removed in c4
    'czr_fn_links_option_map',
    'czr_fn_images_option_map',
    //'czr_fn_responsive_option_map', //Removed in c4, handled with Bootstrap css
    'czr_fn_authors_option_map',
    'czr_fn_smoothscroll_option_map',
    //HEADER
    'czr_fn_header_design_option_map',
    'czr_fn_navigation_option_map',
    //CONTENT
    'czr_fn_front_page_option_map',
    'czr_fn_layout_option_map',
    'czr_fn_comment_option_map',
    'czr_fn_breadcrumb_option_map',
    'czr_fn_post_metas_option_map',
    'czr_fn_post_list_option_map',
    'czr_fn_single_post_option_map',
    'czr_fn_single_page_option_map',
    //'czr_fn_gallery_option_map', //No gallery options in c4 as of now
    'czr_fn_paragraph_option_map',
    'czr_fn_post_navigation_option_map',
    //SIDEBARS
    'czr_fn_sidebars_option_map',
    //FOOTER
    'czr_fn_footer_global_settings_option_map',
    //ADVANCED OPTIONS
    'czr_fn_custom_css_option_map',
    'czr_fn_performance_option_map',
    'czr_fn_placeholders_notice_map',
    'czr_fn_external_resources_option_map'
  );

  $_settings_sections = apply_filters( 'czr_settings_sections', $_settings_sections );

  foreach ( $_settings_sections as $_section_cb ) {
    if ( ! function_exists( $_section_cb ) )
      continue;
    //applies a filter to each section settings map => allows plugins (featured pages for ex.) to add/remove settings
    //each section map takes one boolean param : $get_default
    $_section_map = apply_filters(
      $_section_cb,
      call_user_func_array( $_section_cb, array( $get_default ) )
    );

    if ( ! is_array( $_section_map) )
      continue;

    $_new_map = array_merge( $_new_map, $_section_map );
  }//foreach
  return array_merge( $_map, $_new_map );
}


/******************************************************************************************************
*******************************************************************************************************
* PANEL : GLOBAL SETTINGS
*******************************************************************************************************
******************************************************************************************************/

/*-----------------------------------------------------------------------------------------------------
                               LOGO & FAVICON SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_logo_favicon_option_map( $get_default = null ) {
  global $wp_version;
  return array(
          'tc_logo_upload'  => array(
                            'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                            'label'     =>  __( 'Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                            'title'     => __( 'LOGO' , 'customizr'),
                            'section'   => 'logo_sec',
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                    //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                            'width'     => 250,
                            'height'    => 70,
                            'flex_width' => true,
                            'flex_height' => true,
                            //to keep the selected cropped size
                            'dst_width'  => false,
                            'dst_height'  => false
          ),
          //force logo resize 250 * 85
          'tc_logo_resize'  => array(
                            'default'   =>  1,
                            'label'     =>  __( 'Force logo height to max-height:70px' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'   =>  'logo_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( "Uncheck this option to keep your original logo dimensions." , 'customizr')
          ),
          'tc_sticky_logo_upload'  => array(
                            'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                            'label'     =>  __( 'Sticky Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                            'section'   =>  'logo_sec' ,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                    //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                            'width'     => 75,
                            'height'    => 30,
                            'flex_width' => true,
                            'flex_height' => true,
                            //to keep the selected cropped size
                            'dst_width'  => false,
                            'dst_height'  => false,
                            'notice'    => __( "Use this upload control to specify a different logo on sticky header mode." , 'customizr')
          ),
  );
}

/*-----------------------------------------------------------------------------------------------------
                                  SKIN SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_skin_option_map( $get_default = null ) {
  return array(
          //skin select
          /*'tc_skin'     => array(
                            'default'   =>  'blue3.css' ,
                            'control'   => 'CZR_controls' ,
                            'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                            'section'   =>  'skins_sec' ,
                            'type'      =>  'select' ,
                            'choices'    =>  czr_fn_build_skin_list(),
                            'transport'   =>  'postMessage',
                            'notice'    => __( 'Disabled if the random option is on.' , 'customizr' )
          ),
          'tc_skin_random' => array(
                            'default'   => 0,
                            'control'   => 'CZR_controls',
                            'label'     => __('Randomize the skin', 'customizr'),
                            'section'   => 'skins_sec',
                            'type'      => 'checkbox',
                            'notice'    => __( 'Apply a random color skin on each page load.' , 'customizr' )
          ),
          */
          'tc_skin_color' => array(
                            'default'     => '#B4B4B4',
                            'control'     => 'WP_Customize_Color_Control',
                            'label'       => __( 'Skin color' , 'customizr' ),
                            'section'     => 'skins_sec',
                            'type'        =>  'color' ,
                            'priority'    => 30,
                            'sanitize_callback'    => 'czr_fn_sanitize_hex_color',
                            'sanitize_js_callback' => 'maybe_hash_hex_color',
                            'transport'   => 'refresh' //postMessage
          ),
    );//end of skin options
}



/*-----------------------------------------------------------------------------------------------------
                                 FONT SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_fonts_option_map( $get_default = null ) {
  return array(
          'tc_fonts'      => array(
                            'default'       => czr_fn_user_started_before_version( '3.4.39' , '1.2.39') ? '_g_fjalla_cantarell': '_g_poppins',
                            'label'         => __( 'Select a beautiful font pair (headings &amp; default fonts) or single font for your website.' , 'customizr' ),
                            'control'       =>  'CZR_controls',
                            'section'       => 'fonts_sec',
                            'type'          => 'select' ,
                            'choices'       => czr_fn_get_font( 'list' , 'name' ),
                            'priority'      => 10,
                            'transport'     => 'postMessage',
                            'notice'        => __( "This font picker allows you to preview and select among a handy selection of font pairs and single fonts. If you choose a pair, the first font will be applied to the site main headings : site name, site description, titles h1, h2, h3., while the second will be the default font of your website for any texts or paragraphs." , 'customizr' )
          ),
          'tc_body_font_size'      => array(
                            'default'       => czr_fn_user_started_before_version( '3.2.9', '1.0.1' ) ? 14 : 15,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'label'         => __( 'Set your website default font size in pixels.' , 'customizr' ),
                            'control'       =>  'CZR_controls',
                            'section'       => 'fonts_sec',
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 0,
                            'priority'      => 20,
                            'transport'     => 'postMessage',
                            'notice'        => __( "This option sets the default font size applied to any text element of your website, when no font size is already applied." , 'customizr' )
          )
  );
}


/*-----------------------------------------------------------------------------------------------------
                         SOCIAL NETWORKS + POSITION SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_social_option_map( $get_default = null  ) {
  return array(
      'tc_social_links' => array(
            'default'   => array(),//empty array by default
            'control'   => 'CZR_Customize_Modules',
            'label'     => __('Create and organize your social links', 'customizr'),
            'section'   => 'socials_sec',
            'type'      => 'czr_module',
            'module_type' => 'czr_social_module',
            'transport' => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
            'priority'  => 10,
      )
  );
}


/*-----------------------------------------------------------------------------------------------------
                               LINKS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_links_option_map( $get_default = null ) {
  return array(
          'tc_link_scroll'  =>  array(
                            'default'       => 0,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Smooth scroll on click' , 'customizr' ),
                            'section'     => 'links_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'      => sprintf( '%s<br/><strong>%s</strong> : %s', __( 'If enabled, this option activates a smooth page scroll when clicking on a link to an anchor of the same page.' , 'customizr' ), __( 'Important note' , 'customizr' ), __('this option can create conflicts with some plugins, make sure that your plugins features (if any) are working fine after enabling this option.', 'customizr') )
          ),
          'tc_link_hover_effect'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Fade effect on link hover" , "customizr" ),
                            'section'       => 'links_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 20,
                            'transport'   => 'postMessage'
          ),

          'tc_ext_link_style'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display an icon next to external links" , "customizr" ),
                            'section'       => 'links_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 30,
                            'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                            'transport'     => 'postMessage'
          ),

          'tc_ext_link_target'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Open external links in a new tab" , "customizr" ),
                            'section'       => 'links_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 40,
                            'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                            'transport'     => 'postMessage'
          )
  );//end of links options
}


/* Removed in c4 */
/*-----------------------------------------------------------------------------------------------------
                               ICONS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_icons_option_map( $get_default = null ) {
  return array(
          'tc_show_title_icon'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display icons next to titles" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 10,
                            'notice'    => __( 'When this option is checked, a contextual icon is displayed next to the titles of pages, posts, archives, and WP built-in widgets.' , 'customizr' ),
                            'transport'   => 'postMessage'
          ),
          'tc_show_page_title_icon'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display a page icon next to the page title" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 20,
                            'transport'   => 'postMessage'
          ),
          'tc_show_post_title_icon'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display a post icon next to the single post title" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 30,
                            'transport'   => 'postMessage'
          ),
          'tc_show_archive_title_icon'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display an icon next to the archive title" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'notice'    => __( 'When this option is checked, an archive type icon is displayed in the heading of every types of archives, on the left of the title. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                            'priority'      => 40,
                            'transport'   => 'postMessage'
          ),
          'tc_show_post_list_title_icon'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.3.0' , '1.0.11' ) ? 1 : 0,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display an icon next to each post title in an archive page" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'notice'    => __( 'When this option is checked, a post type icon is displayed on the left of each post titles in an archive page. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                            'priority'      => 50,
                            'transport'   => 'postMessage'
          ),
          'tc_show_sidebar_widget_icon'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "WP sidebar widgets : display icons next to titles" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 60,
                            'transport'   => 'postMessage'
          ),
          'tc_show_footer_widget_icon'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "WP footer widgets : display icons next to titles" , "customizr" ),
                            'section'       => 'titles_icons_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 70,
                            'transport'   => 'postMessage'
          )
  );
}


/*-----------------------------------------------------------------------------------------------------
                               IMAGE SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_images_option_map( $get_default = null ) {
  global $wp_version;

  $_image_options =  array(
          'tc_fancybox' =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Lightbox effect on images' , 'customizr' ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'If enabled, this option activates a popin window whith a zoom effect when an image is clicked. Note : to enable this effect on the images of your pages and posts, images have to be linked to the Media File.' , 'customizr' ),
          ),
          /* removed in c4
          'tc_fancybox_autoscale' =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Autoscale images on zoom' , 'customizr' ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
          ),
          */
          'tc_retina_support' =>  array(
                            'default'       => 0,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'High resolution (Retina) support' , 'customizr' ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => sprintf('%1$s <strong>%2$s</strong> : <a href="%4$splugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails" title="%5$s" target="_blank">%3$s</a>.',
                                __( 'If enabled, your website will include support for high resolution devices.' , 'customizr' ),
                                __( "It is strongly recommended to regenerate your media library images in high definition with this free plugin" , 'customizr'),
                                __( "regenerate thumbnails" , 'customizr'),
                                admin_url(),
                                __( "Open the description page of the Regenerate thumbnails plugin" , 'customizr')
                            )
          ),
          /* removed in c4, always true
           'tc_display_slide_loader'  =>  array(
                            'default'       => 0,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Sliders : display on loading icon before rendering the slides" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'When checked, this option displays a loading icon when the slides are being setup.' , 'customizr' ),
          ),
          */
          'tc_slider_parallax'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Sliders : use parallax scrolling" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'If enabled, your slides scroll slower than the page (parallax effect).' , 'customizr' ),
          ),
          'tc_center_slider_img'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Dynamic slider images centering on any devices" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            //'notice'    => __( 'This option dynamically centers your images on any devices vertically or horizontally (without stretching them) according to their initial dimensions.' , 'customizr' ),
          ),
          'tc_center_img'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Dynamic thumbnails centering on any devices" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'This option dynamically centers your images on any devices, vertically or horizontally according to their initial aspect ratio.' , 'customizr' ),
          )
  );//end of images options

  //add responsive image settings for wp >= 4.4
  if ( version_compare( $wp_version, '4.4', '>=' ) )
    $_image_options = array_merge( $_image_options, array(
           'tc_resp_slider_img'  =>  array(
                            'default'     => 0,
                            'control'     => 'CZR_controls' ,
                            'title'       => __( 'Responsive settings', 'customizr' ),
                            'label'       => __( "Enable the WordPress responsive image feature for the slider" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
          ),
          'tc_resp_thumbs_img'  =>  array(
                            'default'     => 0,
                            'control'     => 'CZR_controls' ,
                            'label'       => __( "Enable the WordPress responsive image feature for the theme's thumbnails" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'notice'      => __( 'This feature has been introduced in WordPress v4.4+ (dec-2015), and might have minor side effects on some of your existing images. Check / uncheck this option to safely verify that your images are displayed nicely.' , 'customizr' ),
                            'type'        => 'checkbox' ,
          )
      )
    );

  return $_image_options;
}





/* Removed in c4 */
/*-----------------------------------------------------------------------------------------------------
                              RESPONSIVE SETTINGS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_responsive_option_map( $get_default = null ) {
  return array(
          'tc_block_reorder'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( 'Dynamic sidebar reordering on small devices' , 'customizr' ) ),
                            'section'     => 'responsive_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'Activate this option to move the sidebars (if any) after the main content block, for smartphones or tablets viewport.' , 'customizr' ),
          )
  );//end of links options
}



/*-----------------------------------------------------------------------------------------------------
                              AUTHORS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_authors_option_map( $get_default = null ) {
  return array(
          'tc_show_author_info'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display an author box after each single post content" , "customizr" ),
                            'section'       => 'authors_sec',
                            'type'          => 'checkbox',
                            'priority'      => 1,
                            'notice'        =>  __( 'Check this option to display an author info block after each single post content. Note : the Biographical info field must be filled out in the user profile.' , 'customizr' ),
          )
  );
}



/*-----------------------------------------------------------------------------------------------------
                              SMOOTH SCROLL SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_smoothscroll_option_map( $get_default = null ) {
  return array(
          'tc_smoothscroll'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __("Enable Smooth Scroll", "customizr"),
                            'section'       => 'smoothscroll_sec',
                            'type'          => 'checkbox',
                            'priority'      => 1,
                            'notice'    => __( 'This option enables a smoother page scroll.' , 'customizr' ),
                            'transport'     => 'postMessage'
          )
  );
}



/******************************************************************************************************
*******************************************************************************************************
* PANEL : HEADER
*******************************************************************************************************
******************************************************************************************************/
/*-----------------------------------------------------------------------------------------------------
                               HEADER DESIGN AND LAYOUT
------------------------------------------------------------------------------------------------------*/
function czr_fn_header_design_option_map( $get_default = null ) {
  return array(
          'tc_header_layout'  =>  array(
                          'default'       => 'left',
                          'title'         => __( 'Header design and layout' , 'customizr'),
                          'control'       => 'CZR_controls' ,
                          'label'         => __( "Choose a layout for the header" , "customizr" ),
                          'section'       => 'header_layout_sec' ,
                          'type'          =>  'select' ,
                          'choices'       => array(
                                  'left'      => __( 'One line: Logo / title on the left' , 'customizr' ),
                                  'right'     => __( 'One line: Logo / title on the right' , 'customizr' ),
                                  'centered'  => __( 'Logo / title centered' , 'customizr'),
                                  'v-left'    => __( 'Logo / title on the left' , 'customizr' ),
                                  'v-right'   => __( 'Logo / title on the right' , 'customizr' )
                          ),
                          'priority'      => 5,
                          'notice'    => __( 'This setting might impact the side on which the menu is revealed.' , 'customizr' ),
          ),
          'tc_header_topnav'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display the topnav" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 10,
          ),
          'tc_header_topnav_mobile'  =>  array(
                            'default'       => 'hide',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Topnav in mobiles" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'choices'       => array(
                                    'hide'      => __( 'Hide' , 'customizr' ),
                                    'show'      => __( 'Show' , 'customizr'),
                                    'collapse'  => __( 'Collapse' , 'customizr' ),
                            ),
                            'type'          => 'select' ,
                            'priority'      => 13,
          ),
          /* removed in c4
          //enable/disable top border
          'tc_top_border' => array(
                            'default'       =>  1,//top border on by default
                            'label'         =>  __( 'Display top border' , 'customizr' ),
                            'control'       =>  'CZR_controls' ,
                            'section'       =>  'header_layout_sec' ,
                            'type'          =>  'checkbox' ,
                            'notice'        =>  __( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
                            'priority'      => 10
          ),
          */
          'tc_show_tagline'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display the tagline" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 15,
                            'transport'     => 'postMessage',
                            'ubq_section'   => array(
                                                'section' => 'title_tagline',
                                                'priority' => '30'
                                             )
          ),
          'tc_woocommerce_header_cart' => array(
                            'default'   => 1,
                            'label'     => sprintf('<span class="dashicons dashicons-cart"></span> %s', __( 'Display the shopping cart in the header' , 'customizr' ) ),
                            'control'   => 'CZR_controls' ,
                            'section'   => 'header_layout_sec',
                            'notice'    => __( "WooCommerce: check to display a cart icon showing the number of items in your cart next to your header's tagline.", 'customizr' ),
                            'type'      => 'checkbox' ,
                            'priority'  => 18,
                            'active_callback' => apply_filters( 'czr_woocommerce_options_enabled', '__return_false' )
          ),
          'tc_social_in_header' =>  array(
                            'default'       => 1,
                            'label'       => __( 'Social links in header' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'header_layout_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'      => 20,
                            //'transport'   => 'postMessage'
          ),
          'tc_social_in_topnav' =>  array(
                            'default'       => 1,
                            'label'       => __( 'Social links in topnav' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'header_layout_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'      => 20,
          ),
          'tc_search_in_header' => array(
                            'default'   => 1,
                            'label'     => __( 'Display a search button in the header' , 'customizr' ),
                            'control'   => 'CZR_controls' ,
                            'section'   => 'header_layout_sec',
                            'type'      => 'checkbox' ,
                            'priority'  => 25,
          ),
          /* REMOVED IN c4
          'tc_display_boxed_navbar'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.3.13', '1.0.18' ) ? 1 : 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display menu in a box" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 25,
                            'transport'     => 'postMessage',
                            'notice'    => __( 'If checked, this option wraps the header menu/tagline/social in a light grey box.' , 'customizr' ),
          ),
          */
          'tc_header_skin'  =>  array(
                            'default'       => 'dark',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Header skin', 'customizr'),
                            'choices'       => array(
                                  'dark'   => __( 'Dark' , 'customizr' ),
                                  'light'  => __( 'Light' , 'customizr')
                            ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'select' ,
                            'priority'      => 26,
          ),
          'tc_header_type'  => array(
                            'default'       => 'standard',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Header type" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          =>  'select',
                            'choices'       => array(
                                  'absolute'  => __( 'Absolute' , 'customizr' ),
                                  'standard'  => __( 'Relative' , 'customizr'),
                            ),
                            'priority'      => 27,
          ),
          'tc_sticky_header'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Sticky header settings' , 'customizr'),
                            'label'         => __( "Sticky on scroll" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 30,
                            'transport'     => 'postMessage',
                            'notice'    => __( 'If checked, this option makes the header stick to the top of the page on scroll down.' , 'customizr' )
          ),
          'tc_sticky_mobile'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky on scroll in mobiles" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 30,
                            'transport'     => 'postMessage',
          ),
          'tc_sticky_show_tagline'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky header : display the tagline" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 40,
                            'transport'     => 'postMessage',
          ),
          /* Removed in c4
          'tc_woocommerce_header_cart_sticky' => array(
                            'default'   => 1,
                            'label'     => sprintf('<span class="dashicons dashicons-cart"></span> %s', __( 'Sticky header: display the shopping cart' , 'customizr' ) ),
                            'control'   => 'CZR_controls' ,
                            'section'   => 'header_layout_sec',
                            'type'      => 'checkbox' ,
                            'priority'  => 45,
                            'transport' => 'postMessage',
                            'active_callback' => apply_filters( 'czr_woocommerce_options_enabled', '__return_false' ),
                            'notice'    => __( 'WooCommerce: if checked, your WooCommerce cart icon will remain visible when scrolling.' , 'customizr' )
           ),*/
           'tc_sticky_show_title_logo'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky header : display the title / logo" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 50,
                            'transport'     => 'postMessage',
          ),
          'tc_sticky_shrink_title_logo'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky header : shrink title / logo" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 60,
                            'transport'     => 'postMessage',
          ),
          'tc_sticky_show_menu'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky header : display the menu" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 60,
                            'transport'     => 'postMessage',
                            /* Only the primary menu is sticky-able*/
                            //'notice'        => __('Also applied to the secondary menu if any.' , 'customizr')
          ),
          /* Removed in c4
          'tc_sticky_transparent_on_scroll'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky header : semi-transparent on scroll" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 67,
                            'transport'     => 'postMessage',
          ),
          */
          'tc_sticky_z_index'  =>  array(
                            'default'       => 100,
                            'control'       => 'CZR_controls' ,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'label'         => __( "Set the header z-index" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 0,
                            'priority'      => 70,
                            'transport'     => 'postMessage',
                            'notice'    => sprintf('%1$s <a href="%2$s" target="_blank">%3$s</a> ?',
                                __( "What is" , 'customizr' ),
                                esc_url('https://developer.mozilla.org/en-US/docs/Web/CSS/z-index'),
                                __( "the z-index" , 'customizr')
                            ),
          )

  );
}




/*-----------------------------------------------------------------------------------------------------
                    NAVIGATION SECTION
------------------------------------------------------------------------------------------------------*/
//NOTE : priorities 10 and 20 are "used" bu menus main and secondary
function czr_fn_navigation_option_map( $get_default = null ) {
  $menu_style = czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'navbar' : 'aside';
  $menu_style = czr_fn_user_started_before_version( '4.0', '2.0' ) ? $menu_style : 'navbar';
  return array(
          'tc_display_second_menu'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display a secondary (horizontal) menu in the header." , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 15,//must be located between the two menus
                            'notice'        => __( 'Displayed in the topnav if enabled' , 'customizr' ),
          ),
          'tc_menu_style'  =>  array(
                          'default'       => $menu_style,
                          'control'       => 'CZR_controls' ,
                          'title'         => __( 'Main menu design' , 'customizr'),
                          'label'         => __( 'Select a design : side menu (vertical) or regular (horizontal)' , 'customizr' ),
                          'section'       => 'nav' ,
                          'type'          => 'select',
                          'choices'       => array(
                                  'navbar'   => __( 'Regular (horizontal)'   ,  'customizr' ),
                                  'aside'    => __( 'Side Menu (vertical)' ,  'customizr' ),
                          ),
                          'priority'      => 30
          ),
          /* by default now
          'tc_menu_resp_dropdown_limit_to_viewport'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( "For mobile devices (responsive), limit the height of the dropdown menu block to the visible viewport." , "customizr" ) ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 35,
                            //'transport'     => 'postMessage',
          ),*/
          /* not anymore
          'tc_display_menu_label'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display a label next to the menu button." , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 45,
                            'notice'        => __( 'Note : the label is hidden on mobile devices.' , 'customizr' ),
          ),
          */
          'tc_menu_position'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'pull-menu-left' : 'pull-menu-right',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Menu position (for "main" horizontal menu)' , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          =>  'select' ,
                            'choices'       => array(
                                    'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                    'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                            ),
                            'priority'      => 50,
                            'transport'     => 'postMessage',

          ),
          'tc_second_menu_position'  =>  array(
                            'default'       => 'pull-menu-left',
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Secondary (horizontal) menu design' , 'customizr'),
                            'label'         => __( 'Menu position (for the horizontal menu)' , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          =>  'select' ,
                            'choices'       => array(
                                    'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                    'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                            ),
                            'priority'      => 55,
                            'transport'     => 'postMessage'
          ),
          //The hover menu type has been introduced in v3.1.0.
          //For users already using the theme (no theme's option set), the default choice is click, for new users, it is hover.
          'tc_menu_type'  => array(
                            'default'   =>  czr_fn_user_started_before_version( '3.1.0' , '1.0.0' ) ? 'click' : 'hover',
                            'control'   =>  'CZR_controls' ,
                            'label'     =>  __( 'Select a submenu expansion option' , 'customizr' ),
                            'section'   =>  'nav' ,
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    'click'   => __( 'Expand submenus on click' , 'customizr'),
                                    'hover'   => __( 'Expand submenus on hover' , 'customizr'  ),
                            ),
                            'priority'  =>   60
          ),
          'tc_menu_submenu_fade_effect'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Reveal the sub-menus blocks with a fade effect" , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 70,
                            'transport'     => 'postMessage',
          ),
          'tc_menu_submenu_item_move_effect'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Hover move effect for the sub menu items" , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 80,
                            'transport'     => 'postMessage',
          ),
          'tc_second_menu_resp_setting'  =>  array(
                            'default'       => 'in-sn-before',
                            'control'       => 'CZR_controls' ,
                            'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( "Choose a mobile devices (responsive) behaviour for the secondary menu." , "customizr" ) ),
                            'section'       => 'nav',
                            'type'      =>  'select',
                            'choices'     => array(
                                'in-sn-before'   => __( 'Move before inside the side menu ' , 'customizr'),
                                'in-sn-after'   => __( 'Move after inside the side menu ' , 'customizr'),
                                'display-in-header'   => __( 'Display in the header' , 'customizr'),
                                'hide'   => __( 'Hide' , 'customizr'  ),
                            ),
                            'priority'      => 90,
                            // 'notice'        => __( 'Note : the label is hidden on mobile devices.' , 'customizr' ),
          ),
          'tc_hide_all_menus'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Remove all the menus.' , 'customizr'),
                            'label'         => __( "Don't display any menus in the header of your website" , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 100,//must be located between the two menus
                            'notice'        => __( 'Use with caution : provide an alternative way to navigate in your website for your users.' , 'customizr' ),
          ),
  ); //end of navigation options
}






/******************************************************************************************************
*******************************************************************************************************
* PANEL : CONTENT
*******************************************************************************************************
******************************************************************************************************/
/*-----------------------------------------------------------------------------------------------------
                               FRONT PAGE SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_front_page_option_map( $get_default = null ) {
  //prepare the cat picker notice
  global $wp_version;
  $_cat_picker_notice = sprintf( '%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' ,
    __( "Click inside the above field and pick post categories you want to display. No filter will be applied if empty.", 'customizr'),
    esc_url('codex.wordpress.org/Posts_Categories_SubPanel'),
    __('Learn more about post categories in WordPress' , 'customizr')
  );
  //for wp version >= 4.3 add deep links
  if ( ! version_compare( $wp_version, '4.3', '<' ) ) {
    $_cat_picker_notice = sprintf( '%1$s<br/><br/><ul><li>%2$s</li><li>%3$s</li></ul>',
      $_cat_picker_notice,
      sprintf( '%1$s <a href="%2$s">%3$s &raquo;</a>',
        __("Set the number of posts to display" , "customizr"),
        "javascript:wp.customize.section('frontpage_sec').container.find('.customize-section-back').trigger('click'); wp.customize.control('posts_per_page').focus();",
        __("here", "customizr")
      ),
      sprintf( '%1$s <a href="%2$s">%3$s &raquo;</a>',
        __('Jump to the blog design options' , 'customizr'),
        "javascript:wp.customize.section('frontpage_sec').container.find('.customize-section-back').trigger('click'); wp.customize.control('tc_theme_options[tc_post_list_grid]').focus();",
        __("here", "customizr")
      )
    );
  }


  return array(
          //title
          'homecontent_title'         => array(
                  'setting_type'  =>  null,
                  'control'   =>  'CZR_controls' ,
                  'title'       => __( 'Choose content and layout' , 'customizr' ),
                  'section'     => 'frontpage_sec' ,
                  'type'      => 'title' ,
                  'priority'      => 0,
          ),

          //show on front
          'show_on_front'           => array(
                            'label'     =>  __( 'Front page displays' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'      => 'select' ,
                            'priority'      => 1,
                            'choices'     => array(
                                    'nothing'   => __( 'Don\'t show any posts or page' , 'customizr'),
                                    'posts'   => __( 'Your latest posts' , 'customizr'),
                                    'page'    => __( 'A static page' , 'customizr'  ),
                            ),
          ),

          //page on front
          'page_on_front'           => array(
                            'label'     =>  __( 'Front page' , 'customizr'  ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'dropdown-pages' ,
                            'priority'      => 1,
          ),

          //page for posts
          'page_for_posts'          => array(
                            'label'       =>  __( 'Posts page' , 'customizr'  ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'dropdown-pages' ,
                            'priority'    => 1,
          ),
          /* Removed in c4 will rely on skope
          'tc_show_post_navigation_home'  =>  array(
                            'default'      => 1,
                            'control'      => 'CZR_controls' ,
                            'label'         => __( "Display navigation in your home blog" , "customizr" ),
                            'section'       => 'frontpage_sec',
                            'type'          => 'checkbox',
                            'priority'      => 1,
                            'transport'     => 'postMessage',
          ),
          */
          //page for posts
          'tc_blog_restrict_by_cat'           => array(
                            'default'         => array(),
                            'label'           =>  __( 'Apply a category filter to your home / blog posts' , 'customizr'  ),
                            'section'         => 'frontpage_sec',
                            'control'         => 'CZR_Customize_Multipicker_Categories_Control',
                            'type'            => 'czr_multiple_picker',
                            'priority'        => 1,
                            'active_callback' => 'is_home',
                            'notice'          => $_cat_picker_notice
          ),
          //layout
          'tc_front_layout' => array(
                            'default'       => 'f' ,//Default layout for home page is full width
                            'label'       =>  __( 'Set up the front page layout' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'control'     => 'CZR_controls' ,
                            'type'        => 'select' ,
                            'choices'     => czr_fn_layout_choices(),
                            'active_callback' => 'czr_fn_is_home',
                            'priority'    => 2,
          ),

          //select slider
          'tc_front_slider' => array(
                            'default'     => 'demo' ,
                            'control'     => 'CZR_controls' ,
                            'title'       => __( 'Slider options' , 'customizr' ),
                            'label'       => __( 'Select front page slider' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'select' ,
                            //!important
                            'choices'     => ( true == $get_default ) ? null : czr_fn_slider_choices(),
                            'priority'    => 20
          ),
          //posts slider
          'tc_posts_slider_number' => array(
                            'default'     => 1 ,
                            'control'     => 'CZR_controls',
                            'label'       => __('Number of posts to display', 'customizr'),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'number',
                            'priority'    => 22,
                            'notice'      => __( "Only the posts with a featured image or at least an image inside their content will qualify for the slider. The number of post slides displayed won't exceed the number of available posts in your website.", 'customizr' )
          ),
          'tc_posts_slider_stickies' => array(
                            'default'     => 0,
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Include only sticky posts' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 23,
                            'notice'      => sprintf('%1$s <a href="https://codex.wordpress.org/Sticky_Posts" target="_blank">%2$s</a>',
                                __( 'You can choose to display only the sticky posts. If you\'re not sure how to set a sticky post, check', 'customizr' ),
                                __('the WordPress documentation.', 'customizr' )
                            )

          ),
          'tc_posts_slider_title' => array(
                            'default'     => 1,
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Display the title' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 24,
                            'notice'      => __( 'The title will be limited to 80 chars max', 'customizr' ),
          ),
          'tc_posts_slider_text' => array(
                            'default'     => 1,
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Display the excerpt' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 25,
                            'notice'      => __( 'The excerpt will be limited to 80 chars max', 'customizr' ),
          ),
          'tc_posts_slider_link' => array(
                            'default'     => 'cta',
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Link post with' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'select' ,
                            'choices'     => array(
                                'cta'        => __('Call to action button', 'customizr' ),
                                'slide'      => __('Entire slide', 'customizr' ),
                                'slide_cta'  => __('Entire slide and call to action button', 'customizr' )
                            ),
                            'priority'    => 26,

          ),
          'tc_posts_slider_button_text' => array(
                            'default'     => __( 'Read more &raquo;' , 'customizr' ),
                            'label'       => __( 'Button text' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'text' ,
                            'priority'    => 28,
                            'notice'      => __( 'The button text will be limited to 80 chars max. Leave this field empty to hide the button', 'customizr' ),
          ),
          //select slider
          'tc_slider_width' => array(
                            'default'       => '1',
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Slider size' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'select' ,
                            'choices'       => array(
                              'boxed' => __( 'Boxed', 'customizr' ),
                              'fw' => __( 'Full-width', 'customizr' ),
                              'fp' => __( 'Full-page', 'customizr' )
                            ),
                            'priority'      => 30,
          ),

          //Delay between each slides
          'tc_slider_delay' => array(
                            'default'       => 5000,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Delay between each slides' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'number' ,
                            'step'      => 500,
                            'min'     => 1000,
                            'notice'    => __( 'in ms : 1000ms = 1s' , 'customizr' ),
                            'priority'      => 50,
          ),
          'tc_slider_default_height' => array(
                            'default'       => 500,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Set slider's height in pixels" , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'number' ,
                            'step'      => 1,
                            'min'       => 0,
                            'priority'      => 52,
                            'transport' => 'postMessage'
          ),
          'tc_slider_default_height_apply_all'  =>  array(
                            'default'       => 1,
                            'label'       => __( 'Apply this height to all sliders' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'       => 53,
          ),
          'tc_slider_change_default_img_size'  =>  array(
                            'default'       => 0,
                            'label'       => __( "Replace the default image slider's height" , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'       => 54,
                            'notice'    => sprintf('%1$s <a href="http://docs.presscustomizr.com/article/74-recommended-plugins-for-the-customizr-wordpress-theme/#images" target="_blank">%2$s</a>',
                                __( "If this option is checked, your images will be resized with your custom height on upload. This is better for your overall loading performance." , 'customizr' ),
                                __( "You might want to regenerate your thumbnails." , 'customizr')
                            ),
          ),

          //Front page widget area
          'tc_show_featured_pages'  => array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'title'       => __( 'Featured pages options' , 'customizr' ),
                            'label'       => __( 'Display home featured pages area' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'select' ,
                            'choices'     => array(
                                    1 => __( 'Enable' , 'customizr' ),
                                    0 => __( 'Disable' , 'customizr' ),
                            ),
                            'priority'        => 55,
          ),

          //display featured page images
          'tc_show_featured_pages_img' => array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Show images' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'The images are set with the "featured image" of each pages (in the page edit screen). Uncheck the option above to disable the featured page images.' , 'customizr' ),
                            'priority'      => 60,
          ),

          //display featured page images
          'tc_featured_page_button_text' => array(
                            'default'       => __( 'Read more &raquo;' , 'customizr' ),
                            'transport'     =>  'postMessage',
                            'label'       => __( 'Button text' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'text' ,
                            'priority'      => 65,
          )

  );//end of front_page_options
}






/*-----------------------------------------------------------------------------------------------------
                               PAGES AND POST LAYOUT SETTINGS
------------------------------------------------------------------------------------------------------*/
function czr_fn_layout_option_map( $get_default = null ) {
  return array(
          //Global sidebar layout
          'tc_sidebar_global_layout' => array(
                          'default'       => 'l' ,//Default sidebar layout is on the left
                          'label'         => __( 'Choose the global default layout' , 'customizr' ),
                          'section'     => 'post_layout_sec' ,
                          'type'          => 'select' ,
                          'choices'     => czr_fn_layout_choices(),
                          'notice'      => __( 'Note : the home page layout has to be set in the home page section' , 'customizr' ),
                          'priority'      => 10
           ),
          /* Removed in c4
          //force default layout on every posts
          'tc_sidebar_force_layout' =>  array(
                          'default'       => 0,
                          'control'     => 'CZR_controls' ,
                          'label'         => __( 'Force default layout everywhere' , 'customizr' ),
                          'section'       => 'post_layout_sec' ,
                          'type'          => 'checkbox' ,
                          'notice'      => __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
                          'priority'      => 20
          ),

          //Post sidebar layout
          'tc_sidebar_post_layout'  =>  array(
                          'default'       => 'l' ,//Default sidebar layout is on the left
                          'label'       => __( 'Choose the posts default layout' , 'customizr' ),
                          'section'     => 'post_layout_sec' ,
                          'type'        => 'select' ,
                          'choices'   => czr_fn_layout_choices(),
                          'priority'      => 30
          ),
          */
          //Post per page
          /*'posts_per_page'  =>  array(
                          'default'     => get_option( 'posts_per_page' ),
                          'sanitize_callback' => 'czr_fn_sanitize_number',
                          'control'     => 'CZR_controls' ,
                          'title'         => __( 'Global Post Lists Settings' , 'customizr' ),
                          'label'         => __( 'Maximum number of posts per page' , 'customizr' ),
                          'section'       => 'post_lists_sec' ,
                          'type'          => 'number' ,
                          'step'        => 1,
                          'min'         => 1,
                          'priority'       => 10,
          ),
          /* Removed in c4
          //Post list length
          'tc_post_list_length' =>  array(
                            'default'       => 'excerpt',
                            'label'         => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'select' ,
                            'choices'       => array(
                                    'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                    'full'    => __( 'Display the full content' , 'customizr' )
                                    ),
                            'priority'       => 20,
          ),

          //Page sidebar layout
          'tc_sidebar_page_layout'  =>  array(
                            'default'       => 'l' ,//Default sidebar layout is on the left
                            'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                            'section'     => 'post_layout_sec' ,
                            'type'        => 'select' ,
                            'choices'   => czr_fn_layout_choices(),
                            'priority'       => 40,
                            'notice'    => sprintf('<br/> %s<br/>%s',
                                sprintf( __("The above layout options will set your layout globally for your post and pages. But you can also define the layout for each post and page individually. Learn how in the %s.", "customizr"),
                                    sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' , esc_url('http://docs.presscustomizr.com/article/107-customizr-theme-options-pages-and-posts-layout'), __("Customizr theme documentation" , "customizr" )
                                    )
                                ),
                                sprintf( __("If you need to change the layout design of the front page, then open the 'Front Page' section above this one.", "customizr") )
                            )
          ),
          */
  );//end of layout_options

}


/*-----------------------------------------------------------------------------------------------------
                              POST LISTS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_post_list_option_map( $get_default = null ) {
  $_post_list_type = czr_fn_user_started_before_version( '3.2.18', '1.0.13' ) ? 'alternate' : 'grid';
  return array(
          //Post per page
          'posts_per_page'  =>  array(
                          'default'     => get_option( 'posts_per_page' ),
                          'sanitize_callback' => 'czr_fn_sanitize_number',
                          'control'     => 'CZR_controls' ,
                          'title'         => __( 'Global Post Lists Settings' , 'customizr' ),
                          'label'         => __( 'Maximum number of posts per page' , 'customizr' ),
                          'section'       => 'post_lists_sec' ,
                          'type'          => 'number' ,
                          'step'        => 1,
                          'min'         => 1,
                          'priority'       => 10,
          ),
          'tc_post_list_grid'  =>  array(
                            'default'       => $_post_list_type,
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Post List Design' , 'customizr' ),
                            'label'         => __( 'Select a Layout' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'select',
                            'choices'       => array(
                                    'alternate'       => __( 'Alternate thumbnails layout' , 'customizr'),
                                    'grid'            => __( 'Grid layout' , 'customizr'),
                                    'plain'           => __( 'Plain full layout' , 'customizr'),
                            ),
                            'priority'      => 40,
                            'notice'    => __( 'When you select the plain Layout, the whole post content will be displayed' , 'customizr' ),
          ),
          'tc_post_list_show_thumb'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Thumbnails/Media options' , 'customizr' ),
                            'label'         => __( "Display the post thumbnails/media" , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 100,
                            'notice'        => sprintf( '%s %s' , __( 'When this option is checked, the post thumbnails are displayed in all post lists : blog, archives, author page, search pages, ...' , 'customizr' ), __( 'Note : thumbnails are always displayed when the grid layout is choosen.' , 'customizr') )
          ),
          'tc_post_list_use_attachment_as_thumb'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "If no featured image is set, use the last image attached to this post." , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 100
          ),
          'tc_post_list_thumb_placeholder'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display thumbnail placeholder if no images available" , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 100
          ),
  );
}

function czr_fn_classical_grid_option_map( $map, $get_default = null ) {
  return array_merge( $map, array(
          //Post per page
          'tc_grid_columns'  =>  array(
                            'default'       => '3',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Number of columns per row' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'title'         => __( 'Classical Grid', 'customizr'),
                            'type'          => 'select',
                            'choices'       => array(
                                    '1'                     => __( '1' , 'customizr'),
                                    '2'                     => __( '2' , 'customizr'),
                                    '3'                     => __( '3' , 'customizr'),
                                    '4'                     => __( '4' , 'customizr')
                            ),
                            'priority'      => 45,
                            'notice'        => __( 'Note : columns are limited to 3 for single sidebar layouts and to 2 for double sidebar layouts.' , 'customizr' )
          ),
          'tc_grid_expand_featured'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Expand the last sticky post (for home and blog page only)' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 47
          ),
          'tc_grid_shadow'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Apply a shadow to each grid items' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 61,
           ),
          'tc_grid_bottom_border'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Apply a colored bottom border to each grid items' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 62,
           ),
          'tc_grid_icons'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Display post format icons on mouse hover when applicable' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 63,
           ),
          'tc_grid_num_words'  =>  array(
                            'default'       => 10,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Max. length for post titles (in words)' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 1,
                            'priority'      => 64
          ),
          /* removed in c4, will be handled with skopw
          'tc_grid_in_blog'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Apply the grid layout to Home/Blog' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 57
          ),
          'tc_grid_in_archive'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Apply the grid layout to Archives (archives, categories, author posts)' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 58
          ),
          'tc_grid_in_search'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Apply the grid layout to Search results' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 60,
                            'notice'        => __( 'Unchecked contexts are displayed with the alternate thumbnails layout.' , 'customizr' ),
          ),
          */
    )
  );
}

function czr_fn_alternate_option_map( $map, $get_default = null ) {
  return array_merge( $map, array(
          //Since Customizr4 you can only have thumb first/second
          'tc_post_list_thumb_position'  =>  array(
                            'default'       => 'right',
                            'control'     => 'CZR_controls',
                            'title'       => __( 'Alternate post list', 'customizr' ),
                            'label'         => __( "Thumbnails position" , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    //'top'     => __( 'Top' , 'customizr' ),
                                    'left'   => __( 'First' , 'customizr' ),
                                   // 'bottom'    => __( 'Bottom' , 'customizr' ),
                                    'right'  => __( 'Second' , 'customizr' ),
                            ),
                            'priority'      => 90
          ),
          'tc_post_list_thumb_alternate'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Alternate thumbnail/content" , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 95
          ),
    )
  );
}

function czr_fn_archive_titles_option_map( $map, $get_default = null ) {
  return array_merge( $map, array(
          /* ARCHIVE TITLES */
          'tc_cat_title'  =>  array(
                            'default'       => '',
                            'title'         => __( 'Archive titles' , 'customizr' ),
                            'label'       => __( 'Category pages titles' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'post_lists_sec' ,
                            'type'        => 'text' ,
                            'priority'       => 110
                            //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
          ),
          'tc_tag_title'  =>  array(
                            'default'         => '',
                            'label'       => __( 'Tag pages titles' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'post_lists_sec' ,
                            'type'        => 'text' ,
                            'priority'       => 115
                            //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
          ),
          'tc_author_title'  =>  array(
                            'default'         => '',
                            'label'       => __( 'Author pages titles' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'post_lists_sec' ,
                            'type'        => 'text' ,
                            'priority'       => 120
                            //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
          ),
          'tc_search_title'  =>  array(
                            'default'         => __( 'Search Results for :' , 'customizr' ),
                            'label'       => __( 'Search results page titles' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'post_lists_sec' ,
                            'type'        => 'text' ,
                            'priority'       => 115
                            //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
          ),
    )
  );
}

/*-----------------------------------------------------------------------------------------------------
                               SINGLE POSTS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_single_post_option_map( $get_default = null ) {
  return array(
      'tc_single_post_thumb_location'  =>  array(
                        'default'       => 'hide',
                        'control'     => 'CZR_controls' ,
                        'label'         => __( "Post thumbnail position" , "customizr" ),
                        'section'       => 'single_posts_sec' ,
                        'type'      =>  'select' ,
                        'choices'     => array(
                                'hide'                    => __( "Don't display" , 'customizr' ),
                                '__before_main_wrapper|200'   => __( 'Before the title in full width' , 'customizr' ),
                                '__before_content|0'     => __( 'Before the title boxed' , 'customizr' ),
                                '__after_content_title|10'    => __( 'After the title' , 'customizr' ),
                        ),
                        'priority'      => 10,
                        'notice'    => sprintf( '%s<br/>%s',
                          __( 'You can display the featured image (also called the post thumbnail) of your posts before their content, when they are displayed individually.' , 'customizr' ),
                          sprintf( __( "Don't know how to set a featured image to a post? Learn how in the %s.", "customizr" ),
                              sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
                          )
                        )
      ),
      'tc_single_post_thumb_height' => array(
                        'default'       => 250,
                        'sanitize_callback' => 'czr_fn_sanitize_number',
                        'control'   => 'CZR_controls' ,
                        'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                        'section'     => 'single_posts_sec' ,
                        'type'        => 'number' ,
                        'step'        => 1,
                        'min'         => 0,
                        'priority'      => 20,
                        'transport'   => 'postMessage'
      )
  );

}


/*-----------------------------------------------------------------------------------------------------
                               SINGLE PAGESS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_single_page_option_map( $get_default = null ) {
  return array(
      'tc_single_page_thumb_location'  =>  array(
                        'default'       => 'hide',
                        'control'     => 'CZR_controls' ,
                        'label'         => __( "Post thumbnail position" , "customizr" ),
                        'section'       => 'single_pages_sec' ,
                        'type'      =>  'select' ,
                        'choices'     => array(
                                'hide'                    => __( "Don't display" , 'customizr' ),
                                '__before_main_wrapper|200'   => __( 'Before the title in full width' , 'customizr' ),
                                '__before_content|0'     => __( 'Before the title boxed' , 'customizr' ),
                                '__after_content_title|10'    => __( 'After the title' , 'customizr' ),
                        ),
                        'priority'      => 10,
                        'notice'    => sprintf( '%s<br/>%s',
                          __( 'You can display the featured image (also called the post thumbnail) of your pages before their content, when they are displayed individually.' , 'customizr' ),
                          sprintf( __( "Don't know how to set a featured image to a page? Learn how in the %s.", "customizr" ),
                              sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
                          )
                        )
      ),
      'tc_single_page_thumb_height' => array(
                        'default'       => 250,
                        'sanitize_callback' => 'czr_fn_sanitize_number',
                        'control'   => 'CZR_controls' ,
                        'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                        'section'     => 'single_pages_sec' ,
                        'type'        => 'number' ,
                        'step'        => 1,
                        'min'         => 0,
                        'priority'      => 20,
                        'transport'   => 'postMessage'
      )
  );
}




/*-----------------------------------------------------------------------------------------------------
                               BREADCRUMB SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_breadcrumb_option_map( $get_default = null ) {
    return array(
          'tc_breadcrumb' => array(
                          'default'       => 1,//Breadcrumb is checked by default
                          'label'         => __( 'Display Breadcrumb' , 'customizr' ),
                          'control'     =>  'CZR_controls' ,
                          'section'       => 'breadcrumb_sec' ,
                          'type'          => 'checkbox' ,
                          'priority'      => 1,
          ),
          'tc_show_breadcrumb_home'  =>  array(
                            'default'       => 0,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display the breadcrumb on home page" , "customizr" ),
                            'section'       => 'breadcrumb_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 20,
                            'active_callback' => 'czr_fn_is_home'
          ),
          'tc_show_breadcrumb_in_pages'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display the breadcrumb in pages" , "customizr" ),
                            'section'       => 'breadcrumb_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 30

          ),
          'tc_show_breadcrumb_in_single_posts'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display the breadcrumb in single posts" , "customizr" ),
                            'section'       => 'breadcrumb_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 40

          ),
          'tc_show_breadcrumb_in_post_lists'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display the breadcrumb in posts lists : blog page, archives, search results..." , "customizr" ),
                            'section'       => 'breadcrumb_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 50

          ),
          'tc_breadcrumb_yoast' => array(
                            'default'   => czr_fn_user_started_before_version( '3.4.39' , '1.2.39' ) ? 0 : 1,
                            'label'     => __( "Use Yoast SEO breadcrumbs" , "customizr" ),
                            'control'   => 'CZR_controls' ,
                            'section'   => 'breadcrumb_sec',
                            'notice'    => sprintf( __( "Jump to the Yoast SEO breadcrumbs %s" , "customizr"),
                                            sprintf( '<a href="%1$s" title="%3$s">%2$s &raquo;</a>',
                                              "javascript:wp.customize.section('wpseo_breadcrumbs_customizer_section').focus();",
                                              __("customization panel" , "customizr"),
                                              esc_attr__("Yoast SEO breadcrumbs settings", "customizr")
                                            )
                                          ),
                            'type'      => 'checkbox' ,
                            'priority'  => 60,
                            'active_callback' => apply_filters( 'tc_yoast_breadcrumbs_option_enabled', '__return_false' )
          ),
  );

}


/*-----------------------------------------------------------------------------------------------------
                              POST METAS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_post_metas_option_map( $get_default = null ){
  return array(
          'tc_show_post_metas'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display posts metas" , "customizr" ),
                            'section'       => 'post_metas_sec' ,
                            'type'          => 'checkbox',
                            'notice'    => __( 'When this option is checked, the post metas (like taxonomies, date and author) are displayed below the post titles.' , 'customizr' ),
                            'priority'      => 5,
                            'transport'   => 'postMessage'
          ),
          'tc_post_metas_design'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'buttons' : 'no-buttons',
                            'control'     => 'CZR_controls' ,
                            'title'         => __( 'Metas Design' , 'customizr' ),
                            'label'         => __( "Select a design for the post metas" , "customizr" ),
                            'section'       => 'post_metas_sec' ,
                            'type'          =>  'select' ,
                            'choices'       => array(
                                'buttons'     => __( 'Buttons and text' , 'customizr' ),
                                'no-buttons'  => __( 'Text only' , 'customizr' )
                            ),
                            'priority'      => 10
          ),
          'tc_show_post_metas_home'  =>  array(
                            'default'       => 0,
                            'control'     => 'CZR_controls' ,
                            'title'         => __( 'Select the contexts' , 'customizr' ),
                            'label'         => __( "Display posts metas on home" , "customizr" ),
                            'section'       => 'post_metas_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 15,
                            'transport'   => 'postMessage'
          ),
          'tc_show_post_metas_single_post'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display posts metas for single posts" , "customizr" ),
                            'section'       => 'post_metas_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 20,
                            'transport'   => 'postMessage'
          ),
          'tc_show_post_metas_post_lists'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display posts metas in post lists (archives, blog page)" , "customizr" ),
                            'section'       => 'post_metas_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 25,
                            'transport'   => 'postMessage'
          ),

          'tc_show_post_metas_categories'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls',
                            'title'         => __( 'Select the metas to display' , 'customizr' ),
                            'label'         => __( "Display hierarchical taxonomies (like categories)" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 30
          ),

          'tc_show_post_metas_tags'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display non-hierarchical taxonomies (like tags)" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 35
          ),

          'tc_show_post_metas_publication_date'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display the publication date" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 40
          ),
          'tc_show_post_metas_author'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display the author" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 45
          ),
          'tc_show_post_metas_update_date'  =>  array(
                            'default'       => 0,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display the update date" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 50,
                            'notice'    => __( 'If this option is checked, additional date informations about the the last post update can be displayed (nothing will show up if the post has never been updated).' , 'customizr' ),
          ),

          'tc_post_metas_update_date_format'  =>  array(
                            'default'       => 'days',
                            'control'       => 'CZR_controls',
                            'label'         => __( "Select the last update format" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          =>  'select' ,
                            'choices'       => array(
                                    'days'     => __( 'Nb of days since last update' , 'customizr' ),
                                    'date'     => __( 'Date of the last update' , 'customizr' )
                            ),
                            'priority'      => 55
          ),

          'tc_post_metas_update_notice_in_title'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 1 : 0,
                            'control'       => 'CZR_controls',
                            'title'         => __( 'Recent update notice after post titles' , 'customizr' ),
                            'label'         => __( "Display a recent update notice" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 65,
                            'notice'    => __( 'If this option is checked, a customizable recent update notice is displayed next to the post title.' , 'customizr' )
          ),
          'tc_post_metas_update_notice_interval'  =>  array(
                            'default'       => 10,
                            'control'       => 'CZR_controls',
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'label'         => __( "Display the notice if the last update is less (strictly) than n days old" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 0,
                            'priority'      => 70,
                            'notice'    => __( 'Set a maximum interval (in days) during which the last update notice will be displayed.' , 'customizr' ),
          ),
          'tc_post_metas_update_notice_text'  =>  array(
                            'default'       => __( "Recently updated !" , "customizr" ),
                            'control'       => 'CZR_controls',
                            'label'         => __( "Update notice text" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'text',
                            'priority'      => 75,
                            'transport'   => 'postMessage'
          ),
          'tc_post_metas_update_notice_format'  =>  array(
                            'default'       => 'label-default',
                            'control'       => 'CZR_controls',
                            'label'         => __( "Update notice style" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          =>  'select' ,
                            'choices'       => array(
                                    'label-default'   => __( 'Default (grey)' , 'customizr' ),
                                    'label-success'   => __( 'Success (green)' , 'customizr' ),
                                    'label-warning'   => __( 'Alert (orange)' , 'customizr' ),
                                    'label-important' => __( 'Important (red)' , 'customizr' ),
                                    'label-info'      => __( 'Info (blue)' , 'customizr' )
                            ),
                            'priority'      => 80,
                            'transport'   => 'postMessage'
          )
  );
}



/*-----------------------------------------------------------------------------------------------------
                               GALLERY SECTION
-----------------------------------------------------------------------------------------------------*/
function czr_fn_gallery_option_map( $get_default = null ){
  return array(
          'tc_enable_gallery'  =>  array(
                            'default'       => 1,
                            'label'         => __('Enable Customizr galleries' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'notice'         => __( "Apply Customizr effects to galleries images" , "customizr" ),
                            'section'       => 'galleries_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 1
          ),
          'tc_gallery_fancybox'=>  array(
                            'default'       => 1,
                            'label'         => __('Enable Lightbox effect in galleries' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'notice'         => __( "Apply lightbox effects to galleries images" , "customizr" ),
                            'section'       => 'galleries_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 1
          ),
          'tc_gallery_style'=>  array(
                            'default'       => 1,
                            'label'         => __('Enable Customizr effects on hover' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'notice'         => __( "Apply nice on hover expansion effect to the galleries images" , "customizr" ),
                            'section'       => 'galleries_sec' ,
                            'type'          => 'checkbox',
                            'transport'     => 'postMessage',
                            'priority'      => 1
          )
  );
}



/*-----------------------------------------------------------------------------------------------------
                               PARAGRAPHS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_paragraph_option_map( $get_default = null ){
  return array(
          'tc_enable_dropcap'  =>  array(
                            'default'       => 0,
                            'title'         => __( 'Drop caps', 'customizr'),
                            'label'         => __('Enable drop caps' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'notice'         => __( "Apply a drop cap to the first paragraph of your post / page content" , "customizr" ),
                            'section'       => 'paragraphs_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 1
          ),
          'tc_dropcap_minwords'  =>  array(
                            'default'       => 50,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Apply a drop cap when the paragraph includes at least the following number of words :" , "customizr" ),
                            'notice'         => __( "(number of words)" , "customizr" ),
                            'section'       => 'paragraphs_sec' ,
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 1,
                            'priority'      => 10
          ),
          'tc_dropcap_design' => array(
                            'default'     => 'skin-shadow',
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Drop cap style' , 'customizr' ),
                            'section'     => 'paragraphs_sec',
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    'skin-shadow'    => __( "Skin color with shadow" , 'customizr' ),
                                    'simple-black'   => __( 'Simple black' , 'customizr' ),
                            ),
                            'priority'    => 20,
          ),
          'tc_post_dropcap'  =>  array(
                            'default'       => 0,
                            'label'         => __('Enable drop caps in posts' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'notice'         => __( "Apply a drop cap to the first paragraph of your single posts content" , "customizr" ),
                            'section'       => 'paragraphs_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 30
          ),
          'tc_page_dropcap'  =>  array(
                            'default'       => 0,
                            'label'         => __('Enable drop caps in pages' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'notice'         => __( "Apply a drop cap to the first paragraph of your pages" , "customizr" ),
                            'section'       => 'paragraphs_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 40
          )
  );
}



/*-----------------------------------------------------------------------------------------------------
                               COMMENTS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_comment_option_map( $get_default = null ) {
  return array(
          'tc_comment_show_bubble'  =>  array(
                            'default'       => 1,
                            'title'         => __('Comments bubbles' , 'customizr'),
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display the number of comments in a bubble next to the post title" , "customizr" ),
                            'section'       => 'comments_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 1
          ),
          /* Removed in c4
          'tc_comment_bubble_shape' => array(
                            'default'     => 'default',
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Comments bubble shape' , 'customizr' ),
                            'section'     => 'comments_sec',
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    'default'             => __( "Small bubbles" , 'customizr' ),
                                    'custom-bubble-one'   => __( 'Large bubbles' , 'customizr' ),
                            ),
                            'priority'    => 10,
          ),

          'tc_comment_bubble_color_type' => array(
                            'default'     => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'custom' : 'skin',
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Comments bubble color' , 'customizr' ),
                            'section'     => 'comments_sec',
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    'skin'     => __( "Skin color" , 'customizr' ),
                                    'custom'   => __( 'Custom' , 'customizr' ),
                            ),
                            'priority'    => 20,
          ),
          'tc_comment_bubble_color' => array(
                            'default'     => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? '#F00' : czr_fn_get_skin_color(),
                            'control'     => 'WP_Customize_Color_Control',
                            'label'       => __( 'Comments bubble color' , 'customizr' ),
                            'section'     => 'comments_sec',
                            'type'        =>  'color' ,
                            'priority'    => 30,
                            'sanitize_callback'    => 'czr_fn_sanitize_hex_color',
                            'sanitize_js_callback' => 'maybe_hash_hex_color',
                            'transport'   => 'postMessage'
          ),
          */
          'tc_page_comments'  =>  array(
                            'default'     => 0,
                            'control'     => 'CZR_controls',
                            'title'       => __( 'Other comments settings' , 'customizr'),
                            'label'       => __( 'Enable comments on pages' , 'customizr' ),
                            'section'     => 'comments_sec',
                            'type'        => 'checkbox',
                            'priority'    => 40,
                            'notice'      => sprintf('%1$s<br/> %2$s <a href="%3$s" target="_blank">%4$s</a>',
                                __( 'If checked, this option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
                                __( "You can also change other comments settings in :" , 'customizr'),
                                admin_url() . 'options-discussion.php',
                                __( 'the discussion settings page.' , 'customizr' )
                            ),
          ),
          'tc_post_comments'  =>  array(
                            'default'     => 1,
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Enable comments on posts' , 'customizr' ),
                            'section'     => 'comments_sec',
                            'type'        => 'checkbox',
                            'priority'    => 45,
                            'notice'      => sprintf('%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>.<br/>%4$s <a href="%5$s" target="_blank">%6$s</a>',
                                __( 'If checked, this option enables comments on all types of single posts. You can disable comments for a single post in quick edit mode from the' , 'customizr' ),
                                esc_url('codex.wordpress.org/Posts_Screen'),
                                __( 'post screen', 'customizr'),
                                __( "You can also change other comments settings in the" , 'customizr'),
                                admin_url('options-discussion.php'),
                                __( 'discussion settings page.' , 'customizr' )
                            ),
          ),
          'tc_show_comment_list'  =>  array(
                            'default'     => 1,
                            'control'     => 'CZR_controls',
                            'label'       => __( 'Display the comment list' , 'customizr' ),
                            'section'     => 'comments_sec',
                            'type'        => 'checkbox',
                            'priority'    => 50,
                            'notice'      =>__( 'By default, WordPress displays the past comments, even if comments are disabled in posts or pages. Unchecking this option allows you to not display this comment history.' , 'customizr' )
          )
  );
}



/*-----------------------------------------------------------------------------------------------------
                               POST NAVIGATION SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_post_navigation_option_map( $get_default = null ) {
  return array(
          'tc_show_post_navigation'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display posts navigation" , "customizr" ),
                            'section'       => 'post_navigation_sec' ,
                            'type'          => 'checkbox',
                            'notice'    => __( 'When this option is checked, the posts navigation is displayed below the posts' , 'customizr' ),
                            'priority'      => 5,
                            'transport'   => 'postMessage'
          ),
          /* Removed in c4, will be handled with skope
          'tc_show_post_navigation_page'  =>  array(
                            'default'       => 0,
                            'control'     => 'CZR_controls' ,
                            'title'         => __( 'Select the contexts' , 'customizr' ),
                            'label'         => __( "Display navigation in pages" , "customizr" ),
                            'section'       => 'post_navigation_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 10,
                            'transport'   => 'postMessage'
          ),
          'tc_show_post_navigation_single'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display posts navigation in single posts" , "customizr" ),
                            'section'       => 'post_navigation_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 20,
                            'transport'   => 'postMessage'
          ),
          'tc_show_post_navigation_archive'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display posts navigation in post lists (archives, blog page, categories, search results ..)" , "customizr" ),
                            'section'       => 'post_navigation_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 25,
                            'transport'   => 'postMessage'
          ),
          */
  );
}


/******************************************************************************************************
*******************************************************************************************************
* PANEL : SIDEBARS
*******************************************************************************************************
******************************************************************************************************/
/*-----------------------------------------------------------------------------------------------------
                               SIDEBAR SOCIAL LINKS SETTINGS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_sidebars_option_map( $get_default = null ) {
  return array(
          'tc_social_in_left-sidebar' =>  array(
                            'default'       => 0,
                            'label'       => __( 'Social links in left sidebar' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'sidebar_socials_sec',
                            'type'        => 'checkbox' ,
                            'priority'       => 20,
                            'transport'   => 'postMessage'
          ),

          'tc_social_in_right-sidebar'  =>  array(
                            'default'       => 0,
                            'label'       => __( 'Social links in right sidebar' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'sidebar_socials_sec',
                            'type'        => 'checkbox' ,
                            'priority'       => 25,
                            'transport'   => 'postMessage'
          ),
          'tc_social_in_sidebar_title'  =>  array(
                            'default'       => __( 'Social links' , 'customizr' ),
                            'label'       => __( 'Social link title in sidebars' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'sidebar_socials_sec',
                            'type'        => 'text' ,
                            'priority'       => 30,
                            'transport'   => 'postMessage',
                            'notice'    => __( 'Will be hidden if empty' , 'customizr' )
          )
  );
}



/******************************************************************************************************
*******************************************************************************************************
* PANEL : FOOTER
*******************************************************************************************************
******************************************************************************************************/
/*-----------------------------------------------------------------------------------------------------
                               FOOTER GLOBAL SETTINGS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_footer_global_settings_option_map( $get_default = null ) {
  return array(
          'tc_footer_skin'  =>  array(
                            'default'       => 'dark',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Footer skin', 'customizr'),
                            'choices'       => array(
                                  'dark'   => __( 'Dark' , 'customizr' ),
                                  'light'  => __( 'Light' , 'customizr')
                            ),
                            'section'       => 'footer_global_sec' ,
                            'type'          => 'select' ,
                            'priority'      => 0,
          ),
          'tc_social_in_footer' =>  array(
                            'default'       => 1,
                            'label'       => __( 'Social links in footer' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'footer_global_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'       => 0,
                            'transport'   => 'postMessage'
          ),
          'tc_sticky_footer'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.4.0' , '1.1.14' ) ? 0 : 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Stick the footer to the bottom of the page", "customizr" ),
                            'section'       => 'footer_global_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 1,
                            'transport'     => 'postMessage'
          ),
          'tc_show_back_to_top'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display a back to top arrow on scroll" , "customizr" ),
                            'section'       => 'footer_global_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 5
          ),
          'tc_back_to_top_position'  =>  array(
                            'default'       => 'right',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Back to top arrow position" , "customizr" ),
                            'section'       => 'footer_global_sec' ,
                            'type'          => 'select',
                            'choices'       => array(
                                  'left'      => __( 'Left' , 'customizr' ),
                                  'right'     => __( 'Right' , 'customizr'),
                            ),
                            'priority'      => 5,
          ),
  );
}




/******************************************************************************************************
*******************************************************************************************************
* PANEL : ADVANCED OPTIONS
*******************************************************************************************************
******************************************************************************************************/
/*-----------------------------------------------------------------------------------------------------
                               CUSTOM CSS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_custom_css_option_map( $get_default = null ) {
  return array(
          'tc_custom_css' =>  array(
                            'sanitize_callback' => 'wp_filter_nohtml_kses',
                            'sanitize_js_callback' => 'wp_filter_nohtml_kses',
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
                            'section'     => 'custom_sec' ,
                            'type'        => 'textarea' ,
                            'notice'    => sprintf('%1$s <a href="%4$ssnippet/creating-child-theme-customizr/" title="%3$s" target="_blank">%2$s</a>',
                                __( "Use this field to test small chunks of CSS code. For important CSS customizations, you'll want to modify the style.css file of a" , 'customizr' ),
                                __( 'child theme.' , 'customizr'),
                                __( 'How to create and use a child theme ?' , 'customizr'),
                                CZR_WEBSITE
                            ),
                            'transport'   => 'postMessage'
          ),
  );//end of custom_css_options
}


/*-----------------------------------------------------------------------------------------------------
                          WEBSITE PERFORMANCES SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_performance_option_map( $get_default = null ) {
  return array(
          'tc_minified_skin'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Performance : use the minified CSS stylesheets", 'customizr' ),
                            'section'     => 'performances_sec' ,
                            'type'        => 'checkbox' ,
                            'notice'    => __( 'Using the minified version of the stylesheets will speed up your webpage load time.' , 'customizr' ),
          ),
          'tc_img_smart_load'  =>  array(
                            'default'       => 0,
                            'label'       => __( 'Load images on scroll' , 'customizr' ),
                            'control'     =>  'CZR_controls',
                            'section'     => 'performances_sec',
                            'type'        => 'checkbox',
                            'priority'    => 20,
                            'notice'      => __('Check this option to delay the loading of non visible images. Images below the viewport will be loaded dynamically on scroll. This can boost performances by reducing the weight of long web pages with images.' , 'customizr')
          )
  );
}

/*-----------------------------------------------------------------------------------------------------
                          FRONT END NOTICES AND PLACEHOLDERS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_placeholders_notice_map( $get_default = null ) {
  return array(
          'tc_display_front_help'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls',
                            'label'       => __( "Display help notices on front-end for logged in users.", 'customizr' ),
                            'section'     => 'placeholder_sec',
                            'type'        => 'checkbox',
                            'notice'    => __( 'When this options is enabled, various help notices and some placeholder blocks are displayed on the front-end of your website. They are only visible by logged in users with administration capabilities.' , 'customizr' )
          )
  );
}

/*-----------------------------------------------------------------------------------------------------
                          FRONT END EXTERNAL RESOURCES SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_external_resources_option_map( $get_default = null ) {
  return array(
          'tc_font_awesome_icons'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls',
                            'label'       => __( "Load Font Awesome resources", 'customizr' ),
                            'section'     => 'extresources_sec',
                            'type'        => 'checkbox',
                            'notice'      => sprintf('<strong>%1$s</strong>. %2$s</br>%3$s',
                                __( 'Use with caution' , 'customizr'),
                                __( 'When checked, the Font Awesome icons and CSS will be loaded on front end. You might want to load the Font Awesome icons with a custom code, or let a plugin do it for you.', 'customizr' ),
                                sprintf('%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>.',
                                                                    __( "Check out some example of uses", 'customizr'),
                                                                    esc_url('http://fontawesome.io/examples/'),
                                                                    __('here', 'customizr')
                                )
                            )
          )

  );
}


/***************************************************************
* POPULATE PANELS
***************************************************************/
/**
* hook : czr_fn_add_panel_map
* @return  associative array of customizer panels
*/
function czr_fn_popul_panels_map( $panel_map ) {
  $_new_panels = array(
    'tc-global-panel' => array(
              'priority'       => 10,
              'capability'     => 'edit_theme_options',
              'title'          => __( 'Global settings' , 'customizr' ),
              'description'    => __( "Global settings for the Customizr theme :skin, socials, links..." , 'customizr' )
    ),
    'tc-header-panel' => array(
              'priority'       => 20,
              'capability'     => 'edit_theme_options',
              'title'          => __( 'Header' , 'customizr' ),
              'description'    => __( "Header settings for the Customizr theme." , 'customizr' )
    ),
    'tc-content-panel' => array(
              'priority'       => 30,
              'capability'     => 'edit_theme_options',
              'title'          => __( 'Content : home, posts, ...' , 'customizr' ),
              'description'    => __( "Content settings for the Customizr theme." , 'customizr' )
    ),
    'tc-sidebars-panel' => array(
              'priority'       => 30,
              'capability'     => 'edit_theme_options',
              'title'          => __( 'Sidebars' , 'customizr' ),
              'description'    => __( "Sidebars settings for the Customizr theme." , 'customizr' )
    ),
    'tc-footer-panel' => array(
              'priority'       => 40,
              'capability'     => 'edit_theme_options',
              'title'          => __( 'Footer' , 'customizr' ),
              'description'    => __( "Footer settings for the Customizr theme." , 'customizr' )
    ),
    'tc-advanced-panel' => array(
              'priority'       => 1000,
              'capability'     => 'edit_theme_options',
              'title'          => __( 'Advanced options' , 'customizr' ),
              'description'    => __( "Advanced settings for the Customizr theme." , 'customizr' )
    )
  );
  return array_merge( $panel_map, $_new_panels );
}





/***************************************************************
* POPULATE REMOVE SECTIONS
***************************************************************/
/**
 * hook : czr_remove_section_map
 */
function czr_fn_popul_remove_section_map( $_sections ) {
  //customizer option array
  $remove_section = array(
    'static_front_page' ,
    'nav',
    'title_tagline',
    'tc_page_comments'
  );
  return array_merge( $_sections, $remove_section );
}


/***************************************************************
* HANDLES THE THEME SWITCHER (since WP 4.2)
***************************************************************/
/**
* Print the themes section (themes switcher) when previewing the themes from wp-admin/themes.php
* hook : czr_remove_section_map
*/
function czr_fn_set_theme_switcher_visibility( $_sections) {
  //Don't do anything is in preview frame
  //=> because once the preview is ready, a postMessage is sent to the panel frame to refresh the sections and panels
  //Do nothing if WP version under 4.2
  global $wp_version;

  if ( czr_fn_is_customize_preview_frame() || ! version_compare( $wp_version, '4.2', '>=') )
    return $_sections;

  //when user access the theme switcher from the admin bar
  $_theme_switcher_requested = false;
  if ( isset( $_GET['autofocus'] ) ) {
    $autofocus = wp_unslash( $_GET['autofocus'] );
    if ( is_array( $autofocus ) && isset($autofocus['section']) ) {
      $_theme_switcher_requested = 'themes' == $autofocus['section'];
    }
  }

  if ( isset($_GET['theme']) || ! is_array($_sections) || $_theme_switcher_requested )
    return $_sections;

  array_push( $_sections, 'themes');
  return $_sections;
}




/***************************************************************
* POPULATE SECTIONS
***************************************************************/
/**
* hook : czr_add_section_map
*/
function czr_fn_popul_section_map( $_sections ) {
  //declare a private property to check wp version >= 4.0
  global $wp_version;
  $is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;

  //For nav menus option
  $locations      = get_registered_nav_menus();
  $menus          = wp_get_nav_menus();
  $num_locations  = count( array_keys( $locations ) );
  global $wp_version;
  $nav_section_desc =  sprintf( _n('Your theme supports %s menu. Select which menu you would like to use.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations, 'customizr' ), number_format_i18n( $num_locations ) );
  //adapt the nav section description for v4.3 (menu in the customizer from now on)
  if ( version_compare( $wp_version, '4.3', '<' ) ) {
    $nav_section_desc .= "<br/>" . sprintf( __("You can create new menu and edit your menu's content %s." , "customizr"),
      sprintf( '<strong><a href="%1$s" target="_blank" title="%3$s">%2$s &raquo;</a></strong>',
        admin_url('nav-menus.php'),
        __("on the Menus screen in the Appearance section" , "customizr"),
        __("create/edit menus", "customizr")
      )
    );
  } else {
    $nav_section_desc .= "<br/>" . sprintf( __("You can create new menu and edit your menu's content %s." , "customizr"),
      sprintf( '<strong><a href="%1$s" title="%3$s">%2$s &raquo;</a><strong>',
        "javascript:wp.customize.section('nav').container.find('.customize-section-back').trigger('click'); wp.customize.panel('nav_menus').focus();",
        __("in the menu panel" , "customizr"),
        __("create/edit menus", "customizr")
      )
    );
  }

  $nav_section_desc .= "<br/><br/>". __( 'If a menu location has no menu assigned to it, a default page menu will be used.', 'customizr');

  $_new_sections = array(
    /*---------------------------------------------------------------------------------------------
    -> PANEL : GLOBAL SETTINGS
    ----------------------------------------------------------------------------------------------*/
    'title_tagline'         => array(
                        'title'    => __( 'Site Title & Tagline', 'customizr' ),
                        'priority' => $is_wp_version_before_4_0 ? 7 : 0,
                        'panel'   => 'tc-global-panel'
    ),
    'logo_sec'            => array(
                        'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 8 : 5,
                        'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'skins_sec'         => array(
                        'title'     =>  __( 'Skin' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 1 : 7,
                        'description' =>  __( 'Select a skin for Customizr' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'fonts_sec'          => array(
                        'title'     =>  __( 'Fonts' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 40 : 10,
                        'description' =>  __( 'Set up the font global settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'socials_sec'        => array(
                        'title'     =>  __( 'Social links' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 9 : 20,
                        'description' =>  __( 'Set up your social links' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'links_sec'         => array(
                        'title'     =>  __( 'Links style and effects' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 22 : 30,
                        'description' =>  __( 'Various links settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'titles_icons_sec'        => array(
                        'title'     =>  __( 'Titles icons settings' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 18 : 40,
                        'description' =>  __( 'Set up the titles icons options' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'images_sec'         => array(
                        'title'     =>  __( 'Image settings' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 95 : 50,
                        'description' =>  __( 'Various images settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'authors_sec'               => array(
                        'title'     =>  __( 'Authors' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 220 : 70,
                        'description' =>  __( 'Post authors settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'smoothscroll_sec'          => array(
                        'title'     =>  __( 'Smooth Scroll' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 97 : 75,
                        'description' =>  __( 'Smooth Scroll settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),

    /*---------------------------------------------------------------------------------------------
    -> PANEL : HEADER
    ----------------------------------------------------------------------------------------------*/
    'header_layout_sec'         => array(
                        'title'    => $is_wp_version_before_4_0 ? __( 'Header design and layout', 'customizr' ) : __( 'Design and layout', 'customizr' ),
                        'priority' => $is_wp_version_before_4_0 ? 5 : 20,
                        'panel'   => 'tc-header-panel'
    ),
    'nav'           => array(
                        'title'          => __( 'Navigation Menus' , 'customizr' ),
                        'theme_supports' => 'menus',
                        'priority'       => $is_wp_version_before_4_0 ? 10 : 40,
                        'description'    => $nav_section_desc,
                        'panel'   => 'tc-header-panel'
    ),


    /*---------------------------------------------------------------------------------------------
    -> PANEL : CONTENT
    ----------------------------------------------------------------------------------------------*/
    'frontpage_sec'       => array(
                        'title'     =>  __( 'Front Page' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 12 : 10,
                        'description' =>  __( 'Set up front page options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),

    'post_layout_sec'        => array(
                        'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 15 : 15,
                        'description' =>  __( 'Set up layout options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),

    'post_lists_sec'        => array(
                        'title'     =>  __( 'Post lists : blog, archives, ...' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 16 : 20,
                        'description' =>  __( 'Set up post lists options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'single_posts_sec'        => array(
                        'title'     =>  __( 'Single posts' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 17 : 24,
                        'description' =>  __( 'Set up single posts options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'single_pages_sec'        => array(
                        'title'     =>  __( 'Single pages' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 18 : 25,
                        'description' =>  __( 'Set up single pages options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'breadcrumb_sec'        => array(
                        'title'     =>  __( 'Breadcrumb' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 11 : 30,
                        'description' =>  __( 'Set up breadcrumb options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'post_metas_sec'        => array(
                        'title'     =>  __( 'Post metas (category, tags, custom taxonomies)' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 20 : 50,
                        'description' =>  __( 'Set up post metas options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'galleries_sec'        => array(
                        'title'     =>  __( 'Galleries' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 20 : 55,
                        'description' =>  __( 'Set up gallery options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'paragraphs_sec'        => array(
                        'title'     =>  __( 'Paragraphs' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 20 : 55,
                        'description' =>  __( 'Set up paragraphs options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'comments_sec'          => array(
                        'title'     =>  __( 'Comments' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 25 : 60,
                        'description' =>  __( 'Set up comments options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'post_navigation_sec'          => array(
                        'title'     =>  __( 'Post/Page Navigation' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 30 : 65,
                        'description' =>  __( 'Set up post/page navigation options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),


    /*---------------------------------------------------------------------------------------------
    -> PANEL : SIDEBARS
    ----------------------------------------------------------------------------------------------*/
    'sidebar_socials_sec'          => array(
                        'title'     =>  __( 'Socials in Sidebars' , 'customizr' ),
                        'priority'    =>  10,
                        'description' =>  __( 'Set up your social profiles links in the sidebar(s).' , 'customizr' ),
                        'panel'   => 'tc-sidebars-panel'
    ),
    'responsive_sec'           => array(
                        'title'     =>  __( 'Responsive settings' , 'customizr' ),
                        'priority'    =>  20,
                        'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                        'panel'   => 'tc-sidebars-panel'
    ),


    /*---------------------------------------------------------------------------------------------
    -> PANEL : FOOTER
    ----------------------------------------------------------------------------------------------*/
    'footer_global_sec'          => array(
                        'title'     =>  __( 'Footer global settings' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 40 : 10,
                        'description' =>  __( 'Set up footer global options' , 'customizr' ),
                        'panel'   => 'tc-footer-panel'
    ),


    /*---------------------------------------------------------------------------------------------
    -> PANEL : ADVANCED
    ----------------------------------------------------------------------------------------------*/
    'custom_sec'           => array(
                        'title'     =>  __( 'Custom CSS' , 'customizr' ),
                        'priority'    =>  $is_wp_version_before_4_0 ? 100 : 10,
                        'panel'   => 'tc-advanced-panel'
    ),
    'performances_sec'      => array(
                        'title'     =>  __( 'Website Performances' , 'customizr' ),
                        'priority'    => 20,
                        'description' =>  __( 'On the web, speed is key ! Improve the load time of your pages with those options.' , 'customizr' ),
                        'panel'   => 'tc-advanced-panel'
    ),
    'placeholder_sec'     => array(
                        'title'     =>  __( 'Front-end placeholders and help blocks' , 'customizr' ),
                        'priority'    => 30,
                        'panel'   => 'tc-advanced-panel'
    ),
    'extresources_sec'    => array(
                        'title'     =>  __( 'Front-end Icons (Font Awesome)' , 'customizr' ),
                        'priority'    => 40,
                        'panel'   => 'tc-advanced-panel'
    )
  );

  if ( CZR_IS_PRO ) {
    $_new_sections = array_merge( $_new_sections, array(
        /*---------------------------------------------------------------------------------------------
        -> SECTION : GO-PRO
        ----------------------------------------------------------------------------------------------*/
        'customizr_go_pro'   => array(
                            'title'         => esc_html__( 'Upgrade to Customizr Pro', 'customizr' ),
                            'pro_text'      => esc_html__( 'Go Pro', 'customizr' ),
                            'pro_url'       => sprintf('%scustomizr-pro/', CZR_WEBSITE ),
                            'priority'      => 0,
                            'section_class' => 'CZR_Customize_Section_Pro'
        ),
    ) );
  }

  return array_merge( $_sections, $_new_sections );
}






/***************************************************************
* CONTROLS HELPERS
***************************************************************/
/**
* Generates the featured pages options
* add the settings/controls to the relevant section
* hook : tc_front_page_option_map
*
* @package Customizr
* @since Customizr 3.0.15
*
*/
function czr_fn_generates_featured_pages( $_original_map ) {
  $default = array(
    'dropdown'  =>  array(
          'one'   => __( 'Home featured page one' , 'customizr' ),
          'two'   => __( 'Home featured page two' , 'customizr' ),
          'three' => __( 'Home featured page three' , 'customizr' )
    ),
    'text'    => array(
          'one'   => __( 'Featured text one (200 char. max)' , 'customizr' ),
          'two'   => __( 'Featured text two (200 char. max)' , 'customizr' ),
          'three' => __( 'Featured text three (200 char. max)' , 'customizr' )
    )
  );

  //declares some loop's vars and the settings array
  $priority       = 70;
  $incr         = 0;
  $fp_setting_control = array();

  //gets the featured pages id from init
  $fp_ids       = apply_filters( 'czr_featured_pages_ids' , CZR_init::$instance -> fp_ids);

  //dropdown field generator
  foreach ( $fp_ids as $id ) {
    $priority = $priority + $incr;
    $fp_setting_control['tc_featured_page_'. $id]    =  array(
                  'default'     => 0,
                  'label'       => isset($default['dropdown'][$id]) ? $default['dropdown'][$id] :  sprintf( __('Custom featured page %1$s' , 'customizr' ) , $id ),
                  'section'     => 'frontpage_sec' ,
                  'type'        => 'dropdown-pages' ,
                  'priority'      => $priority
                );
    $incr += 10;
  }

  //text field generator
  $incr         = 10;
  foreach ( $fp_ids as $id ) {
    $priority = $priority + $incr;
    $fp_setting_control['tc_featured_text_' . $id]   = array(
                  'sanitize_callback' => 'czr_fn_sanitize_textarea',
                  'transport'   => 'postMessage',
                  'control'   => 'CZR_controls' ,
                  'label'       => isset($default['text'][$id]) ? $default['text'][$id] : sprintf( __('Featured text %1$s (200 char. max)' , 'customizr' ) , $id ),
                  'section'     => 'frontpage_sec' ,
                  'type'        => 'textarea' ,
                  'notice'    => __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
                  'priority'      => $priority,
                );
    $incr += 10;
  }

  return array_merge( $_original_map , $fp_setting_control );
}




/**
* Generates skin select list
*
* @package Customizr
* @since Customizr 3.0.15
*
*/
function czr_fn_get_skins($path) {
  //checks if path exists
  if ( !file_exists($path) )
    return;

  //gets the skins from init
  $default_skin_list    = CZR_init::$instance -> skins;

  //declares the skin list array
  $skin_list        = array();

  //gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
  $files            = scandir($path) ;
  foreach( $files as $file ) {
      //skips the minified and tc_common
      if ( false !== strpos($file, '.min.') || false !== strpos($file, 'tc_common') )
        continue;

      if ( $file[0] != '.' && !is_dir($path.$file) ) {
        if ( substr( $file, -4) == '.css' ) {
          $skin_list[$file] = isset($default_skin_list[$file]) ?  call_user_func( '__' , $default_skin_list[$file] , 'customizr' ) : substr_replace( $file , '' , -4 , 4);
        }
      }
    }//endforeach
  $_to_return = array();

  //Order skins like in the default array
  foreach( $default_skin_list as $_key => $value ) {
    if( isset($skin_list[$_key]) ) {
      $_to_return[$_key] = $skin_list[$_key];
    }
  }
  //add skins not included in default
  foreach( $skin_list as $_file => $_name ) {
    if( ! isset( $_to_return[$_file] ) )
      $_to_return[$_file] = $_name;
  }
  return $_to_return;
}//end of function



/**
* Returns the layout choices array
*
* @package Customizr
* @since Customizr 3.1.0
*/
function czr_fn_layout_choices() {
    $global_layout  = apply_filters( 'czr_global_layout' , CZR_init::$instance -> global_layout );
    $layout_choices = array();
    foreach ($global_layout as $key => $value) {
      $layout_choices[$key]   = ( $value['customizer'] ) ? call_user_func(  '__' , $value['customizer'] , 'customizr' ) : null ;
    }
    return $layout_choices;
}


/**
* Retrieves slider names and generate the select list
* @package Customizr
* @since Customizr 3.0.1
*/
function czr_fn_slider_choices() {
  $__options    =   get_option('tc_theme_options');
  $slider_names   =   isset($__options['tc_sliders']) ? $__options['tc_sliders'] : array();

  $slider_choices = array(
    0     =>  __( '&mdash; No slider &mdash;' , 'customizr' ),
    'demo'  =>  __( '&mdash; Demo Slider &mdash;' , 'customizr' ),
    'tc_posts_slider' => __('&mdash; Auto-generated slider from your blog posts &mdash;', 'customizr')
    );
  if ( $slider_names ) {
    foreach( $slider_names as $tc_name => $slides) {
      $slider_choices[$tc_name] = $tc_name;
    }
  }
  return $slider_choices;
}


/**
* Returns the list of available skins from child (if exists) and parent theme
*
* @package Customizr
* @since Customizr 3.0.11
* @updated Customizr 3.0.15
*/
function czr_fn_build_skin_list() {
  $parent_skins   = czr_fn_get_skins( CZR_BASE . CZR_ASSETS_PREFIX . 'front/css' );
  $child_skins    = array();
  $CZR            = CZR();

  if ( $CZR -> czr_fn_is_child() ){
    $child_skins    = file_exists(CZR_BASE_CHILD . CZR_ASSETS_PREFIX . 'front/css') ? czr_fn_get_skins(CZR_BASE_CHILD . CZR_ASSETS_PREFIX . 'front/css') : $child_skins;
    //backward compatibilty (the assets had a different relative path before 3.5)
    $child_skins    = empty( $child_skins ) && file_exists(CZR_BASE_CHILD . 'inc/assets/css') ? czr_fn_get_skins(CZR_BASE_CHILD . 'inc/assets/css') : $child_skins;
  }

  $skin_list      = array_merge( $parent_skins , $child_skins );

  return apply_filters( 'czr_skin_list', $skin_list );
}



/***************************************************************
* SANITIZATION HELPERS
***************************************************************/
/**
 * adds sanitization callback funtion : textarea
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_textarea( $value) {
  $value = esc_html( $value);
  return $value;
}



/**
 * adds sanitization callback funtion : number
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_number( $value) {
  if ( ! $value || is_null($value) )
    return $value;
  $value = esc_attr( $value); // clean input
  $value = (int) $value; // Force the value into integer type.
    return ( 0 < $value ) ? $value : null;
}

/**
 * adds sanitization callback funtion : url
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_url( $value) {
  $value = esc_url( $value);
  return $value;
}

/**
 * adds sanitization callback funtion : email
 * @package Customizr
 * @since Customizr 3.4.11
 */
function czr_fn_sanitize_email( $value) {
  return sanitize_email( $value );
}

/**
 * adds sanitization callback funtion : colors
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_hex_color( $color ) {
  if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
    return '#' . $unhashed;

  return $color;
}


/**
* Change upload's path to relative instead of absolute
* @package Customizr
* @since Customizr 3.1.11
*/
function czr_fn_sanitize_uploads( $url ) {
  $upload_dir = wp_upload_dir();
  return str_replace($upload_dir['baseurl'], '', $url);
}
