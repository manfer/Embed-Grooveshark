<?php
/*
Plugin Name: Embed Grooveshark
Plugin URI: http://www.manfersite.tk/embed-grooveshark
Description: Add grooveshark songs or playlists to your posts.
Version: 0.3
Author: Fernando San Julián
Email: manfer.site@gmail.com
Author URI: http://www.manfersite.tk
Text Domain: embed_grooveshark
Domain Path: /languages/
License: GPL2
*/

/*  Copyright 2011 Fernando San Julián (email: manfer.site@gmail.com)
    
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301
*/

// Uncomment the following two lines for debug purpose.
//error_reporting( E_ALL | E_STRICT );
//ini_set( 'display_errors', 1 );

require_once('includes/GroovesharkShortcode.class.php');

require_once('includes/GroovesharkSongWidget.class.php');
require_once('includes/GroovesharkPlaylistWidget.class.php');
require_once('includes/GroovesharkSongListWidget.class.php');
load_plugin_textdomain('embed_grooveshark', false, basename( dirname( __FILE__ ) ) . '/languages' );

?>