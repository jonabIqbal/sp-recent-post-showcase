<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
$number_of_posts     = $attributes['numberOfPosts'] ?? 4;
$show_post_author    = $attributes['showPostAuthor'] ?? false;
$link_to_author_page = $attributes['linkToAuthorPage'] ?? false;
$show_post_date      = $attributes['showPostDate'] ?? false;
$show_post_image     = $attributes['showPostImage'] ?? false;

$posts = get_posts(
	array(
		'numberposts' => $number_of_posts,
		'post_type'   => 'post',
		'orderby'     => 'date',
		'order'       => 'DESC',
		'post_status' => 'publish',
	)
);


?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<div class="styble-post-grid-wrapper">
		<?php foreach ( $posts as $post ) : ?>
			<div class="styble-post-item">
				<?php if ( $show_post_image ) : ?>
					<div class="styble-post-image">
						<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
					</div>
				<?php endif; ?>

				<div class="styble-post-body">
					<h2 class="styble-post-title">
						<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
							<?php echo esc_html( get_the_title( $post ) ); ?>
						</a>
					</h2>

					<?php if ( $show_post_author || $show_post_date ) : ?>
						<div class="styble-post-meta">
							<?php if ( $show_post_author ) : ?>
								<span class="styble-post-author">
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
								<?php $date = new DateTime( $post->post_date ); ?>
								<time class="styble-post-date" datetime="<?php echo esc_attr( $date->format( 'c' ) ); ?>">
									<?php echo esc_html__( 'On', 'sp-recent-post-showcase' ) . ' ' . esc_html( $date->format( 'F j, Y' ) ); ?>
								</time>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="styble-post-content">
						<?php echo wp_kses_post( get_the_excerpt( $post->ID ) ); ?>
					</div>

					<div class="styble-post-btn">
						<a class="styble-post-read-more" href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
							<span><?php esc_html_e( 'Read More', 'sp-recent-post-showcase' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
    <div class="swiper styble-post-grid-wrapper">
	<div class="swiper-wrapper">
		<?php foreach ( $posts as $post ) : ?>
			<div class="swiper-slide styble-post-item">
				<?php if ( $show_post_image ) : ?>
					<div class="styble-post-image">
						<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
					</div>
				<?php endif; ?>

				<div class="styble-post-body">
					<h2 class="styble-post-title">
						<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
							<?php echo esc_html( get_the_title( $post ) ); ?>
						</a>
					</h2>

					<?php if ( $show_post_author || $show_post_date ) : ?>
						<div class="styble-post-meta">
							<?php if ( $show_post_author ) : ?>
								<span class="styble-post-author">
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
								<?php $date = new DateTime( $post->post_date ); ?>
								<time class="styble-post-date" datetime="<?php echo esc_attr( $date->format( 'c' ) ); ?>">
									<?php echo esc_html__( 'On', 'sp-recent-post-showcase' ) . ' ' . esc_html( $date->format( 'F j, Y' ) ); ?>
								</time>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="styble-post-content">
						<?php echo wp_kses_post( get_the_excerpt( $post->ID ) ); ?>
					</div>

					<div class="styble-post-btn">
						<a class="styble-post-read-more" href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
							<span><?php esc_html_e( 'Read More', 'sp-recent-post-showcase' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<!-- Optional navigation buttons -->
	<div class="swiper-button-prev"></div>
	<div class="swiper-button-next"></div>
	<div class="swiper-pagination"></div>
</div>


</div>
