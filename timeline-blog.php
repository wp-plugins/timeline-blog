<?php
/**
 * Plugin Name: Timeline Blog
 * Plugin URI: https://wordpress.org/plugins/timeline-blog/
 * Description: Create a timeline look and feel for your blog.
 * Version: 1.0
 * Author: Jinesh, Senior Software Engineer
 * Author URI: http://www.offshorent.com/
 * Requires at least: 3.0
 * Tested up to: 4.2.3
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'timelineBlog' ) ) :

/**
 * Main timelineBlog Class
 *
 * @class timelineBlog
 * @version	1.0
 */
final class timelineBlog {
	
	/**
	* @var string
	* @since 1.0
	*/
	 
	public $version = '1.0';

	/**
	* @var timelineBlog The single instance of the class
	* @since 1.0
	*/
	 
	protected static $_instance = null;

	/**
	* Main timelineBlog Instance
	*
	* Ensures only one instance of timelineBlog is loaded or can be loaded.
	*
	* @since 1.0
	* @static
	* @return timelineBlog - Main instance
	*/
	 
	public static function init_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
	}

	/**
	* Cloning is forbidden.
	*
	* @since 1.0
	*/

	public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'timeline' ), '1.0' );
	}

	/**
	* Unserializing instances of this class is forbidden.
	*
	* @since 1.0
	*/
	 
	public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'timeline' ), '1.0' );
	}
        
	/**
	* Get the plugin url.
	*
	* @since 1.0
	*/

	public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	* Get the plugin path.
	*
	* @since 1.0
	*/

	public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	* Get Ajax URL.
	*
	* @since 1.0
	*/

	public function ajax_url() {
        return admin_url( 'admin-ajax.php', 'relative' );
	}
        
	/**
	* timelineBlog Constructor.
	* @access public
	* @return timelineBlog
	* @since 1.0
	*/
	 
	public function __construct() {
		
        register_activation_hook( __FILE__, array( &$this, 'timeline_install' ) );

        // Define constants
        self::timeline_constants();

        // Include required files
        self::timeline_admin_includes();

        // Action Hooks
        add_action( 'init', array( $this, 'timeline_init' ), 0 );
        add_action( 'admin_init', array( $this, 'timeline_admin_init' ) );
        add_action( 'admin_menu', array( $this, 'timeline_add_admin_menu' ) );
        add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'timeline_admin_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'timeline_frontend_styles' ) );

		// Filter  Hook
		add_filter( 'template_include', array( &$this, 'timeline_page_template' ) );
		add_filter( 'excerpt_more', array( &$this, 'timeline_excerpt_more' ) ) ;       
	}
        
	/**
	* Install timelineBlog
	* @since 1.0
	*/
	 
	public function timeline_install (){
		
        // Flush rules after install
        flush_rewrite_rules();

        // Redirect to welcome screen
        set_transient( '_timeline_activation_redirect', 1, 60 * 60 );
	}
        
	/**
	* Define timelineBlog Constants
	* @since 1.0
	*/
	 
	private function timeline_constants() {
		
		define( 'TIMELINE_PLUGIN_FILE', __FILE__ );
		define( 'TIMELINE_PLUGIN_BASENAME', plugin_basename( dirname( __FILE__ ) ) );
		define( 'TIMELINE_PLUGIN_URL', plugins_url() . '/' . TIMELINE_PLUGIN_BASENAME );
		define( 'TIMELINE_VERSION', $this->version );
		define( 'TIMELINE_TEXT_DOMAIN', 'timeline' );
		define( 'TIMELINE_PERMALINK_STRUCTURE', get_option( 'permalink_struture' ) ? '&' : '?' );
		
	}
        
	/**
	* includes admin defaults files
	*
	* @since 1.0
	*/
	 
	private function timeline_admin_includes() {
	}
        
	/**
	* Init timelineBlog when WordPress Initialises.
	* @since 1.0
	*/
	 
	public function timeline_init() {
            
        self::timeline_do_output_buffer();
	}
    
    /**
	* Add web portfolio image sizes to WP
	* @since 1.0
	*/
	public function add_image_sizes() {

		//add_image_size( 'timeline_medium', 549, 411, true );
		//add_image_size( 'timeline_full', 549, 411, true );
	}

	/**
	* Clean all output buffers
	*
	* @since  1.0
	*/
	 
	public function timeline_do_output_buffer() {
            
        ob_start( array( &$this, "timeline_do_output_buffer_callback" ) );
	}

	/**
	* Callback function
	*
	* @since  1.0
	*/
	 
	public function timeline_do_output_buffer_callback( $buffer ){
        return $buffer;
	}
	
	/**
	* Clean all output buffers
	*
	* @since  1.0
	*/
	 
	public function timeline_flush_ob_end(){
        ob_end_flush();
	}
    
    /**
	* Add admin menu for timeline blog
	*
	* @since  1.0
	*/
	 
	public function timeline_add_admin_menu () {
		$icon_url = TIMELINE_PLUGIN_URL  . '/images/timeline.png';
		add_menu_page( 'Timeline Blog Settings', 'Timeline', 'manage_options', 'timeline-blog', array( $this, 'timeline_settings_admin_menu' ), $icon_url );
    	add_submenu_page( 'timeline-blog', 'About Offshorent', 'About', 'manage_options', 'about_developer', array( $this, 'about_timeline_developer' ) );
	} 

	/**
	* about_timeline_developer for timeline blog
	*
	* @since  1.0
	*/

	public function about_timeline_developer() {

		ob_start();
		?>
		<div class="wrap">
			<div id="dashboard-widgets">
				<h2><?php _e( 'About Offshorent' );?></h2> 
				<div class="postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<h2><?php _e( "We build your team. We build your trust.." );?></h2>
						<img src="<?php echo TIMELINE_PLUGIN_URL;?>/images/about.jpg" alt="" width="524">
						<p><?php _e( "We are experts at building web and mobile products. And more importantly, we are committed to building your trust. We are a leading offshore outsourcing center that works primarily with digital agencies and software development firms. Offshorent was founded by U.S. based consultants specializing in software development and who built a reputation for identifying the very best off-shore outsourcing talent. We are now applying what we learned over the past ten years with a mission to become the world’s most trusted off-shore outsourcing provider." );?></p>
						<ul class="offshorent">
							<li><a href="http://offshorent.com/services" target="_blank"><?php _e( 'Services' );?></a></li>
							<li><a href="http://offshorent.com/our-work" target="_blank"><?php _e( 'Our Works' );?></a></li>
							<li><a href="http://offshorent.com/clients-speak" target="_blank"><?php _e( 'Testimonials' );?></a></li>
							<li><a href="http://offshorent.com/our-team" target="_blank"><?php _e( 'Our Team' );?></a></li>
							<li><a href="http://offshorent.com/process" target="_blank"><?php _e( 'Process' );?></a></li>
							<li><a href="http://offshorent.com/life-offshorent" target="_blank"><?php _e( 'Life @ Offshorent' );?></a></li>
							<li><a href="https://www.facebook.com/Offshorent" target="_blank"><?php _e( 'Facebook Page' );?></a></li>
							<li><a href="http://offshorent.com/blog" target="_blank"><?php _e( 'Blog' );?></a></li>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>	
				</div>
				<div class="postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<h2><?php _e( "Contact Us" );?></h2>
						<p><?php _e( "Email: " );?><a href="mailto:<?php _e( "info@offshorent.com" );?>"><?php _e( "info@offshorent.com" );?></a></p>
						<p><?php _e( "Project Support: " );?><a href="mailto:<?php _e( "project-support@offshorent.com" );?>"><?php _e( "project-support@offshorent.com" );?></a></p>
						<p><?php _e( "Phone - US Office: " );?><?php _e( "+1(484) 313 – 4264" );?></p>					
						<p><?php _e( "Phone - India: " );?><?php _e( "+91 484 – 2624225" );?></p>
						<div class="location-col">
							<b>Philadelphia / USA</b>
							<p>1150 1st Ave #501,<br> King Of Prussia,PA 19406<br> Tel: (484) 313 &ndash; 4264 <br>Email <a href="mailto:philly@offshorent.com">philly@offshorent.com</a></p>
						</div>
						<div class="location-col">
							<b>Chicago / USA</b>
							<p> 233 South Wacker Drive, Suite 8400,<br> Chicago, IL 60606<br> Tel: (312) 380 &ndash; 0775 <br>Email: <a href="mailto:chicago@offshorent.com">chicago@offshorent.com</a></p>
						</div>
						<div class="location-col">
							<b>California / USA</b>
							<p>17311 Virtuoso. #102 Irvine,<br> CA 92620 <br>Tel: +1 949 391 1012 <br>Email: <a href="mailto:california@offshorent.com">california@offshorent.com</a></p>
						</div>
						<div class="location-col">
							<b>Sydney / AUSTRALIA</b>
							<p>Suite 59, 38 Ricketty St, Mascot,<br> New South Wales &ndash; 2020,<br> Sydney, Australia,<br> Tel: 02 8011 3413 <br>Email: <a href="mailto:sydney@offshorent.com">sydney@offshorent.com</a></p>
						</div>
						<div class="location-col">
							<b>Cochin / INDIA</b>
							<p>Palm Lands, 3rd Floor,<br> Temple Road, Bank Jn,<br> Aluva &ndash; 01, Cochin, Kerala <br>Tel: +91 484 &ndash; 2624225 <br>Email: <a href="mailto:aluva@offshorent.com">aluva@offshorent.com</a></p>
						</div>	
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="social">
				<img src="<?php echo TIMELINE_PLUGIN_URL;?>/images/social.png" usemap="#av92444" width="173" height="32" alt="click map" border="0" />
				<map id="av92444" name="av92444">
					<!-- Region 1 -->
					<area shape="rect" alt="Facebook" title="Facebook" coords="1,2,29,30" href="https://www.facebook.com/Offshorent" target="_blank" />
					<!-- Region 2 -->
					<area shape="rect" alt="Twitter" title="Twitter" coords="36,1,64,31" href="https://twitter.com/Offshorent" target="_blank" />
					<!-- Region 3 -->
					<area shape="rect" alt="Google" title="Google" coords="73,3,98,29" href="https://plus.google.com/+Offshorent/posts" target="_blank" />
					<!-- Region 4 -->
					<area shape="rect" alt="Linkedin" title="Linkedin" coords="110,1,136,30" href="https://www.linkedin.com/company/offshorent" target="_blank" />
					<!-- Region 5 -->
					<area shape="rect" alt="Youtube" title="Youtube" coords="145,3,169,31" href="http://www.youtube.com/user/Offshorent" target="_blank" />
					<area shape="default" nohref alt="" />
				</map>
			</div>			
		</div>
		<?php

		//return ob_get_contents();
	}

	/**
	* Setting function for timeline blog
	*
	* @since  1.0
	*/
	 
	public function timeline_settings_admin_menu () {

		ob_start();

		$options = get_option( 'timeline_settings' );
			
		// General option values
		$blog_page_title = isset( $options['blog_page_title'] ) ? esc_attr( $options['blog_page_title'] ) : 'Blog';
		$font_family = isset( $options['font_family'] ) ? esc_attr( $options['font_family'] ) : 'Open Sans';
		
		// Heading option values
		$heading_font_size = isset( $options['heading_font_size'] ) ? esc_attr( $options['heading_font_size'] ) : '24px';
		$heading_font_color = isset( $options['heading_font_color'] ) ? esc_attr( $options['heading_font_color'] ) : '#000000';			
		$heading_font_hover_color = isset( $options['heading_font_hover_color'] ) ? esc_attr( $options['heading_font_hover_color'] ) : '#cccccc';

		// Content option values
		$content_font_size = isset( $options['content_font_size'] ) ? esc_attr( $options['content_font_size'] ) : '14px';
		$content_font_color = isset( $options['content_font_color'] ) ? esc_attr( $options['content_font_color'] ) : '#999999';
		
		// Color option values
		$first_bg_color = isset( $options['first_bg_color'] ) ? esc_attr( $options['first_bg_color'] ) : '#f0e7cd';
		$first_border_color = isset( $options['first_border_color'] ) ? esc_attr( $options['first_border_color'] ) : '#f6c542';
		$second_bg_color = isset( $options['second_bg_color'] ) ? esc_attr( $options['second_bg_color'] ) : '#ebdbf2';
		$second_border_color = isset( $options['second_border_color'] ) ? esc_attr( $options['second_border_color'] ) : '#9a75ac';
		$third_bg_color = isset( $options['third_bg_color'] ) ? esc_attr( $options['third_bg_color'] ) : '#f3ded5';
		$third_border_color = isset( $options['third_border_color'] ) ? esc_attr( $options['third_border_color'] ) : '#f77322';			
		?>
		<div class="wrap">
			<h2><?php _e( 'Timeline Blog Settings' );?></h2>           
			<form method="post" action="options.php">
				<?php settings_fields( 'timeline_blog' ); ?>
                <div class="form-table">
                	<div class="form-widefat">
					    <h3>General Settings</h3>
					    <div class="row-table">
					        <label>Blog Page Title: </label>
					        <input type="text" name="timeline_settings[blog_page_title]" value="<?php echo esc_attr( $blog_page_title );?>">
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Font Family: </label>
					        <select id="font_family" name="timeline_settings[font_family]">
					            <option value="Arial" <?php selected( $font_family, 'Arial' ); ?>>Arial</option>
					            <option value="Verdana" <?php selected( $font_family, 'Verdana' ); ?>>Verdana</option>
					            <option value="Helvetica" <?php selected( $font_family, 'Helvetica' ); ?>>Helvetica</option>
					            <option value="Comic Sans MS" <?php selected( $font_family, 'Comic Sans MS' ); ?>>Comic Sans MS</option>
					            <option value="Georgia" <?php selected( $font_family, 'Georgia' ); ?>>Georgia</option>
					            <option value="Trebuchet MS" <?php selected( $font_family, 'Trebuchet MS' ); ?>>Trebuchet MS</option>
					            <option value="Times New Roman" <?php selected( $font_family, 'Times New Roman' ); ?>>Times New Roman</option>
					            <option value="Tahoma" <?php selected( $font_family, 'Tahoma' ); ?>>Tahoma</option>
					            <option value="Oswald" <?php selected( $font_family, 'Oswald' ); ?>>Oswald</option>
					            <option value="Open Sans" <?php selected( $font_family, 'Open Sans' ); ?>>Open Sans</option>
					            <option value="Fontdiner Swanky" <?php selected( $font_family, 'Fontdiner Swanky' ); ?>>Fontdiner Swanky</option>
					            <option value="Crafty Girls" <?php selected( $font_family, 'Crafty Girls' ); ?>>Crafty Girls</option>
					            <option value="Pacifico" <?php selected( $font_family, 'Pacifico' ); ?>>Pacifico</option>
					            <option value="Satisfy" <?php selected( $font_family, 'Satisfy' ); ?>>Satisfy</option>
					            <option value="Gloria Hallelujah" <?php selected( $font_family, 'TGloria Hallelujah' ); ?>>TGloria Hallelujah</option>
					            <option value="Bangers" <?php selected( $font_family, 'Bangers' ); ?>>Bangers</option>
					            <option value="Audiowide" <?php selected( $font_family, 'Audiowide' ); ?>>Audiowide</option>
					            <option value="Sacramento" <?php selected( $font_family, 'Sacramento' ); ?>>Sacramento</option>
					        </select>
					        <div class="clear"></div>
					    </div>
					</div>
					<div class="form-widefat">
						<h3>Heading Settings</h3>                            
						<div class="row-table">
					        <label>Font Size: </label>
					        <select name="timeline_settings[heading_font_size]">
					            <?php for( $i = 16; $i < 33; $i++ ) { ?> 
					            <option value="<?php echo $i;?>px" <?php selected( $heading_font_size, $i . 'px' ); ?>><?php echo $i;?>px</option>
					            <?php } ?>
					        </select>
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Font Color: </label>
					        <input type="color" name="timeline_settings[heading_font_color]" value="<?php echo $heading_font_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Font Hover Color: </label>
					        <input type="color" name="timeline_settings[heading_font_hover_color]" value="<?php echo $heading_font_hover_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					</div>
					<div class="form-widefat">
						<h3>Content Settings</h3>     
					    <div class="row-table">
					        <label>Font Size: </label>
					        <select name="timeline_settings[content_font_size]">
					            <?php for( $j = 10; $j < 21; $j++ ) { ?> 
					            <option value="<?php echo $j;?>px" <?php selected( $content_font_size, $j . 'px' ); ?>><?php echo $j;?>px</option>
					            <?php } ?>
					        </select>
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Content Font Color: </label>
					        <input type="color" name="timeline_settings[content_font_color]" value="<?php echo $content_font_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>                        
					</div>
					<div class="form-widefat">
						<h3>Background Color Settings</h3>
						<h3>First Element Settings</h3>      
					    <div class="row-table">
					        <label>Background Color: </label>
					        <input type="color" name="timeline_settings[first_bg_color]" value="<?php echo $first_bg_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Border Color: </label>
					        <input type="color" name="timeline_settings[first_border_color]" value="<?php echo $first_border_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					    <h3>Second Element Settings</h3> 
					    <div class="row-table">
					        <label>Second Color: </label>
					        <input type="color" name="timeline_settings[second_bg_color]" value="<?php echo $second_bg_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Third Color: </label>
					        <input type="color" name="timeline_settings[second_border_color]" value="<?php echo $second_border_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					    <h3>Third Element Settings</h3> 
					    <div class="row-table">
					        <label>Second Color: </label>
					        <input type="color" name="timeline_settings[third_bg_color]" value="<?php echo $third_bg_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>
					    <div class="row-table">
					        <label>Third Color: </label>
					        <input type="color" name="timeline_settings[third_border_color]" value="<?php echo $third_border_color;?>" class="small" />
					        <div class="clear"></div>
					    </div>	                        
					</div>
                </div>	                				
				<?php submit_button(); ?>
			</form>
		</div>
		<?php 

		return ob_get_contents();
	}  

	/**
	* Admin init timelineBlog when WordPress Initialises.
	* @since  1.0
	*/
	 
	public function timeline_admin_init() {

		register_setting(
			'timeline_blog', // Option group
			'timeline_settings', // Option name
			array( &$this, 'sanitize' ) // Sanitize
		);
	}
    
    /**
	* Sanitize each setting field as needed
	* @since 1.0
	*/
		 
	public function sanitize( $input ) {
		
		 
		$new_input = array();
		
		// General Settings option values			
		if( isset( $input['blog_page_title'] ) )
			$new_input['blog_page_title'] = sanitize_text_field( $input['blog_page_title'] );

		if( isset( $input['font_family'] ) )
			$new_input['font_family'] = sanitize_text_field( $input['font_family'] );			
			
		// Heading Settings option values
		if( isset( $input['heading_font_size'] ) )
			$new_input['heading_font_size'] = sanitize_text_field( $input['heading_font_size'] );
			
		if( isset( $input['heading_font_color'] ) )
			$new_input['heading_font_color'] = sanitize_text_field( $input['heading_font_color'] );	
			
		if( isset( $input['heading_font_hover_color'] ) )
			$new_input['heading_font_hover_color'] = sanitize_text_field( $input['heading_font_hover_color'] );
							
						
		// Content Settings option values
		if( isset( $input['content_font_size'] ) )
			$new_input['content_font_size'] = sanitize_text_field( $input['content_font_size'] );
			
		if( isset( $input['content_font_color'] ) )
			$new_input['content_font_color'] = sanitize_text_field( $input['content_font_color'] );	
		

		// Background Color Settings option values	
		if( isset( $input['first_bg_color'] ) )
			$new_input['first_bg_color'] = sanitize_text_field( $input['first_bg_color'] );
			
		if( isset( $input['first_border_color'] ) )
			$new_input['first_border_color'] = sanitize_text_field( $input['first_border_color'] );		

		if( isset( $input['second_bg_color'] ) )
			$new_input['second_bg_color'] = sanitize_text_field( $input['second_bg_color'] );

		if( isset( $input['second_border_color'] ) )
			$new_input['second_border_color'] = sanitize_text_field( $input['second_border_color'] );
			
		if( isset( $input['third_bg_color'] ) )
			$new_input['third_bg_color'] = sanitize_text_field( $input['third_bg_color'] );		

		if( isset( $input['third_border_color'] ) )
			$new_input['third_border_color'] = sanitize_text_field( $input['third_border_color'] );			
			
		return $new_input;
	}

	
	/**
	* admin style hook for timelineBlog
	*
	* @since  1.0
	*/
	 
	public function timeline_admin_styles() {	        
        wp_enqueue_style( 'admin-style', plugins_url( 'css/admin/style.css', __FILE__ ) );    
	}

	/**
	* Frontend style hook for timelineBlog
	*
	* @since  1.0
	*/
	 
	public function timeline_frontend_styles() {
		if( !is_admin() ){

			$options = get_option( 'timeline_settings' );

			// General option values
			$font_family = isset( $options['font_family'] ) ? esc_attr( $options['font_family'] ) : '';

			// Heading option values
			$heading_font_size = isset( $options['heading_font_size'] ) ? esc_attr( $options['heading_font_size'] ) : '';
			$heading_font_color = isset( $options['heading_font_color'] ) ? esc_attr( $options['heading_font_color'] ) : '';         
			$heading_font_hover_color = isset( $options['heading_font_hover_color'] ) ? esc_attr( $options['heading_font_hover_color'] ) : '';

			// Content option values
			$content_font_size = isset( $options['content_font_size'] ) ? esc_attr( $options['content_font_size'] ) : '';
			$content_font_color = isset( $options['content_font_color'] ) ? esc_attr( $options['content_font_color'] ) : '';

			// Color option values
			$first_bg_color = isset( $options['first_bg_color'] ) ? esc_attr( $options['first_bg_color'] ) : '';
			$first_border_color = isset( $options['first_border_color'] ) ? esc_attr( $options['first_border_color'] ) : '';
			$second_bg_color = isset( $options['second_bg_color'] ) ? esc_attr( $options['second_bg_color'] ) : '';
			$second_border_color = isset( $options['second_border_color'] ) ? esc_attr( $options['second_border_color'] ) : '';
			$third_bg_color = isset( $options['third_bg_color'] ) ? esc_attr( $options['third_bg_color'] ) : '';
			$third_border_color = isset( $options['third_border_color'] ) ? esc_attr( $options['third_border_color'] ) : '';

			$custom_css = "
			                #blog-area {
							    padding: 20px;
							    font: " . $content_font_size . "  " . $font_family . ";
							}
							#blog-area .blog_timeline > li .blog_timeline_label h2,
							#blog-area .blog_timeline .comment-respond h3 {
							    background: #F6C542;
							    margin: 0 0 0.4em;
							    padding: 0.3em 0.6em;
							    overflow: hidden;    
							    line-height:32px;
							    text-transform: uppercase;
							    font-size: " . $heading_font_size .";
							}
							#blog-area .blog_timeline > li .blog_timeline_label h2 a {
							    line-height:23px;
							    color:" . $heading_font_color . ";
							}
							#blog-area .blog_timeline > li .blog_timeline_label h2 a:hover {
							    color:" . $heading_font_hover_color . ";
							}
							#blog-area .blog_timeline > li .blog_timeline_label p {
							    color:" . $content_font_color . " ;
							    font-size: " . $content_font_size . " ;
							    padding: 0 1em 1em;
							    margin: 0;
							    line-height: 19px;
							}
							#blog-area .blog_timeline .comment-list .reply a {
							    font-size : " . $content_font_size . " ;
							    color:#fff;
							}
							#blog-area .blog_timeline .comment-respond label {
							    color:" . $content_font_color . " ;
							    font: " . $content_font_size . "  " . $font_family . " ;
							    text-transform: none;
							}
							#blog-area .blog_timeline .comment-respond input,
							#blog-area .blog_timeline .comment-respond textarea {
							    padding: 5px 10px;
							    color:" . $content_font_color . " ;
							    font: " . $content_font_size . "  " . $font_family . " ;
							    border-radius: 5px;
							    -o-border-radius: 5px;
							    -ms-border-radius: 5px;
							    -moz-border-radius: 5px;
							    -webkit-border-radius: 5px;
							    resize: none;
							}
							/************************ first blog *************************/

							#blog-area .blog_timeline li:nth-child( 3n+1 ):before{
							    background: " . $first_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_timeline_label{
							    border: 1px solid " . $first_border_color . " ;
							    background: " . $first_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_timeline_label h2,
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .comment-respond h3 { 
							    background:" . $first_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_timeline_time, 
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_author,
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_comments{
							    box-shadow:0 0 0 8px " . $first_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_timeline_label a.more-link{
							    background: " . $first_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .blog_timeline_content blockquote {
							    border-left: 4px solid " . $first_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .comment-list .reply a {
							    background: " . $first_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .comment-respond input,
							#blog-area .blog_timeline li:nth-child( 3n+1 ) .comment-respond textarea {
							    border: 1px solid  " . $first_border_color . " ;
							}

							/************************ second blog *************************/

							#blog-area .blog_timeline li:nth-child( 3n+2 ):before{
							    background: " . $second_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_timeline_label{
							    border: 1px solid " . $second_border_color . " ;
							    background: " . $second_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_timeline_label h2,
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .comment-respond h3 {
							    background: " . $second_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_timeline_time, 
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_author, 
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_comments{
							    box-shadow:0 0 0 8px " . $second_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_timeline_label a.more-link{
							    background: " . $second_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .blog_timeline_content blockquote {
							    border-left: 4px solid " . $second_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .comment-list .reply a {
							    background: " . $second_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .comment-respond input,
							#blog-area .blog_timeline li:nth-child( 3n+2 ) .comment-respond textarea {
							    border: 1px solid  " . $second_border_color . " ;
							}

							/************************ third blog *************************/

							#blog-area .blog_timeline li:nth-child( 3n+3 ):before{
							    background: " . $third_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_timeline_label{
							    border: 1px solid " . $third_border_color . " ;
							    background: " . $third_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_timeline_label h2,
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .comment-respond h3{
							    background: " . $third_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_timeline_time, 
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_author, 
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_comments{
							    box-shadow:0 0 0 8px " . $third_bg_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_timeline_label a.more-link{
							    background: " . $third_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .blog_timeline_content blockquote {
							    border-left: 4px solid " . $third_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .comment-list .reply a {
							    background: " . $third_border_color . " ;
							}
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .comment-respond input,
							#blog-area .blog_timeline li:nth-child( 3n+3 ) .comment-respond textarea {
							    border: 1px solid  " . $third_border_color . " ;
							}";


	        wp_enqueue_style( 'frontend-fonts', 'http://fonts.googleapis.com/css?family=Oswald|Open+Sans|Fontdiner+Swanky|Crafty+Girls|Pacifico|Satisfy|Gloria+Hallelujah|Bangers|Audiowide|Sacramento' );              
	        wp_enqueue_style( 'frontend-style', plugins_url( 'css/frontend-style.css', __FILE__ ) );
	        wp_add_inline_style( 'frontend-style', $custom_css );  
        }      
	}

	/**
	* timeline_page_template
	* @since 1.0
	*/
	 
	public function timeline_page_template( $page_template ) {

		if ( is_home() || is_category() || is_tag() ) {
			if( file_exists( dirname( __FILE__ ) . '/templates/timeline-page-template.php' ) )
				$page_template = dirname( __FILE__ ) . '/templates/timeline-page-template.php';
		} else if ( is_single() ){
			if( file_exists( dirname( __FILE__ ) . '/templates/single-timeline-template.php' ) )
				$page_template = dirname( __FILE__ ) . '/templates/single-timeline-template.php';
		}

		return $page_template;
	}

	/**
	* timeline_excerpt_more
	* @since 1.0
	*/
	 
	public function timeline_excerpt_more ( $more ) {
    	return ' &hellip;' . self::continue_reading_link();
	}

	public function continue_reading_link() {
		return ' <a href="' . esc_url( get_permalink() ) . '" class="readmore">' . __( 'Read more', 'timeline' ) . '</a>';
	}
}

endif;

/**
 * Returns the main instance of timelineBlog to prevent the need to use globals.
 *
 * @since  1.0
 * @return timelineBlog
 */
 
return new timelineBlog;