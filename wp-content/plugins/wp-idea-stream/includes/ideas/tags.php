<?php
/**
 * WP Idea Stream Ideas tags.
 *
 * template tags specific to ideas
 *
 * @package WP Idea Stream\core
 *
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/** Ideas Main nav ************************************************************/

/**
 * Displays the Ideas Search form
 *
 * @since 2.0.0
 *
 * @return string Output for the search form.
 */
function wp_idea_stream_ideas_search_form() {
	$placeholder = __( 'Search Ideas', 'wp-idea-stream' );
	$action = '';
	$hidden = '';

	if ( ! wp_idea_stream_is_pretty_links() ) {
		$hidden = "\n" . '<input type="hidden" name="post_type" value="' . wp_idea_stream_get_post_type() . '"/>';
	} else {
		$action = apply_filters( 'wp_idea_stream_ideas_search_form_action_url', wp_idea_stream_get_root_url() );
	}

	// Init the output.
	$search_form_html = '';

	/**
	 * Filter here by returning false to carry on using the good old search form.
	 *
	 * @since  2.4.0
	 *
	 * @param  bool $value True to use the Theme search form template. False otherwise.
	 */
	if ( true === apply_filters( 'wp_idea_stream_use_search_form_template', true ) ) {
		add_filter( 'get_search_query', 'wp_idea_stream_get_search_query' );

		$search_form_html = get_search_form( false );

		remove_filter( 'get_search_query', 'wp_idea_stream_get_search_query' );

		if ( ! empty( $action ) ) {
			preg_match( '/action=["|\']([^"]*)["|\']/i', $search_form_html, $action_attr );

			if ( ! empty( $action_attr[1] ) ) {
				$a = str_replace( $action_attr[1], esc_url( $action ), $action_attr[0] );
				$search_form_html = str_replace( $action_attr[0], $a, $search_form_html );
			}
		}

		preg_match( '/name=["|\']s["|\']/i', $search_form_html, $name_attr );

		if ( ! empty( $name_attr[0] ) ) {
			$search_form_html = str_replace( $name_attr[0], 'name="' . wp_idea_stream_search_rewrite_id() . '"', $search_form_html );
		}

		if ( ! empty( $hidden ) ) {
			$search_form_html = str_replace( '</form>', "{$hidden}\n</form>", $search_form_html );
		}
	}

	// The good old search form!
	if ( ! $search_form_html ) {
		$search_value = wp_idea_stream_get_search_query();

		if ( ! empty( $search_value ) ) {
			$search_value = esc_html( $search_value );
		}

		$search_form_html = '<form action="' . esc_url( $action ) . '" method="get" id="ideas-search-form" class="nav-form">' . $hidden;
		$search_form_html .= '<label><input type="text" name="' . wp_idea_stream_search_rewrite_id() . '" id="ideas-search-box" placeholder="'. esc_attr( $placeholder ) .'" value="' . $search_value . '" /></label>';
		$search_form_html .= '<input type="submit" id="ideas-search-submit" value="'. esc_attr__( 'Search', 'wp-idea-stream' ) .'" /></form>';
	}

	echo apply_filters( 'wp_idea_stream_ideas_search_form', $search_form_html );
}

/**
 * Displays the Orderby form
 *
 * @since 2.0.0
 *
 * @return string Output for the search form.
 */
function wp_idea_stream_ideas_order_form() {
	$order_options = wp_idea_stream_ideas_get_order_options();
	$order_value   = get_query_var( 'orderby' );
	$category      = get_query_var( wp_idea_stream_get_category() );
	$tag           = get_query_var( wp_idea_stream_get_tag() );
	$action        = '';
	$hidden        = '';

	if ( ! empty( $order_value ) ) {
		$order_value = esc_html( $order_value );
	} else {
		$order_value = 'date';
	}

	if ( ! wp_idea_stream_is_pretty_links() ) {
		if ( ! empty( $category ) ) {
			$hidden = "\n" . '<input type="hidden" name="' . esc_attr( wp_idea_stream_get_category() ). '" value="' . $category . '"/>';
		} else if ( ! empty( $tag ) ) {
			$hidden = "\n" . '<input type="hidden" name="' . esc_attr( wp_idea_stream_get_tag() ). '" value="' . $tag . '"/>';
		} else {
			$hidden = "\n" . '<input type="hidden" name="post_type" value="' . wp_idea_stream_get_post_type() . '"/>';
		}

	// We need to set the action url
	} else {
		// Viewing tags
		if ( wp_idea_stream_is_tag() ) {
			$action = wp_idea_stream_get_tag_url( $tag );

		// Viewing categgories
		} else if ( wp_idea_stream_is_category() ) {
			$action = wp_idea_stream_get_category_url( $category );

		// Defaults to roout url
		} else {
			$action = wp_idea_stream_get_root_url();
		}

		/**
		 * @param string $action the action form attribute
		 * @param string the current category term slug if set
		 * @param string the current tag term slug if set
		 */
		$action = apply_filters( 'wp_idea_stream_ideas_order_form_action_url', $action, $category, $tag );
	}

	$options_output = '';
	foreach ( $order_options as $query_var => $label ) {
		$options_output .= '<option value="' . esc_attr( $query_var ) . '" ' . selected( $order_value, $query_var, false ) . '>' . esc_html( $label ) . '</option>';
	}

	$order_form_html = sprintf( '
		<form action="%1$s" method="get" id="ideas-order-form" class="nav-form">%2$s
			<label for="orderby">
				<span class="screen-reader-text">%3$s</span>
			</label>
			<select name="orderby" id="ideas-order-box">
				%4$s
			</select>

			<button type="submit" class="submit-sort">
				<span class="dashicons dashicons-filter"></span>
				<span class="screen-reader-text">%5$s</span>
			</button>
		</form>
	', esc_url( $action ), $hidden, esc_attr__( 'Select the sort order', 'wp-idea-stream' ), $options_output, esc_attr__( 'Sort', 'wp-idea-stream' ) );

	echo apply_filters( 'wp_idea_stream_ideas_order_form', $order_form_html );
}

/**
 * Displays the current term description if it exists
 *
 * @since 2.0.0
 *
 * @return string Output for the current term description.
 */
function wp_idea_stream_ideas_taxonomy_description() {

	if ( wp_idea_stream_is_category() || wp_idea_stream_is_tag() ) {
		$term = wp_idea_stream_get_current_term();

		if ( ! empty( $term->description ) ) {
			?>
			<p class="idea-term-description"><?php echo esc_html( $term->description ) ; ?></p>
			<?php
		}
	}
}

/** Idea Loop *****************************************************************/

/**
 * Initialize the ideas loop.
 *
 * @since 2.0.0
 *
 * @param array $args {
 *     Arguments for customizing ideas retrieved in the loop.
 *     Arguments must be passed as an associative array
 *     @type int 'author' to restrict the loop to one author
 *     @type int 'per_page' Number of results per page.
 *     @type int 'page' the page of results to display.
 *     @type string 'search' to limit the query to ideas containing the requested search terms
 *     @type array|string 'exclude' Array or comma separated list of idea IDs to exclude
 *     @type array|string 'include' Array or comma separated list of idea IDs to include
 *     @type string 'orderby' to customize the sorting order type for the ideas (default is by date)
 *     @type string 'order' the way results should be sorted : 'DESC' or 'ASC' (default is DESC)
 *     @type array 'meta_query' Limit ideas regarding their post meta by passing an array of
 *           meta_query conditions. See {@link WP_Meta_Query->queries} for a
 *           description of the syntax.
 *     @type array 'tax_query' Limit ideas regarding their terms by passing an array of
 *           tax_query conditions. See {@link WP_Tax_Query->queries} for a
 *           description of the syntax.
 *     @type string 'idea_name' Limit results by a the post name of the idea.
 *     @type bool 'is_widget' is the query performed inside a widget ?
 * }
 * @return bool         true if ideas were found, false otherwise
 */
function wp_idea_stream_ideas_has_ideas( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$args = wp_parse_args( $args, array() );
	}

	$template_args = array();

	/**
	 * We have arguments, so let's override the main query
	 */
	if ( ! empty( $args ) ) {
		$search_terms = '';

		if ( isset( $_GET[ wp_idea_stream_search_rewrite_id() ] ) ) {
			$search_terms = stripslashes( $_GET[ wp_idea_stream_search_rewrite_id() ] );
		}

		$r = wp_parse_args( $args, array(
			'author'     => wp_idea_stream_is_user_profile_ideas() ? wp_idea_stream_users_displayed_user_id() : '',
			'per_page'   => wp_idea_stream_ideas_per_page(),
			'page'       => 1,
			'search'     => '',
			'exclude'    => '',
			'include'    => '',
			'orderby'    => 'date',
			'order'      => 'DESC',
			'meta_query' => array(),
			'tax_query'  => array(),
			'idea_name'  => '',
			'is_widget'  => false
		) );

		$template_args = array(
			'author'     => (int) $r['author'],
			'per_page'   => (int) $r['per_page'],
			'page'       => (int) $r['page'],
			'search'     => $r['search'],
			'exclude'    => $r['exclude'],
			'include'    => $r['include'],
			'orderby'    => $r['orderby'],
			'order'      => $r['order'],
			'meta_query' => (array) $r['meta_query'],
			'tax_query'  => (array) $r['tax_query'],
			'idea_name'  => $r['idea_name'],
			'is_widget'  => (bool) $r['is_widget'],
		);
	}

	// Get the ideas
	$query_loop = new WP_Idea_Stream_Loop_Ideas( $template_args );

	// Setup the global query loop
	wp_idea_stream()->query_loop = $query_loop;

	/**
	 * @param  bool   true if ideas were found, false otherwise
	 * @param  object $query_loop the ideas loop
	 * @param  array  $template_args arguments used to build the loop
	 * @param  array  $args requested arguments
	 */
	return apply_filters( 'wp_idea_stream_ideas_has_ideas', $query_loop->has_items(), $query_loop, $template_args, $args );
}

/**
 * Get the Ideas returned by the template loop.
 *
 * @since 2.0.0
 *
 * @return array List of Ideas.
 */
function wp_idea_stream_ideas_the_ideas() {
	return wp_idea_stream()->query_loop->items();
}

/**
 * Get the current Idea object in the loop.
 *
 * @since 2.0.0
 *
 * @return object The current Idea within the loop.
 */
function wp_idea_stream_ideas_the_idea() {
	return wp_idea_stream()->query_loop->the_item();
}

/** Loop Output ***************************************************************/
// Mainly inspired by The BuddyPress notifications loop

/**
 * Displays a message in case no idea was found
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_not_found() {
	echo wp_idea_stream_ideas_get_not_found();
}

	/**
	 * Gets a message in case no idea was found
	 *
	 * @since 2.0.0
	 *
	 * @return string the message to output
	 */
	function wp_idea_stream_ideas_get_not_found() {
		// general feedback
		$output = esc_html__( 'It looks like no idea has been submitted yet, please sign in or sign up to add yours!', 'wp-idea-stream' );

		if ( wp_idea_stream_is_user_profile() ) {

			if ( ! wp_idea_stream_is_user_profile_rates() ) {
				$output = sprintf(
					__( 'It looks like %s has not submitted any idea yet', 'wp-idea-stream' ),
					wp_idea_stream_users_get_displayed_user_displayname()
				);
			// We're viewing the idea the user rated
			} else {
				$output = sprintf(
					__( 'It looks like %s has not rated any ideas yet', 'wp-idea-stream' ),
					wp_idea_stream_users_get_displayed_user_displayname()
				);
			}

		} else if ( wp_idea_stream_is_category() ) {
			$output = __( 'It looks like no idea has been published in this category yet', 'wp-idea-stream' );

		} else if ( wp_idea_stream_is_tag() ) {
			$output = __( 'It looks like no idea has been marked with this tag yet', 'wp-idea-stream' );

		} else if ( wp_idea_stream_is_search() ) {
			$output = __( 'It looks like no idea matches your search terms.', 'wp-idea-stream' );

		} else if ( wp_idea_stream_is_search() ) {
			$output = __( 'It looks like no idea matches your search terms.', 'wp-idea-stream' );

		} else if ( wp_idea_stream_is_orderby( 'rates_count' ) ) {
			$output = __( 'It looks like no ideas have been rated yet.', 'wp-idea-stream' );

		} else if ( wp_idea_stream_user_can( 'publish_ideas' ) ) {
			$output = sprintf(
				__( 'It looks like no ideas has been submitted yet, <a href="%s" title="Submit your idea">add yours</a>', 'wp-idea-stream' ),
				esc_url( wp_idea_stream_get_form_url() )
			);
		}

		/**
		 * @param  string $output the message to output
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_not_found', $output );
	}

/**
 * Output the pagination count for the current Ideas loop.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_pagination_count() {
	echo wp_idea_stream_ideas_get_pagination_count();
}
	/**
	 * Return the pagination count for the current Ideas loop.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML for the pagination count.
	 */
	function wp_idea_stream_ideas_get_pagination_count() {
		$query_loop = wp_idea_stream()->query_loop;
		$start_num  = intval( ( $query_loop->page - 1 ) * $query_loop->per_page ) + 1;
		$from_num   = number_format_i18n( $start_num );
		$to_num     = number_format_i18n( ( $start_num + ( $query_loop->per_page - 1 ) > $query_loop->total_idea_count ) ? $query_loop->total_idea_count : $start_num + ( $query_loop->per_page - 1 ) );
		$total      = number_format_i18n( $query_loop->total_idea_count );
		$pag        = sprintf( _n( 'Viewing %1$s to %2$s (of %3$s ideas)', 'Viewing %1$s to %2$s (of %3$s ideas)', $total, 'wp-idea-stream' ), $from_num, $to_num, $total );

		/**
		 * @param  string $pag the pagination count to output
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_pagination_count', $pag );
	}

/**
 * Output the pagination links for the current Ideas loop.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_pagination_links() {
	echo wp_idea_stream_ideas_get_pagination_links();
}

	/**
	 * Return the pagination links for the current Rendez Vous loop.
	 *
	 * @since 2.0.0
	 *
	 * @return string output for the pagination links.
	 */
	function wp_idea_stream_ideas_get_pagination_links() {
		/**
		 * @param  string the pagination links to output
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_pagination_links', wp_idea_stream()->query_loop->pag_links );
	}

/**
 * Output the ID of the idea currently being iterated on.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_id() {
	echo wp_idea_stream_ideas_get_id();
}

	/**
	 * Return the ID of the Idea currently being iterated on.
	 *
	 * @since 2.0.0
	 *
	 * @return int ID of the current Idea.
	 */
	function wp_idea_stream_ideas_get_id() {
		/**
		 * @param  int the idea ID to output
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_id', wp_idea_stream()->query_loop->idea->ID );
	}

/**
 * Checks if the Idea being iterated on is sticky
 *
 * @since 2.0.0
 *
 * @return bool True if the Idea being iterating on is sticky, false otherwise
 */
function wp_idea_stream_ideas_is_sticky_idea() {
	$query_loop = wp_idea_stream()->query_loop;
	$idea = $query_loop->idea;

	if ( ! wp_idea_stream_is_idea_archive() || wp_idea_stream_get_idea_var( 'orderby' ) || wp_idea_stream_is_search() ) {
		return;
	}

	if ( empty( $query_loop->page ) || ( ! empty( $query_loop->page ) && 1 < $query_loop->page ) ) {
		return;
	}

	// Bail if sticky is disabled
	if ( ! wp_idea_stream_is_sticky_enabled() ) {
		return;
	}

	if ( ! empty( $idea->is_sticky ) ) {
		return true;
	} else {
		return wp_idea_stream_ideas_is_sticky( $idea->ID );
	}
}

/**
 * Output the row classes of the Idea being iterated on.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_classes() {
	echo wp_idea_stream_ideas_get_classes();
}

	/**
	 * Gets the row classes for the Idea being iterated on
	 *
	 * @since 2.0.0
	 *
	 * @return string output the row class attribute
	 */
	function wp_idea_stream_ideas_get_classes() {
		$classes = array( 'idea' );

		if ( wp_idea_stream_ideas_is_sticky_idea() ) {
			$classes[] = 'sticky-idea';
		}

		/**
		 * @param  array $classes the idea row classes
		 */
		$classes = apply_filters( 'wp_idea_stream_ideas_get_classes', $classes );

		return 'class="' . join( ' ', $classes ) . '"';
	}

/**
 * Does the idea have a featured image ?
 *
 * @since 2.4.0
 *
 * @return bool True if the idea has a featured image. False otherwise.
 */
function wp_idea_stream_ideas_has_featured_image() {
	$idea = wp_idea_stream()->query_loop->idea;

	if ( ! post_type_supports( wp_idea_stream_get_post_type(), 'thumbnail' ) ) {
		return false;
	}

	if ( isset( $idea->featured_image ) ) {
		return (bool) $idea->featured_image;
	}

	$size = apply_filters( 'wp_idea_stream_ideas_featured_image_size', 'post-thumbnail' );

	$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $idea->ID ), $size );

	$idea_index = array_flip( wp_filter_object_list( wp_idea_stream()->query_loop->ideas, array( 'ID' => $idea->ID ), 'and', 'ID' ) );
	$idea_index = reset( $idea_index );

	if ( ! $featured_image ) {
		wp_idea_stream()->query_loop->ideas[ $idea_index ]->featured_image = array();
		wp_idea_stream()->query_loop->idea->featured_image = array();
		return false;
	}

	wp_idea_stream()->query_loop->ideas[ $idea_index ]->featured_image = $featured_image;
	wp_idea_stream()->query_loop->idea->featured_image = $featured_image;

	return true;
}

/**
 * Output the featured image.
 *
 * @since 2.4.0
 */
function wp_idea_stream_ideas_featured_image() {
	echo wp_idea_stream_ideas_get_featured_image();
}

	/**
	 * Get the featured image
	 *
	 * @since 2.4.0
	 *
	 * @return string HTML Output.
	 */
	function wp_idea_stream_ideas_get_featured_image() {
		$idea   = wp_idea_stream()->query_loop->idea;
		$output = '';

		if ( empty( $idea->featured_image ) ) {
			return $output;
		}

		$image = reset( $idea->featured_image );

		return apply_filters( 'wp_idea_stream_ideas_get_featured_image', sprintf( '
			<div class="featured-image">
				<img src="%1$s" alt="%2$s" class="post-thumbnail aligncenter">
			</div>
		', esc_url( $image ), sprintf( __( 'Featured image for: %s', 'wp-idea-stream'), esc_attr( $idea->post_title ) ) ) );
	}

/**
 * Output the css class for the idea's content container in loops.
 *
 * @since 2.4.0
 *
 * @param  string Space separated list of CSS classes.
 */
function wp_idea_stream_ideas_content_class( $class = '' ) {
	echo wp_idea_stream_ideas_get_content_class( $class );
}

	/**
	 * Get the css class for the idea's content container in loops.
	 *
	 * @since 2.4.0
	 *
	 * @param  string Space separated list of CSS classes.
	 * @return string Space separated list of CSS classes.
	 */
	function wp_idea_stream_ideas_get_content_class( $class = '' ) {
		$classes = array();
		$idea    = wp_idea_stream()->query_loop->idea;

		if ( $class ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_map( 'esc_attr', $class );
		} else {
			$class = array();
		}

		if ( wp_idea_stream_ideas_has_featured_image() ) {
			$classes[] = 'has_featured_image';
		}

		$classes_array = (array) apply_filters( 'wp_idea_stream_ideas_get_content_class', $classes, $idea );

		return join( ' ', array_map( 'sanitize_html_class', $classes_array ) );
	}

/**
 * Output the author avatar of the Idea being iterated on.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_author_avatar() {
	echo wp_idea_stream_ideas_get_author_avatar();
}

	/**
	 * Gets the author avatar
	 *
	 * @since 2.0.0
	 *
	 * @return string output the author's avatar
	 */
	function wp_idea_stream_ideas_get_author_avatar() {
		$idea   = wp_idea_stream()->query_loop->idea;
		$author = $idea->post_author;
		$avatar = get_avatar( $author );
		$avatar_link = '<a href="' . esc_url( wp_idea_stream_users_get_user_profile_url( $author ) ) . '" title="' . esc_attr__( 'User&#39;s profile', 'wp-idea-stream' ) . '">' . $avatar . '</a>';

		/**
		 * @param  string  $avatar_link the avatar output
		 * @param  int     $author the author ID
		 * @param  string  $avatar the avatar
		 * @param  WP_Post $idea the idea object
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_author_avatar', $avatar_link, $author, $avatar, $idea );
	}

/**
 * Prefix idea title.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_before_idea_title() {
	echo wp_idea_stream_ideas_get_before_idea_title();
}

	/**
	 * Gets the idea title prefix
	 *
	 * @since 2.0.0
	 *
	 * @return string output the idea title prefix
	 */
	function wp_idea_stream_ideas_get_before_idea_title() {
		$output = '';

		if ( wp_idea_stream_ideas_is_sticky_idea() ) {
			$output = '<span class="sticky-idea"></span> ';
		}

		/**
		 * @param  string  $output the avatar output
		 * @param  int     the idea ID
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_before_idea_title', $output, wp_idea_stream()->query_loop->idea->ID );
	}

/**
 * Displays idea title.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_title() {
	echo wp_idea_stream_ideas_get_title();
}

	/**
	 * Gets the title of the idea
	 *
	 * @since 2.0.0
	 *
	 * @return string output the title of the idea
	 */
	function wp_idea_stream_ideas_get_title() {
		$idea = wp_idea_stream()->query_loop->idea;
		$title = get_the_title( $idea );

		/**
		 * @param  string  $title the title to output
		 * @param  WP_Post $idea the idea object
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_title', $title, $idea );
	}

/**
 * Displays idea permalink.
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_permalink() {
	echo wp_idea_stream_ideas_get_permalink();
}

	/**
	 * Gets the permalink of the idea
	 *
	 * @since 2.0.0
	 *
	 * @return string output the permalink to the idea
	 */
	function wp_idea_stream_ideas_get_permalink() {
		$idea = wp_idea_stream()->query_loop->idea;
		$permalink = wp_idea_stream_ideas_get_idea_permalink( $idea );

		/**
		 * @param  string  the permalink url
		 * @param  WP_Post $idea the idea object
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_permalink', esc_url( $permalink ), $idea );
	}

/**
 * Adds to idea's permalink an attribute containg the idea's title
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_title_attribute() {
	echo wp_idea_stream_ideas_get_title_attribute();
}

	/**
	 * Gets the title attribute of the idea's permalink
	 *
	 * @since 2.0.0
	 *
	 * @return string output of the attribute
	 */
	function wp_idea_stream_ideas_get_title_attribute() {
		$idea = wp_idea_stream()->query_loop->idea;
		$title = '';

		if ( ! empty( $idea->post_password ) ) {
			$title = _x( 'Protected:', 'idea permalink title protected attribute', 'wp-idea-stream' ) . ' ';
		} else if ( ! empty( $idea->post_status ) && 'private' == $idea->post_status ) {
			$title = _x( 'Private:', 'idea permalink title private attribute', 'wp-idea-stream' ) . ' ';
		}

		$title .= $idea->post_title;

		/**
		 * @param  string  the title to output
		 * @param  string  the db title
		 * @param  WP_Post $idea the idea object
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_title_attribute', esc_attr( $title ), $idea->post_title, $idea );
	}

/**
 * Displays the number of comments about an idea
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_comment_number() {
	echo wp_idea_stream_ideas_get_comment_number();
}

	/**
	 * Gets the title attribute of the idea's permalink
	 *
	 * @since 2.0.0
	 *
	 * @param  int $id the idea ID
	 * @return int the comments number
	 */
	function wp_idea_stream_ideas_get_comment_number( $id = 0 ) {
		if ( empty( $id ) ) {
			$id = wp_idea_stream()->query_loop->idea->ID;
		}

		return get_comments_number( $id );
	}

/**
 * Displays the comment link of an idea
 *
 * @since 2.0.0
 *
 * @param  mixed $zero       false or the text to show when idea got no comments
 * @param  mixed $one        false or the text to show when idea got one comment
 * @param  mixed $more       false or the text to show when idea got more than one comment
 * @param  string $css_class the name of the css classes to use
 * @param  mixed $none       false or the text to show when no idea comment link
 */
function wp_idea_stream_ideas_the_idea_comment_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	echo wp_idea_stream_ideas_get_idea_comment_link( $zero, $one, $more, $css_class, $none );
}

	/**
	 * Gets the comment link of an idea
	 *
	 * @since 2.0.0
	 *
	 * @param  mixed $zero       false or the text to show when idea got no comments
	 * @param  mixed $one        false or the text to show when idea got one comment
	 * @param  mixed $more       false or the text to show when idea got more than one comment
	 * @param  string $css_class the name of the css classes to use
	 * @param  mixed $none       false or the text to show when no idea comment link
	 * @return string             output for the comment link
	 */
	function wp_idea_stream_ideas_get_idea_comment_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
		$output = '';
		$idea = wp_idea_stream()->query_loop->idea;

		if ( false === $zero ) {
			$zero = __( 'No Comments', 'wp-idea-stream' );
		}
		if ( false === $one ) {
			$one = __( '1 Comment', 'wp-idea-stream' );
		}
		if ( false === $more ) {
			$more = __( '% Comments', 'wp-idea-stream' );
		}
		if ( false === $none ) {
			$none = __( 'Comments Off', 'wp-idea-stream' );
		}

		$number = wp_idea_stream_ideas_get_comment_number( $idea->ID );
		$title = '';

		if ( post_password_required( $idea->ID ) ) {
			$title = _x( 'Comments are protected.', 'idea protected comments message', 'wp-idea-stream' );
			$output .= '<span class="idea-comments-protected">' . $title . '</span>';
		} else if ( ! empty( $idea->post_status ) && 'private' == $idea->post_status && ! wp_idea_stream_user_can( 'read_idea', $idea->ID ) ) {
			$title = _x( 'Comments are private.', 'idea private comments message', 'wp-idea-stream' );
			$output .= '<span class="idea-comments-private">' . $title . '</span>';
		} else if ( ! comments_open( $idea->ID ) ) {
			$output .= '<span' . ( ( ! empty( $css_class ) ) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';
		} else {
			$comment_link = ( 0 == $number ) ? wp_idea_stream_ideas_get_idea_permalink( $idea ) . '#respond' : wp_idea_stream_ideas_get_idea_comments_link( $idea );
			$output .= '<a href="' . esc_url( $comment_link ) . '"';

			if ( ! empty( $css_class ) ) {
				$output .= ' class="' . esc_attr( $css_class ) . '" ';
			}

			$title = esc_attr( strip_tags( $idea->post_title ) );

			$output .= ' title="' . esc_attr( sprintf( __('Comment on %s', 'wp-idea-stream'), $title ) ) . '">';

			$comment_number_output = '';

			if ( $number > 1 ) {
				$comment_number_output = str_replace( '%', number_format_i18n( $number ), $more );
			} elseif ( $number == 0 ) {
				$comment_number_output = $zero;
			} else { // must be one
				$comment_number_output = $one;
			}

			/**
			 * Filter the comments count for display just like WordPress does
			 * in get_comments_number_text()
			 *
			 * @param  string  $comment_number_output
			 * @param  int     $number
			 */
			$comment_number_output = apply_filters( 'comments_number', $comment_number_output, $number );

			$output .= $comment_number_output . '</a>';
		}

		/**
		 * @param  string  $output the comment link to output
		 * @param  int     the idea ID
		 * @param  string  $title the title attribute
		 * @param  int     $number amount of comments about the idea
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_idea_comment_link', $output, $idea->ID, $title, $number );
	}

/**
 * Displays the average rating of an idea
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_average_rating() {
	echo wp_idea_stream_ideas_get_average_rating();
}

	/**
	 * Gets the average rating of an idea
	 *
	 * @since 2.0.0
	 *
	 * @param  int $id the idea ID
	 * @return string  output for the average rating
	 */
	function wp_idea_stream_ideas_get_average_rating( $id = 0 ) {
		if ( empty( $id ) ) {
			$id = wp_idea_stream()->query_loop->idea->ID;
		}

		$rating = get_post_meta( $id, '_ideastream_average_rate', true );

		if ( ! empty( $rating ) && is_numeric( $rating ) ) {
			$rating = number_format_i18n( $rating, 1 );
		}

		return $rating;
	}

/**
 * Displays the rating link of an idea
 *
 * @since 2.0.0
 *
 * @param  mixed $zero       false or the text to show when idea got no rates
 * @param  mixed $more       false or the text to show when idea got one or more rates
 * @param  string $css_class the name of the css classes to use
 */
function wp_idea_stream_ideas_the_rating_link( $zero = false, $more = false, $css_class = '' ) {
	// Bail if ratings are disabled
	if ( wp_idea_stream_is_rating_disabled() ) {
		return false;
	}

	if ( wp_idea_stream_is_single_idea() ) {
		echo '<div id="rate" data-idea="' . wp_idea_stream()->query_loop->idea->ID . '"></div><div class="rating-info"></div>';
	} else {
		echo wp_idea_stream_ideas_get_rating_link( $zero, $more, $css_class );
	}
}

	/**
	 * Gets the rating link of an idea
	 *
	 * @since 2.0.0
	 *
	 * @param  mixed $zero       false or the text to show when idea got no rates
	 * @param  mixed $more       false or the text to show when idea got one or more rates
	 * @param  string $css_class the name of the css classes to use
	 * @return string             output for the rating link
	 */
	function wp_idea_stream_ideas_get_rating_link( $zero = false, $more = false, $css_class = '' ) {
		$output = '';
		$idea = wp_idea_stream()->query_loop->idea;

		// Simply dont display votes if password protected or private.
		if ( post_password_required( $idea->ID ) ) {
			return $output;
		} else if ( ! empty( $idea->post_status ) && 'private' == $idea->post_status && ! wp_idea_stream_user_can( 'read_idea', $idea->ID ) ) {
			return $output;
		}

		if ( false === $zero ) {
			$zero = __( 'Not rated yet', 'wp-idea-stream' );
		}
		if ( false === $more ) {
			$more = __( 'Average rating: %', 'wp-idea-stream' );
		}

		$average = wp_idea_stream_ideas_get_average_rating( $idea->ID );

		$rating_link = wp_idea_stream_ideas_get_idea_permalink( $idea ) . '#rate';

		$title = esc_attr( strip_tags( $idea->post_title ) );
		$title = sprintf( __('Rate %s', 'wp-idea-stream'), $title );

		if ( ! is_user_logged_in() ) {
			$rating_link = wp_login_url( $rating_link );
			$title = _x( 'Please, log in to rate.', 'idea rating not logged in message', 'wp-idea-stream' );
		}

		$output .= '<a href="' . esc_url( $rating_link ) . '"';

		if ( ! empty( $css_class ) ) {
			if ( empty( $average ) ) {
				$css_class .= ' empty';
			}
			$output .= ' class="' . $css_class . '" ';
		}

		$output .= ' title="' . esc_attr( $title ) . '">';

		if ( ! empty( $average  ) ) {
			$output .= str_replace( '%', $average, $more );
		} else {
			$output .= $zero;
		}

		$output .= '</a>';

		/**
		 * @param  string  $output the rating link to output
		 * @param  int     the idea ID
		 * @param  string  $title the title attribute
		 * @param  string  $average the average rating of an idea
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_rating_link', $output, $idea->ID, $title, $average );
	}

/**
 * Displays the excerpt of an idea
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_excerpt() {
	echo wp_idea_stream_ideas_get_excerpt();
}

	/**
	 * Gets the excerpt of an idea
	 *
	 * @since 2.0.0
	 * @since 2.3.0 Use the $post global to make sure Theme filtering the excerpt_more will display the right link
	 *
	 * @global  WP_Post $post the current post
	 * @return string  output for the excerpt
	 */
	function wp_idea_stream_ideas_get_excerpt() {
		global $post;
		$reset_post = $post;
		$idea = wp_idea_stream()->query_loop->idea;

		// Password protected
		if ( post_password_required( $idea ) ) {
			$excerpt = __( 'This idea is password protected, you will need it to view its content.', 'wp-idea-stream' );

		// Private
		} else if ( ! empty( $idea->post_status ) && 'private' == $idea->post_status && ! wp_idea_stream_user_can( 'read_idea', $idea->ID ) ) {
			$excerpt = __( 'This idea is private, you cannot view its content.', 'wp-idea-stream' );

		// Public
		} else {
			$excerpt = strip_shortcodes( $idea->post_excerpt );
		}

		if ( empty( $excerpt ) ) {
			// This is temporary!
			$post = $idea;

			$excerpt = wp_idea_stream_create_excerpt( $idea->post_content, 20 );

			// Reset the post
			$post = $reset_post;
		} else {
			/**
			 * @param  string  $excerpt the excerpt to output
			 * @param  WP_Post $idea the idea object
			 */
			$excerpt = apply_filters( 'wp_idea_stream_create_excerpt_text', $excerpt, $idea );
		}

		return $excerpt;
	}

/**
 * Displays the content of an idea
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_content() {
	echo wp_idea_stream_ideas_get_content();
}

	/**
	 * Gets the content of an idea
	 *
	 * @since 2.0.0
	 * @since 2.3.0 Set the $post global with the Idea post type
	 *
	 * @global WP_Post $post
	 * @return string  output for the excerpt
	 */
	function wp_idea_stream_ideas_get_content() {
		global $post;
		$reset_post = $post;

		// Temporarly set the post to be the idea so that embeds works!
		$post = wp_idea_stream()->query_loop->idea;

		// Password protected
		if ( post_password_required( $post ) ) {
			$content = __( 'This idea is password protected, you will need it to view its content.', 'wp-idea-stream' );

		// Private
		} else if ( ! empty( $post->post_status ) && 'private' == $post->post_status && ! wp_idea_stream_user_can( 'read_idea', $post->ID ) ) {
			$content = __( 'This idea is private, you cannot view its content.', 'wp-idea-stream' );

		// Public
		} else {
			$content = $post->post_content;
		}

		/**
		 * @param  string  $content the content to output
		 * @param  WP_Post $post the idea object
		 */
		$content = apply_filters( 'wp_idea_stream_ideas_get_content', $content, $post );

		// Reset the post.
		$post = $reset_post;

		/**
		 * shortcode_unautop filter fails in groups ??
		 * So we're manually executing the shortcodes
		 * before returning the content.
		 */
		return do_shortcode( $content );
	}

/**
 * Displays the term list links
 *
 * @since 2.0.0
 *
 * @param   integer $id       the idea ID
 * @param   string  $taxonomy the taxonomy of the terms
 * @param   string  $before   the string to display before
 * @param   string  $sep      the separator for the term list
 * @param   string  $after    the string to display after
 * @return  string the term list links
 */
function wp_idea_stream_ideas_get_the_term_list( $id = 0, $taxonomy = '', $before = '', $sep = ', ', $after = '' ) {
	// Bail if no idea ID or taxonomy identifier
	if ( empty( $id ) || empty( $taxonomy ) ) {
		return false;
	}

	/**
	 * @param  string  the term list
	 * @param  int $id the idea ID
	 * @param  string $taxonomy the taxonomy identifier
	 */
	return apply_filters( 'wp_idea_stream_ideas_get_the_term_list', get_the_term_list( $id, $taxonomy, $before, $sep, $after ), $id, $taxonomy );
}

/**
 * Displays a custom field in single idea's view
 *
 * @since 2.0.0
 *
 * @param  string $display_meta the meta field single output
 * @param  object $meta_object  the meta object
 * @param  string $context      the display context (single/form/admin)
 */
function wp_idea_stream_meta_single_display( $display_meta = '', $meta_object = null, $context = '' ) {
	echo wp_idea_stream_get_meta_single_display( $display_meta, $meta_object, $context );
}

	/**
	 * Gets the custom field output for single idea's view
	 *
	 * @since 2.0.0
	 *
	 * @param  string $display_meta the meta field single output
	 * @param  object $meta_object  the meta object
	 * @param  string $context      the display context (single/form/admin)
	 * @return string               HTML Output
	 */
	function wp_idea_stream_get_meta_single_display( $display_meta = '', $meta_object = null, $context = '' ) {
		// Bail if no field name.
		if ( empty( $meta_object->field_name ) ) {
			return;
		}

		$output = '';

		if ( 'single' != $context ) {
			return;
		}

		$output  = '<p><strong>' . esc_html( $meta_object->label ) . '</strong> ';
		$output .= esc_html( $meta_object->field_value ) . '</p>';

		/**
		 * @param  string $output       the meta field single output
		 * @param  object $meta_object  the meta object
		 * @param  string $context      the display context (single/form/admin)
		 */
		return apply_filters( 'wp_idea_stream_get_meta_single_display', $output, $meta_object, $context );
	}

/**
 * Displays the footer of an idea
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_idea_footer() {
	echo wp_idea_stream_ideas_get_idea_footer();
}

	/**
	 * Gets the footer of an idea
	 *
	 * @since 2.0.0
	 *
	 * @return string  output for the footer
	 */
	function wp_idea_stream_ideas_get_idea_footer() {
		$idea = wp_idea_stream()->query_loop->idea;

		$date = apply_filters( 'get_the_date', mysql2date( get_option( 'date_format' ), $idea->post_date ) );
		$placeholders = array( 'date' => $date );

		$category_list = wp_idea_stream_ideas_get_the_term_list( $idea->ID, wp_idea_stream_get_category() );
		$tag_list      = wp_idea_stream_ideas_get_the_term_list( $idea->ID, wp_idea_stream_get_tag() );

		// Translators: 1 is category, 2 is tag and 3 is the date.
		$retarray = array(
			'utility_text' => _x( 'This idea was posted on %3$s.', 'default idea footer utility text', 'wp-idea-stream' ),
		);

		if ( ! empty( $category_list ) ) {
			// Translators: 1 is category, 2 is tag and 3 is the date.
			$retarray['utility_text'] = _x( 'This idea was posted in %1$s on %3$s.', 'idea attached to at least one category footer utility text', 'wp-idea-stream' );
			$placeholders['category'] = $category_list;
		}

		if ( ! empty( $tag_list ) ) {
			// Translators: 1 is category, 2 is tag and 3 is the date.
			$retarray['utility_text'] = _x( 'This idea was tagged %2$s on %3$s.', 'idea attached to at least one tag footer utility text', 'wp-idea-stream' );
			$placeholders['tag'] = $tag_list;

			if ( ! empty( $category_list ) ) {
				// Translators: 1 is category, 2 is tag and 3 is the date.
				$retarray['utility_text'] =  _x( 'This idea was posted in %1$s and tagged %2$s on %3$s.', 'idea attached to at least one tag and one category footer utility text', 'wp-idea-stream' );
			}
		}

		if ( wp_idea_stream_is_single_idea() || wp_idea_stream_ideas_has_featured_image() ) {
			$user = wp_idea_stream_users_get_user_data( 'id', $idea->post_author );
			$user_link = '<a class="idea-author" href="' . esc_url( wp_idea_stream_users_get_user_profile_url( $idea->post_author, $user->user_nicename ) ) . '" title="' . esc_attr( $user->display_name ) . '">';
			$user_link .= get_avatar( $idea->post_author, 20 ) . esc_html( $user->display_name ) . '</a>';

			// Translators: 1 is category, 2 is tag, 3 is the date and 4 is author.
			$retarray['utility_text']  = _x( 'This idea was posted on %3$s by %4$s.', 'default single idea footer utility text', 'wp-idea-stream' );
			$placeholders['user_link'] = $user_link;

			if ( ! empty( $category_list ) ) {
				// Translators: 1 is category, 2 is tag, 3 is the date and 4 is author.
				$retarray['utility_text'] = _x( 'This idea was posted in %1$s on %3$s by %4$s.', 'single idea attached to at least one category footer utility text', 'wp-idea-stream' );
			}

			if ( ! empty( $tag_list ) ) {
				// Translators: 1 is category, 2 is tag, 3 is the date and 4 is author.
				$retarray['utility_text'] = _x( 'This idea was tagged %2$s on %3$s by %4$s.', 'single idea attached to at least one tag footer utility text', 'wp-idea-stream' );

				if ( ! empty( $category_list ) ) {
					// Translators: 1 is category, 2 is tag, 3 is the date and 4 is author.
					$retarray['utility_text'] =  _x( 'This idea was posted in %1$s and tagged %2$s on %3$s by %4$s.', 'single idea attached to at least one tag and one category footer utility text', 'wp-idea-stream' );
				}
			}

			// Print placeholders
			$retarray['utility_text'] = sprintf(
				$retarray['utility_text'],
				$category_list,
				$tag_list,
				$date,
				$user_link
			);

		} else {
			// Print placeholders
			$retarray['utility_text'] = sprintf(
				$retarray['utility_text'],
				$category_list,
				$tag_list,
				$date
			);
		}

		// Init edit url
		$edit_url = '';

		// Super admin will use the IdeaStream Administration screens
		if ( wp_idea_stream_user_can( 'wp_idea_stream_ideas_admin' ) ) {
			$edit_url = get_edit_post_link( $idea->ID );

		// The author will use the front end edit form
		} else if ( wp_idea_stream_ideas_can_edit( $idea ) ) {
			$edit_url = wp_idea_stream_get_form_url( wp_idea_stream_edit_slug(), $idea->post_name );
		}

		if ( ! empty( $edit_url ) ) {
			$edit_class = 'edit-idea';
			$edit_title = __( 'Edit Idea', 'wp-idea-stream' );

			if ( 'ideas' !== $idea->post_type ) {
				$post_type_labels = get_post_type_labels( get_post_type_object( $idea->post_type ) );
				if ( ! empty( $post_type_labels->singular_name ) ) {
					$edit_class = 'edit-' . strtolower( $post_type_labels->singular_name );
					$edit_title = $post_type_labels->edit_item;
				}
			}

			$retarray['edit'] = '<a class="' . sanitize_html_class( $edit_class ) . '" href="' . esc_url( $edit_url ) . '" title="' . esc_attr( $edit_title ) . '">' . esc_html( $edit_title ) . '</a>';
		}

		/**
		 * Filter here to edit the idea footer utility text
		 *
		 * @since 2.3.2 Added the placeholders parameter
		 *
		 * @param  string  the footer to output
		 * @param  array   $retarray the parts of the footer organized in an associative array
		 * @param  WP_Post $idea the idea object
		 * @param  array   $placeholders the placeholders for the footer utility text
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_idea_footer', join( ' ', $retarray ), $retarray, $idea, $placeholders );
	}

/**
 * Displays a bottom nav on single template
 *
 * @since 2.0.0
 *
 * @return string the bottom nav output
 */
function wp_idea_stream_ideas_bottom_navigation() {
	$idea_root = wp_idea_stream_get_root_url();
	if ( wp_idea_stream_is_front_page() ) {
		$idea_root = home_url();
	}

	?>
	<ul class="idea-nav-single">
		<li class="idea-nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'wp-idea-stream' ) . '</span> %title' ); ?></li>
		<li class="idea-nav-all"><span class="meta-nav">&uarr;</span> <a href="<?php echo esc_url( $idea_root );?>" title="<?php esc_attr_e( 'All Ideas', 'wp-idea-stream') ;?>"><?php esc_html_e( 'All Ideas', 'wp-idea-stream') ;?></a></li>
		<li class="idea-nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'wp-idea-stream' ) . '</span>' ); ?></li>
	</ul>
	<?php
}

/** Idea Form *****************************************************************/

/**
 * Displays a message to not logged in users
 *
 * @since 2.0.0
 *
 * @return string the not logged in message output
 */
function wp_idea_stream_ideas_not_loggedin() {
	$output = esc_html__( 'You are not allowed to submit ideas', 'wp-idea-stream' );

	if ( ! is_user_logged_in() ) {

		if ( wp_idea_stream_is_signup_allowed_for_current_blog() ) {
			$output = sprintf(
				__( 'Please <a href="%s" title="Log in">log in</a> or <a href="%s" title="Sign up">register</a> to this site to submit an idea.', 'wp-idea-stream' ),
				esc_url( wp_login_url( wp_idea_stream_get_form_url() ) ),
				esc_url( wp_idea_stream_users_get_signup_url() )
			);
		} else {
			$output = sprintf(
				__( 'Please <a href="%s" title="Log in">log in</a> to this site to submit an idea.', 'wp-idea-stream' ),
				esc_url( wp_login_url( wp_idea_stream_get_form_url() ) )
			);
		}

		// Check for a custom message..
		$custom_message = wp_idea_stream_login_message();

		if ( ! empty( $custom_message ) ) {
			$output = $custom_message;
		}

	}

	/**
	 * @param  string $output the message to output
	 */
	echo apply_filters( 'wp_idea_stream_ideas_not_loggedin', $output );
}

/**
 * Displays the field to edit the idea title
 *
 * @since 2.0.0
 *
 * @return string output for the idea title field
 */
function wp_idea_stream_ideas_the_title_edit() {
	?>
	<label for="_wp_idea_stream_the_title"><?php esc_html_e( 'Title', 'wp-idea-stream' );?> <span class="required">*</span></label>
	<input type="text" id="_wp_idea_stream_the_title" name="wp_idea_stream[_the_title]" value="<?php wp_idea_stream_ideas_get_title_edit();?>"/>
	<?php
}

	/**
	 * Gets the value of the title field of an idea
	 *
	 * @since 2.0.0
	 *
	 * @return string  output for the title field
	 */
	function wp_idea_stream_ideas_get_title_edit() {
		$wp_idea_stream = wp_idea_stream();

		// Did the user submitted a title ?
		if ( ! empty( $_POST['wp_idea_stream']['_the_title'] ) ) {
			$edit_title = $_POST['wp_idea_stream']['_the_title'];

		// Are we editing an idea ?
		} else if ( ! empty( $wp_idea_stream->query_loop->idea->post_title ) ) {
			$edit_title = $wp_idea_stream->query_loop->idea->post_title;

		// Fallback to empty
		} else {
			$edit_title = '';
		}

		/**
		 * @param  string $edit_title the title field
		 */
		echo apply_filters( 'wp_idea_stream_ideas_get_title_edit', esc_attr( $edit_title ) );
	}

/**
 * Displays the field to edit the idea content
 *
 * @since 2.0.0
 *
 * @return string output for the idea content field
 */
function wp_idea_stream_ideas_the_editor() {
	$args = array(
		'textarea_name' => 'wp_idea_stream[_the_content]',
		'wpautop'       => true,
		'media_buttons' => false,
		'editor_class'  => 'wp-idea-stream-tinymce',
		'textarea_rows' => get_option( 'default_post_edit_rows', 10 ),
		'teeny'         => false,
		'dfw'           => false,
		'tinymce'       => true,
		'quicktags'     => false
	);

	// Temporarly filter the editor
	add_filter( 'mce_buttons', 'wp_idea_stream_teeny_button_filter', 10, 1 );
	?>

	<label for="wp_idea_stream_the_content"><?php esc_html_e( 'Description', 'wp-idea-stream' ) ;?> <span class="required">*</span></label>

	<?php
	do_action( 'wp_idea_stream_media_buttons' );
	wp_editor( wp_idea_stream_ideas_get_editor_content(), 'wp_idea_stream_the_content', $args );

	remove_filter( 'mce_buttons', 'wp_idea_stream_teeny_button_filter', 10, 1 );
}

	/**
	 * Gets the value of the content field of an idea
	 *
	 * @since 2.0.0
	 *
	 * @return string  output for the content field
	 */
	function wp_idea_stream_ideas_get_editor_content() {
		$wp_idea_stream = wp_idea_stream();

		// Did the user submitted a content ?
		if ( ! empty( $_POST['wp_idea_stream']['_the_content'] ) ) {
			$edit_content = $_POST['wp_idea_stream']['_the_content'];

		// Are we editing an idea ?
		} else if ( ! empty( $wp_idea_stream->query_loop->idea->post_content ) ) {
			$edit_content = do_shortcode( $wp_idea_stream->query_loop->idea->post_content );

		// Fallback to empty
		} else {
			$edit_content = '';
		}

		/**
		 * @param  string $edit_content the content field
		 */
		return apply_filters( 'wp_idea_stream_ideas_get_editor_content', $edit_content );
	}

/**
 * Displays the list of inserted images to let the user
 * choose the one he wishes to use as the Idea Featured image
 *
 * @since 2.3.0
 *
 * @return string HTML Output
 */
function wp_idea_stream_ideas_the_images_list() {
	if ( ! wp_idea_stream_featured_images_allowed() || ! current_theme_supports( 'post-thumbnails' ) ) {
		return;
	}

	$selected       = false;
	$content        = '';
	$srcs           = array();
	$wp_idea_stream = wp_idea_stream();
	$class          = ' class="hidden"';

	// There was an error eg: missing title
	if( ! empty( $_POST['wp_idea_stream']['_the_content'] ) ) {
		$content = wp_unslash( $_POST['wp_idea_stream']['_the_content'] );

		// Did the user selected a featured image ?
		if ( ! empty( $_POST['wp_idea_stream']['_the_thumbnail'] ) ) {
			$selected = (array) $_POST['wp_idea_stream']['_the_thumbnail'];
			$selected = reset( $selected );
		}

	// Are we editing an idea ?
	} else if ( ! empty( $wp_idea_stream->query_loop->idea->post_content ) ) {
		$idea    = $wp_idea_stream->query_loop->idea;
		$content = $idea->post_content;

		// Try to get the current featured image
		$selected = (int) get_post_thumbnail_id( $idea );

		if ( ! empty( $selected ) ) {
			$original_url = get_post_meta( $selected, '_ideastream_original_src', true );

			if ( empty( $original_url ) ) {
				$original_url = wp_get_attachment_url( $selected );
			}

			$srcs = array( $original_url => $selected );
		}

		/**
		 * Get all idea attachments (those who have an _ideastream_original_url meta)
		 *
		 * We need to do this in case the featured image was edited and for some reason the
		 * user deleted one or more images from the content
		 */
		$srcs = array_replace( $srcs, WP_Idea_Stream_Ideas_Thumbnail::get_idea_attachments( $idea->ID ) );
	}

	// Find image into the content
	if ( ! empty( $content ) ) {
		$class = '';

		if ( false !== stripos( $content, 'src=' ) ) {
			preg_match_all( '#src=(["\'])([^"\']+)\1#i', $content, $img_srcs );
			if ( ! empty( $img_srcs[2] ) ) {
				// Avoid duplicates
				$content_srcs = array_unique( $img_srcs[2] );

				// Create a non numeric keys array
				$content_srcs = array_combine( $content_srcs, $content_srcs );

				/**
				 * Make sure to use attachment ids if some were found earlier
				 */
				$srcs = array_replace( $content_srcs, $srcs );
			}
		}
	}

	// Can be an attachment ID
	if ( ! empty( $selected ) ) {
		if ( is_numeric( $selected ) ) {
			$selected = (int) $selected;

		// Or an url
		} else {
			$selected = esc_url( $selected );
		}
	}
	?>
	<div id="idea-images-list"<?php echo $class; ?>>
		<label><?php esc_html_e( 'Select the featured image for your idea.', 'wp-idea-stream' );?></label>
		<?php if ( ! empty( $srcs ) ) : ?>
			<ul>
			<?php foreach ( $srcs as $ksrc => $src )  : ?>
				<li>
					<img src="<?php echo esc_url( $ksrc ) ;?>"/>

					<?php if ( is_numeric( $src ) ) {
						$thumbnail = (int) $src;
					} else {
						$thumbnail = esc_url( $src );
					};?>

					<div class="cb-container">
						<input type="checkbox" name="wp_idea_stream[_the_thumbnail][<?php echo esc_url_raw( $ksrc ) ;?>]" value="<?php echo $thumbnail ;?>" <?php checked( $selected, $thumbnail ); ?>/>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif ;?>
	</div>
	<?php
}

/**
 * Checks if the category taxonomy has terms
 *
 * @since 2.0.0
 *
 * @return bool true if category has terms, false otherwise
 */
function wp_idea_stream_ideas_has_terms() {
	// Allow hiding cats
	$pre_has_terms = apply_filters( 'wp_idea_stream_ideas_pre_has_terms', true );

	if ( empty( $pre_has_terms ) ) {
		return false;
	}

	// Allow category listing override
	$args = apply_filters( 'wp_idea_stream_ideas_get_terms_args', array() );

	// Get all terms matching args
	$terms = wp_idea_stream_ideas_get_terms( wp_idea_stream_get_category(), $args );

	if ( empty( $terms ) ) {
		return false;
	}

	// Catch terms
	wp_idea_stream_set_idea_var( 'edit_form_terms', $terms );

	// Inform we have categories
	return true;
}

/**
 * Displays the checkboxes to select categories
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_category_edit() {
	if ( ! taxonomy_exists( wp_idea_stream_get_category() ) || ! wp_idea_stream_ideas_has_terms() ) {
		return;
	}
	?>
	<label><?php esc_html_e( 'Categories', 'wp-idea-stream' );?></label>
	<?php wp_idea_stream_ideas_get_category_edit();
}

	/**
	 * Builds a checkboxes list of categories
	 *
	 * @since 2.0.0
	 *
	 * @return string  output for the list of categories
	 */
	function wp_idea_stream_ideas_get_category_edit() {
		$wp_idea_stream = wp_idea_stream();

		// Did the user submitted categories ?
		if ( ! empty( $_POST['wp_idea_stream']['_the_category'] ) ) {
			$edit_categories = (array) $_POST['wp_idea_stream']['_the_category'];

		// Are we editing an idea ?
		} else if ( ! empty( $wp_idea_stream->query_loop->idea->ID ) ) {
			$edit_categories = (array) wp_get_object_terms( $wp_idea_stream->query_loop->idea->ID, wp_idea_stream_get_category(), array( 'fields' => 'ids' ) );

		// Default to en empty array
		} else {
			$edit_categories = array();
		}

		$terms = wp_idea_stream_get_idea_var( 'edit_form_terms' );

		// Default output
		$output = esc_html__( 'No categories are available.', 'wp-idea-stream' );

		if ( empty( $terms ) ) {
			/**
			 * @param  string $output the output when no categories
			 */
			echo apply_filters( 'wp_idea_stream_ideas_get_category_edit_none', $output );
			return;
		}

		$output = '<ul class="category-list">';

		foreach ( $terms as $term ) {
			$output .= '<li><label for="_wp_idea_stream_the_category_' . esc_attr( $term->term_id ) . '">';
			$output .= '<input type="checkbox" name="wp_idea_stream[_the_category][]" id="_wp_idea_stream_the_category_' . esc_attr( $term->term_id ) . '" value="' . esc_attr( $term->term_id ) . '" ' . checked( true, in_array( $term->term_id, $edit_categories  ), false ) . '/>';
			$output .= ' ' . esc_html( $term->name ) . '</label></li>';

		}

		$output .= '</ul>';

		/**
		 * @param  string $output the output when has categories
		 * @param  array  $edit_categories selected term ids
		 * @param  array  $terms available terms for the category taxonomy
		 */
		echo apply_filters( 'wp_idea_stream_ideas_get_category_edit', $output, $edit_categories, $terms );
	}


/**
 * Displays the tag editor for an idea
 *
 * @since 2.0.0
 */
function wp_idea_stream_ideas_the_tags_edit() {
	if ( ! taxonomy_exists( wp_idea_stream_get_tag() ) ) {
		return;
	}
	?>
	<label for="_wp_idea_stream_the_tags"><?php esc_html_e( 'Tags', 'wp-idea-stream' );?></label>
	<p class="description"><?php esc_html_e( 'Type your tag, then hit the return or space key to add it','wp-idea-stream' ); ?></p>
	<div id="_wp_idea_stream_the_tags"><?php wp_idea_stream_ideas_get_tags();?></div>
	<?php wp_idea_stream_ideas_the_tag_cloud();
}

	/**
	 * Builds a checkboxes list of categories
	 *
	 * @since 2.0.0
	 *
	 * @return string  output for the list of tags
	 */
	function wp_idea_stream_ideas_get_tags() {
		$wp_idea_stream = wp_idea_stream();

		// Did the user submitted tags ?
		if ( ! empty( $_POST['wp_idea_stream']['_the_tags'] ) ) {
			$edit_tags = (array) $_POST['wp_idea_stream']['_the_tags'];

		// Are we editing tags ?
		} else if ( ! empty( $wp_idea_stream->query_loop->idea->ID ) ) {
			$edit_tags = (array) wp_get_object_terms( $wp_idea_stream->query_loop->idea->ID, wp_idea_stream_get_tag(), array( 'fields' => 'names' ) );

		// Default to an empty array
		} else {
			$edit_tags = array();
		}

		// Sanitize tags
		$edit_tags = array_map( 'esc_html', $edit_tags );

		/**
		 * @param  string the tags list output
		 * @param  array  $edit_tags selected term slugs
		 */
		echo apply_filters( 'wp_idea_stream_ideas_get_tags', join( ', ', $edit_tags ), $edit_tags );
	}

/**
 * Displays a tag cloud to show the most used one
 *
 * @since 2.0.0
 *
 * @param  int $number the number of tags to display
 * @return string output for the tag cloud
 */
function wp_idea_stream_ideas_the_tag_cloud( $number = 10 ) {
	$tag_cloud = wp_idea_stream_generate_tag_cloud();

	if ( empty( $tag_cloud ) ) {
		return;
	}

	if ( $tag_cloud['number'] != $number  ) {
		$number = $tag_cloud['number'];
	}

	$number = number_format_i18n( $number );
	?>
	<div id="wp_idea_stream_most_used_tags">
		<p class="description"><?php printf( _n( 'Choose the most used tag', 'Choose from the %d most used tags', $number, 'wp-idea-stream' ), $number ) ;?></p>
		<div class="tag-items">
			<?php echo $tag_cloud['tagcloud'] ;?>
		</div>
	</div>
	<?php
}

/**
 * Displays a meta field for form/admin views
 *
 * @since 2.0.0
 *
 * @param  string $display_meta the meta field single output
 * @param  object $meta_object  the meta object
 * @param  string $context      the display context (single/form/admin)
 */
function wp_idea_stream_meta_admin_display( $display_meta = '', $meta_object = null, $context = '' ) {
	echo wp_idea_stream_get_meta_admin_display( $display_meta, $meta_object, $context );
}

	/**
	 * Gets the custom field output for form/admin idea's view
	 *
	 * @since 2.0.0
	 *
	 * @param  string $display_meta the meta field single output
	 * @param  object $meta_object  the meta object
	 * @param  string $context      the display context (single/form/admin)
	 * @return string               HTML Output
	 */
	function wp_idea_stream_get_meta_admin_display( $display_meta = '', $meta_object = null, $context = '' ) {
		if ( empty( $meta_object->field_name ) ) {
			return;
		}

		$output = '';

		if ( 'admin' == $context ) {
			$output  = '<p><strong class="label">' . esc_html( $meta_object->label ) . '</strong> ';
			$output .= '<input type="text" name="' . esc_attr( $meta_object->field_name ) . '" value="' . esc_attr( $meta_object->field_value ) . '"/></p>';
		} else if ( 'form' == $context ) {
			$output  = '<p><label for="_wp_idea_stream_' . $meta_object->meta_key . '">' . esc_html( $meta_object->label ) . '</label>';
			$output .= '<input type="text" id="_wp_idea_stream_' . $meta_object->meta_key . '" name="' . esc_attr( $meta_object->field_name ) . '" value="' . esc_attr( $meta_object->field_value ) . '"/></p>';
		}

		/**
		 * @param  string $output       the meta field admin/form output
		 * @param  object $meta_object  the meta object
		 * @param  string $context      the display context (single/form/admin)
		 */
		return apply_filters( 'wp_idea_stream_get_meta_admin_display', $output, $meta_object, $context );
	}

/**
 * Displays the form submit/reset buttons
 *
 * @since 2.0.0
 *
 * @return string output for submit/reset buttons
 */
function wp_idea_stream_ideas_the_form_submit() {
	$wp_idea_stream = wp_idea_stream();

	wp_nonce_field( 'wp_idea_stream_save' );

	do_action( 'wp_idea_stream_ideas_the_form_submit' ); ?>

	<?php if ( wp_idea_stream_is_addnew() ) : ?>

		<input type="reset" value="<?php esc_attr_e( 'Reset', 'wp-idea-stream' ) ;?>"/>
		<input type="submit" value="<?php esc_attr_e( 'Submit', 'wp-idea-stream' ) ;?>" name="wp_idea_stream[save]"/>

	<?php elseif( wp_idea_stream_is_edit() && ! empty( $wp_idea_stream->query_loop->idea->ID ) ) : ?>

		<input type="hidden" value="<?php echo esc_attr( $wp_idea_stream->query_loop->idea->ID ) ;?>" name="wp_idea_stream[_the_id]"/>
		<input type="submit" value="<?php esc_attr_e( 'Update', 'wp-idea-stream' ) ;?>" name="wp_idea_stream[save]"/>

	<?php endif ; ?>

	<?php
}

/**
 * If BuddyDrive is activated, then use it to allow files
 * to be added to ideas !
 *
 * @since  2.2.0
 */
function wp_idea_stream_buddydrive_button() {
	if ( function_exists( 'buddydrive_editor' ) ) {
		buddydrive_editor();
	}
}
add_action( 'wp_idea_stream_media_buttons', 'wp_idea_stream_buddydrive_button' );

/**
 * Output the Idea Ratings if needed into the Embedded idea
 *
 * @since  2.3.0
 *
 * @return string HTML output
 */
function wp_idea_stream_ideas_embed_meta() {
	$idea = get_post();

	if ( ! isset( $idea->post_type ) || wp_idea_stream_get_post_type() !== $idea->post_type || wp_idea_stream_is_rating_disabled() ) {
		return;
	}

	// Get the Average Rate
	$average_rate = wp_idea_stream_ideas_get_average_rating( $idea->ID );

	if ( ! $average_rate ) {
		return;
	}

	// Get rating link
	$rating_link = wp_idea_stream_ideas_get_idea_permalink( $idea ) . '#rate';
	?>
	<div class="wp-idea-stream-embed-ratings">
		<a href="<?php echo esc_url( $rating_link ); ?>" target="_top">
			<span class="dashicons ideastream-star-filled"></span>
			<?php printf(
				esc_html__( '%1$sAverage Rating:%2$s%3$s', 'wp-idea-stream' ),
				'<span class="screen-reader-text">',
				'</span>',
				$average_rate
			); ?>
		</a>
	</div>
	<?php
}
