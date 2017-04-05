<?php
/**
 * WP Idea Stream template functions.
 *
 * @package   WP Idea Stream\core
 *
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Check the main WordPress query to match WP Idea Stream conditions
 * Eventually Override query vars and set global template conditions / vars
 *
 * This the key function of the plugin, it is definining the templates
 * to load and is setting the displayed user.
 *
 * Inspired by bbPress 's bbp_parse_query()
 *
 * @since 2.0.0
 *
 * @param WP_Query $posts_query The WP_Query instance
 */
function wp_idea_stream_parse_query( $posts_query = null ) {
	// Bail if $posts_query is not the main loop
	if ( ! $posts_query->is_main_query() ) {
		return;
	}

	// Bail if filters are suppressed on this query
	if ( true === $posts_query->get( 'suppress_filters' ) ) {
		return;
	}

	// Handle the specific queries in IdeaStream Admin
	if ( wp_idea_stream_is_admin() ) {

		// Display sticky ideas if requested
		if ( wp_idea_stream_is_sticky_enabled() && ! empty( $_GET['sticky_ideas'] ) ) {
			$posts_query->set( 'post__in', wp_idea_stream_ideas_get_stickies() );
		}

		// Build meta_query if orderby rates is set
		if ( ! wp_idea_stream_is_rating_disabled() && ! empty( $_GET['orderby'] ) && 'rates_count' == $_GET['orderby'] ) {
			$posts_query->set( 'meta_query', array(
				array(
					'key'     => '_ideastream_average_rate',
					'compare' => 'EXISTS'
				)
			) );

			// Set the orderby idea var
			wp_idea_stream_set_idea_var( 'orderby', 'rates_count' );
		}

		do_action( 'wp_idea_stream_admin_request', $posts_query );

		return;
	}

	// Bail if else where in admin
	if ( is_admin() ) {
		return;
	}

	// Ideas post type for a later use
	$idea_post_type = wp_idea_stream_get_post_type();

	/** User's profile ************************************************************/

	// Are we requesting the user-profile template ?
	$user       = $posts_query->get( wp_idea_stream_user_rewrite_id() );
	$embed_page = wp_idea_stream_is_embed_profile();

	if ( ! empty( $user ) ) {

		if ( ! is_numeric( $user ) ) {
			// Get user by his username
			$user = wp_idea_stream_users_get_user_data( 'slug', $user );
		} else {
			// Get user by his id
			$user = wp_idea_stream_users_get_user_data( 'id', $user );
		}

		// No user id: no profile!
		if ( empty( $user->ID ) || true === apply_filters( 'wp_idea_stream_users_is_spammy', is_multisite() && is_user_spammy( $user ), $user ) ) {
			$posts_query->set_404();

			// Make sure the WordPress Embed Template will be used
			if ( ( 'true' === get_query_var( 'embed' ) || true === get_query_var( 'embed' ) ) ) {
				$posts_query->is_embed = true;
				$posts_query->set( 'p', -1 );
			}

			return;
		}

		// Set the displayed user id
		wp_idea_stream_set_idea_var( 'is_user', absint( $user->ID ) );

		// Make sure the post_type is set to ideas.
		$posts_query->set( 'post_type', $idea_post_type );

		// Are we requesting user rates
		$user_rates    = $posts_query->get( wp_idea_stream_user_rates_rewrite_id() );

		// Or user comments ?
		$user_comments = $posts_query->get( wp_idea_stream_user_comments_rewrite_id() );

		if ( ! empty( $user_rates ) && ! wp_idea_stream_is_rating_disabled() ) {
			// We are viewing user's rates
			wp_idea_stream_set_idea_var( 'is_user_rates', true );

			// Define the Meta Query to get his rates
			$posts_query->set( 'meta_query', array(
				array(
					'key'     => '_ideastream_rates',
					'value'   => ';i:' . $user->ID .';',
					'compare' => 'LIKE'
				)
			) );

		} else if ( ! empty( $user_comments ) ) {
			// We are viewing user's comments
			wp_idea_stream_set_idea_var( 'is_user_comments', true );

			/**
			 * Make sure no result.
			 * Query will be built later in user comments loop
			 */
			$posts_query->set( 'p', -1 );

		} else {
			if ( ( 'true' === get_query_var( 'embed' ) || true === get_query_var( 'embed' ) ) ) {
				$posts_query->is_embed = true;
				$posts_query->set( 'p', -1 );

				if ( $embed_page ) {
					wp_idea_stream_set_idea_var( 'is_user_embed', true );
				} else {
					$posts_query->set_404();
					return;
				}
			}

			// Default to the ideas the user submitted
			$posts_query->set( 'author', $user->ID  );
		}

		// No stickies on user's profile
		$posts_query->set( 'ignore_sticky_posts', true );

		// Make sure no 404
		$posts_query->is_404  = false;

		// Set the displayed user.
		wp_idea_stream_set_idea_var( 'displayed_user', $user );
	}

	/** Actions (New Idea) ********************************************************/

	$action = $posts_query->get( wp_idea_stream_action_rewrite_id() );

	if ( ! empty( $action ) ) {
		// Make sure the post type is set to ideas
		$posts_query->set( 'post_type', $idea_post_type );

		// Define a global to inform we're dealing with an action
		wp_idea_stream_set_idea_var( 'is_action', true );

		// Is the new idea form requested ?
		if ( wp_idea_stream_addnew_slug() == $action ) {
			// Yes so set the corresponding var
			wp_idea_stream_set_idea_var( 'is_new', true );

			/**
			 * Make sure no result.
			 * We are not querying any content, but creating one
			 */
			$posts_query->set( 'p', -1 );

		// Edit action ?
		} else if ( wp_idea_stream_edit_slug() == $action ) {
			// Yes so set the corresponding var
			wp_idea_stream_set_idea_var( 'is_edit', true );

		// Signup support
		} else if ( wp_idea_stream_signup_slug() == $action && wp_idea_stream_is_signup_allowed_for_current_blog() ) {
			// Set the signup global var
			wp_idea_stream_set_idea_var( 'is_signup', true );

			/**
			 * Make sure no result.
			 * We are not querying any content, but creating one
			 */
			$posts_query->set( 'p', -1 );

		} else if ( has_action( 'wp_idea_stream_custom_action' ) ) {
			/**
			 * Allow plugins to other custom idea actions
			 *
			 * @param string   $action      The requested action
			 * @param WP_Query $posts_query The WP_Query instance
			 */
			do_action( 'wp_idea_stream_custom_action', $action, $posts_query );
		} else {
			$posts_query->set_404();
			return;
		}
	}

	/** Ideas by category *********************************************************/

	$category = $posts_query->get( wp_idea_stream_get_category() );

	if ( ! empty( $category ) ) {
		// Make sure the post type is set to ideas
		$posts_query->set( 'post_type', $idea_post_type );

		// Define the current category
		wp_idea_stream_set_idea_var( 'is_category', $category );
	}

	/** Ideas by tag **************************************************************/

	$tag = $posts_query->get( wp_idea_stream_get_tag() );

	if ( ! empty( $tag ) ) {
		// Make sure the post type is set to ideas
		$posts_query->set( 'post_type', $idea_post_type );

		// Define the current tag
		wp_idea_stream_set_idea_var( 'is_tag', $tag );
	}

	/** Searching ideas ***********************************************************/

	$search = $posts_query->get( wp_idea_stream_search_rewrite_id() );

	if ( ! empty( $search ) ) {
		// Make sure the post type is set to ideas
		$posts_query->set( 'post_type', $idea_post_type );

		// Define the query as a search one
		$posts_query->set( 'is_search', true );

		/**
		 * Temporarly set the 's' parameter of WP Query
		 * This will be reset while building ideas main_query args
		 * @see wp_idea_stream_set_template()
		 */
		$posts_query->set( 's', $search );

		// Set the search conditionnal var
		wp_idea_stream_set_idea_var( 'is_search', true );
	}

	/** Changing order ************************************************************/

	// Here we're using built-in var
	$orderby = $posts_query->get( 'orderby' );

	// Make sure we are ordering ideas
	if ( ! empty( $orderby ) && $idea_post_type == $posts_query->get( 'post_type' ) ) {

		if ( ! wp_idea_stream_is_rating_disabled() && 'rates_count' == $orderby ) {
			/**
			 * It's an order by rates request, set the meta query to achieve this.
			 * Here we're not ordering yet, we simply make sure to get ideas that
			 * have been rated.
			 * Order will happen thanks to wp_idea_stream_set_rates_count_orderby()
			 * filter.
			 */
			$posts_query->set( 'meta_query', array(
				array(
					'key'     => '_ideastream_average_rate',
					'compare' => 'EXISTS'
				)
			) );
		}

		// Set the order by var
		wp_idea_stream_set_idea_var( 'orderby', $orderby );
	}

	// Set the idea archive var if viewing ideas archive
	if ( $posts_query->is_post_type_archive() ) {
		wp_idea_stream_set_idea_var( 'is_idea_archive', true );
	}

	$is_front_ideas = false;
	if ( wp_idea_stream_is_front_page() ) {
		$is_front_ideas = $posts_query->is_page && (int) $posts_query->get( 'page_id' ) === (int) get_option( 'page_on_front' ) && 'page' === get_option( 'show_on_front' );
	}

	/**
	 * Finally if post_type is ideas, then we're in IdeaStream's
	 * territory so set this
	 */
	if ( $idea_post_type === $posts_query->get( 'post_type' ) || $is_front_ideas ) {
		wp_idea_stream_set_idea_var( 'is_ideastream', true );

		// Reset the pagination
		if ( -1 !== $posts_query->get( 'p' ) ) {
			$posts_query->set( 'posts_per_page', wp_idea_stream_ideas_per_page() );
		}

		if ( $is_front_ideas ) {
			wp_idea_stream_set_idea_var( 'is_front_ideas', true );
		}
	}

	/**
	 * Fires after WP Idea Stream has parsed the main query vars.
	 *
	 * @since 2.4.0
	 *
	 * @param WP_Query &$posts_query The WP_Query instance (passed by reference).
	 */
	do_action_ref_array( 'wp_idea_stream_parse_query', array( &$posts_query ) );
}

/**
 * Loads the plugin's stylesheet
 *
 * @since 2.0.0
 */
function wp_idea_stream_enqueue_style() {
	$style_deps = apply_filters( 'wp_idea_stream_style_deps', array( 'dashicons' ) );
	wp_enqueue_style( 'wp-idea-stream-style', wp_idea_stream_get_stylesheet(), $style_deps, wp_idea_stream_get_version() );

	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$min = '';
	}

	if ( wp_idea_stream_is_user_profile() && wp_idea_stream_is_embed_profile() ) {
		wp_enqueue_style( 'wp-idea-stream-sharing-profile', includes_url( "css/wp-embed-template{$min}.css" ), array(), wp_idea_stream_get_version() );
	}
}

/**
 * Loads the embed stylesheet to be used inside
 * WordPress & IdeaStream embed templates
 *
 * @since 2.3.0
 */
function wp_idea_stream_enqueue_embed_style() {
	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$min = '';
	}

	wp_enqueue_style( 'wp-idea-stream-embed-style', wp_idea_stream_get_stylesheet( "embed-style{$min}" ), array(), wp_idea_stream_get_version() );
}

/** Conditional template tags *************************************************/

/**
 * Is this the admin part of IdeaStream
 *
 * @since 2.0.0
 *
 * @return bool true if on IdeaStream admin part, false otherwise
 */
function wp_idea_stream_is_admin() {
	$retval = false;

	// using this as is_admin() can be true in case of AJAX
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $retval;
	}

	// Get current screen
	$current_screen = get_current_screen();

	// Make sure the current screen post type is step and is the ideas one
	if ( ! empty( $current_screen->post_type ) && wp_idea_stream_get_post_type() == $current_screen->post_type ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is this Plugin's front end territory ?
 *
 * @since 2.0.0
 *
 * @return bool true if viewing an IdeaStream page, false otherwise
 */
function wp_idea_stream_is_ideastream() {
	return (bool) wp_idea_stream_get_idea_var( 'is_ideastream' );
}

/**
 * Is this the new idea form ?
 *
 * @since 2.0.0
 *
 * @return bool true if on the addnew form, false otherwise
 */
function wp_idea_stream_is_addnew() {
	return (bool) wp_idea_stream_get_idea_var( 'is_new' );
}

/**
 * Is this the edit idea form ?
 *
 * @since 2.0.0
 *
 * @return bool true if on the edit form, false otherwise
 */
function wp_idea_stream_is_edit() {
	return (bool) wp_idea_stream_get_idea_var( 'is_edit' );
}

/**
 * Is this the signup form ?
 *
 * @since 2.1.0
 *
 * @return bool true if on the edit form, false otherwise
 */
function wp_idea_stream_is_signup() {
	return (bool) wp_idea_stream_get_idea_var( 'is_signup' );
}

/**
 * Are we viewing a single idea ?
 *
 * @since 2.0.0
 *
 * @return bool true if on a single idea template, false otherwise
 */
function wp_idea_stream_is_single_idea() {
	return (bool) apply_filters( 'wp_idea_stream_is_single_idea', is_singular( wp_idea_stream_get_post_type() ) );
}

/**
 * Current ID for the idea being viewed
 *
 * @since 2.0.0
 *
 * @return int the current idea ID
 */
function wp_idea_stream_get_single_idea_id() {
	return (int) apply_filters( 'wp_idea_stream_get_single_idea_id', wp_idea_stream_get_idea_var( 'single_idea_id' ) );
}

/**
 * Are we viewing ideas archive ?
 *
 * @since 2.0.0
 *
 * @return bool true if on ideas archive, false otherwise
 */
function wp_idea_stream_is_idea_archive() {
	$retval = false;

	if ( is_post_type_archive( wp_idea_stream_get_post_type() ) || wp_idea_stream_get_idea_var( 'is_idea_archive' ) ) {
		$retval = true;
	}

	return apply_filters( 'wp_idea_stream_is_idea_archive', $retval );
}

/**
 * Are we viewing ideas by category ?
 *
 * @since 2.0.0
 *
 * @return bool true if viewing ideas categorized in a sepecific term, false otherwise.
 */
function wp_idea_stream_is_category() {
	$retval = false;

	if ( is_tax( wp_idea_stream_get_category() ) || wp_idea_stream_get_idea_var( 'is_category' ) ) {
		$retval = true;
	}

	return apply_filters( 'wp_idea_stream_is_category', $retval );
}

/**
 * Are we viewing ideas by tag ?
 *
 * @since 2.0.0
 *
 * @return bool true if viewing ideas tagged with a sepecific term, false otherwise.
 */
function wp_idea_stream_is_tag() {
	$retval = false;

	if ( is_tax( wp_idea_stream_get_tag() ) || wp_idea_stream_get_idea_var( 'is_tag' ) ) {
		$retval = true;
	}

	return apply_filters( 'wp_idea_stream_is_tag', $retval );
}

/**
 * Get / Set the current term being viewed
 *
 * @since 2.0.0
 *
 * @return object $current_term
 */
function wp_idea_stream_get_current_term() {
	$current_term = wp_idea_stream_get_idea_var( 'current_term' );

	if ( empty( $current_term ) ) {
		$current_term = get_queried_object();
	}

	wp_idea_stream_set_idea_var( 'current_term', $current_term );

	return apply_filters( 'wp_idea_stream_get_current_term', $current_term );
}

/**
 * Get the current term name
 *
 * @since 2.0.0
 *
 * @return string the term name
 */
function wp_idea_stream_get_term_name() {
	$term = wp_idea_stream_get_current_term();

	return apply_filters( 'wp_idea_stream_get_term_name', $term->name );
}

/**
 * Are we searching ideas ?
 *
 * @since 2.0.0
 *
 * @return bool true if an idea search is performed, otherwise false
 */
function wp_idea_stream_is_search() {
	$retval = false;

	if ( get_query_var( wp_idea_stream_search_rewrite_id() ) || wp_idea_stream_get_idea_var( 'is_search' ) ) {
		$retval = true;
	}

	return apply_filters( 'wp_idea_stream_is_search', $retval );
}

/**
 * Has the order changed to the type being checked
 *
 * @since 2.0.0
 *
 * @param  string $type the order to check
 * @return bool true if the order has changed from default one, false otherwise
 */
function wp_idea_stream_is_orderby( $type = '' ) {
	$retval = false;

	$orderby = wp_idea_stream_get_idea_var( 'orderby' );

	if ( empty( $orderby ) ) {
		$orderby = get_query_var( 'orderby' );
	}

	if ( ! empty( $orderby ) && $orderby == $type ) {
		$retval = true;
	}

	return apply_filters( 'wp_idea_stream_is_orderby', $retval, $type );
}

/**
 * Are viewing a user's profile ?
 *
 * @since 2.0.0
 *
 * @return bool true a user's profile is being viewed, false otherwise
 */
function wp_idea_stream_is_user_profile() {
	return (bool) apply_filters( 'wp_idea_stream_is_user_profile', wp_idea_stream_get_idea_var( 'is_user' ) );
}

/**
 * Are we viewing comments in user's profile
 *
 * @since 2.0.0
 *
 * @return bool true if viewing user's profile comments, false otherwise
 */
function wp_idea_stream_is_user_profile_comments() {
	return (bool) apply_filters( 'wp_idea_stream_is_user_profile_comments', wp_idea_stream_get_idea_var( 'is_user_comments' ) );
}

/**
 * Are we viewing rates in user's profile
 *
 * @since 2.0.0
 *
 * @return bool true if viewing user's profile rates, false otherwise
 */
function wp_idea_stream_is_user_profile_rates() {
	return (bool) apply_filters( 'wp_idea_stream_is_user_profile_rates', wp_idea_stream_get_idea_var( 'is_user_rates' ) );
}

/**
 * Are we viewing ideas in user's profile
 *
 * @since 2.0.0
 *
 * @return bool true if viewing ideas in the user's profile, false otherwise
 */
function wp_idea_stream_is_user_profile_ideas() {
	return (bool) ( ! wp_idea_stream_is_user_profile_comments() && ! wp_idea_stream_is_user_profile_rates() );
}

/**
 * Is this self profile ?
 *
 * @since 2.0.0
 *
 * @return bool true if current user is viewing his profile, false otherwise
 */
function wp_idea_stream_is_current_user_profile() {
	$current_user      = wp_idea_stream_get_idea_var( 'current_user' );
	$displayed_user_id = wp_idea_stream_get_idea_var( 'is_user' );

	if( empty( $current_user->ID ) ) {
		return false;
	}

	$is_user_profile = ( $current_user->ID == $displayed_user_id );

	/**
	 * Used Internally to map this function to BuddyPress bp_is_my_profile one
	 *
	 * @param  bool $is_user_profile whether the user is viewing his profile or not
	 */
	return (bool) apply_filters( 'wp_idea_stream_is_current_user_profile', $is_user_profile );
}

/**
 * Reset the page (post) title depending on the context
 *
 * @since 2.0.0
 *
 * @param string $context the context to build the title for
 * @return string the post title
 */
function wp_idea_stream_reset_post_title( $context = '' ) {
	$post_title = wp_idea_stream_archive_title();

	$link = wp_idea_stream_get_root_url();
	if ( wp_idea_stream_is_front_page() ) {
		$link = home_url();
	}

	switch( $context ) {
		case 'archive' :
			$post_title =  '<a href="' . esc_url( $link ) . '">' . $post_title . '</a>';

			if ( wp_idea_stream_user_can( 'publish_ideas' ) ) {
				$post_title .= ' <a href="' . esc_url( wp_idea_stream_get_form_url() ) .'" class="button wpis-title-button">' . esc_html__( 'Add new', 'wp-idea-stream' ) . '</a>';
			}
			break;

		case 'taxonomy' :
			$post_title = '<a href="' . esc_url( $link ) . '">' . $post_title . '</a>';
			$post_title .= '<span class="idea-title-sep"></span>' . wp_idea_stream_get_term_name();
			break;

		case 'user-profile':
			$post_title = '<a href="' . esc_url( $link ) . '">' . $post_title . '</a>';
			$post_title .= '<span class="idea-title-sep"></span>' . sprintf( esc_html__( '%s&#39;s profile', 'wp-idea-stream' ), wp_idea_stream_users_get_displayed_user_displayname() );
			break;

		case 'new-idea' :
			$post_title = '<a href="' . esc_url( $link ) . '">' . $post_title . '</a>';
			$post_title .= '<span class="idea-title-sep"></span>' . __( 'New Idea', 'wp-idea-stream' );
			break;

		case 'edit-idea' :
			$post_title = '<a href="' . esc_url( $link ) . '">' . $post_title . '</a>';
			$post_title .= '<span class="idea-title-sep"></span>' . __( 'Edit Idea', 'wp-idea-stream' );
			break;

		case 'signup' :
			$post_title = '<a href="' . esc_url( $link ) . '">' . $post_title . '</a>';
			$post_title .= '<span class="idea-title-sep"></span>' . __( 'Create an account', 'wp-idea-stream' );
			break;
	}

	/**
	 * @param  string $post_title the title for the template
	 * @param  string $context the context
	 */
	return apply_filters( 'wp_idea_stream_reset_post_title', $post_title, $context );
}

/**
 * Filters the <title> content
 *
 * Inspired by bbPress's bbp_title()
 *
 * @since 2.0.0
 *
 * @param array $title the title parts
 * @return string the page title meta tag
 */
function wp_idea_stream_title( $title_array = array() ) {
	if ( ! wp_idea_stream_is_ideastream() ) {
		return $title_array;
	}

	$new_title = array();

	if ( wp_idea_stream_is_addnew() ) {
		$new_title[] = esc_attr__( 'New idea', 'wp-idea-stream' );
	} elseif ( wp_idea_stream_is_edit() ) {
		$new_title[] = esc_attr__( 'Edit idea', 'wp-idea-stream' );
	} elseif ( wp_idea_stream_is_user_profile() ) {
		$new_title[] = sprintf( esc_html__( '%s&#39;s profile', 'wp-idea-stream' ), wp_idea_stream_users_get_displayed_user_displayname() );
	} elseif ( wp_idea_stream_is_single_idea() ) {
		$new_title[] = single_post_title( '', false );
	} elseif ( is_tax() ) {
		$term = wp_idea_stream_get_current_term();
		if ( $term ) {
			$tax = get_taxonomy( $term->taxonomy );

			// Catch the term for later use
			wp_idea_stream_set_idea_var( 'current_term', $term );

			$new_title[] = single_term_title( '', false );
			$new_title[] = $tax->labels->name;
		}
	} elseif ( wp_idea_stream_is_signup() ) {
		$new_title[] = esc_html__( 'Create an account', 'wp-idea-stream' );
	} else {
		$new_title[] = esc_html__( 'Ideas', 'wp-idea-stream' );
	}

	// Compare new title with original title
	if ( empty( $new_title ) ) {
		return $title_array;
	}

	$title_array = array_diff( $title_array, $new_title );
	$new_title_array = array_merge( $title_array, $new_title );

	/**
	 * @param  string $new_title the filtered title
	 * @param  string $sep
	 * @param  string $seplocation
	 * @param  string $title the original title meta tag
	 */
	return apply_filters( 'wp_idea_stream_title', $new_title_array, $title_array, $new_title );
}

/**
 * Set the document title for IdeaStream pages
 *
 * @since  2.3.0
 *
 * @param  array  $document_title The WordPress Document title
 * @return array                  The IdeaStream Document title
 */
function wp_idea_stream_document_title_parts( $document_title = array() ) {
	if ( ! wp_idea_stream_is_ideastream() ) {
		return $document_title;
	}

	$new_document_title = $document_title;

	// Reset the document title if needed
	if ( ! wp_idea_stream_is_single_idea() ) {
		$title = (array) wp_idea_stream_title();

		// On user's profile, add some piece of info
		if ( wp_idea_stream_is_user_profile() && count( $title ) === 1 ) {
			// Seeing comments of the user
			if ( wp_idea_stream_is_user_profile_comments() ) {
				$title[] = __( 'Idea Comments', 'wp-idea-stream' );

				// Get the pagination page
				if ( get_query_var( wp_idea_stream_cpage_rewrite_id() ) ) {
					$cpage = get_query_var( wp_idea_stream_cpage_rewrite_id() );

				} elseif ( ! empty( $_GET[ wp_idea_stream_cpage_rewrite_id() ] ) ) {
					$cpage = $_GET[ wp_idea_stream_cpage_rewrite_id() ];
				}

				if ( ! empty( $cpage ) ) {
					$title['page'] = sprintf( __( 'Page %s', 'wp-idea-stream' ), (int) $cpage );
				}

			// Seeing Ratings for the user
			} elseif( wp_idea_stream_is_user_profile_rates() ) {
				$title[] = __( 'Idea Ratings', 'wp-idea-stream' );

			// Seeing The root profile
			} else {
				$title[] = __( 'Ideas', 'wp-idea-stream' );
			}
		}

		// Get WordPress Separator
		$sep = apply_filters( 'document_title_separator', '-' );

		$new_document_title['title'] = implode( " $sep ", array_filter( $title ) );;
	}

	// Set the site name if not already set.
	if ( ! isset( $new_document_title['site'] ) ) {
		$new_document_title['site'] = get_bloginfo( 'name', 'display' );
	}

	// Unset tagline for IdeaStream Pages
	if ( isset( $new_document_title['tagline'] ) ) {
		unset( $new_document_title['tagline'] );
	}

	return apply_filters( 'wp_idea_stream_document_title_parts', $new_document_title, $document_title );
}

/**
 * Remove the site description from title.
 * @todo we should make sure $wp_query->is_home is false in a future release
 *
 * @since 2.1.0
 *
 * @param  string $new_title the filtered title
 * @param  string $sep
 * @param  string $seplocation
 */
function wp_idea_stream_title_adjust( $title = '', $sep = '&raquo;', $seplocation = '' ) {
	if ( ! wp_idea_stream_is_ideastream() ) {
		return $title;
	}

	$site_description = get_bloginfo( 'description', 'display' );
	if ( ! empty( $sep ) ) {
		$site_description = ' ' . $sep . ' ' . $site_description;
	}

	$new_title = str_replace( $site_description, '', $title );

	return apply_filters( 'wp_idea_stream_title_adjust', $new_title, $title, $sep, $seplocation );
}

/**
 * Output a body class if in IdeaStream's territory
 *
 * Inspired by bbPress's bbp_body_class()
 *
 * @since 2.0.0
 *
 * @param  array $wp_classes
 * @param  array $custom_classes
 * @return array the new Body Classes
 */
function wp_idea_stream_body_class( $wp_classes, $custom_classes = false ) {

	$ideastream_classes = array();

	/** IdeaStream **************************************************************/

	if ( wp_idea_stream_is_ideastream() ) {
		$ideastream_classes[] = 'ideastream';

		// Adapts the display to the Twentyseventeen page layout option.
		if ( 'twentyseventeen' === get_template() ) {
			$wp_classes = array_diff( $wp_classes, array( 'has-sidebar', 'blog', 'archive' ) );
			$ideastream_classes[] = 'page';

			if ( ! is_single() ) {
				if ( 'one-column' === get_theme_mod( 'page_layout' ) ) {
					$ideastream_classes[] = 'page-one-column';
				} else {
					$ideastream_classes[] = 'page-two-column';
				}
			}
		}
	}

	/** Clean up **************************************************************/

	// Merge WP classes with IdeaStream classes and remove any duplicates
	$classes = array_unique( array_merge( (array) $ideastream_classes, (array) $wp_classes ) );

	/**
	 * @param array $classes returned classes
	 * @param array $ideastream_classes specific IdeaStream classes
	 * @param array $wp_classes regular WordPress classes
	 * @param array $custom_classes
	 */
	return apply_filters( 'wp_idea_stream_body_class', $classes, $ideastream_classes, $wp_classes, $custom_classes );
}

/**
 * Adds a 'type-page' class as the page template is the the most commonly targetted
 * as the root template.
 *
 * NB: TwentySixteen needs this to display the content on full available width
 *
 * @since  2.3.0
 *
 * @param  $wp_classes
 * @param  $theme_class
 * @return array Ideastream Post Classes
 */
function wp_idea_stream_post_class( $wp_classes, $theme_class ) {
	if ( wp_idea_stream_is_ideastream() ) {
		$classes = array_unique( array_merge( array( 'type-page' ), (array) $wp_classes ) );
	} else {
		$classes = $wp_classes;
	}

	return apply_filters( 'wp_idea_stream_body_class', $classes, $wp_classes, $theme_class );
}

/**
 * Reset postdata if needed
 *
 * @since 2.0.0
 */
function wp_idea_stream_maybe_reset_postdata() {
	if ( wp_idea_stream_get_idea_var( 'needs_reset' ) ) {
		wp_reset_postdata();

		/**
		 * Internally used in BuddyPress Groups pages
		 * to reset the $wp_query->post to BuddyPress Group's page one
		 */
		do_action( 'wp_idea_stream_maybe_reset_postdata' );
	}
}

/**
 * Get the WP Nav Items for WP Idea Stream main areas.
 *
 * @since  2.4.0
 *
 * @return array A list of WP Nav Items object.
 */
function wp_idea_stream_get_nav_items() {
	$nav_items = array(
		'idea_archive' => array(
			'id'         => 'wp-idea-stream-archive',
			'title'      => html_entity_decode( wp_idea_stream_archive_title(), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'url'        => esc_url_raw( wp_idea_stream_get_root_url() ),
			'object'     => 'wp-idea-stream-archive',
		),
		'addnew' => array(
			'id'         => 'wp-idea-stream-new',
			'title'      => html_entity_decode( __( 'New idea', 'wp-idea-stream' ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'url'        => esc_url_raw( wp_idea_stream_get_form_url() ),
			'object'     => 'wp-idea-stream-new',
		)
	);

	if ( is_user_logged_in() ) {
		$nav_items['current_user_profile'] = array(
			'id'         => 'wp-idea-stream-profile',
			'title'      => html_entity_decode( __( 'My profile', 'wp-idea-stream' ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'url'        => esc_url_raw( wp_idea_stream_users_get_logged_in_profile_url() ),
			'object'     => 'wp-idea-stream-profile',
		);
	}

	foreach ( $nav_items as $nav_item_key => $nav_item ) {
		$nav_items[ $nav_item_key ] = array_merge( $nav_item, array(
			'type'       => 'wp_idea_stream_nav_item',
			'type_label' => _x( 'Custom Link', 'customizer menu type label', 'wp-idea-stream' ),
			'object_id'  => -1,
		) );
	}

	return apply_filters( 'wp_idea_stream_get_nav_items', $nav_items );
}

/**
 * Validate & Populate nav item URLs.
 *
 * @since  2.4.0
 *
 * @param  array  $menu_items WP Nav Items list.
 * @return array              WP Nav Items list.
 */
function wp_idea_stream_validate_nav_menu_items( $menu_items = array() ) {
	$nav_items = wp_filter_object_list( $menu_items, array( 'type' => 'wp_idea_stream_nav_item' ), 'and', 'object' );

	if ( empty( $nav_items ) ) {
		return $menu_items;
	}

	$nav_items_urls = wp_list_pluck( wp_idea_stream_get_nav_items(), 'url', 'id' );

	foreach ( $menu_items as $km => $om ) {
		// It's not a WP Idea Stream menu
		if ( ! in_array( $om->object, $nav_items, true ) ) {
			continue;
		}

		// Url is not available.
		if ( ! isset( $nav_items_urls[ $om->object ] ) ) {
			unset( $menu_items[ $km ] );
			continue;
		}

		$menu_items[ $km ]->url = $nav_items_urls[ $om->object ];

		if ( ( 'wp-idea-stream-archive' === $om->object && wp_idea_stream_is_ideastream() )
			|| ( 'wp-idea-stream-profile' === $om->object && wp_idea_stream_is_current_user_profile() )
			|| ( 'wp-idea-stream-new' === $om->object && wp_idea_stream_is_addnew() )
		) {
			$menu_items[ $km ]->classes = array_merge( $om->classes, array( 'current-menu-item', 'current_page_item' ) );
		}
	}

	return apply_filters( 'wp_idea_stream_validate_nav_menu_items', $menu_items, $nav_items, $nav_items_urls );
}

/**
 * Add WP Idea Stream Nav Items to the Customizer.
 *
 * @since  2.4.0
 *
 * @param  array  $items  The array of menu items.
 * @param  string $type   The object type.
 * @param  string $object The object name.
 * @param  int    $page   The current page number.
 * @return array          The array of menu items.
 */
function wp_idea_stream_customizer_get_nav_menus_items( $items = array(), $type = '', $object = '', $page = 0 ) {
	if ( 'wp_idea_stream' !== $type ) {
		return $items;
	}

	// Get the nav items.
	$items = array_values( wp_idea_stream_get_nav_items() );

	return array_slice( $items, 10 * $page, 10 );
}

/**
 * Add WP Idea Stream nav item type to the available Customizer Post types.
 *
 * @since  2.4.0
 *
 * @param  array $item_types Custom menu item types.
 * @return array             Custom menu item types + WP Idea Stream item types.
 */
function wp_idea_stream_customizer_set_nav_menus_item_types( $item_types = array() ) {
	$item_types = array_merge( $item_types, array(
		'wp_idea_stream' => array(
			'title'  => _x( 'WP Idea Stream', 'customizer menu section title', 'wp-idea-stream' ),
			'type'   => 'wp_idea_stream',
			'object' => 'wp_idea_stream',
		),
	) );

	return $item_types;
}

/**
 * Filters nav menus looking for the root page to eventually make it current if not the
 * case although it's IdeaStream's territory
 *
 * @package WP Idea Stream
 * @subpackage core/template-functions
 *
 * @since 2.0.0
 * @deprecated 2.4.0
 *
 * @param  array  $sorted_menu_items list of menu items of the wp_nav menu
 * @param  array  $args
 * @return array  the menu items with specific classes if needed
 */
function wp_idea_stream_wp_nav( $sorted_menu_items = array(), $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.4.0' );

	return apply_filters( 'wp_idea_stream_wp_nav', $sorted_menu_items );
}

/**
 * Filters edit post link to avoid its display when needed
 *
 * @since 2.0.0
 *
 * @param  string $edit_link the link to edit the post
 * @param  int    $post_id   the post ID
 * @return mixed false if needed, original edit link otherwise
 */
function wp_idea_stream_edit_post_link( $edit_link = '', $post_id = 0 ) {
	/**
	 * using the capability check prevents edit link to display in case current user is the
	 * author of the idea and don't have the minimal capability to open the idea in WordPress
	 * Administration edit screen
	 */
	if ( wp_idea_stream_is_ideastream() && ( 0 === $post_id || ! wp_idea_stream_user_can( 'edit_ideas' ) ) ) {
		/**
		 * @param  bool false to be sure the edit link won't show
		 * @param  string $edit_link
		 * @param  int $post_id
		 */
		return apply_filters( 'wp_idea_stream_edit_post_link', false, $edit_link, $post_id );
	}

	return $edit_link;
}

/**
 * Use the Embed Profile template when an Embed profile is requested
 *
 * @since 2.3.0
 *
 * @param  string $template The WordPress Embed template
 * @return string           The appropriate template to use
 */
function wp_idea_stream_embed_profile( $template = '' ) {
	if ( ! wp_idea_stream_get_idea_var( 'is_user_embed' ) || ! wp_idea_stream_get_idea_var( 'is_user' ) ) {
		return $template;
	}

	return wp_idea_stream_get_template_part( 'embed', 'profile', false );
}

/**
 * Adds oEmbed discovery links in the website <head> for the IdeaStream user's profile root page.
 *
 * @since 2.3.0
 */
function wp_idea_stream_oembed_add_discovery_links() {
	if ( ! wp_idea_stream_is_user_profile_ideas() || ! wp_idea_stream_is_embed_profile() ) {
		return;
	}

	$user_link = wp_idea_stream_users_get_user_profile_url( wp_idea_stream_users_displayed_user_id(), '', true );
	$output = '<link rel="alternate" type="application/json+oembed" href="' . esc_url( get_oembed_endpoint_url( $user_link ) ) . '" />' . "\n";

	if ( class_exists( 'SimpleXMLElement' ) ) {
		$output .= '<link rel="alternate" type="text/xml+oembed" href="' . esc_url( get_oembed_endpoint_url( $user_link, 'xml' ) ) . '" />' . "\n";
	}

	/**
	 * Filter the oEmbed discovery links HTML.
	 *
	 * @since 2.3.0
	 *
	 * @param string $output HTML of the discovery links.
	 */
	echo apply_filters( 'wp_idea_stream_users_oembed_add_discovery_links', $output );
}

/**
 * Get the search query.
 *
 * @since  2.4.0
 *
 * @return string The search query
 */
function wp_idea_stream_get_search_query() {
	return get_query_var( wp_idea_stream_search_rewrite_id() );
}
