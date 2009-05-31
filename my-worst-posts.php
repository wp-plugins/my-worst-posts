<?php


/*  Copyright 2009  Waseem Senjer  (email : waseem.senjer@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/*
Plugin Name: My Worst Posts
Plugin URI: http://www.shamekh.ws
Description: a Widget Show the Worst Posts in your blog - the less comments - , because may be some one get interested and decide to comment :)
Author: Waseem Senjer وسيم سنجر
Version: 1.0 
Author URI: http://www.shamekh.ws
*/




function worst_posts($no_posts = 5, $before = '<li>', $after = '</li>', $show_pass_post = false, $duration='') {
    global $wpdb;
	
	$worst_posts = wp_cache_get('worst_posts');
	if ($worst_posts === false) {
		$request = "SELECT ID, post_title, comment_count FROM $wpdb->posts";
		$request .= " WHERE post_status = 'publish'";
		if (!$show_pass_post) $request .= " AND post_password =''";
	
		if ($duration !="") $request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
	
		$request .= " ORDER BY comment_count ASC LIMIT $no_posts";
		$posts = $wpdb->get_results($request);

		if ($posts) {
			foreach ($posts as $post) {
				$post_title = htmlspecialchars($post->post_title);
				$comment_count = $post->comment_count;
				$permalink = get_permalink($post->ID);
				$worst_posts .= $before . '<a href="' . $permalink . '" title="' . $post_title.'">' . $post_title . '</a> (' . $comment_count.')' . $after;
			}
		} else {
			$worst_posts .= $before . "None found" . $after;
		}
	
		wp_cache_set('worst_posts', $worst_posts);
	} 

    echo $worst_posts;
}





////the widget function

function widget_worst_posts($args) {
  extract($args);
  $defaults = array('title'=>'The Worst Posts' );
  $options = (array) get_option('widget_worst_posts');
  
  
  echo $before_widget;
  echo $before_title;
 
  if ($options['title']!="") {
  echo $options['title'];
  } else { echo $defaults['title']; }
  
  echo $after_title;
  worst_posts(5,'<li>','</li>',false,'');
  echo $after_widget;
}
/////////////////////////////////////////////////



//////////////////////////////////////////////////
function worst_posts_init()
{
  register_sidebar_widget(__('My Worst Posts'), 'widget_worst_posts'); 
  register_widget_control('My Worst Posts', 'worst_posts_control');  
}
add_action("plugins_loaded", "worst_posts_init");

////////////////////////////////////////////////////

//////////////////////////////////////////////////////
// CONTROL
function worst_posts_control () {
		$options = $newoptions = get_option('widget_worst_posts');
		if ( $_POST['worst-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['worst-title']));
			
			
			
			
		}
		// if the options are new , swap between the old and the new options .
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_worst_posts', $options);
		}
?>
				<p style="text-align: right">
					<label for="worst-title" ><?php _e('Widget Name:', 'widgets'); ?> <input type="text" id="worst-title" name="worst-title" value="<?php echo $options['title']; ?>" /></label>
				</p>
				
				<p style="text-align: right">
					
					<input type="hidden" name="worst-submit" id="feeds-submit" value="1" />
				</p>				
<?php
	}

?>