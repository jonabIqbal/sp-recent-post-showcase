<?php
/**
 * Server-side rendering for Recent Post Showcase block.
 */

$number_of_posts     = $attributes['numberOfPosts'] ?? 4;
$show_post_author    = $attributes['showPostAuthor'] ?? false;
$link_to_author_page = $attributes['linkToAuthorPage'] ?? false;
$show_post_date      = $attributes['showPostDate'] ?? false;
$show_post_image     = $attributes['showPostImage'] ?? false;
$layout              = $attributes['layout'] ?? 'grid';
$selected_category   = $attributes['selectedCategory'] ?? 0;

$args = array(
	'numberposts' => $number_of_posts,
	'post_type'   => 'post',
	'post_status' => 'publish',
	'orderby'     => 'date',
	'order'       => 'DESC',
);

if ( $selected_category && $selected_category > 0 ) {
	$args['category'] = $selected_category;
}

$posts = get_posts( $args );

if ( empty( $posts ) ) {
	return '<p>' . esc_html__( 'No posts found.', 'sp-recent-post-showcase' ) . '</p>';
}

ob_start();
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php if ( 'carousel' === $layout ) : ?>
	<div class="swiper sp-post-grid-wrapper">
		<div class="swiper-wrapper">
			<?php foreach ( $posts as $post ) : ?>
				<?php setup_postdata( $post ); ?>
			<div class="swiper-slide sp-post-item">
				<?php if ( $show_post_image ) : ?>
				<div class="sp-post-image">
					<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
				</div>
				<?php endif; ?>

				<div class="sp-post-body">
					<h2 class="sp-post-title">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
							<?php echo esc_html( get_the_title( $post ) ); ?>
						</a>
					</h2>

					<?php if ( $show_post_author || $show_post_date ) : ?>
					<div class="sp-post-meta">
						<?php if ( $show_post_author ) : ?>
						<span class="sp-post-author">
							<?php esc_html_e( 'By', 'sp-recent-post-showcase' ); ?>
							<?php if ( $link_to_author_page ) : ?>
							<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>">
								<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
							</a>
							<?php else : ?>
								<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
							<?php endif; ?>
						</span>
						<?php endif; ?>

						<?php if ( $show_post_date ) : ?>
						<time class="sp-post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>">
							<?php echo esc_html__( 'On', 'sp-recent-post-showcase' ) . ' ' . esc_html( get_the_date( 'F j, Y', $post ) ); ?>
						</time>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<div class="sp-post-content">
						<?php echo wp_kses_post( get_the_excerpt( $post ) ); ?>
					</div>

					<div class="sp-post-btn">
						<a class="sp-post-read-more" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
							<span><?php esc_html_e( 'Read More', 'sp-recent-post-showcase' ); ?></span>
						</a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>

		<!-- Navigation and Pagination -->
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
		<div class="swiper-pagination"></div>
	</div>

	<?php else : ?>
	<div class="sp-post-grid-wrapper <?php echo esc_attr( $layout ); ?>-layout">
		<?php foreach ( $posts as $post ) : ?>
			<?php setup_postdata( $post ); ?>
		<div class="sp-post-item">
			<?php if ( $show_post_image ) : ?>
			<div class="sp-post-image">
				<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
			</div>
			<?php endif; ?>

			<div class="sp-post-body">
				<h2 class="sp-post-title">
					<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
						<?php echo esc_html( get_the_title( $post ) ); ?>
					</a>
				</h2>

				<?php if ( $show_post_author || $show_post_date ) : ?>
				<div class="sp-post-meta">
					<?php if ( $show_post_author ) : ?>
					<span class="sp-post-author">
						<?php esc_html_e( 'By', 'sp-recent-post-showcase' ); ?>
						<?php if ( $link_to_author_page ) : ?>
						<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>">
							<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
						</a>
						<?php else : ?>
							<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
						<?php endif; ?>
					</span>
					<?php endif; ?>

					<?php if ( $show_post_date ) : ?>
					<time class="sp-post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>">
						<?php echo esc_html__( 'On', 'sp-recent-post-showcase' ) . ' ' . esc_html( get_the_date( 'F j, Y', $post ) ); ?>
					</time>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="sp-post-content">
					<?php echo wp_kses_post( get_the_excerpt( $post->ID ) ); ?>
				</div>

				<div class="sp-post-btn">
					<a class="sp-post-read-more" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
						<span><?php esc_html_e( 'Read More', 'sp-recent-post-showcase' ); ?></span>
					</a>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>
	</div>
	<?php endif; ?>
</div>

<?php
echo ob_get_clean();