<?php
/**
 * Loads parent and child themes' style.css.
 */
function wputapi_twentyseventeen_child_theme_enqueue_styles() {
    $parent_style = 'wputapi_twentyseventeen_parent_style';
    $parent_base_dir = 'twentyseventeen';

    wp_enqueue_style( $parent_style,
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( $parent_base_dir ) ? wp_get_theme( $parent_base_dir )->get('Version') : ''
    );

    wp_enqueue_style( $parent_style . '_child_style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

add_action( 'wp_enqueue_scripts', 'wputapi_twentyseventeen_child_theme_enqueue_styles' );

// Show the results on the homepage.
function wputapi_show_results( $query ) {

	if ( is_home() && $query->is_main_query() )
		$query->set( 'post_type', array( 'results' ) );

	return $query;
}

add_filter( 'pre_get_posts', 'wputapi_show_results' );

// Remove default filters.
remove_filter( 'the_content', 'wpautop' ); remove_filter( 'the_excerpt', 'wpautop' );
