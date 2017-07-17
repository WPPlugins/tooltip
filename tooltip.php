<?php
/*
Plugin Name: Tooltip
Plugin URI: http://nutomic.bplaced.net
Description: A plugin that displays preview tooltips for all external links on your site.
Version: 1.0
Author: Felix Ableitner
Author URI: http://nutomic.bplaced.net
License: GPL2

    Copyright 2010  Felix Ableitner  (email : felix.ableitner@web.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

function add_tooltips_all ( $post_content ) {

	$done         = 0;
	$offset       = 0;
	//replace
	while ( ! $done ) {
		//grab all links
		$url     = '';
		$a_start = strpos ( $post_content, "<a", $offset );
		if ( $a_start !== false ) {
			$url_start  = strpos ( $post_content, 'href="', $a_start ) + 6;
				
			if ( $url_start !== false ) {
				$url_length = strpos ( $post_content, '"', $url_start ) - $url_start;
				$url 	    = substr ( $post_content, $url_start, $url_length );
				$offset = $url_start + $url_length + 1;
				if ( ! ( strpos ( $post_content, "onmouseover=" . '"drawpreview(' . "'", $offset ) > $url_start ) ) {
					//grabbing the url till here
					if ( ( ! get_option('only_external') or substr ( $url, 0, strlen ( get_option('home') ) ) != get_option ( 'home' ) ) and substr ( $url, 0, 7  ) == 'http://' ) { 
					//only continue if tooltips should be displayed for all links, or the link leads to an external site
						$url = substr ( $url, 7 ); 
						$url_filename = str_replace ( '/', '-', $url ); 
						if ( ! is_file ( "../wp-content/plugins/tooltip/images/$url_filename.png" ) ) { 
							//create image if it doesnt exist
							$image = imagecreatefrompng  ( "http://s.wordpress.com/mshots/v1/http%3A%2F%2F" . $url  );
							$newimage = imagecreatetruecolor  ( get_option('tooltip_w'), get_option('tooltip_h') );
							imagecopyresampled ( $newimage, $image, 0, 0, 0, 0, get_option('tooltip_w'), get_option('tooltip_h'), 1024, 768 );
							imagepng ( $newimage, "../wp-content/plugins/tooltip/images/$url_filename.png" );
						}
						$post_content = substr ( $post_content, 0, $url_start + $url_length + 1 ) . " onmouseover=" . '"drawpreview(' . "'" . plugins_url() . "/tooltip/images/$url.png')" . '" onmouseout="removepreview()" onclick="removepreview()"' . substr ( $post_content, $url_start + $url_length + 1 );
						//insert js calls
					}
				}
					
			}
			else $done = 1;
			//all links processed
				
		}
		else $done = 1;
		//all links processed
			
	}
	
	return $post_content;
		
}
		
function add_tooltips_post ( $post_id ) {

	if ( ( get_post_meta ( $post_id, "tooltip_timestamp", 1 )  ==  '' ) or ( get_post_meta ( $post_id, "tooltip_timestamp", 1 ) + 100  <  time ( ) ) ) {
		//make sure we dont get an endless loop (this function updates the post, so we wait for some time before working on the same post again)
		
		$post         = get_post( $post_id, "ARRAY_A" );
		
		$newpost = array();
		$newpost['ID'] = $post_id;
		$newpost['post_content'] = add_tooltips_all ( $post["post_content"] );
		if ( get_post_meta ( $post_id, "tooltip_timestamp", 1 ) == '' ) add_post_meta ( $post_id, "tooltip_timestamp", time ( ) );
		else update_post_meta ( $post_id, "tooltip_timestamp", time ( ) );
		//save the time we edited the post
		wp_update_post ( $newpost );
		//save processed post
		
	}

}

function add_tooltips_page ( $post_id ) {

	if ( ( get_post_meta ( $post_id, "tooltip_timestamp", 1 )  ==  '' ) or ( get_post_meta ( $post_id, "tooltip_timestamp", 1 ) + 100  <  time ( ) ) ) {
		//make sure we dont get an endless loop (this function updates the post, so we wait for some time before working on the same post again)
		
		$post         = get_page( $post_id, "ARRAY_A" );
		
		$newpost = array();
		$newpost['ID'] = $post_id;
		$newpost['post_content'] = add_tooltips_all ( $post["post_content"] );
		if ( get_post_meta ( $post_id, "tooltip_timestamp", 1 ) == '' ) add_post_meta ( $post_id, "tooltip_timestamp", time ( ) );
		else update_post_meta ( $post_id, "tooltip_timestamp", time ( ) );
		//save the time we edited the post
		wp_update_post ( $newpost );
		//save processed post
		
	}

}

add_action ( "publish_post" , "add_tooltips_post" );
add_action ( "publish_page" , "add_tooltips_page" );

function echo_script ( ) {
?>
<script type="text/javascript">
<!--
	function drawpreview ( file ) {
		//generate the tooltip div + image
		var tooltip = document.createElement('div');
		tooltip.setAttribute ( 'id', "tooltip" );
		tooltip.style.display = "block";
		tooltip.style.position = "absolute";
		tooltip.innerHTML = "<img src='" + file + "' alt=''>";
		var body = document.getElementsByTagName("body")[0];
		body.appendChild(tooltip);
	}

	document.onmousemove = updateposition;
 
	function updateposition ( e ) {
		tooltip = document.getElementById( "tooltip" );
		if (tooltip != null && tooltip.style.display == 'block') {
			x = (e.pageX ? e.pageX : window.event.x) + tooltip.offsetParent.scrollLeft - tooltip.offsetParent.offsetLeft;
			y = (e.pageY ? e.pageY : window.event.y) + tooltip.offsetParent.scrollTop - tooltip.offsetParent.offsetTop;
			tooltip.style.left = (x + <?php echo get_option('position_y'); ?>) + "px";
			tooltip.style.top   = (y + <?php echo get_option('position_x'); ?>) + "px";
		}
	}

	function removepreview ( ) {
		//delete div + image when cursor leaves the link
		tooltip = document.getElementById( "tooltip" );
		tooltip.parentNode.removeChild(tooltip);
	}
//-->
</script>
<?php
	if ( get_option('style') != '' ) {
	//echo custom style if set
		?>
		<style type="text/css">
			#tooltip {
			<?php echo get_option('style'); ?>
			}
		</style>
		<?php
	}
}

add_action ( "wp_head" , "echo_script" );

//SETTINGS

add_action('admin_init', 'tooltip_options_init' );
add_action('admin_menu', 'tooltip_options_add_page');
//adding options pages

function tooltip_options_init(){
	register_setting( 'tooltip_options','only_external', 'intval' );
	register_setting( 'tooltip_options','tooltip_w', 'intval' );
	register_setting( 'tooltip_options','tooltip_h', 'tooltip_set_h' );
	register_setting( 'tooltip_options','position_x', 'intval' );
	register_setting( 'tooltip_options','position_y', 'intval' );
	register_setting( 'tooltip_options','style', 'wp_filter_nohtml_kses' );
}

function tooltip_set_h ($input) {
	if ( $input == '' ) return ( get_option('tooltip_w') ) * 0.75;
	//autoset image height in the right ratio
	else return $input;
}

// add menu page
function tooltip_options_add_page() {
	add_options_page('Tooltip Options', 'Tooltip', 'manage_options', 'tooltip_options', 'tooltip_options_echo_page');
}

// draw the menu page
function tooltip_options_echo_page() {
	?>
	<div class="wrap">
		<h2>Tooltip Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('tooltip_options'); ?>
			<table class="form-table">
				
				<tr valign="top"><th scope="row">Show tooltips for external links only</th>
					<td><input name="only_external" type="checkbox" value="1" <?php checked('1', get_option('only_external')); ?> /></td>
				</tr>
				
				<tr valign="top"><th scope="row">Tooltip image width:</th>
					<td><input type="text" name="tooltip_w" value="<?php echo get_option('tooltip_w'); ?>" /> default: 200</td>
				</tr>
				
				<tr valign="top"><th scope="row">Tooltip image height: <br>(Leave empty for default ratio)</th>
					<td><input type="text" name="tooltip_h" value="<?php echo get_option('tooltip_h'); ?>" /> default: 150</td>
				</tr>
				
				<tr valign="top"><th scope="row">Tooltip position x: </th>
					<td><input type="text" name="position_x" value="<?php echo get_option('position_x'); ?>" /> default: 25</td>
				</tr>
				
				<tr valign="top"><th scope="row">Tooltip position y: </th>
					<td><input type="text" name="position_y" value="<?php echo get_option('position_y'); ?>" /> default: -100</td>
				</tr>	

				<tr valign="top"><th scope="row">Custom CSS style: </th>
					<td><textarea name="style"><?php echo get_option('style'); ?></textarea></td>
				</tr>
				
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}


?>