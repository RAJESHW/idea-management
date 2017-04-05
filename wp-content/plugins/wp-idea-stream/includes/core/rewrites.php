<?php
/**
 * WP Idea Stream Rewrites.
 *
 * Mainly inspired by bbPress way of dealing with rewrites
 * @see bbpress main class.
 *
 * Most of the job is done in the class WP_Idea_Stream_Rewrites
 * @see  core/classes
 *
 * @package WP Idea Stream\core
 *
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Checks whether the current site is using default permalink settings or custom one
 *
 * @since 2.0.0
 *
 * @return bool True if custom permalink are one, false otherwise
 */
function wp_idea_stream_is_pretty_links() {
	$pretty_links = wp_idea_stream_get_idea_var( 'pretty_links' );
	return (bool) apply_filters( 'wp_idea_stream_is_pretty_links', ! empty( $pretty_links ) );
}

/**
 * Get the slug used for paginated requests
 *
 * @since 2.0.0
 *
 * @global object $wp_rewrite The WP_Rewrite object
 * @return string             the pagination slug
 */
function wp_idea_stream_paged_slug() {
	global $wp_rewrite;

	if ( empty( $wp_rewrite ) ) {
		return false;
	}

	return $wp_rewrite->pagination_base;
}

/**
 * Rewrite id for the user's profile
 *
 * @since 2.0.0
 *
 * @return string          the user's profile rewrite id
 */
function wp_idea_stream_user_rewrite_id( $default = 'is_user' ) {
	return apply_filters( 'wp_idea_stream_user_rewrite_id', $default );
}

/**
 * Rewrite id for the user's rates
 *
 * @since 2.0.0
 *
 * @return string          the user's rates rewrite id
 */
function wp_idea_stream_user_rates_rewrite_id( $default = 'is_rates' ) {
	return apply_filters( 'wp_idea_stream_user_rates_rewrite_id', $default );
}

/**
 * Rewrite id for the user's comments
 *
 * @since 2.0.0
 *
 * @return string          the user's comments rewrite id
 */
function wp_idea_stream_user_comments_rewrite_id( $default = 'is_comments' ) {
	return apply_filters( 'wp_idea_stream_user_comments_rewrite_id', $default );
}

/**
 * Rewrite id for actions
 *
 * @since 2.0.0
 *
 * @return string          the actions rewrite id
 */
function wp_idea_stream_action_rewrite_id( $default = 'is_action' ) {
	return apply_filters( 'wp_idea_stream_action_rewrite_id', $default );
}

/**
 * Rewrite id for searching in ideas
 *
 * @since 2.0.0
 *
 * @return string          searching in ideas rewrite id
 */
function wp_idea_stream_search_rewrite_id( $default = 'idea_search' ) {
	return apply_filters( 'wp_idea_stream_search_rewrite_id', $default );
}

/**
 * Rewrite id for user's comments pagination
 *
 * @since 2.0.0
 *
 * @return string          user's comments pagination rewrite id
 */
function wp_idea_stream_cpage_rewrite_id( $default = 'cpaged' ) {
	return apply_filters( 'wp_idea_stream_cpage_rewrite_id', $default );
}

/**
 * Delete a blogs rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * Inspired by bbPress.
 * @see bbp_delete_rewrite_rules()
 *
 * @since 2.0.0
 */
function wp_idea_stream_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}
