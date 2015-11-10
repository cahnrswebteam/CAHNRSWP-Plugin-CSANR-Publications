<?php get_header(); ?>

<main class="publications-archive">

	<?php get_template_part('parts/headers'); ?>

	<?php
		global $query_string;
		global $wp_query;

		query_posts( $query_string . '&posts_per_page=50' );

		$mid_size = ( is_year() || is_tax() ) ? 2: 1;
		$pagination_args = array(
			'base'               => str_replace( 99164, '%#%', esc_url( get_pagenum_link( 99164 ) ) ),
			'format'             => 'page/%#%',
			'total'              => $wp_query->max_num_pages, // Provide the number of pages this query expects to fill.
			'current'            => max( 1, get_query_var('paged') ), // Provide either 1 or the page number we're on.
			'mid_size'           => $mid_size,
			'before_page_number' => '<span class="screen-reader-text">Page </span>'
		);
		$current_page = $wp_query->query_vars['paged'];
		$total_posts = $wp_query->found_posts;
		$first_post = is_paged() ? ( $current_page - 1 ) * 50 + 1 : 1;
		$last_post = is_paged() ? $first_post + $wp_query->post_count - 1 : 50;
		$currently_viewing = ( $total_posts > 50 ) ? $first_post . ' - ' . $last_post . ' of ' : '';
		$search_results = is_search() ? ' search results for "' . get_search_query() . '"' : ' publications';
	?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php
      	if ( is_year() ) {
        	$title_prefix = get_the_date( 'Y' ) . ' ';
    		} elseif ( is_tax() ) {
        	$title_prefix = single_term_title( '', false ) . ' ';
				} else {
					$title_prefix = '';
				}
			?>

			<header class="archive-header">
				<h1 class="archive-title"><?php echo $title_prefix; ?>Publications</h1>
			</header>

			<p><?php echo $currently_viewing . $total_posts . $search_results; ?></p>

			<?php if ( $total_posts > 50 ) : ?>
			<p class="publications pager"><?php echo paginate_links( $pagination_args ); ?></p>
			<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					$publication_url = get_post_meta( get_the_ID(), '_csanr_publication_url', true );
					$publication_external = get_post_meta( get_the_ID(), '_csanr_publication_external', true );
					/*$temp = get_post_meta( get_the_ID(), '_pub', true );
					$publication_url = $temp['url'];
					$publication_year = $temp['year'];
					$publication_featured = $temp['featured'];
					$publication_external = $temp['external'];
					if ( $publication_url ) {
						add_post_meta( get_the_ID(), '_csanr_publication_url', $publication_url );
					}
					if ( $publication_year ) {
						add_post_meta( get_the_ID(), '_csanr_publication_year', $publication_year );
					}
					if ( $publication_featured ) {
						add_post_meta( get_the_ID(), '_csanr_publication_featured', 1 );
					}
					if ( $publication_external ) {
						add_post_meta( get_the_ID(), '_csanr_publication_external', 1 );
					}
					delete_post_meta( get_the_ID(), '_pub' );*/
				?>

				<h2>
					<a href="<?php echo esc_url( $publication_url ); ?>"<?php if ( $publication_external ) { echo ' class="external-publication"'; } ?>><?php the_title(); ?></a>
				</h2>

				<?php the_content(); ?>

			<?php endwhile; ?>
			

		</div><!--/column-->

	</section>

	<?php if ( $total_posts > 50 ) : ?>
	<footer class="main-footer archive-footer">
		<section class="row single pager prevnext gutter">
			<div class="column one">
				<?php echo paginate_links( $pagination_args ); ?>
			</div>
		</section>
	</footer>
	<?php endif; ?>

	<?php wp_reset_query(); ?>

	<?php get_template_part( 'parts/footers' ); ?>

</main>

<?php

get_footer();