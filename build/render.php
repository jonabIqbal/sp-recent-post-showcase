<?php
/**
 * Server-side rendering for Recent Post Showcase block.
 *
 * @package SP_Recent_Post_Showcase
 */

// Extract attributes with default values.
$number_of_posts     = isset( $attributes['numberOfPosts'] ) ? absint( $attributes['numberOfPosts'] ) : 4;
$show_post_author    = isset( $attributes['showPostAuthor'] ) ? (bool) $attributes['showPostAuthor'] : false;
$link_to_author_page = isset( $attributes['linkToAuthorPage'] ) ? (bool) $attributes['linkToAuthorPage'] : false;
$show_post_date      = isset( $attributes['showPostDate'] ) ? (bool) $attributes['showPostDate'] : false;
$show_post_image     = isset( $attributes['showPostImage'] ) ? (bool) $attributes['showPostImage'] : false;
$layout              = isset( $attributes['layout'] ) ? sanitize_text_field( $attributes['layout'] ) : 'grid';
$selected_category   = isset( $attributes['selectedCategory'] ) ? absint( $attributes['selectedCategory'] ) : 0;
$grid_column         = isset( $attributes['gridColumn'] ) ? absint( $attributes['gridColumn'] ) : 3;

$args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
	'posts_per_page' => $number_of_posts,
);

$sp_paged      = isset( $_GET['sp_page'] ) ? max( 1, absint( $_GET['sp_page'] ) ) : 1;
$args['paged'] = $sp_paged;

if ( $selected_category > 0 ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $selected_category,
		),
	);
}

$query = new WP_Query( $args );

ob_start();
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => "sp-recent-posts {$layout}-layout" ) ); ?>>
	<?php if ( 'carousel' === $layout ) { ?>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const carousels = document.querySelectorAll('.swiper.sp-post-grid-wrapper');
			carousels.forEach(function(carousel) {
				new PCPSwiper(carousel, {
					loop: true,
					slidesPerView: <?php echo $grid_column; ?>,
					spaceBetween: 20,
					navigation: {
						nextEl: carousel.querySelector('.swiper-button-next'),
						prevEl: carousel.querySelector('.swiper-button-prev'),
					},
					pagination: {
						el: carousel.querySelector('.swiper-pagination'),
						clickable: true,
					},
					breakpoints: {
						640: {
							slidesPerView: 1,
						},
						768: {
							slidesPerView: 2,
						},
						1024: {
							slidesPerView: <?php echo $grid_column; ?>,
						},
					}
				});
			});
		});
		</script>
		<div class="swiper sp-post-grid-wrapper">
			<div class="swiper-wrapper">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					$post = get_post();
					?>
					<div class="swiper-slide sp-post-item">
						<?php if ( $show_post_image && has_post_thumbnail( $post->ID ) ) { ?>
						<div class="sp-post-image">
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
								<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
							</a>
						</div>
						<?php } ?>

						<div class="sp-post-body">
							<h2 class="sp-post-title">
								<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
									<?php echo esc_html( get_the_title( $post ) ); ?>
								</a>
							</h2>

							<?php if ( $show_post_author || $show_post_date ) { ?>
							<div class="sp-post-meta">
								<?php if ( $show_post_author ) { ?>
								<span class="sp-post-author">
									<?php esc_html_e( 'By', 'sp-recent-post-showcase' ); ?>
									<?php if ( $link_to_author_page ) { ?>
									<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>">
										<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
									</a>
									<?php } else { ?>
										<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
									<?php } ?>
								</span>
								<?php } ?>

								<?php if ( $show_post_date ) { ?>
								<time class="sp-post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>">
									<?php
									if ( $show_post_author ) {
										echo ' ';
									}
									echo esc_html__( 'On', 'sp-recent-post-showcase' ) . ' ' . esc_html( get_the_date( 'F j, Y', $post ) );
									?>
								</time>
								<?php } ?>
							</div>
							<?php } ?>

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
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<!-- Navigation and Pagination -->
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
			<div class="swiper-pagination"></div>
		</div>
	<?php } else { ?>
		<div class="sp-post-grid-wrapper" style="grid-template-columns: repeat(<?php echo esc_attr( $grid_column ); ?>, 1fr);">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				?>
				<div class="sp-post-item">
					<?php if ( $show_post_image && has_post_thumbnail( $post->ID ) ) { ?>
					<div class="sp-post-image">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
							<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
						</a>
					</div>
					<?php } ?>

					<div class="sp-post-body">
						<h2 class="sp-post-title">
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
								<?php echo esc_html( get_the_title( $post ) ); ?>
							</a>
						</h2>

						<?php if ( $show_post_author || $show_post_date ) { ?>
						<div class="sp-post-meta">
							<?php if ( $show_post_author ) { ?>
							<span class="sp-post-author">
								<?php esc_html_e( 'By', 'sp-recent-post-showcase' ); ?>
								<?php if ( $link_to_author_page ) { ?>
								<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>">
									<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
								</a>
								<?php } else { ?>
									<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
								<?php } ?>
							</span>
							<?php } ?>

							<?php if ( $show_post_date ) { ?>
							<time class="sp-post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>">
								<?php
								if ( $show_post_author ) {
									echo ' ';
								}
								echo esc_html__( 'On', 'sp-recent-post-showcase' ) . ' ' . esc_html( get_the_date( 'F j, Y', $post ) );
								?>
							</time>
							<?php } ?>
						</div>
						<?php } ?>

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
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
		<?php
		$total_pages = $query->max_num_pages;
		if ( $total_pages > 1 ) {
			echo '<nav class="sp-post-pagination" aria-label="' . esc_attr__( 'Posts navigation', 'sp-recent-post-showcase' ) . '">';
			echo paginate_links(
				array(
					'current'   => $sp_paged,
					'total'     => $total_pages,
					'prev_text' => __( '« Prev', 'sp-recent-post-showcase' ),
					'next_text' => __( 'Next »', 'sp-recent-post-showcase' ),
					'format'    => '?sp_page=%#%',
					'add_args'  => array(),
				)
			);
			echo '</nav>';
		}
		?>
	<?php } ?>
</div>

<?php
$output = ob_get_clean();

// Enqueue assets only when needed.
if ( 'carousel' === $layout ) {
	wp_enqueue_style(
		'sp-swiper-css',
		SP_RECENT_POST_SHOWCASE_URL . '/assets/css/swiper.min.css',
		array(),
		SP_RECENT_POST_SHOWCASE_VERSION
	);
	wp_enqueue_script(
		'sp-swiper-js',
		SP_RECENT_POST_SHOWCASE_URL . '/assets/js/swiper.min.js',
		array(),
		SP_RECENT_POST_SHOWCASE_VERSION,
		true
	);
}

echo $output;