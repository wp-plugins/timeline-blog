<?php get_header(); ?>
<div id="blog-area">               
	<ul class="blog_timeline">
	    <li id="post-<?php the_ID(); ?>" class="<?php echo $color;?>">
	        <div class="blog_timeline_label" data-animate="fromBottom">
	            <?php if( has_post_thumbnail() ) : ?>
	            <div class="post-image">
	                <?php the_post_thumbnail( 'full' ); ?>
	            </div>
	            <?php endif; ?>
	            <h2><?php the_title(); ?></h2>
	            <div class="blog_timeline_content">
	            	<?php the_content(); ?>
	            </div>
	            <?php
	            // If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>
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
	</ul>
</div> <!-- #blog-area -->
<?php get_footer(); ?>