<?php
/**
 * GrooveShark shortcode
 */
class Grooveshark_Shortcode {
	static $add_js;
 
	/**
	 * Shortcode initialization, register shorcode and scripts
	 */
	static function init() {
		add_shortcode('grooveshark', array(__CLASS__, 'shortcode'));
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}

	/**
	 * The shortcode handler
	 */
	static function shortcode( $atts, $content = null ) {

		extract( shortcode_atts(
			array(
				'swfobject' => 0,
				'autoplay'  => 0,
				'random'    => 0,
				'width'     => 250,
				'height'    => 40,
				'type'      => 'song',
				'id'        => '29214064',
				'style'     => 'metal',
				'skin'      => 'bbg=000000&bth=000000&pfg=000000&lfg=000000&bt=FFFFFF&pbg=FFFFFF&pfgh=FFFFFF&si=FFFFFF&lbg=FFFFFF&lfgh=FFFFFF&sb=FFFFFF&bfg=666666&pbgh=666666&lbgh=666666&sbh=666666'
			),
			$atts
		) );

		if ( ! isset($atts['height'] ) && $type != 'song' ) $height = 250;

		self::$add_js = $swfobject;
 
		if ( $type === 'song' ) {
			$name   = 'gsSong' . $id;
			$widget = 'songWidget.swf';
			$gstype = 'songIDs';
			$theme  = 'style=' . $style;
		} elseif ( $type === 'songlist' ) {
			$name   = 'asManySongs';
			$widget = 'widget.swf';
			$gstype = 'songIDs';
			$theme  = $skin;
			$id     = preg_split( "/[\s,]+/", $id );
			if ( $random ) shuffle( $id );
			$id     = implode( ",", $id );
		} else {
			$name   = 'gsPlaylist' . $id;
			$widget = 'widget.swf';
			$gstype = 'playlistID';
			$theme  = $skin;
		}


		// I thought I read that texturizer filter was not applied to shortcodes
		// but it is. So we revert &amp; which code is #038; to & to use in parse_str 
		parse_str( str_replace("#038;", "&", $skin) );

		// It is strange someone wants to add same playlist or song to same page.
		// But to even prevent that case we add uniqueid to the name identifier.
		$name .= '_' . md5( uniqid() );

		$output = <<<WPGROOVESHARK
		<object width="{$width}" height="{$height}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="{$name}" name="{$name}"><param name="movie" value="http://grooveshark.com/{$widget}" /><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="flashvars" value="hostname=cowbell.grooveshark.com&{$gstype}={$id}&{$theme}&p={$autoplay}" /><!--[if !IE]>--><object type="application/x-shockwave-flash" data="http://grooveshark.com/{$widget}" width="{$width}" height="{$height}"><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="flashvars" value="hostname=cowbell.grooveshark.com&{$gstype}={$id}&{$theme}&p={$autoplay}" />{$content}</object><!--<![endif]--></object>
WPGROOVESHARK;
		if ( $swfobject ) {
			$output = <<<WPGROOVESHARK
<div id="{$name}">
	{$content}
</div>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function() {

	var flashvars = {
		hostname: "cowbell.grooveshark.com",
		{$gstype}: "{$id}",

WPGROOVESHARK;

			if ( $type === 'song' ) {
				$output .= <<<WPGROOVESHARK
		style: "{$style}",
WPGROOVESHARK;
			} else {
				$output .= <<<WPGROOVESHARK
		bbg: "{$bbg}",
		bth: "{$bth}",
		pfg: "{$pfg}",
		lfg: "{$lfg}",
		bt: "{$bt}",
		pbg: "{$pbg}",
		pfgh: "{$pfgh}",
		si: "{$si}",
		lbg: "{$lbg}",
		lfgh: "{$lfgh}",
		sb: "{$sb}",
		bfg: "{$bfg}",
		pbgh: "{$pbgh}",
		lbgh: "{$lbgh}",
		sbh: "{$sbh}",
WPGROOVESHARK;
			}
			$output .= <<<WPGROOVESHARK

		p: {$autoplay}
	};
	var params = {
	  	wmode: "opaque",
		allowScriptAccess: "always"
	};
	var attributes = {
		id: "{$name}",
		name: "{$name}"
	};

	swfobject.embedSWF("http://grooveshark.com/{$widget}", "{$name}", "{$width}", "{$height}", "9.0.0", "expressInstall.swf", flashvars, params, attributes);
	});
/* ]]> */
</script>
WPGROOVESHARK;
		}

		return $output;
	}

	/**
	 * Register javascript with its dependencies
	 */
	static function register_script() {
		wp_register_script('embed_grooveshark', plugins_url( 'js/embed_grooveshark.js' , __FILE__ ), array('swfobject', 'jquery'), '0.0.1', true);
	}

	/**
	 * Print javascript code, included dependencies if needed
	 * Only when the shortcode is used
	 */
	static function print_script() {
		// Test if shortcode is being used.
		if ( ! self::$add_js )
			return;
 
		wp_print_scripts('embed_grooveshark');
	}
}
 
Grooveshark_Shortcode::init();

?>