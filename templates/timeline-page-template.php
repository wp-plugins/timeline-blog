<?php get_header(); ?>
<?php 
$excerpt			=	wpautop( $post->post_excerpt );
$options = get_option( 'timeline_settings' );
?>
<div id="blog-area">
	<h1><?php echo esc_attr( $options['blog_page_title'] );?></h1>
    <ul class="blog_timeline list">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
					?>
					<li id="post-<?php the_ID(); ?>">
						<div class="blog_timeline_label" data-animate="fromBottom">
							<?php if( has_post_thumbnail() ) : ?>
							<div class="post-image">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'large' ); ?>
							</a>
							</div>
							<?php endif; ?>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="category"><?php the_category( ' ' ); ?></div>
							<?php the_excerpt() ?>
							<div class="tag"><?php the_tags( 'Tags: ', ', ', '' ); ?></div>
						</div>
						<div class="blog_timeline_time" data-animate="scaleUp">
							<span>
								<div class="blog_month"><?php the_time('M') ?></div>
								<div class="blog_date"><?php the_time('d') ?></div>
								<div class="blog_year"><?php the_time('Y') ?></div>
							</span>
						</div> 
						<?php $user_id = $post->post_author; ?>
						<div class="blog_author" data-animate="scaleUp">
							<a href="<?php echo get_the_author_link(); ?>">
								<?php echo get_avatar( $user_id, 120 ); ?>
							</a>
						</div>
						<div class="blog_comments" data-animate="scaleUp">
							<?php comments_popup_link( '<span>' . __( '0', 'offshore' ) . '</span>', __( '1', 'offshore' ), __( '%', 'offshore' ) ); ?>
						</div> 
					</li>							
                    <?php 
				endwhile;
			else :
				echo '<p>Nothing Found.</p>';
			endif;
        ?>
    </ul>
    <?php
	if ( function_exists( 'wp_pagenavi' ) )
		wp_pagenavi();
	?>
</div> <!-- #blog-area -->
<?php get_footer(); ?>