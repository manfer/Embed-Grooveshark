<?php
/**
 * Grooveshark playlist widget
 *
 * @since 3.2.1
 */
class Grooveshark_Playlist_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' => 'grooveshark_playlist_widget',
			'description' => __( 'Add a grooveshark playlist player.', 'embed_grooveshark' )
		);
		parent::__construct( 'grooveshark-playlist', __( 'Grooveshark Playlist', 'embed_grooveshark' ), $widget_ops );
		$this->alt_option_name = 'grooveshark_playlist_widget';

		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	/**
	 * Widget output
	 */
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'grooveshark_playlist_widget', 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ]) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Grooveshark Playlist', 'embed_grooveshark' ) : $instance['title'], $instance, $this->id_base);
		$useSWFObject = $instance['useSWFObject'] ? 'swfobject="1" ' : '';
		if ( ! $width = absint( $instance['width'] ) ) $width = 250;
		if ( ! $height = absint( $instance['height'] ) ) $height = 250;
		$id = $instance['id'] ? $instance['id'] : '63783735';
		$skin = $instance['skin'] ? $instance['skin'] : '';
		$alternative = apply_filters( 'widget_text', $instance['alternative'], $instance );
		$alternative = $alternative ? $alternative : $title;
		$autoplay = $instance['autoplay'] ? '1' : '0';

		$output = '[grooveshark ';
		$output .= $useSWFObject;
		$output .= 'width="' . $width . '" ';
		$output .= 'height="' . $height . '" ';
		$output .= 'type="playlist" ';
		$output .= 'id="' . $id . '" ';
		$output .= 'autoplay="' . $autoplay . '" ';
		$output .= ( $skin == "" ) ? '' : 'skin="' . $skin . '"]';
		$output .= $alternative;
		$output .= '[/grooveshark]';

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		echo do_shortcode( $output );
		echo $after_widget;
		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'grooveshark_playlist_widget', $cache, 'widget' );
	}

	/**
	 * Update widget options
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['useSWFObject'] = ! empty( $new_instance['useSWFObject'] ) ? 1 : 0;
		$instance['width'] = (int) $new_instance['width'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['id'] = strip_tags( $new_instance['id'] );
		$instance['skin'] = strip_tags( $new_instance['skin'] );
		if ( current_user_can( 'unfiltered_html' ) )
			$instance['alternative'] =  $new_instance['alternative'];
		else
			$instance['alternative'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['alternative'] ) ) ); // wp_filter_post_kses() expects slashed
		$instance['autoplay'] = ! empty( $new_instance['autoplay'] ) ? 1 : 0;
		$instance['filter'] = ! empty( $new_instance['filter'] ) ? 1 : 0;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['grooveshark_playlist_widget'] ) )
			delete_option( 'grooveshark_playlist_widget' );

		return $instance;
	}

	/**
	 * Flush cache
	 */
	function flush_widget_cache() {
		wp_cache_delete( 'grooveshark_playlist_widget', 'widget' );
	}

	/**
	 * Widget options form
	 */
	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'width' => 250,
			'height' => 250,
			'id' => '',
			'skin' => '',
			'alternative' => ''
		) );

		$title = esc_attr( $instance['title'] );
		$width = absint( $instance['width'] );
		$height = absint( $instance['height'] );
		$id = strip_tags( $instance['id'] );
		$skin = esc_textarea(  $instance['skin'] );
		$alternative = esc_textarea( $instance['alternative'] );

?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><input id="<?php echo $this->get_field_id( 'useSWFObject' ) ?>" name="<?php echo $this->get_field_name( 'useSWFObject' ); ?>" type="checkbox" <?php checked( isset( $instance['useSWFObject'] ) ? (bool) $instance['useSWFObject'] : false ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'useSWFObject' ); ?>"><?php _e( 'use SWFObject for output', 'embed_grooveshark' ) ?></label></p>

		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'embed_grooveshark' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $width; ?>" size="3" />
		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'embed_grooveshark' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $height; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Playlist ID:', 'embed_grooveshark' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" type="text" value="<?php echo $id; ?>" /></p>

	    <p><input id="<?php echo $this->get_field_id( 'autoplay' ) ?>" name="<?php echo $this->get_field_name( 'autoplay' ); ?>" type="checkbox" <?php checked( isset( $instance['autoplay'] ) ? (bool) $instance['autoplay'] : false ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'autoplay' ); ?>"><?php _e( 'autoplay', 'embed_grooveshark' ) ?></label></p>

	    <p>
			<label for="<?php echo $this->get_field_id( 'skin' ); ?>"><?php _e( 'Skin:', 'embed_grooveshark' ); ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'skin' ); ?>" name="<?php echo $this->get_field_name( 'skin' ); ?>"><?php echo $skin; ?></textarea>
		</p>

	    <p>
			<label for="<?php echo $this->get_field_id( 'alternative' ); ?>"><?php _e( 'Alternative Content:', 'embed_grooveshark' ); ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'alternative' ); ?>" name="<?php echo $this->get_field_name( 'alternative' ); ?>"><?php echo $alternative; ?></textarea>
		</p>

		<p><input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" type="checkbox" <?php checked( isset( $instance['filter'] ) ? (bool) $instance['filter'] : false ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Automatically add paragraphs' ); ?></label></p>

<?php
	}
}

// register widget
add_action( 'widgets_init', create_function( '', 'register_widget( "Grooveshark_Playlist_Widget" );' ) );

?>