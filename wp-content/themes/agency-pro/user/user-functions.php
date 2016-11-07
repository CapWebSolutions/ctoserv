<?php

//* Enqueue user scripts

add_action( 'wp_enqueue_scripts', 'user_scripts' );
function user_scripts() {
	wp_enqueue_script( 'user-jquery', get_stylesheet_directory_uri() . '/user/user-jquery.js', array( 'jquery' ), '' );
    wp_enqueue_style( 'user-style', get_stylesheet_directory_uri() . '/user/user-style.css' );
}

/*
    User custom functions
*/

 // Replace "Howdy" with "Logged in as" in WordPress bar
 function replace_howdy( $wp_admin_bar ) {
    $my_account=$wp_admin_bar->get_node('my-account');
    $newtitle = str_replace( 'Howdy,', 'Logged in as', $my_account->title );
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => $newtitle,
    ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );
//*
// Add support to add a shortcode to widget
define('widget_text', 'do_shortcode');

// Reduce Post Revisions to 3
define( 'WP_POST_REVISIONS', 3 );

// Point to custom favicon
function cws_point_to_custom_favicon() { ?>
    <link rel="shortcut icon" href="<?php echo bloginfo('stylesheet_directory') ?>/images/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="<?php echo bloginfo('stylesheet_directory') ?>/images/favicon.ico" type="image/x-icon" />
<?php }
add_action('wp_head', 'cws_point_to_custom_favicon');

/*
* Ever accidentally publicize a post that you didn’t mean to?
 * This snippet will prevent the connections from being auto-selected, so you need to manually select them if you’d like to publicize something.
 *  Source: http://jetpack.me/2013/10/15/ever-accidentally-publicize-a-post-that-you-didnt/
 */
add_filter( 'publicize_checkbox_default', '__return_false' );

//* Add Support for custom menu in footer
//* Ref: https://sridharkatakam.com/adding-custom-menu-footer-genesis/

// add_action( 'genesis_footer', 'cws_custom_footer_menu' );
function cws_custom_footer_menu() {

	$class = 'menu genesis-nav-menu menu-footer';

	$args = array(
	'menu'           => 'Custom Footer Menu', // Enter name of your custom menu here
	'container'      => '',
	'menu_class'     => $class,
	'echo'           => 0,
	'depth'           => 1, 			// This prevents sub-menu from opening
	);

	$nav = wp_nav_menu( $args );

	$nav_markup_open = genesis_markup( array(
	'html5'   => '<nav %s>',
	'xhtml'   => '<div id="nav">',
	'context' => 'nav-footer',
	'echo'    => false,
	) );
	$nav_markup_open .= genesis_structural_wrap( 'menu-footer', 'open', 0 );

	$nav_markup_close  = genesis_structural_wrap( 'menu-footer', 'close', 0 );
	$nav_markup_close .= genesis_html5() ? '</nav>' : '</div>';

	$nav_output = $nav_markup_open . $nav . $nav_markup_close;

	echo $nav_output;
}

//* Add Support for having different background images in different pages
//* Ref: https://sridharkatakam.com/using-different-background-images-different-pages-minimum-pro/
//*
//* We are going to use the default background image on all pages EXCEPT the following pageid
//*    Custom backgrounds on:

//* Enqueue scripts
//* add_action( 'wp_enqueue_scripts', 'cws_agency_enqueue_backstretch_scripts' );   <<<< Client not ready for unique BG images yet.
function cws_agency_enqueue_backstretch_scripts() {

if (is_page()) {
		//* Enqueue Backstretch scripts
	wp_enqueue_script( 'agency-pro-backstretch', get_bloginfo( 'stylesheet_directory' ).'/js/backstretch.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_script( 'agency-pro-backstretch-set', get_bloginfo( 'stylesheet_directory' ).'/js/backstretch-set.js' , array( 'jquery','agency-pro-backstretch' ), '1.0.0' );

	if (is_page('15'))					//About Us
			wp_localize_script( 'agency-pro-backstretch-set', 'BackStretchImg', array( 'src' => 'http://cto.capwebhatrack.com/wp-content/themes/agency-pro/images/Backstretch-Image1600-x-1000.png' ) );
	elseif (is_page('103'))				//About Glenn Butler
			wp_localize_script( 'agency-pro-backstretch-set', 'BackStretchImg', array( 'src' => 'http://cto.capwebhatrack.com/wp-content/themes/agency-pro/images/Backstretch-Image1600-x-1000.png' ) );
	elseif (is_page())					//all the others get the default background image
			wp_localize_script( 'agency-pro-backstretch-set', 'BackStretchImg', array( 'src' => 'http://cto.capwebhatrack.com/wp-content/themes/agency-pro/images/bg.jpg' ) );

		//* Add custom body class
		add_filter( 'body_class', 'agency_pro_add_body_class' );
	}
}

//* Agency Pro custom body class
function agency_pro_add_body_class( $classes ) {
	$classes[] = 'agency-pro';
	return $classes;
}

// Create a shortcode to display our custom Go to top link in footer
// Ref: https://sridharkatakam.com/add-working-return-top-page-footer-link-genesis-themes/
add_shortcode( 'footer_custombacktotop', 'set_footer_custombacktotop' );
function set_footer_custombacktotop( $atts ) {
	return '<a href="#" class="top">Return to top of page</a>';
}

// Set up split custom footer
// Ref: https://sridharkatakam.com/split-footer-genesis/

add_shortcode( 'sitename', 'site_name' );
function site_name() {
	return '<a href="' . get_bloginfo( 'url' ) . '" title="' . get_bloginfo( 'sitename' ) . '">' . get_bloginfo( 'name' ) . '</a>';
}

//* Change the footer text
add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');
function sp_footer_creds_filter( $creds ) {
	$creds = '
	<div class="alignleft">
	<a href="http://ctoserv.com/newwp/privacy-policy/">Privacy Policy</a> &middot; <a href="http://ctoserv.com/newwp/terms-of-use/">Terms of Use</a><br/>
	Copyright [footer_copyright first=2008] [sitename] &middot; All Rights Reserved.
	</div>
	
	<div class="alignright">
	[footer_custombacktotop]<br/>
	Website by Cap Web Solutions, LLC
	</div>

	';
	return $creds;
}

/* Display Featured image in single Posts in Genesis
 */

add_action( 'genesis_entry_content', 'sk_show_featured_image_single_posts', 9 );
/**
 * Display Featured Image floated to the right in single Posts.
 *
 * @author Sridhar Katakam
 * @link   http://sridharkatakam.com/how-to-display-featured-image-in-single-posts-in-genesis/
 */
function sk_show_featured_image_single_posts() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$image_args = array(
		'size' => 'medium',
		'attr' => array(
			'class' => 'alignright',
		),
	);

	genesis_image( $image_args );
}
