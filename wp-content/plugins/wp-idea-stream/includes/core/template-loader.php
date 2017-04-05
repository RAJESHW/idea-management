<?php
/**
 * WP Idea Stream template loader.
 *
 * functions to load and prepare IdeaStream templates
 * Mainly Inspired by bbPress
 *
 * Most of the job is done in the class WP_Idea_Stream_Template_Loader
 * @see  core/classes
 *
 * @package   WP Idea Stream\core
 *
 * @since  2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Buffer a template part to build the content of a page
 *
 * @since 2.0.0
 *
 * @param  string  $slug template slug
 * @param  string  $name template name
 * @param  bool    $echo output or return ?
 * @return string $output html of the buffered template part
 */
function wp_idea_stream_buffer_template_part( $slug, $name = null, $echo = true ) {
	ob_start();

	wp_idea_stream_template_part( $slug, $name );

	// Get the output buffer contents
	$output = ob_get_clean();

	// Echo or return the output buffer contents
	if ( true === $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

/**
 * Add a specific header and footer parts to single idea.
 *
 * @since 2.0.0
 * @since 2.3.0 buffer feedback message to eventually display them on the single idea
 *
 * @param  string $content the content of the idea
 * @return string $new_content the content of the idea
 */
function wp_idea_stream_buffer_single_idea( $content = '' ) {
	$new_content  = '<div id="wp-idea-stream">';

	// Eventually include some feedback messages
	ob_start();

	wp_idea_stream_user_feedback();

	$new_content .= ob_get_clean();

	// add a header
	$new_content .= wp_idea_stream_buffer_template_part( 'idea', 'header', false );

	// keep the content unchanged
	$new_content .= $content;

	// add a footer
	$new_content .= wp_idea_stream_buffer_template_part( 'idea', 'footer', false );

	$new_content .= '</div>';

	return $new_content;
}

/**
 * Load a template part
 *
 * @since 2.0.0
 *
 * @param  string  $slug template slug
 * @param  string  $name template name
 * @param  bool    $load should we load ?
 * @param  bool    $require_once should we load it once only ?
 */
function wp_idea_stream_get_template_part( $slug, $name = null, $load = true, $require_once = true ) {
	$templates = new WP_Idea_Stream_Template_Loader;

	return $templates->get_template_part( $slug, $name, $load, $require_once );
}

/**
 * Load a template part as many time as needed.
 *
 * Shortcut for wp_idea_stream_get_template_part() having require once set to false.
 *
 * @since 2.0.0
 *
 * @param  string $slug template slug
 * @param  string $name template name
 * @param  bool   $require_once default to false (for use in loops)
 */
function wp_idea_stream_template_part( $slug, $name = null, $require_once = false ) {
	return wp_idea_stream_get_template_part( $slug, $name, true, $require_once );
}

/**
 * Get the stylesheet to apply by first looking in
 * the theme's wp-idea-stream subdirectory
 *
 * @since 2.0.0
 * @since 2.3.0 Added the $css parameter to be able to get any stylesheet
 *
 * @param  string $css the name of the file to load
 * @return string the url to the stylesheet
 */
function wp_idea_stream_get_stylesheet( $css = 'style' ) {
	$style = new WP_Idea_Stream_Template_Loader;

	return $style->get_stylesheet( $css );
}

/**
 * Fill up some WordPress globals with dummy data
 *
 * Based on bbPress bbp_theme_compat_reset_post() function
 *
 * @since 2.0.0
 *
 * @global WP_Query $wp_query
 * @global WP_Post  $post
 * @param  array    $args
 */
function wp_idea_stream_reset_post( $args = array() ) {
	global $wp_query, $post;

	// Switch defaults if post is set
	if ( isset( $wp_query->post ) ) {
		$dummy = wp_parse_args( $args, array(
			'ID'                    => $wp_query->post->ID,
			'post_status'           => $wp_query->post->post_status,
			'post_author'           => $wp_query->post->post_author,
			'post_parent'           => $wp_query->post->post_parent,
			'post_type'             => $wp_query->post->post_type,
			'post_date'             => $wp_query->post->post_date,
			'post_date_gmt'         => $wp_query->post->post_date_gmt,
			'post_modified'         => $wp_query->post->post_modified,
			'post_modified_gmt'     => $wp_query->post->post_modified_gmt,
			'post_content'          => $wp_query->post->post_content,
			'post_title'            => $wp_query->post->post_title,
			'post_excerpt'          => $wp_query->post->post_excerpt,
			'post_content_filtered' => $wp_query->post->post_content_filtered,
			'post_mime_type'        => $wp_query->post->post_mime_type,
			'post_password'         => $wp_query->post->post_password,
			'post_name'             => $wp_query->post->post_name,
			'guid'                  => $wp_query->post->guid,
			'menu_order'            => $wp_query->post->menu_order,
			'pinged'                => $wp_query->post->pinged,
			'to_ping'               => $wp_query->post->to_ping,
			'ping_status'           => $wp_query->post->ping_status,
			'comment_status'        => $wp_query->post->comment_status,
			'comment_count'         => $wp_query->post->comment_count,
			'filter'                => $wp_query->post->filter,

			'is_404'                => false,
			'is_page'               => false,
			'is_single'             => false,
			'is_archive'            => false,
			'is_tax'                => false,
		) );
	} else {
		$dummy = wp_parse_args( $args, array(
			'ID'                    => -9999,
			'post_status'           => 'publish',
			'post_author'           => 0,
			'post_parent'           => 0,
			'post_type'             => 'page',
			'post_date'             => 0,
			'post_date_gmt'         => 0,
			'post_modified'         => 0,
			'post_modified_gmt'     => 0,
			'post_content'          => '',
			'post_title'            => '',
			'post_excerpt'          => '',
			'post_content_filtered' => '',
			'post_mime_type'        => '',
			'post_password'         => '',
			'post_name'             => '',
			'guid'                  => '',
			'menu_order'            => 0,
			'pinged'                => '',
			'to_ping'               => '',
			'ping_status'           => '',
			'comment_status'        => 'closed',
			'comment_count'         => 0,
			'filter'                => 'raw',

			'is_404'                => false,
			'is_page'               => false,
			'is_single'             => false,
			'is_archive'            => false,
			'is_tax'                => false,
		) );
	}

	// Bail if dummy post is empty
	if ( empty( $dummy ) ) {
		return;
	}

	// Set the $post global
	$post = new WP_Post( (object) $dummy );

	// Copy the new post global into the main $wp_query
	$wp_query->post       = $post;
	$wp_query->posts      = array( $post );

	// Prevent comments form from appearing
	$wp_query->post_count = 1;
	$wp_query->is_404     = $dummy['is_404'];
	$wp_query->is_page    = $dummy['is_page'];
	$wp_query->is_single  = $dummy['is_single'];
	$wp_query->is_archive = $dummy['is_archive'];
	$wp_query->is_tax     = $dummy['is_tax'];

	// Clean up the dummy post
	unset( $dummy );

	/**
	 * Force the header back to 200 status if not a deliberate 404
	 */
	if ( ! $wp_query->is_404() ) {
		status_header( 200 );
	}
}

/**
 * Set the template to use, buffers the needed template parts
 * and resets post vars.
 *
 * @since 2.0.0
 *
 * @global $wp_query
 * @param  string $template name of the template to use
 * @return string $template.
 */
function wp_idea_stream_set_template( $template = '' ) {
	global $wp_query;

	/**
	 * Bail if BuddyPress, we'll use its theme compatibility
	 * feature.
	 */
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return $template;
	}

	if ( wp_idea_stream_get_idea_var( 'is_ideastream' ) && ! is_404() && ! is_embed() ) {

		// Try to see if the theme has a specific template for WP Idea Stream
		$template = get_query_template( 'ideastream' );

		if ( empty( $template ) ) {
			// else Try the page template
			$template = get_query_template( 'page', array( 'page.php' ) );
		}

		if (  empty( $template ) ) {
			// finally fall back to the index template
			$template = get_index_template();
		}

		// Define it into plugin's vars
		wp_idea_stream_set_idea_var( 'template_file', $template );

		/**
		 * First get results of the main query if not on a single idea.
		 * and build plugin's main_query var.
		 */
		if( ! wp_idea_stream_is_single_idea() ) {
			if ( ! wp_idea_stream_get_idea_var( 'is_front_ideas' ) ) {
				wp_idea_stream_set_idea_var( 'main_query', array(
					'ideas'      => $wp_query->posts,
					'total'      => $wp_query->found_posts,
					'query_vars' => array(
						'author'     => $wp_query->query_vars['author'],
						'per_page'   => $wp_query->query_vars['posts_per_page'],
						'page'       => ! empty( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1,
						'search'     => $wp_query->query_vars['s'],
						'exclude'    => $wp_query->query_vars['post__not_in'],
						'include'    => $wp_query->query_vars['post__in'],
						'orderby'    => ! empty( $wp_query->query_vars['orderby'] ) ? $wp_query->query_vars['orderby'] : 'date',
						'order'      => $wp_query->query_vars['order'],
						'meta_query' => $wp_query->meta_query->queries,
						'tax_query'  => $wp_query->tax_query->queries,
					)
				) );
			}

			// Resetting the 's' query var now we got main query's result.
			set_query_var( 's', '' );

			// Init template args
			$template_args = array(
				'post_title'     => '',
				'comment_status' => 'closed',
				'is_archive'     => true,
				'is_tax'         => false,
				'template_slug'  => 'archive',
				'template_name'  => '',
				'context'        => '',
			);

			// Main plugin's archive page
			if ( is_post_type_archive( wp_idea_stream_get_post_type() ) || wp_idea_stream_get_idea_var( 'is_front_ideas' ) ) {
				$template_args['context'] = 'archive';
			}

			// Category / tag archive pages
			if ( wp_idea_stream_get_idea_var( 'is_category' ) || wp_idea_stream_get_idea_var( 'is_tag' ) ) {
				$template_args['is_tax']  = true;
				$template_args['context'] = 'taxonomy';
			}

			// User's profile pages
			if ( wp_idea_stream_get_idea_var( 'is_user' ) ) {
				$template_args['template_slug'] = 'user';
				$template_args['template_name'] = 'profile';
				$template_args['context']       = 'user-profile';
			}

			if ( wp_idea_stream_get_idea_var( 'is_action' ) ) {
				$template_args['is_archive']    = false;

				// New idea form
				if ( wp_idea_stream_is_addnew() ) {
					$template_args['template_slug'] = 'idea';
					$template_args['template_name'] = 'form';
					$template_args['context']       = 'new-idea';

				} else if ( wp_idea_stream_is_signup() ) {
					$template_args['template_slug'] = 'signup';
					$template_args['context']       = 'signup';

				// Allow plugins to add custom action
				} else if ( has_filter( 'wp_idea_stream_template_args' ) ) {
					/**
					 * Custom action ?
					 *
					 * @param array $template_args the template arguments used to reset the post
					 */
					$template_args = apply_filters( 'wp_idea_stream_template_args', $template_args );
				}
			}

			// Reset WordPress $post global.
			wp_idea_stream_reset_post( array(
				'ID'             => 0,
				'post_title'     => wp_idea_stream_reset_post_title( $template_args['context'] ),
				'post_author'    => 0,
				'post_date'      => 0,
				'post_type'      => 'ideas',
				'post_status'    => 'publish',
				'is_archive'     => $template_args['is_archive'],
				'comment_status' => $template_args['comment_status'],
				'post_password'  => false,
				'is_tax'         => $template_args['is_tax'],
			) );

			/**
			 * Internally used to redirect to BuddyPress member's profile
			 * if needed
			 *
			 * @param  string $context to help choosing the best template to use
			 */
			do_action( 'wp_idea_stream_set_core_template', $template_args['context'], $template_args );

		} else {
			$query_loop = new stdClass();
			$query_loop->idea = $wp_query->post;

			// Should we use a custom template for single ideas ?
			$specific_single_template = get_query_template( 'single-ideastream' );

			if ( ! empty( $specific_single_template ) ) {
				$template = $specific_single_template;
			}

			// Populate the global query loop with current idea
			wp_idea_stream_set_idea_var( 'query_loop', $query_loop );

			// Add the id to globals
			wp_idea_stream_set_idea_var( 'single_idea_id', $wp_query->post->ID );

			// Are we editing an idea ?
			if ( wp_idea_stream_is_edit() ) {

				// Check if the idea is currently being edited by someone else
				$user_is_editing = wp_idea_stream_ideas_lock_idea( $query_loop->idea->ID );

				if ( ! empty( $user_is_editing ) ) {
					// Redirect the user
					wp_safe_redirect( add_query_arg( 'info', 1, wp_idea_stream_get_redirect_url() ) );
					exit();
				}

				// Bail if user can't edit the idea
				if ( ! wp_idea_stream_ideas_can_edit( $query_loop->idea ) ) {
					// Redirect the user and inform him the talk cannot be edited by him.
					wp_safe_redirect( add_query_arg( 'error', 2, wp_idea_stream_get_redirect_url() ) );
					exit();
				}

				// Inform the idea is to display in an edit form
				$query_loop->idea->is_edit = true;

				$template_args = array(
					'template_slug' => 'idea',
					'template_name' => 'form',
					'context'       => 'edit-idea',
				);

				$single_args = array(
					'ID'             => 0,
					'post_title'     => wp_idea_stream_reset_post_title( $template_args['context'] ),
					'post_author'    => 0,
					'post_date'      => 0,
					'post_type'      => 'ideas',
					'post_status'    => 'publish',
					'is_archive'     => false,
					'comment_status' => false,
					'post_password'  => false,
				);

			// Or simply viewing one ?
			} else {
				$template_args = array( 'context' => 'single-idea' );
				$single_args = array(
					'is_single'    => true,
				);
			}

			/**
			 * @param array $single_args the single arguments used to reset the post
			 */
			wp_idea_stream_reset_post( apply_filters( 'wp_idea_stream_single_template_args', $single_args ) );

			/**
			 * Internally used to redirect to Buddypress Group's
			 * single idea template if needed
			 *
			 * @param  WP_Post $query_loop->idea the idea to display
			 */
			do_action( 'wp_idea_stream_set_single_template', $query_loop->idea, $template_args );
		}
	}

	/**
	 * No IdeaStream template matched
	 */
	do_action( 'wp_idea_stream_set_template' );

	return $template;
}
