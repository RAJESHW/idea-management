<?php
/**
 * WP Idea Stream Functions.
 *
 * Generic functions used at various places in the plugin
 *
 * @package WP Idea Stream\core
 *
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/** Globals *******************************************************************/

/**
 * Get the plugin's current version
 *
 * @since 2.0.0
 *
 * @return string Plugin's current version
 */
function wp_idea_stream_get_version() {
	return wp_idea_stream()->version;
}

/**
 * Get the DB verion of the plugin
 *
 * Used to check wether to run the upgrade
 * routine of the plugin.
 * @see  core/upgrade > wp_idea_stream_is_upgrade()
 *
 * @since 2.0.0
 *
 * @return string DB version of the plugin
 */
function wp_idea_stream_db_version() {
	$db_version = get_option( '_ideastream_vestion' );

	if ( empty( $db_version ) ) {
		$db_version = get_option( '_ideastream_version', 0 );
	}

	return $db_version;
}

/**
 * Get plugin's basename
 *
 * @since 2.0.0
 *
 * @return string Plugin's basename
 */
function wp_idea_stream_get_basename() {
	return apply_filters( 'wp_idea_stream_get_basename', wp_idea_stream()->basename );
}

/**
 * Get plugin's main path
 *
 * @since 2.0.0
 *
 * @return string plugin's main path
 */
function wp_idea_stream_get_plugin_dir() {
	return apply_filters( 'wp_idea_stream_get_plugin_dir', wp_idea_stream()->plugin_dir );
}

/**
 * Get plugin's main url
 *
 * @since 2.0.0
 *
 * @return string plugin's main url
 */
function wp_idea_stream_get_plugin_url() {
	return apply_filters( 'wp_idea_stream_get_plugin_url', wp_idea_stream()->plugin_url );
}

/**
 * Get plugin's javascript url
 *
 * That's where the plugin's js file are all available
 *
 * @since 2.0.0
 *
 * @return string plugin's javascript url
 */
function wp_idea_stream_get_js_url() {
	return apply_filters( 'wp_idea_stream_get_js_url', wp_idea_stream()->js_url );
}

/**
 * Get a specific javascript file url (minified or not)
 *
 * @since 2.0.0
 *
 * @param  string $script the name of the script
 * @return string         url to the minified or regular script
 */
function wp_idea_stream_get_js_script( $script = '' ) {
	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$min = '';
	}

	return wp_idea_stream_get_js_url() . $script . $min . '.js';
}

/**
 * Attach localized data to a specific Javascript handle.
 *
 * @since  2.4.0
 *
 * @param  array  $data   The list of localized data to merge with default ones.
 * @param  string $handle The Jascript handle to attach localize data to.
 * @param  string $filter The filter to use to let developer edit localized data.
 */
function wp_idea_stream_get_js_script_localized_data( $data = array(), $handle = 'wp-idea-stream-script', $filter = '' ) {
	if ( empty( $handle ) ) {
		return;
	}

	$url     = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	$js_vars = array(
		'canonical' => remove_query_arg( array( 'success', 'error', 'info' ), $url ),
	);

	if ( ! empty( $data ) ) {
		$js_vars = array_merge( $js_vars, $data );
	}

	if ( empty( $filter ) ) {
		$filter = 'wp_idea_stream_data_script';
	}

	wp_localize_script( $handle, 'wp_idea_stream_vars', apply_filters( $filter, $js_vars ) );
}

/**
 * Get plugin's path to includes directory
 *
 * @since 2.0.0
 *
 * @return string includes directory path
 */
function wp_idea_stream_get_includes_dir() {
	return apply_filters( 'wp_idea_stream_get_includes_dir', wp_idea_stream()->includes_dir );
}

/**
 * Get plugin's url to includes directory
 *
 * @since 2.0.0
 *
 * @return string includes directory url
 */
function wp_idea_stream_get_includes_url() {
	return apply_filters( 'wp_idea_stream_get_includes_url', wp_idea_stream()->includes_url );
}

/**
 * Get plugin's path to templates directory
 *
 * That's where all specific plugin's templates are located
 * You can create a directory called 'wp-idea-stream' in your theme
 * copy the content of this folder in it and customize the templates
 * from your theme's 'wp-idea-stream' directory. Templates in there
 * will override plugin's default ones.
 *
 * @since 2.0.0
 *
 * @return string path to templates directory
 */
function wp_idea_stream_get_templates_dir() {
	return apply_filters( 'wp_idea_stream_get_templates_dir', wp_idea_stream()->templates_dir );
}

/**
 * Set a global var to be used by the plugin at different times
 * during WordPress loading process.
 *
 * @since 2.0.0
 *
 * @param  string $var_key   the key to access to the globalized value
 * @param  mixed  $var_value a value to globalize, can be object, array, int.. whatever
 */
function wp_idea_stream_set_idea_var( $var_key = '', $var_value ='' ) {
	return wp_idea_stream()->set_idea_var( $var_key, $var_value );
}

/**
 * Get a global var set thanks to wp_idea_stream_set_idea_var()
 *
 * @since 2.0.0
 *
 * @param  string $var_key the key to access to the globalized value
 * @return mixed           the globalized value for the requested key
 */
function wp_idea_stream_get_idea_var( $var_key = '' ) {
	return wp_idea_stream()->get_idea_var( $var_key );
}

/** Post Type (ideas) *********************************************************/

/**
 * Outputs the post type identifier (ideas) for the plugin
 *
 * @since 2.0.0
 *
 * @return string the post type identifier
 */
function wp_idea_stream_post_type() {
	echo wp_idea_stream_get_post_type();
}

	/**
	 * Gets the post type identifier (ideas)
	 *
	 * @since 2.0.0
	 *
	 * @return string the post type identifier
	 */
	function wp_idea_stream_get_post_type() {
		return apply_filters( 'wp_idea_stream_get_post_type', wp_idea_stream()->post_type );
	}

/**
 * Gets plugin's main post type init arguments
 *
 * @since 2.0.0
 *
 * @return array the init arguments for the 'ideas' post type
 */
function wp_idea_stream_post_type_register_args() {
	$supports = array( 'title', 'editor', 'author', 'comments', 'revisions' );

	if ( wp_idea_stream_featured_images_allowed() ) {
		$supports[] = 'thumbnail';
	}

	// We need custom-fields support to "rest fetch" custom-fields
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		$supports[] = 'custom-fields';
	}

	return apply_filters( 'wp_idea_stream_post_type_register_args', array(
		'public'                => true,
		'query_var'             => wp_idea_stream_get_post_type(),
		'rewrite'               => array(
			'slug'              => wp_idea_stream_idea_slug(),
			'with_front'        => false
		),
		'has_archive'           => wp_idea_stream_root_slug(),
		'exclude_from_search'   => true,
		'show_in_nav_menus'     => false,
		'show_in_admin_bar'     => wp_idea_stream_user_can( 'wp_idea_stream_ideas_admin' ),
		'menu_icon'             => 'dashicons-lightbulb',
		'supports'              => $supports,
		'taxonomies'            => array(
			wp_idea_stream_get_category(),
			wp_idea_stream_get_tag()
		),
		'capability_type'       => array( 'idea', 'ideas' ),
		'capabilities'          => wp_idea_stream_get_post_type_caps(),
		'delete_with_user'      => true,
		'can_export'            => true,
		'show_in_rest'          => true,
		'rest_controller_class' => 'WP_Idea_Stream_Ideas_REST_Controller',
	) );
}

/**
 * Gets the labels for the plugin's post type
 *
 * @since 2.0.0
 * @since 2.3.0 New labels added
 *
 * @return array post type labels
 */
function wp_idea_stream_post_type_register_labels() {
	return apply_filters( 'wp_idea_stream_post_type_register_labels', array(
		'labels' => array(
			'name'                  => __( 'Ideas',                     'wp-idea-stream' ),
			'menu_name'             => _x( 'Ideas', 'Main Plugin menu', 'wp-idea-stream' ),
			'all_items'             => __( 'All Ideas',                 'wp-idea-stream' ),
			'singular_name'         => __( 'Idea',                      'wp-idea-stream' ),
			'add_new'               => __( 'Add New Idea',              'wp-idea-stream' ),
			'add_new_item'          => __( 'Add New Idea',              'wp-idea-stream' ),
			'edit_item'             => __( 'Edit Idea',                 'wp-idea-stream' ),
			'new_item'              => __( 'New Idea',                  'wp-idea-stream' ),
			'view_item'             => __( 'View Idea',                 'wp-idea-stream' ),
			'search_items'          => __( 'Search Ideas',              'wp-idea-stream' ),
			'not_found'             => __( 'No Ideas Found',            'wp-idea-stream' ),
			'not_found_in_trash'    => __( 'No Ideas Found in Trash',   'wp-idea-stream' ),
			'insert_into_item'      => __( 'Insert into idea',          'wp-idea-stream' ),
			'uploaded_to_this_item' => __( 'Uploaded to this idea',     'wp-idea-stream' ),
			'filter_items_list'     => __( 'Filter Ideas list',         'wp-idea-stream' ),
			'items_list_navigation' => __( 'Ideas list navigation',     'wp-idea-stream' ),
			'items_list'            => __( 'Ideas list',                'wp-idea-stream' ),
		)
	) );
}

/**
 * Get plugin's post type "category" identifier (category-ideas)
 *
 * @since 2.0.0
 *
 * @return string hierarchical taxonomy identifier
 */
function wp_idea_stream_get_category() {
	return apply_filters( 'wp_idea_stream_get_category', wp_idea_stream()->category );
}

/**
 * Gets the "category" taxonomy init arguments
 *
 * @since 2.0.0
 *
 * @return array taxonomy init arguments
 */
function wp_idea_stream_category_register_args() {
	return apply_filters( 'wp_idea_stream_category_register_args', array(
		'rewrite'               => array(
			'slug'              => wp_idea_stream_category_slug(),
			'with_front'        => false,
			'hierarchical'      => false,
		),
		'capabilities'          => wp_idea_stream_get_category_caps(),
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => wp_idea_stream_get_category(),
		'hierarchical'          => true,
		'show_in_nav_menus'     => false,
		'public'                => true,
		'show_tagcloud'         => false,
		'show_in_rest'          => true,
		'rest_controller_class' => 'WP_Idea_Stream_Ideas_Term_REST_Controller',
	) );
}

/**
 * Get the "category" taxonomy labels
 *
 * @since 2.0.0
 *
 * @return array "category" taxonomy labels
 */
function wp_idea_stream_category_register_labels() {
	return apply_filters( 'wp_idea_stream_category_register_labels', array(
		'labels' => array(
			'name'             => __( 'Idea Categories',   'wp-idea-stream' ),
			'singular_name'    => __( 'Idea Category',     'wp-idea-stream' ),
			'edit_item'        => __( 'Edit Category',     'wp-idea-stream' ),
			'update_item'      => __( 'Update Category',   'wp-idea-stream' ),
			'add_new_item'     => __( 'Add New Category',  'wp-idea-stream' ),
			'new_item_name'    => __( 'New Category Name', 'wp-idea-stream' ),
			'all_items'        => __( 'All Categories',    'wp-idea-stream' ),
			'search_items'     => __( 'Search Categories', 'wp-idea-stream' ),
			'parent_item'      => __( 'Parent Category',   'wp-idea-stream' ),
			'parent_item_colon'=> __( 'Parent Category:',  'wp-idea-stream' ),
		)
	) );
}

/**
 * Get plugin's post type "tag" identifier (tag-ideas)
 *
 * @since 2.0.0
 *
 * @return string non hierarchical taxonomy identifier
 */
function wp_idea_stream_get_tag() {
	return apply_filters( 'wp_idea_stream_get_tag', wp_idea_stream()->tag );
}

/**
 * Gets the "tag" taxonomy init arguments
 *
 * @since 2.0.0
 *
 * @return array taxonomy init arguments
 */
function wp_idea_stream_tag_register_args() {
	return apply_filters( 'wp_idea_stream_tag_register_args', array(
		'rewrite'               => array(
			'slug'              => wp_idea_stream_tag_slug(),
			'with_front'        => false,
			'hierarchical'      => false,
		),
		'capabilities'          => wp_idea_stream_get_tag_caps(),
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => wp_idea_stream_get_tag(),
		'hierarchical'          => false,
		'show_in_nav_menus'     => false,
		'public'                => true,
		'show_tagcloud'         => true,
		'show_in_rest'          => true,
		'rest_controller_class' => 'WP_Idea_Stream_Ideas_Term_REST_Controller',
	) );
}

/**
 * Get the "tag" taxonomy labels
 *
 * @since 2.0.0
 *
 * @return array "tag" taxonomy labels
 */
function wp_idea_stream_tag_register_labels() {
	return apply_filters( 'wp_idea_stream_tag_register_labels', array(
		'labels' => array(
			'name'                       => __( 'Idea Tags',                         'wp-idea-stream' ),
			'singular_name'              => __( 'Idea Tag',                          'wp-idea-stream' ),
			'edit_item'                  => __( 'Edit Tag',                          'wp-idea-stream' ),
			'update_item'                => __( 'Update Tag',                        'wp-idea-stream' ),
			'add_new_item'               => __( 'Add New Tag',                       'wp-idea-stream' ),
			'new_item_name'              => __( 'New Tag Name',                      'wp-idea-stream' ),
			'all_items'                  => __( 'All Tags',                          'wp-idea-stream' ),
			'search_items'               => __( 'Search Tags',                       'wp-idea-stream' ),
			'popular_items'              => __( 'Popular Tags',                      'wp-idea-stream' ),
			'separate_items_with_commas' => __( 'Separate tags with commas',         'wp-idea-stream' ),
			'add_or_remove_items'        => __( 'Add or remove tags',                'wp-idea-stream' ),
			'choose_from_most_used'      => __( 'Choose from the most popular tags', 'wp-idea-stream' )
		)
	) );
}

/** Urls **********************************************************************/

/**
 * Gets plugin's post type main url
 *
 * @since 2.0.0
 *
 * @return string root url for the post type
 */
function wp_idea_stream_get_root_url() {
	return apply_filters( 'wp_idea_stream_get_root_url', get_post_type_archive_link( wp_idea_stream_get_post_type() ) );
}

/**
 * Gets a specific "category" term url
 *
 * @since 2.0.0
 *
 * @param  object $category the term to build the url for
 * @return string          url to reach all ideas categorized with the requested term
 */
function wp_idea_stream_get_category_url( $category = null ) {
	if ( empty( $category ) ) {
		$category = wp_idea_stream_get_current_term();
	}

	$term_link = get_term_link( $category, wp_idea_stream_get_category() );

	/**
	 * @param  string $term_link url to reach the ideas categorized with the term
	 * @param  object $category the term for this taxonomy
	 */
	return apply_filters( 'wp_idea_stream_get_category_url', $term_link, $category );
}

/**
 * Gets a specific "tag" term url
 *
 * @since 2.0.0
 *
 * @param  object $tag the term to build the url for
 * @return string          url to reach all ideas tagged with the requested term
 */
function wp_idea_stream_get_tag_url( $tag = '' ) {
	if ( empty( $tag ) ) {
		$tag = wp_idea_stream_get_current_term();
	}

	$term_link = get_term_link( $tag, wp_idea_stream_get_tag() );

	/**
	 * @param  string $term_link url to reach the ideas tagged with the term
	 * @param  object $tag the term for this taxonomy
	 */
	return apply_filters( 'wp_idea_stream_get_tag_url', $term_link, $tag );
}

/**
 * Gets a global redirect url
 *
 * Used after posting an idea failed
 * Defaults to root url
 *
 * @since 2.0.0
 *
 * @return string the url to redirect the user to
 */
function wp_idea_stream_get_redirect_url() {
	return apply_filters( 'wp_idea_stream_get_redirect_url', wp_idea_stream_get_root_url() );
}

/**
 * Gets the url to the form to submit new ideas
 *
 * So far only adding new ideas is supported, but
 * there will surely be an edit action to allow users
 * to edit their ideas. Reason of the $type param
 *
 * @since 2.0.0
 *
 * @global $wp_rewrite
 * @param  string $type action (defaults to new)
 * @param  string $idea_name the post name of the idea to edit
 * @return string the url of the form to add ideas
 */
function wp_idea_stream_get_form_url( $type = '', $idea_name = '' ) {
	global $wp_rewrite;

	if ( empty( $type ) ) {
		$type = wp_idea_stream_addnew_slug();
	}

	/**
	 * Early filter to override form url before being built
	 *
	 * @param mixed false or url to override
	 * @param string $type (only add new for now)
	 */
	$early_form_url = apply_filters( 'wp_idea_stream_pre_get_form_url', false, $type, $idea_name );

	if ( ! empty( $early_form_url ) ) {
		return $early_form_url;
	}

	// Pretty permalinks
	if ( $wp_rewrite->using_permalinks() ) {
		$url = $wp_rewrite->root . wp_idea_stream_action_slug() . '/%' . wp_idea_stream_action_rewrite_id() . '%';

		$url = str_replace( '%' . wp_idea_stream_action_rewrite_id() . '%', $type, $url );
		$url = home_url( user_trailingslashit( $url ) );

	// Unpretty permalinks
	} else {
		$url = add_query_arg( array( wp_idea_stream_action_rewrite_id() => $type ), home_url( '/' ) );
	}

	if ( $type == wp_idea_stream_edit_slug() && ! empty( $idea_name ) ) {
		$url = add_query_arg( wp_idea_stream_get_post_type(), $idea_name, $url );
	}

	/**
	 * Filter to override form url after being built
	 *
	 * @param string url to override
	 * @param string $type add new or edit
	 * @param string $idea_name the post name of the idea to edit
	 */
	return apply_filters( 'wp_idea_stream_get_form_url', $url, $type, $idea_name );
}

/** Feedbacks *****************************************************************/

/**
 * Sanitize the feedback.
 *
 * @since 2.4.0
 *
 * @param  string $text The text to sanitize.
 * @return string       The sanitized text.
 */
function wp_idea_stream_sanitize_feedback( $text = '' ) {
	$text = wp_kses( $text, array(
		'a'      => array( 'href' => true ),
		'strong' => array(),
		'img'    => array(
			'src'    => true,
			'height' => true,
			'width'  => true,
			'class'  => true,
			'alt'    => true
		),
	) );

	return wp_unslash( $text );
}

/**
 * Get the feedback message or the list of feedback messages to output to the user.
 *
 * @since 2.4.0
 *
 * @param string|array  $type  The type of the feedback (success, error or info) or
 *                             An associative array keyed by the type of feedback and
 *                             containing the list of message ids.
 * @param  bool|int     $id    False or the ID of the feedback message to get.
 * @return string|array        The feedback message or a list of feedback messages.
 */
function wp_idea_stream_get_feedback_messages( $type = '', $id = false ) {
	$messages = apply_filters( 'wp_idea_stream_get_feedback_messages', array(
		'success' => array(
			1 => __( 'Saved successfully',                                'wp-idea-stream' ),
			2 => __( 'Registration complete. Please check your mailbox.', 'wp-idea-stream' ),
			3 => __( 'The idea was successfully created.',                'wp-idea-stream' ),
			4 => __( 'The idea was successfully updated.',                'wp-idea-stream' ),
			5 => __( 'Description updated.',                              'wp-idea-stream' ),
		),
		'error' => array(
			1  => __( 'Something went wrong, please try again',                        'wp-idea-stream' ),
			2  => __( 'You are not allowed to edit this idea.',                        'wp-idea-stream' ),
			3  => __( 'You are not allowed to publish ideas',                          'wp-idea-stream' ),
			4  => __( 'Title and description are required fields.',                    'wp-idea-stream' ),
			5  => __( 'Something went wrong while trying to save your idea.',          'wp-idea-stream' ),
			6  => __( 'There was a problem saving the featured image, sorry.',         'wp-idea-stream' ),
			7  => __( 'Please choose a username having at least 4 characters.',        'wp-idea-stream' ),
			8  => __( 'Please fill all required fields.',                              'wp-idea-stream' ),
			9  => __( 'The idea you are trying to edit does not seem to exist.',       'wp-idea-stream' ),
			10 => __( 'Something went wrong while trying to update your idea.',        'wp-idea-stream' ),
			11 => __( 'Please enter some content in your description',                 'wp-idea-stream' ),
			12 => __( 'Something went wrong while trying to update your description.', 'wp-idea-stream' ),
		),
		'info'  => array(
			1 => __( 'This idea is already being edited by another user.', 'wp-idea-stream' ),
			2 => __( 'Your idea is currently awaiting moderation.',        'wp-idea-stream' ),
		),
	) );

	// Check for a custom pending message
	$custom_pending_message = wp_idea_stream_moderation_message();
	if ( ! empty( $custom_pending_message ) ) {
		$messages['info'][2] = $custom_pending_message;
	}

	if ( empty( $type ) ) {
		return $messages;
	}

	if ( ! is_array( $type ) && isset( $messages[ $type ] ) ) {
		$messages = $messages[ $type ];

		if ( false === $id || ! isset( $messages[ $type ][ $id ] ) ) {
			return $messages;
		}

		return $messages[ $type ][ $id ];
	}

	foreach ( $type as $kt => $kv ) {
		$message_ids = array_filter( wp_parse_id_list( $kv ) );

		// If we have ids, get the corresponding messages.
		if ( $message_ids ) {
			$type[ $kt ] = array_intersect_key( $messages[ $kt ], array_flip( $message_ids ) );
		}
	}

	return $type;
}

/**
 * Explode arrays of values before using WordPress's add_query_arg() function.
 *
 * @since  2.4.0
 *
 * @param  array  $args The query arguments to add to the url.
 * @param  string $url  The url.
 * @return string       The url with query arguments.
 */
function wp_idea_stream_add_feedback_args( $args = array(), $url = '' ) {
	foreach ( $args as $k => $v ) {
		if ( ! is_array( $v ) ) {
			continue;
		}

		$args[ $k ] = join( ',', $v );
	}

	return add_query_arg( $args, $url );
}

/**
 * Add a new feedback message to inform the user.
 *
 * @since 2.0.0
 * @since 2.4.0 Change the expected argument and stop using cookies.
 *
 * @param  array $feedback_data A list of feedback message or message ids keyed by their type.
 */
function wp_idea_stream_add_message( $feedback_data = array() ) {
	// Success is the default
	if ( empty( $feedback_data ) ) {
		$feedback_data = array(
			'success' => array( 1 ),
		);
	}

	wp_idea_stream_set_idea_var( 'feedback', $feedback_data );
}

/**
 * Sets a new message to inform user
 *
 * Inspired by ClusterPress's cp_feedbacks() function.
 *
 * @since 2.0.0
 * @since 2.4.0 Gets the feedback message ID and stop using cookies.
 */
function wp_idea_stream_set_user_feedback() {
	// Check the URL query to find a feedback message
	$current_url = parse_url( $_SERVER['REQUEST_URI'] );

	if ( ! empty( $current_url['query'] ) ) {
		$vars = wp_parse_args( $current_url['query'] );

		$feedback = array_intersect_key( $vars, array(
			'error'   => true,
			'success' => true,
			'info'    => true,
		) );
	}

	if ( empty( $feedback ) ) {
		return;
	}

	wp_idea_stream_set_idea_var( 'feedback', $feedback );
}

/**
 * Displays the feedback message to the user.
 *
 * @since 2.0.0
 * @since 2.4.0 Display one or more feedback messages to the user.
 *
 * @return string HTML Output.
 */
function wp_idea_stream_user_feedback() {
	$feedback = wp_idea_stream_get_idea_var( 'feedback' );

	if ( empty( $feedback ) || ! is_array( $feedback ) || ! empty( $feedback['admin_notices'] ) ) {
		return;
	}

	$messages = wp_idea_stream_get_feedback_messages( $feedback );

	if ( empty( $messages ) ) {
		return;
	}

	foreach ( (array) $messages as $class => $message ) : ?>
		<div class="message <?php echo esc_attr( $class ); ?>">
			<p>
				<?php if ( is_array( $message ) ) :
						echo( join( '</p><p>', array_map( 'wp_idea_stream_sanitize_feedback', $message ) ) );

					else :
						echo wp_idea_stream_sanitize_feedback( $message );

				endif ; ?>
			</p>
		</div>
	<?php endforeach;
}

/** Rating Ideas **************************************************************/

/**
 * Checks wether the builtin rating system should be used
 *
 * In previous versions of the plugin this was an option that
 * could be deactivated from plugin settings. This is no more
 * the case, as i think like comments, this is a core functionality
 * when managing ideas. To deactivate the ratings, use the filter.
 *
 * @since 2.0.0
 *
 * @param  int  $default   by default enabled
 * @return bool            True if disabled, false if enabled
 */
function wp_idea_stream_is_rating_disabled( $default = 0 ) {
	return (bool) apply_filters( 'wp_idea_stream_is_rating_disabled', $default );
}

/**
 * Gets a fallback hintlist for ratings
 *
 * @since 2.0.0
 *
 * @return array the hintlist
 */
function wp_idea_stream_get_hint_list() {
	$hintlist = wp_idea_stream_hint_list();

	if ( empty( $hintlist ) ) {
		$hintlist = array(
			esc_html__( 'bad',      'wp-idea-stream' ),
			esc_html__( 'poor',     'wp-idea-stream' ),
			esc_html__( 'regular',  'wp-idea-stream' ),
			esc_html__( 'good',     'wp-idea-stream' ),
			esc_html__( 'gorgeous', 'wp-idea-stream' )
		);
	}

	return $hintlist;
}

/**
 * Count rating stats for a specific idea or gets the rating of a specific user for a given idea
 *
 * @since 2.0.0
 *
 * @param  integer $id      the ID of the idea object
 * @param  integer $user_id the user id
 * @param  boolean $details whether to include detailed stats
 * @return mixed            int|array the rate of the user or the stats
 */
function wp_idea_stream_count_ratings( $id = 0, $user_id = 0, $details = false ) {
	// Init a default array
	$retarray = array(
		'average' => 0,
		'users'   => array()
	);
	// Init a default user rating
	$user_rating = 0;

	// No idea, try to find it in the query loop
	if ( empty( $id ) ) {
		if ( ! wp_idea_stream()->query_loop->idea->ID ) {
			return $retarray;
		} else {
			$id = wp_idea_stream()->query_loop->idea->ID;
		}
	}

	// Get all the rates for the idea
	$rates = get_post_meta( $id, '_ideastream_rates', true );

	// Build the stats
	if ( ! empty( $rates ) && is_array( $rates ) ) {
		foreach ( $rates as $rate => $users ) {
			// We need the user's rating
			if ( ! empty( $user_id ) && in_array( $user_id, (array) $users['user_ids'] ) ) {
				$user_rating = $rate;

			// We need average rating
			} else {
				$retarray['users'] = array_merge( $retarray['users'], (array) $users['user_ids'] );
				$retarray['average'] += $rate * count( (array) $users['user_ids'] );

				if ( ! empty( $details ) ) {
					$retarray['details'][ $rate ] = (array) $users['user_ids'];
				}
			}
		}
	}

	// Return the user rating
	if ( ! empty( $user_id ) ) {
		/**
		 * @param  int $user_rating the rate given by the user to the idea
		 * @param  int $id the ID of the idea
		 * @param  int $user_id the user id who rated the idea
		 */
		return apply_filters( 'wp_idea_stream_get_user_ratings', $user_rating, $id, $user_id );
	}

	if ( ! empty( $retarray['users'] ) ) {
		$retarray['average'] = number_format( $retarray['average'] / count( $retarray['users'] ), 1 );
	} else {
		$retarray['average'] = 0;
	}

	/**
	 * @param  array $retarray the idea rating stats
	 * @param  int $id the ID of the idea
	 * @param  array $rates all idea rates organized in an array
	 */
	return apply_filters( 'wp_idea_stream_count_ratings', $retarray, $id, $rates );
}

/**
 * Delete a specific rate for a given idea
 *
 * This action is only available from the idea edit Administration screen
 * @see  WP_Idea_Stream_Admin->maybe_delete_rate() in admin/admin
 *
 * @since 2.0.0
 *
 * @param  int $idea    the ID of the idea
 * @param  int $user_id the ID of the user
 * @return mixed       string the new average rating or false if no more rates
 */
function wp_idea_stream_delete_rate( $idea = 0, $user_id = 0 ) {
	if ( empty( $idea ) || empty( $user_id ) ) {
		return false;
	}

	$rates = get_post_meta( $idea, '_ideastream_rates', true );

	if ( empty( $rates ) ) {
		return false;
	} else {
		foreach ( $rates as $rate => $users ) {
			if ( in_array( $user_id, (array) $users['user_ids'] ) ) {
				$rates[ $rate ]['user_ids'] = array_diff( $users['user_ids'], array( $user_id ) );

				// Unset the rate if no more users.
				if ( count( $rates[ $rate ]['user_ids'] ) == 0 ) {
					unset( $rates[ $rate ] );
				}
			}
		}
	}

	if ( update_post_meta( $idea, '_ideastream_rates', $rates ) ) {
		$ratings = wp_idea_stream_count_ratings( $idea );
		update_post_meta( $idea, '_ideastream_average_rate', $ratings['average'] );

		/**
		 * @param  int $idea the ID of the idea
		 * @param  int $user_id the ID of the user
		 * @param  string       the formatted average.
		 */
		do_action( 'wp_idea_stream_deleted_rate', $idea, $user_id, $ratings['average'] );

		return $ratings['average'];
	} else {
		return false;
	}
}

/**
 * Saves a new rate for the idea
 *
 * @since 2.0.0
 * @since 2.3.0 Improve the way votes are saved into the DB by using
 *              non numeric keys for the array in order to avoid this
 *              bug: {@see https://github.com/imath/wp-idea-stream/issues/35}}
 *
 * @param  int         $idea    the ID of the idea
 * @param  int         $user_id the ID of the user
 * @param  int         $rate    the rate of the user
 * @return string|bool          the new average rating or false if no more rates
 */
function wp_idea_stream_add_rate( $idea = 0, $user_id = 0, $rate = 0 ) {
	if ( empty( $idea ) || empty( $user_id ) || empty( $rate ) ) {
		return false;
	}

	$rates = get_post_meta( $idea, '_ideastream_rates', true );

	if ( empty( $rates ) ) {
		$rates = array( $rate => array( 'user_ids' => array( 'u-' . $user_id => $user_id ) ) );
	} else if ( ! empty( $rates[ $rate ] ) && ! in_array( $user_id, $rates[ $rate ]['user_ids'] ) ) {
		$rates[ $rate ]['user_ids'] = array_merge( $rates[ $rate ]['user_ids'], array( 'u-' . $user_id => $user_id ) );
	} else if ( empty( $rates[ $rate ] ) ) {
		$rates = $rates + array( $rate => array( 'user_ids' => array( 'u-' . $user_id => $user_id ) ) );
	} else {
		return false;
	}

	if ( update_post_meta( $idea, '_ideastream_rates', $rates ) ) {
		$ratings = wp_idea_stream_count_ratings( $idea );
		update_post_meta( $idea, '_ideastream_average_rate', $ratings['average'] );

		/**
		 * @param  int $idea the ID of the idea
		 * @param  int $user_id the ID of the user
		 * @param  int $rate the user's rating
		 * @param  string       the formatted average.
		 */
		do_action( 'wp_idea_stream_added_rate', $idea, $user_id, $rate, $ratings['average'] );

		return $ratings['average'];
	} else {
		return false;
	}
}

/**
 * Intercepts the user ajax action to rate the idea
 *
 * @since 2.0.0
 * @deprecated 2.4.0 (Replaced by a Rest request)
 *
 * @return mixed the average rate or 0
 */
function wp_idea_stream_ajax_rate() {
	_deprecated_function( __FUNCTION__, '2.4.0' );

	if ( ! wp_idea_stream_user_can( 'rate_ideas' ) ) {
		exit( '0' );
	}

	$user_id = wp_idea_stream_users_current_user_id();
	$idea = ! empty( $_POST['idea'] ) ? absint( $_POST['idea'] ) : 0;
	$rate = ! empty( $_POST['rate'] ) ? absint( $_POST['rate'] ) : 0;

	check_ajax_referer( 'wp_idea_stream_rate', 'wpnonce' );

	$new_average_rate = wp_idea_stream_add_rate( $idea, $user_id, $rate );

	if ( empty( $new_average_rate ) ) {
		exit( '0' );
	} else {
		exit( $new_average_rate );
	}
}

/**
 * Order the ideas by rates when requested
 *
 * This function is hooking to WordPress 'posts_clauses' filter. As the
 * rating query is first built by using a specific WP_Meta_Query, we need
 * to also make sure the ORDER BY clause of the sql query is customized.
 *
 * @since 2.0.0
 *
 * @param  array    $clauses  the idea query sql parts
 * @param  WP_Query $wp_query the WordPress query object
 * @return array              new order clauses if needed
 */
function wp_idea_stream_set_rates_count_orderby( $clauses = array(), $wp_query = null ) {

	if ( ( wp_idea_stream_is_ideastream() || wp_idea_stream_is_admin() || wp_idea_stream_get_idea_var( 'rating_widget' ) ) && wp_idea_stream_is_orderby( 'rates_count' ) ) {
		preg_match( '/\(?(\S*).meta_key = \'_ideastream_average_rate\'/', $clauses['where'], $matches );
		if ( ! empty( $matches[1] ) ) {
			// default order
			$order = 'DESC';

			// Specific case for IdeaStream administration.
			if ( ! empty( $clauses['orderby'] ) && 'ASC' == strtoupper( substr( $clauses['orderby'], -3 ) ) ) {
				$order = 'ASC';
			}

			$clauses['orderby'] = "{$matches[1]}.meta_value + 0 {$order}";
		}
	}

	return $clauses;
}

/**
 * Retrieve total rates for a user.
 *
 * @since 2.3.0
 *
 * @global $wpdb
 * @param  int $user_id the User ID.
 * @return int Rates count.
 */
function wp_idea_stream_count_user_rates( $user_id = 0 ) {
	$count = 0;

	if ( empty( $user_id ) ) {
		return $count;
	}

	global $wpdb;
	$user_id = (int) $user_id;

	$count = wp_cache_get( "idea_rates_count_{$user_id}", 'wp_idea_stream' );

	if ( false !== $count ) {
		return $count;
	}

	$like  = '%' . $wpdb->esc_like( ';i:' . $user_id .';' ) . '%';
	$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( post_id ) FROM {$wpdb->postmeta} WHERE meta_key= %s AND meta_value LIKE %s", '_ideastream_rates', $like ) );

	wp_cache_set( "idea_rates_count_{$user_id}", $count, 'wp_idea_stream' );

	return $count;
}

/**
 * Clean the user's rates count cache
 *
 * @since 2.3.0
 *
 * @param int $idea_id the idea ID
 * @param int $user_id the user ID
 */
function wp_idea_stream_clean_rates_count_cache( $idea_id, $user_id = 0 ) {
	// Bail if no user id
	if ( empty( $user_id ) ) {
		return;
	}

	$user_id = (int) $user_id;

	wp_cache_delete( "idea_rates_count_{$user_id}", 'wp_idea_stream' );
}

/** Utilities *****************************************************************/

/**
 * Creates a specific excerpt for the content of an idea
 *
 * @since 2.0.0
 * @since 2.3.0 Added the $nofilter parameter
 *
 * @param  string  $text   the content to truncate
 * @param  integer $length the number of words
 * @param  string  $more   the more string
 * @return string          the excerpt of an idea
 */
function wp_idea_stream_create_excerpt( $text = '', $length = 55, $more = ' [&hellip;]', $nofilter = false ) {
	if ( empty( $text ) ) {
		return $text;
	}

	$text = strip_shortcodes( $text );

	/**
	 * Used internally to sanitize outputs
	 * @see  core/filters
	 *
	 * @param string $text the content without shortcodes
	 */
	$text = apply_filters( 'wp_idea_stream_create_excerpt_text', $text );

	$text = str_replace( ']]>', ']]&gt;', $text );

	if ( false === $nofilter ) {
		/**
		 * Filter the number of words in an excerpt.
		 */
		$excerpt_length = apply_filters( 'excerpt_length', $length );
		/**
		 * Filter the string in the "more" link displayed after a trimmed excerpt.
		 */
		$excerpt_more = apply_filters( 'excerpt_more', $more );
	} else {
		$excerpt_length = $length;
		$excerpt_more   = $more;
	}

	return wp_trim_words( $text, $excerpt_length, $excerpt_more );
}

/**
 * Prepare the content to be output in a csv file
 *
 * @since 2.1.0
 *
 * @param  string $content the content
 * @return string          the content to be displayed in a csv file
 */
function wp_idea_stream_generate_csv_content( $content = '' ) {
	// Avoid some chars
	$content = str_replace( array( '&#8212;', '"' ), array( 0, "'" ), $content );

	// Strip shortcodes
	$content = strip_shortcodes( $content );

	// Strip slashes
	$content = wp_unslash( $content );

	// Strip all tags
	$content = wp_strip_all_tags( $content, true );

	// Make sure =, +, -, @ are not the first char of the field.
	if ( in_array( mb_substr( $content, 0, 1 ), array( '=', '+', '-', '@' ), true ) ) {
		$content = "'" . $content;
	}

	return apply_filters( 'wp_idea_stream_generate_csv_content', $content );
}

/**
 * Specific tag cloud count text callback
 *
 * By Default, WordPress uses "topic/s", This will
 * make sure "idea/s" will be used instead. Unfortunately
 * it's only possible in front end tag clouds.
 *
 * @since 2.0.0
 *
 * @param  int $count Number of ideas associated with the tag
 * @return string     the count text for ideas
 */
function wp_idea_stream_tag_cloud_count_callback( $count = 0 ) {
	return sprintf( _nx( '%s idea', '%s ideas', $count, 'ideas tag cloud count text', 'wp-idea-stream' ), number_format_i18n( $count )  );
}

/**
 * Filters the tag cloud args by referencing a specific count text callback
 * if the plugin's "tag" taxonomy is requested.
 *
 * @since 2.0.0
 *
 * @param  array  $args the tag cloud arguments
 * @return array        the arguments with the new count text callback if needed
 */
function wp_idea_stream_tag_cloud_args( $args = array() ) {
	if( ! empty( $args['taxonomy'] ) && wp_idea_stream_get_tag() == $args['taxonomy'] ) {
		$args['topic_count_text_callback'] = 'wp_idea_stream_tag_cloud_count_callback';
	}

	return $args;
}

/**
 * Generates an ideas tag cloud
 *
 * Used when writing a new idea to allow the author to choose
 * one or more popular idea tags.
 *
 * @since 2.0.0
 *
 * @param  integer $number number of tag to display
 * @param  array   $args   the tag cloud args
 * @return array           associative array containing the number of tags and the content of the cloud.
 */
function wp_idea_stream_generate_tag_cloud( $number = 10, $args = array() ) {
	$tags = get_terms( wp_idea_stream_get_tag(), apply_filters( 'wp_idea_stream_generate_tag_cloud_args',
		array( 'number' => $number, 'orderby' => 'count', 'order' => 'DESC' )
	) );

	if ( empty( $tags ) ) {
		return;
	}

	foreach ( $tags as $key => $tag ) {
		$tags[ $key ]->link = '#';
		$tags[ $key ]->id = $tag->term_id;
	}

	$args = wp_parse_args( $args,
		wp_idea_stream_tag_cloud_args( array( 'taxonomy' => wp_idea_stream_get_tag() ) )
	);

	$retarray = array(
		'number'   => count( $tags ),
		'tagcloud' => wp_generate_tag_cloud( $tags, $args )
	);

	return apply_filters( 'wp_idea_stream_generate_tag_cloud', $retarray );
}

/**
 * Filters WP Editor Buttons depending on plugin's settings.
 *
 * @since 2.0.0
 *
 * @param  array  $buttons the list of buttons for the editor
 * @return array           the filtered list of buttons to match plugin's needs
 */
function wp_idea_stream_teeny_button_filter( $buttons = array() ) {

	$remove_buttons = array(
		'wp_more',
		'spellchecker',
		'wp_adv',
	);

	if ( ! wp_idea_stream_idea_editor_link() ) {
		$remove_buttons = array_merge( $remove_buttons, array(
			'link',
			'unlink',
		) );
	}

	// Remove unused buttons
	$buttons = array_diff( $buttons, $remove_buttons );

	// Eventually add the image button
	if ( wp_idea_stream_idea_editor_image() ) {
		$buttons = array_diff( $buttons, array( 'fullscreen' ) );
		array_push( $buttons, 'image', 'fullscreen' );
	}

	return $buttons;
}

/**
 * Since WP 4.3 _WP_Editors is now including the format_for_editor filter to sanitize
 * the content to edit. As we were using format_to_edit to sanitize the editor content,
 * it's then sanitized twice and tinymce fails to wysiwyg!
 *
 * So we just need to only apply format_to_edit if WP < 4.3!
 *
 * @since  2.2.0
 *
 * @param  string $text the editor content.
 * @return string the sanitized text or the text without any changes
 */
function wp_idea_stream_format_to_edit( $text = '' ) {
	if ( function_exists( 'format_for_editor' ) ) {
		return $text;
	}

	return format_to_edit( $text );
}

/**
 * Adds wp_idea_stream to global cache groups
 *
 * Mainly used to cach comments about ideas count
 *
 * @since 2.0.0
 */
function wp_idea_stream_cache_global_group() {
	wp_cache_add_global_groups( array( 'wp_idea_stream' ) );
}

/**
 * Adds a shortcut to Idea Stream Backend using the appearence menus
 *
 * While developing the plugin i've found it usefull to be able to easily access
 * to IdeaStream backend from front end, so i've left it. You can disable it by using
 * the filer.
 *
 * @since 2.0.0
 *
 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance
 */
function wp_idea_stream_adminbar_menu( $wp_admin_bar = null ){
	$use_admin_bar = apply_filters( 'wp_idea_stream_adminbar_menu', true );

	if ( empty( $use_admin_bar ) ) {
		return;
	}

	if ( ! empty( $wp_admin_bar ) && wp_idea_stream_user_can( 'edit_ideas' ) ) {
		$menu_url = add_query_arg( 'post_type', wp_idea_stream_get_post_type(), admin_url( 'edit.php' ) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'appearance',
			'id'     => 'ideastream',
			'title'  => _x( 'WP Idea Stream', 'Admin bar menu', 'wp-idea-stream' ),
			'href'   => $menu_url,
		) );
	}
}

/**
 * Checks wether signups are allowed
 *
 * @since 2.1.0
 *
 * @return bool true if user signups are allowed, false otherwise
 */
function wp_idea_stream_is_signup_allowed() {
	// Default to single site option
	$option = 'users_can_register';

	// Multisite config is using the registration site meta
	if ( is_multisite() ) {
		$option = 'registration';
	}

	$registration_status = get_site_option( $option, 0 );

	// On multisite config, just deal with user signups and avoid blog signups
	$signup_allowed = ( 1 == $registration_status || 'user' == $registration_status );

	return (bool) apply_filters( 'wp_idea_stream_is_signup_allowed', $signup_allowed );
}

/**
 * Disable signups managment by WP Idea Stream if BuddyPress should manage them
 *
 * There can be a situation when WP Idea Stream is not activated on
 * the network while BuddyPress is.
 *
 * @since 2.2.0
 *
 * @param  bool $signup_allowed
 * @return bool True if BuddyPress is not active on the current blog or the network, false otherwise
 */
function wp_idea_stream_buddypress_is_managing_signup( $signup_allowed ) {
	if ( true === $signup_allowed && function_exists( 'buddypress' ) ) {
		$signup_allowed = ! bp_is_root_blog() && ! bp_is_network_activated() ;
	}

	return $signup_allowed;
}

/**
 * Checks wether signups are allowed for current blog
 *
 * @since 2.2.0
 *
 * @return bool true if signups are allowed for current site, false otherwise
 */
function wp_idea_stream_is_signup_allowed_for_current_blog() {
	$signups_allowed = wp_idea_stream_is_signup_allowed();

	if ( ! is_multisite() ) {
		return $signups_allowed;
	}

	return apply_filters( 'wp_idea_stream_is_signup_allowed_for_current_blog', wp_idea_stream_allow_signups() );
}

/**
 * Make sure to remove the front page setting if posts are listed on front page
 *
 * @since  2.4.0
 *
 * @param  string $oldvalue The option previous value.
 * @param  string $value    The option new value.
 */
function wp_idea_stream_reset_ideas_as_front( $oldvalue = '', $value = '' ) {
	if ( 'page' === $value ) {
		return;
	}

	delete_option( '_ideastream_as_front_page' );
}
