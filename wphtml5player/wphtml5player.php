<?php
/*
Plugin Name: HTML5 Multimedia Framework
Plugin URI: http://code.google.com/p/html5videoplayer/
Description: A Highly Customisable HTML5 Multimedia Framework for Wordpress
Version: 2.1.0
Author: Christopher John Jackson
Author URI: http://cj-jackson.com/
License: New BSD License (GPLv2 and v3 Compatible)

    Copyright (c) 2010, Christopher John Jackson
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification,
    are permitted provided that the following conditions are met:

     * Redistributions of source code must retain the above copyright notice,
     this list of conditions and the following disclaimer.
     * Redistributions in binary form must reproduce the above copyright notice,
     this list of conditions and the following disclaimer in the documentation and/or
     other materials provided with the distribution.
     * Neither the name of the cj-jackson.com nor the names of its contributors may
     be used to endorse or promote products derived from this software without specific
     prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
    AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
    IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
    INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
    NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
    OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
    WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
    OF SUCH DAMAGE.
*/

if(!function_exists("json_encode") || !function_exists("json_decode")) {
    deactivate_plugins(basename(__FILE__));
    wp_die("PHP function 'json_decode' and 'json_encode' must exists before this plugin will run.");
}

$wphtml5playerclass;

add_action('init', 'wphtml5player_call');
add_action('atom_head', 'wphtml5player_XML');
add_action('rss_head', 'wphtml5player_XML');
add_action('rss2_head', 'wphtml5player_XML');
add_action('rdf_header', 'wphtml5player_XML');
add_filter('the_content', 'wphtml5player_parse');
add_filter('the_excerpt', 'wphtml5player_excerpt');
add_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);

function wphtml5player_call() {
    global $wphtml5playerclass;
    $scriptRoot = WP_PLUGIN_DIR."/wphtml5player";
    $scriptUrl = WP_PLUGIN_URL."/wphtml5player";
    require_once 'html5player.class.php';
    $wphtml5playerclass = new html5player($scriptUrl, get_bloginfo('url'), $scriptRoot);
    define("HTML5FRAMEWORK_ACTIVATED!", "hello!");
    wphtml5player_localise($scriptRoot);
    wphtml5player_setTag();
}

function wphtml5player_localise($scriptRoot) {
    global $wphtml5playerclass;

    $domain = "wphtml5player";
    load_plugin_textdomain($domain, null, $scriptRoot."/lang");

    $lang = array(
            'noVideo' => __("No video playback capabilities, please download the video below", $domain),
            'noAudio' => __("No audio playback capabilities, please download the audio below", $domain),
            'downloadVideo' => __('<strong>Download Video:</strong>', $domain),
            'downloadAudio' => __('<strong>Download Audio:</strong>', $domain),
            'closedFormat' => __('Closed Format:', $domain),
            'openFormat' => __('Open Format:', $domain)
    );

    foreach($lang as $param => $value) {
        $wphtml5playerclass->setLanguage($param, $value);
    }
}

function wphtml5player_oembed($html, $url) {
    global $wphtml5playerclass;
    return $wphtml5playerclass->oembedFilter($html, $url);
}

function wphtml5player_parse($content) {
    global $wphtml5playerclass;
    $video = $wphtml5playerclass->getTag("video");
    $audio = $wphtml5playerclass->getTag("audio");
    $flowplayer = $wphtml5playerclass->getTag("flowplayer");
    $oembed = $wphtml5playerclass->getTag("oembed");
    $content = preg_replace("#<p(.*?)>\[#i","[",$content);
    $content = preg_replace("#\]</p>#i","]",$content);
    remove_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);
    $content = preg_replace_callback("#\[".$oembed."\](.+?)\[/".$oembed."\]#is", array(&$wphtml5playerclass,"oEmbedJSON"), $content);
    $content = preg_replace_callback("#\[".$flowplayer."\](.+?)\[/".$flowplayer."\]#is", array(&$wphtml5playerclass,"flowPlayerJSON"), $content);
    $content = preg_replace_callback("#\[".$video."\](.+?)\[/".$video."\]#is", array(&$wphtml5playerclass,"videoreplaceJSON"), $content);
    $content = preg_replace_callback("#\[".$audio."\](.+?)\[/".$audio."\]#is", array(&$wphtml5playerclass,"audioreplaceJSON"), $content);
    $content = preg_replace_callback("#\[".$video.":(.+?)\]#i", array(&$wphtml5playerclass,"videoreplace"), $content);
    $content = preg_replace_callback("#\[".$audio.":(.+?)\]#i", array(&$wphtml5playerclass,"audioreplace"), $content);
    add_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);
    return $content;
}

function wphtml5player_excerpt($content) {
    global $wphtml5playerclass;
    $content = preg_replace("#\[video:(.+?)\]#i", "", $content);
    $content = preg_replace("#\[audio:(.+?)\]#i", "", $content);
    return $content;
}

function wphtml5player_VfE() {
    global $wphtml5playerclass;
    $wphtml5playerclass->setOption("beforeVideo", '<!-- Video for Everybody, Kroc Camen of Camen Design -->');
    $wphtml5playerclass->setOption("afterVideo", '');
    $wphtml5playerclass->setOption("videoLinkOutside", true);
}
add_action("html5player_videoForEverybody", "wphtml5player_VfE",
        10,0);

function wphtml5player_XML() {
    global $wphtml5playerclass;
    $wphtml5playerclass->setOption("xmlMode", true);
    wphtml5player_VfE();
}

function wphtml5player_setOptions($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if($wphtml5playerclass->is_assoc($json)) {
        unset($json['xmlMode']);
        foreach($json as $key => $value) {
            $wphtml5playerclass->setOption($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError()." @ Options";
    }
}
add_action("html5player_options", "wphtml5player_setOptions",
        10,1);

function wphtml5player_setFlowPlayerOptions($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if($wphtml5playerclass->is_assoc($json)) {
        unset($json["flashIsSetup"]);
        foreach($json as $key => $value) {
            $wphtml5playerclass->setFlowPlayerOption($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError()." @ FlowPlayer Options";
    }
}
add_action("html5player_flowplayer_options", "wphtml5player_setFlowPlayerOptions",
        10,1);

function wphtml5player_setFlowPlayerConfig($json) {
    global $wphtml5playerclass;
    $wphtml5playerclass->setFlowplayerConfig($json);
}
add_action("html5player_flowplayer_config", "wphtml5player_setFlowPlayerConfig",
        10,1);

function wphtml5player_setVideoAttribute($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if($wphtml5playerclass->is_assoc($json)) {
        foreach($json as $key => $value) {
            $wphtml5playerclass->setVideoAttribute($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError()." @ Video Attribute";
    }
}
add_action("html5player_video_attribute", "wphtml5player_setVideoAttribute",
        10,1);

function wphtml5player_setAudioAttribute($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if($wphtml5playerclass->is_assoc($json)) {
        foreach($json as $key => $value) {
            $wphtml5playerclass->setAudioAttribute($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError()." @ Audio Attribute";
    }
}
add_action("html5player_audio_attribute", "wphtml5player_setAudioAttribute",
        10,1);

function wphtml5player_setObjectAttribute($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if($wphtml5playerclass->is_assoc($json)) {
        foreach($json as $key => $value) {
            $wphtml5playerclass->setUserObjectAttribute($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError()." @ Attribute";
    }
}
add_action("html5player_ombed_object_attribute", "wphtml5player_setObjectAttribute",
        10,1);

function wphtml5player_setObjectParameter($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if($wphtml5playerclass->is_assoc($json)) {
        foreach($json as $key => $value) {
            $wphtml5playerclass->setUserObjectParameter($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError()." @ Param";
    }
}
add_action("html5player_oembed_object_param", "wphtml5player_setObjectParameter",
        10,1);

function wphtml5player_setTag() {
    global $wphtml5playerclass;
    if(defined('WPHTML5_VIDEO_TAG')) {
        $wphtml5playerclass->setTag("video", WPHTML5_VIDEO_TAG);
    }
    if(defined('WPHTML5_AUDIO_TAG')) {
        $wphtml5playerclass->setTag("audio", WPHTML5_AUDIO_TAG);
    }
    if(defined('WPHTML5_FLOWPLAYER_TAG')) {
        $wphtml5playerclass->setTag("flowplayer", WPHTML5_FLOWPLAYER_TAG);
    }
    if(defined('WPHTML5_OEMBED_TAG')) {
        $wphtml5playerclass->setTag("oembed", WPHTML5_OEMBED_TAG);
    }
}

?>