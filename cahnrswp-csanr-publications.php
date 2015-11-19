<?php
/*
Plugin Name: CAHNRSWP CSANR Publications
Description: Enables a publications library.
Author: CAHNRS, philcable
Version: 0.1.0
*/

class CAHNRSWP_CSANR_Publications {

	/**
	 * @var string Content type slug.
	 */
	var $publications_post_type = 'publications';

	/**
	 * @var string Taxonomy slugs.
	 */
	var $publications_authors_taxonomy = 'publication-authors';
	var $publications_programs_taxonomy = 'publication-program-areas';
	var $publications_topics_taxonomy = 'publication-topics';
	var $publications_keywords_taxonomy = 'keywords';

	/**
	 * Start the plugin and apply associated hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ), 11 );
		add_action( 'init', array( $this, 'register_taxonomies' ), 10 );
		add_action( 'init', array( $this, 'programs_rewrite_rules' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter( 'manage_edit-publications_columns', array( $this, 'publications_columns' ), 10, 1 );
		add_action( 'manage_publications_posts_custom_column', array( $this, 'publication_columns_data' ), 10, 2 );
		add_filter( 'manage_edit-publications_sortable_columns', array( $this, 'publications_columns_sortability' ), 10, 1 );
		add_action( 'load-edit.php', array( $this, 'publications_edit_load' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 1 );
		add_action( 'save_post_publications', array( $this, 'save_post' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'template_include', array( $this, 'template_include' ), 1 );
		add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class'), 100, 3 );
		add_shortcode( 'csanr_publications_browse', array( $this, 'csanr_publications_browse' ) );
		add_shortcode( 'csanr_publications', array( $this, 'csanr_publications' ) );
	}

	/**
	 * Register content type.
	 */
	public function register_post_type() {
		$publications = array(
			'description'   => 'CSANR Publications.',
			'public'        => true,
			'hierarchical'  => false,
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-media-text',
			'has_archive'   => true,
			'labels'        => array(
				'name'               => 'Publications',
				'singular_name'      => 'Publication',
				'all_items'          => 'All Publications',
				'view_item'          => 'View Publication',
				'add_new_item'       => 'Add New Publication',
				'add_new'            => 'Add New',
				'edit_item'          => 'Edit Publication',
				'update_item'        => 'Update Publication',
				'search_items'       => 'Search publications',
				'not_found'          => 'No publications found',
				'not_found_in_trash' => 'No publications found in Trash',
			),
			'supports'      => array(
				'title',
				'editor',
				'revisions',
			),
			'rewrite'       => array(
				'slug'       => $this->publications_post_type,
				'with_front' => false,
			),
		);
		register_post_type( $this->publications_post_type, $publications );
	}

	/**
	 * Register taxonomies.
	 */
	public function register_taxonomies() {

		$authors = array(
			'labels'            => array(
				'name'                       => 'Authors',
				'singular_name'              => 'Author',
				'all_items'                  => 'All Authors',
				'edit_item'                  => 'Edit Author',
				'view_item'                  => 'View Author',
				'update_item'                => 'Update Author',
				'add_new_item'               => 'Add New Author',
				'new_item_name'              => 'New Author Name',
				'search_items'               => 'Search Authors',
				'popular_items'              => 'Popular Authors',
				'separate_items_with_commas' => 'Separate authors with commas',
				'add_or_remove_items'        => 'Add or remove authors',
				'choose_from_most_used'      => 'Choose from the most used authors',
				'not_found'                  => 'No Authors found.',
			),
			'rewrite'      			=> array(
				'slug'       => $this->publications_post_type . '/author',
				'with_front' => false
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);
		register_taxonomy( $this->publications_authors_taxonomy, $this->publications_post_type, $authors );

		$programs = array(
			'labels'            => array(
				'name'              => 'Programs',
				'singular_name'     => 'Program',
				'all_items'         => 'All Programs',
				'edit_item'         => 'Edit Program',
				'view_item'         => 'View Program',
				'update_item'       => 'Update Program',
				'add_new_item'      => 'Add New Program',
				'new_item_name'     => 'New Program Name',
				'parent_item'       => 'Parent Program',
				'parent_item_colon' => 'Parent Program:',
				'search_items'      => 'Search Programs',
				'not_found'         => 'No Programs found.',
			),
			'rewrite'      			=> array(
				'slug'       => $this->publications_post_type . '/program',
				'with_front' => false
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);
		register_taxonomy( $this->publications_programs_taxonomy, $this->publications_post_type, $programs );

		$topics = array(
			'labels'            => array(
				'name'              => 'Topics',
				'singular_name'     => 'Topic',
				'all_items'         => 'All Topics',
				'edit_item'         => 'Edit Topic',
				'view_item'         => 'View Topic',
				'update_item'       => 'Update Topic',
				'add_new_item'      => 'Add New Topic',
				'new_item_name'     => 'New Topic Name',
				'parent_item'       => 'Parent Topic',
				'parent_item_colon' => 'Parent Topic:',
				'search_items'      => 'Search Topics',
				'not_found'         => 'No Topics found.',
			),
			'rewrite'      			=> array(
				'slug'       => $this->publications_post_type . '/topic',
				'with_front' => false
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);
		register_taxonomy( $this->publications_topics_taxonomy, $this->publications_post_type, $topics );

		$keywords = array(
			'labels'            => array(
				'name'              => 'Keywords',
				'singular_name'     => 'Keyword',
				'all_items'         => 'All Keywords',
				'edit_item'         => 'Edit Keyword',
				'view_item'         => 'View Keyword',
				'update_item'       => 'Update Keyword',
				'add_new_item'      => 'Add New Keyword',
				'new_item_name'     => 'New Keyword Name',
				'parent_item'       => 'Parent Keyword',
				'parent_item_colon' => 'Parent Keyword:',
				'search_items'      => 'Search Keywords',
				'not_found'         => 'No Keywords found.',
			),
			'rewrite'      			=> array(
				'slug'       => $this->publications_post_type . '/keyword',
				'with_front' => false
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);
		register_taxonomy( $this->publications_keywords_taxonomy, $this->publications_post_type, $keywords );

	}

	/**
	 * Add rewrite rules for publications/{year}/ and publications/{year}/page/{number}/ URLs.
	 */
	public function programs_rewrite_rules() {
		add_rewrite_rule(
			$this->publications_post_type . '/([0-9]{4})/?$',
			'index.php?post_type=' . $this->publications_post_type . '&year=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			$this->publications_post_type . '/([0-9]{4})/page/([0-9]{1,})/?$',
			'index.php?post_type=' . $this->publications_post_type . '&year=$matches[1]&paged=$matches[2]',
			'top'
		);
	}

	/**
	 * Enqueue scripts and styles for the admin interface.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( 'edit.php' === $hook && $this->publications_post_type === $screen->post_type ) {
			wp_enqueue_style( 'publications-admin', plugins_url( 'css/admin-publications.css', __FILE__ ), array() );
		}
	}

	/**
	 * Add options page link to the menu.
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=' . $this->publications_post_type, 'Publications Library Settings', 'Settings', 'manage_options', 'publications_settings', array( $this, 'publications_settings_page' ) );
	}

	/**
	 * Options page settings.
	 */
	public function admin_init() {
		register_setting( 'publications_options', 'publications_menu_item' );
	}

	/**
	 * Options page content.
	 */
	public function publications_settings_page() {
		?>
		<div class="wrap">
			<h2>Publications Library Settings</h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'publications_options' ); ?>
				<?php do_settings_sections( 'publications_options' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Publications Library Menu Item</th>
						<td>
							<p>Select a menu item to mark as active when viewing a publication archive.</p>
							<?php
								$menu_name = 'site';
								$locations = get_nav_menu_locations();
								if ( isset( $locations[ $menu_name ] ) ) :
									$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
									$menu_items = wp_get_nav_menu_items( $menu->term_id );
									?>
									<select name="publications_menu_item">
									<?php foreach ( $menu_items as $menu_item ) : ?>
										<option value="<?php echo $menu_item->ID; ?>" <?php selected( get_option( 'publications_menu_item' ), $menu_item->ID ); ?>><?php echo $menu_item->title; ?></option>
									<?php endforeach; ?>
									</select>
								<?php endif; ?>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Replace the list of columns to print on the All Publications screen.
	 *
	 * @param array $columns Default columns.
	 *
	 * @return array Columns to display.
	 */
	public function publications_columns( $columns ) {
		return array(
			'cb' => '<input type="checkbox" />',
			//'year' => __( 'Year' ),
			'title' => __( 'Title' ),
			'taxonomy-publication-authors' => __( 'Authors' ),
			'taxonomy-publication-program-areas' => __( 'Programs' ),
			'taxonomy-publication-topics' => __( 'Topics' ),
			'taxonomy-keywords' => __( 'Keywords' ),
			'featured' =>  __( 'Featured' ),
			'external' =>  __( 'External' ),
			'date' => __( 'Date' ),
		);
	}

	/**
	 * Output values for custom Publications columns.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int $post_id The ID of the current post.
	 */
	public function publication_columns_data( $column_name, $post_id ) {
		$publication_year = get_post_meta( get_the_ID(), '_csanr_publication_year', true );
		$publication_featured = get_post_meta( get_the_ID(), '_csanr_publication_featured', true );
		$publication_external = get_post_meta( get_the_ID(), '_csanr_publication_external', true );
		switch( $column_name ) {
			/*case 'year' :
				if ( $publication_year ) {
					echo esc_html( $publication_year );
				}
				break;*/
			case 'featured' :
				if ( $publication_featured ) {
					echo 'Yes';
				}
				break;
			case 'external' :
				if ( $publication_external ) {
					echo 'Yes';
				}
				break;
		}
	}

	/**
	 * Enable sorting functionality on the "Featured" column.
	 */
	public function publications_columns_sortability( $columns ) {
		$columns['featured'] = 'featured';
		return $columns;
	}

	/**
	 * Enable sorting functionality on the "Featured" column.
	 */
	public function publications_edit_load() {
		add_filter( 'request', array( $this, 'publication_columns_sort' ) );
	}

	/**
	 * Enable sorting functionality on the "Featured" column.
	 */
	function publication_columns_sort( $vars ) {
		if ( $this->publications_post_type === $vars['post_type'] && 'featured' === $vars['orderby'] ) {
			$order_parameters = array(
				'meta_key' => '_featured_pub',
				'orderby' => 'meta_value'
			);
			$vars = array_merge( $vars, $order_parameters);
		}
		return $vars;
	}

	/**
	 * Add custom meta boxes.
	 *
	 * @param string $post_type The slug of the current post type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( $this->publications_post_type !== $post_type ) {
			return;
		}
		add_meta_box(
			'csanr_publication_information',
			'Publication Information',
			array( $this, 'csanr_publication_information' ),
			$this->publications_post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Publication information input markup.
	 */
	public function csanr_publication_information( $post ) {
		wp_nonce_field( 'publications_meta', 'publications_meta_nonce' );
		$publication_url = get_post_meta( $post->ID, '_csanr_publication_url', true );
		$feature = get_post_meta( $post->ID, '_csanr_publication_featured', true );
		$external = get_post_meta( $post->ID, '_csanr_publication_external', true );
		print_r(get_post_meta( $post->ID));
		?>
		<p>
			<label for="csanr-publication-url">Web Address</label>
			<input type="text" class="widefat" name="_csanr_publication_url" id="csanr-publication-url" value="<?php echo esc_attr( $publication_url ); ?>">
		</p>
		<p>
			<label>
				<input type="checkbox" name="_csanr_publication_featured" value="1"<?php checked( $feature, 1 ); ?>>Feature
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="_csanr_publication_external" value="1"<?php checked( $external, 1 ); ?>>External Resource
			</label>
		</p>
		<?php
	}

	/**
	 * Save data associated with a Publication.
	 *
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['publications_meta_nonce'] ) ) {
			return $post_id;
		}
		$nonce = $_POST['publications_meta_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'publications_meta' ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Sanitize and save text inputs.
		if ( isset( $_POST['_csanr_publication_url'] ) ) {
			update_post_meta( $post_id, '_csanr_publication_url', sanitize_text_field( $_POST['_csanr_publication_url'] ) );
		} else {
			delete_post_meta( $post_id, '_csanr_publication_url' );
		}
		// Sanitize and save checkboxes.
		$checkboxes = array( '_csanr_publication_featured', '_csanr_publication_external' );
		foreach( $checkboxes as $checkbox ) {
			if ( isset( $_POST[ $checkbox ] ) ) {
				update_post_meta( $post_id, $checkbox, 1 );
			} else {
				delete_post_meta( $post_id, $checkbox );
			}
		}
	}

	/**
	 * Enqueue the scripts and styles used on the front end.
	 */
	public function wp_enqueue_scripts() {
		$post = get_post();
		if ( is_singular() ) { 
			if ( has_shortcode( $post->post_content, 'csanr_publications_browse' ) ) {
				wp_enqueue_style( 'publication-browse-shortcode', plugins_url( 'css/publication-browse-shortcode.css', __FILE__ ) );
			}
			if ( has_shortcode( $post->post_content, 'csanr_publications' ) ) {
				wp_enqueue_style( 'publications-shortcode', plugins_url( 'css/publications-shortcode.css', __FILE__ ) );
			}
		}
		if ( is_single() && $this->publications_post_type == get_post_type() ) {
			wp_enqueue_style( 'publication', plugins_url( 'css/publication.css', __FILE__ ) );
		}
		$taxonomies = array( $this->publications_authors_taxonomy, $this->publications_programs_taxonomy, $this->publications_topics_taxonomy, $this->publications_keywords_taxonomy );
		if ( is_post_type_archive( $this->publications_post_type ) || is_tax( $taxonomies ) ) {
			wp_enqueue_style( 'publications-archive', plugins_url( 'css/publications.css', __FILE__ ), array( 'spine-theme' ) );
		}
	}

	/**
	 * Add templates for the Publications content type.
	 *
	 * @param string $template
	 *
	 * @return string template path
	 */
	public function template_include( $template ) {
		if ( is_single() && $this->publications_post_type == get_post_type() ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/single.php';
		}
		$taxonomies = array( $this->publications_authors_taxonomy, $this->publications_programs_taxonomy, $this->publications_topics_taxonomy, $this->publications_keywords_taxonomy );
		if ( is_post_type_archive( $this->publications_post_type ) || is_tax( $taxonomies ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/index.php';
		}
		return $template;
	}

	/**
	 * Apply 'dogeared' class to the publications menu item when viewing a publication.
	 *
	 * @param array $classes Current list of nav menu classes.
	 * @param WP_Post $item Post object representing the menu item.
	 * @param stdClass $args Arguments used to create the menu.
	 *
	 * @return array Modified list of nav menu classes.
	 */
	public function nav_menu_css_class( $classes, $item, $args ) {
		$id = esc_attr( get_option( 'publications_menu_item' ) );
		$publication = $this->publications_post_type === get_post_type();
		$publication_archive = is_post_type_archive( $this->publications_post_type );
		$publication_taxonomy_archive = is_tax( array( $this->publications_authors_taxonomy, $this->publications_programs_taxonomy, $this->publications_topics_taxonomy, $this->publications_keywords_taxonomy ) );
		if ( 'site' === $args->theme_location && $item->ID == $id && ! is_page() && ( $publication || $publication_archive || $publication_taxonomy_archive ) ) {
			$classes[] = 'dogeared';
		}
		return $classes;
	}

	/**
	 * Filter for publication year archives.
	 */
	public function publications_archive_filter( $where ) {
		$where = 'WHERE post_type = "' . $this->publications_post_type . '" AND post_status = "publish"';
		return $where;
	}

	/**
	 * Function (leveraging publication year archive filter) to display year archive links for publications.
	 */
	public function get_publications_archives() {
		add_filter( 'getarchives_where', array( $this, 'publications_archive_filter' ), 10, 1 );
		$html = wp_get_archives( array(
			'type'		        => 'yearly',
			'format'	        => 'html', 
			'show_post_count' => 1,
			'echo'		        => 0,
			'order'           => 'ASC',
		) );
		$html = str_replace( "href='" . get_bloginfo('url') . "/", "href='" . get_bloginfo('url') . "/publications/", $html );
		echo $html;
		remove_filter( 'getarchives_where', array( $this, 'publications_archive_filter' ), 10, 1 );
	}

	/**
	 * Display a list of ways to browse Publications.
	 *
	 * @param array $atts Attributes passed to the shortcode.
	 *
	 * @return string Content to display in place of the shortcode.
	 */
	public function csanr_publications_browse( $atts ) {
		ob_start();
		?>
		<dl class="cahnrs-accordion slide">
			<dt>
				<h3>Year Published</h3>
			</dt>
			<dd>
				<ul class="publication-years">
					<?php $this->get_publications_archives(); ?>
				</ul>
			</dd>
		</dl>
		<?php
			$publication_taxonomies = array(
				'Researcher' => $this->publications_authors_taxonomy,
				'Type'       => $this->publications_programs_taxonomy,
				'Topic'      => $this->publications_topics_taxonomy,
				'Keyword'    => $this->publications_keywords_taxonomy,
			);
		?>
		<?php foreach ( $publication_taxonomies as $name => $publication_taxonomy ) : ?>
		<dl class="cahnrs-accordion slide">
			<dt>
				<h3><?php echo $name; ?></h3>
			</dt>
			<dd>
				<?php $publication_taxonomy_terms = get_terms( $publication_taxonomy ); ?>
				<ul>
					<?php
						foreach( $publication_taxonomy_terms as $term ) {
							$term_link = get_term_link( $term, $publication_taxonomy );
							?><li><a href="<?php echo $term_link; ?>"><?php echo $term->name; ?></a> (<?php echo $term->count; ?>)</li><?php
						}
					?>
				</ul>
			</dd>
		</dl>
		<?php endforeach; ?>
    <h3 class="all-publications"><a href="<?php echo get_post_type_archive_link( $this->publications_post_type ); ?>">All Publications &raquo;</a></h3>
    <form role="search" method="get" class="cahnrs-search" action="<?php echo home_url( '/' ); ?>">
			<input type="hidden" name="post_type" value="<?php echo $this->publications_post_type; ?>">
			<label>
				<span class="screen-reader-text">Search Publications for:</span>
				<input type="search" class="cahnrs-search-field" placeholder="Search Publications" value="<?php echo get_search_query(); ?>" name="s" title="Search Publications for:" />
			</label>
			<input type="submit" class="cahnrs-search-submit" value="$" />
		</form>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * Display Publications.
	 *
	 * @param array $atts Attributes passed to the shortcode.
	 *
	 * @return string Content to display in place of the shortcode.
	 */
	public function csanr_publications( $atts ) {
		extract( shortcode_atts(
			array(
				'keywords' => '',
				'count' => 10,
				'title' => '',
			), $atts )
		);
		if ( empty( $keywords ) ) {
			return '';
		}
		$query_base = array(
			'post_type' => $this->publications_post_type,
			'tax_query' => array(
				array(
					'taxonomy' => $this->publications_keywords_taxonomy,
					'field' => 'slug',
					'terms' => sanitize_key( $keywords ),
				)
			),
		);
		$features_query = array(
			'posts_per_page' => -1,
			'meta_key' => '_csanr_publication_featured',
			'meta_value' => '1',
		);
		$additional_query = array(
			'posts_per_page' => $count,
			'meta_query' => array(
				array(
					'key' => '_csanr_publication_featured',
					'compare' => 'NOT EXISTS'
				),
			),
		);
		$featured_publications = new WP_Query( array_merge( $query_base, $features_query ) );
		$additional_publications = new WP_Query( array_merge( $query_base, $additional_query ) );
		ob_start();
		if ( $featured_publications->have_posts() ) :
			?><h2>Featured<?php echo ' ' . $title; ?> Publications</h2><?php
			?><ul class="csanr-publications featured"><?php
			while( $featured_publications->have_posts() ) : $featured_publications->the_post();
				$publication_url = get_post_meta( get_the_ID(), '_csanr_publication_url', true );
				$publication_external = get_post_meta( get_the_ID(), '_csanr_publication_external', true );
				?><li>
					<h3>
						<a href="<?php echo esc_url( $publication_url ); ?>"<?php if ( $publication_external ) { echo ' class="external-publication"'; } ?>>
							<?php the_title(); ?>
						</a>
					</h3>
					<?php the_content(); ?>
				</li><?php
			endwhile;
			?></ul><?php
		endif;
		if ( $additional_publications->have_posts() ) :
			?><h2>Additional<?php echo ' ' . $title; ?> Publications</h2><?php
			?><ul class="csanr-publications additional"><?php
			while( $additional_publications->have_posts() ) : $additional_publications->the_post();
				$publication_url = get_post_meta( get_the_ID(), '_csanr_publication_url', true );
				$publication_external = get_post_meta( get_the_ID(), '_csanr_publication_external', true );
				?><li>
					<h3>
						<a href="<?php echo esc_url( $publication_url ); ?>"<?php if ( $publication_external ) { echo ' class="external-publication"'; } ?>>
							<?php the_title(); ?>
						</a>
					</h3>
					<?php the_content(); ?>
				</li><?php
			endwhile;
			?></ul><?php
		endif;
		wp_reset_postdata();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

}

new CAHNRSWP_CSANR_Publications();