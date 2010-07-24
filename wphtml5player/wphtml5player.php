<?php
/*
Plugin Name: HTML5 Player for Wordpress
Plugin URI: http://code.google.com/p/html5videoplayer/
Description: Embed video using shortcodes, using flowplayer as fallback.
Version: 0.9.4
Author: Christopher John Jackson
Author URI: http://cj-jackson.com/
*/

/**
 * HTML5 Player for Wordpress 0.9.4
 * Embed video using shortcodes, using flowplayer as fallback.
 * Copyright (C) 2010, Christopher John Jackson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$wphtml5playerclass;

add_action('init', 'html5player_call');
add_filter('the_content', 'html5player_parse');
add_filter('the_excerpt', 'html5player_excerpt');

function html5player_call() {
    global $wphtml5playerclass;
    $scriptRoot = WP_PLUGIN_DIR."/wphtml5player";
    $scriptUrl = WP_PLUGIN_URL."/wphtml5player";
    require_once 'html5player.class.php';
    $wphtml5playerclass = new html5player($scriptUrl, get_bloginfo('url'), $scriptRoot);
    html5player_localise($scriptRoot);
}

function html5player_enableSWFObject() {
    global $wphtml5playerclass;
    wp_enqueue_script('swfobject');
    $wphtml5playerclass->setSWFObject(true);
}

function html5player_localise($scriptRoot) {
    global $wphtml5playerclass;
    $lang = '';
    if(WPLANG == '') {
        $lang = 'default';
    } else {
        $lang = WPLANG;
    }

    $lang = str_replace("_", "-", $lang);

    if(file_exists($scriptRoot.'/lang/'.$lang.'.php')) {
        include_once $scriptRoot.'/lang/'.$lang.'.php';
    } else {
        include_once $scriptRoot.'/lang/default.php';
    }

    $lang = wphtml5lang();

    foreach($lang as $param => $value) {
        $wphtml5playerclass->setLanguage($param, $value);
    }
}

function html5player_parse($content) {
    global $wphtml5playerclass;
    $content = preg_replace("#\[video:(.+?) demo\]#i", "&#91;video:$1&#93;", $content);
    $content = preg_replace("#\[audio:(.+?) demo\]#i", "&#91;audio:$1&#93;", $content);
    $content = preg_replace_callback("#\[video:(.+?)\]#i", array(&$wphtml5playerclass,"videoreplace"), $content);
    $content = preg_replace_callback("#\[audio:(.+?)\]#i", array(&$wphtml5playerclass,"audioreplace"), $content);
    $content = preg_replace("#\<p><div class=\"video-js-box\">#i","<div class=\"video-js-box\">",$content);
    $content = preg_replace("#\</div></p>#i","</div>",$content);
    return $content;
}

function html5player_excerpt($content) {
    global $wphtml5playerclass;
    $content = preg_replace("#\[video:(.+?)\]#i", "", $content);
    $content = preg_replace("#\[audio:(.+?)\]#i", "", $content);
    return $content;
}

function html5player_videoParam($param) {
    global $wphtml5playerclass;
    $wphtml5playerclass->setOption("videoParam", $param);
}

function html5player_audioParam($param) {
    global $wphtml5playerclass;
    $wphtml5playerclass->setOption("audioParam", $param);
}

?>
