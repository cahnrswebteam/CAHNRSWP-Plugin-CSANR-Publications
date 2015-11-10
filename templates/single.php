<?php
$publication_url = get_post_meta( get_the_ID(), '_csanr_publication_url', true );
if ( $publication_url ) :
	wp_redirect( $publication_url );
	exit();
else :
?>

<main>

	<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="article-header">
					<hgroup>
						<h1 class="article-title"><?php the_title(); ?></h1>
					</hgroup>
				</header>

				<div class="article-body">
					<?php the_content(); ?>
				</div>

			</article>

			<?php endwhile; ?>

		</div><!--/column-->

	</section>

	<?php get_template_part( 'parts/footers' ); ?>

</main><!--/#page-->

<?php get_footer(); ?>

<?php endif; ?>