<?php
/*
Plugin Name: HTML5 Player for Wordpress
Plugin URI: http://cj-jackson.com
Description: Embed video using shortcodes, using flowplayer as fallback.
Version: 0.1.0
Author: Christopher John Jackson
Author URI: http://cj-jackson.com/
*/

/**
 * HTML5 Player for Wordpress 0.1.0
 * Embed video using shortcodes, using flowplayer as fallback.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$wphtml5playerclass;

add_action('init', 'html5player_call');
add_filter('the_content', 'html5player_parse');
add_filter('the_excerpt', 'html5player_excerpt');

function html5player_call() {
    global $wphtml5playerclass;
    $scriptRoot = ABSPATH."/wp-content/plugins/wphtml5player";
    $scriptUrl = get_settings('siteurl')."/wp-content/plugins/wphtml5player";
    require_once 'html5player.class.php';
    $wphtml5playerclass = new html5player($scriptUrl, $scriptUrl);
}

function html5player_parse($content) {
    global $wphtml5playerclass;
    $content = preg_replace_callback("#\[video:(.+?)\]#i", array(&$wphtml5playerclass,"videoreplace"), $content);
    $content = preg_replace_callback("#\[audio:(.+?)\]#i", array(&$wphtml5playerclass,"audioreplace"), $content);
    return $content;
}

function html5player_excerpt($content) {
    global $wphtml5playerclass;
    $content = preg_replace("#\[video:(.+?)\]#", "", $content);
    $content = preg_replace("#\[audio:(.+?)\]#", "", $content);
    return $content;
}

?>
