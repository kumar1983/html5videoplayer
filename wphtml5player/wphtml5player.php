<?php
/*
  Plugin Name: HTML5 Multimedia Framework
  Plugin URI: http://code.google.com/p/html5videoplayer/
  Description: A Highly Customisable HTML5 Multimedia Framework for Wordpress
  Version: 3.0.0
  Author: Christopher John Jackson
  Author URI: http://cj-jackson.com/
  License: MIT License

  Copyright (C) 2011 by Christopher John Jackson

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */

if (!function_exists("json_encode") || !function_exists("json_decode")) {
    deactivate_plugins(basename(__FILE__));
    wp_die("PHP function 'json_decode' and 'json_encode' must exists before this plugin will run.");
}

$wphtml5playerclass;
$scriptRoot = WP_PLUGIN_DIR . "/wphtml5player";
$scriptUrl = WP_PLUGIN_URL . "/wphtml5player";
require_once 'html5player.class.php';
$wphtml5playerclass = new html5player($scriptUrl, get_bloginfo('url'), $scriptRoot);

add_action('init', 'wphtml5player_call');
add_action('atom_head', 'wphtml5player_XML');
add_action('rss_head', 'wphtml5player_XML');
add_action('rss2_head', 'wphtml5player_XML');
add_action('rdf_header', 'wphtml5player_XML');
add_filter('embed_oembed_html', 'wphtml5player_oembed', null, 2);

function wphtml5player_call() {
    global $wphtml5playerclass, $scriptRoot;
    define("HTML5FRAMEWORK_ACTIVATED!", "hello!");
    wphtml5player_getAndSetAdminOptions();
    wphtml5player_setTag();
}

function wphtml5player_getAndSetAdminOptions() {
    global $wphtml5playerclass;
    if (get_option('html5framework_order') == '1') {
        define('USE_FLOWPLAYER_IN_WP_EMBED', 'true');
    }

    if (get_option('html5framework_flowplayer_location') != '') {
        $wphtml5playerclass->setFlowLocation(get_option('html5framework_flowplayer_location'));
    }

    if (get_option('html5framework_flowplayer_config') != '') {
        define('FLOWPLAYER_JSON', '{' . get_option('html5framework_flowplayer_config') . '}');
    }

    if (get_option('html5framework_flowplayer_videoClassName') != "") {
        $flowplayer['videoClassNameForTag'] = get_option('html5framework_flowplayer_videoClassName');
    }

    if (get_option('html5framework_flowplayer_audioClassName') != "") {
        $flowplayer['audioClassNameForTag'] = get_option('html5framework_flowplayer_audioClassName');
    }

    if (get_option('html5framework_flowplayer_videoEnable') != "true") {
        $flowplayer['videoFlowPlayerEnabled'] = false;
    }

    if (get_option('html5framework_flowplayer_audioEnable') != "true") {
        $flowplayer['audioFlowPlayerEnabled'] = false;
    }

    if (get_option('html5framework_flowplayer_rangeRequests') == "true") {
        define('FLOWPLAYER_RANGE_REQUESTS', true);
    }

    if (get_option('html5framework_html5_config') != '') {
        wphtml5player_setOptions('{'.get_option('html5framework_html5_config').'}');
    }

    if (get_option('html5framework_html5_videoAttribute') != '') {
        wphtml5player_setVideoAttribute('{'.get_option('html5framework_html5_videoAttribute').'}');
    }

    if (get_option('html5framework_html5_audioAttribute') != '') {
        wphtml5player_setAudioAttribute('{'.get_option('html5framework_html5_audioAttribute').'}');
    }

    if (isset($flowplayer)) {
        wphtml5player_setFlowPlayerOptions(json_encode($flowplayer));
    }
}

register_activation_hook(__FILE__, 'wphtml5player_activate');

function wphtml5player_activate() {
    // Set default
    add_option('html5framework_order', '0');
    add_option('html5framework_flowplayer_videoEnable', 'true');
    add_option('html5framework_flowplayer_audioEnable', 'true');
    add_option('html5framework_flowplayer_location', '');
    add_option('html5framework_flowplayer_config', '');
    add_option('html5framework_flowplayer_videoClassName', '');
    add_option('html5framework_flowplayer_audioClassName', '');
    add_option('html5framework_flowplayer_rangeRequests', '');
    add_option('html5framework_html5_config', '');
    add_option('html5framework_html5_videoAttribute', '');
    add_option('html5framework_html5_audioAttribute', '');
}

function wphtml5player_oembed($html, $url) {
    global $wphtml5playerclass;
    return $wphtml5playerclass->oembedFilter($html, $url);
}

if (!defined('EMBED_SHORTCODE_ONLY_MODE')) {
    add_filter('the_content', 'wphtml5player_parse');

    function wphtml5player_parse($content) {
        global $wphtml5playerclass;
        $video = $wphtml5playerclass->getTag("video");
        $audio = $wphtml5playerclass->getTag("audio");
        $flowplayer = $wphtml5playerclass->getTag("flowplayer");
        $oembed = $wphtml5playerclass->getTag("oembed");
        remove_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);
        $content = preg_replace_callback("#\[" . $oembed . "\](.+?)\[/" . $oembed . "\]#is", array(&$wphtml5playerclass, "oEmbedJSON"), $content);
        $content = preg_replace_callback("#\[" . $flowplayer . "\](.+?)\[/" . $flowplayer . "\]#is", array(&$wphtml5playerclass, "flowPlayerJSON"), $content);
        $content = preg_replace_callback("#\[" . $video . "\](.+?)\[/" . $video . "\]#is", array(&$wphtml5playerclass, "videoreplaceJSON"), $content);
        $content = preg_replace_callback("#\[" . $audio . "\](.+?)\[/" . $audio . "\]#is", array(&$wphtml5playerclass, "audioreplaceJSON"), $content);
        $content = preg_replace_callback("#\[" . $video . ":(.+?)\]#i", array(&$wphtml5playerclass, "videoreplace"), $content);
        $content = preg_replace_callback("#\[" . $audio . ":(.+?)\]#i", array(&$wphtml5playerclass, "audioreplace"), $content);
        add_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);
        return $content;
    }

}

function wphtml5player_VfE() {
    global $wphtml5playerclass;
    $wphtml5playerclass->setOption("beforeVideo", '<!-- Video for Everybody, Kroc Camen of Camen Design -->');
    $wphtml5playerclass->setOption("afterVideo", '');
    $wphtml5playerclass->setOption("videoLinkOutside", true);
}

add_action("html5player_videoForEverybody", "wphtml5player_VfE",
        10, 0);

function wphtml5player_XML() {
    global $wphtml5playerclass;
    $wphtml5playerclass->setOption("xmlMode", true);
    wphtml5player_VfE();
}

function wphtml5player_setOptions($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        unset($json['xmlMode']);
        foreach ($json as $key => $value) {
            $wphtml5playerclass->setOption($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError() . " @ Options";
    }
}

add_action("html5player_options", "wphtml5player_setOptions",
        10, 1);

function wphtml5player_setFlowPlayerOptions($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        unset($json["flashIsSetup"]);
        foreach ($json as $key => $value) {
            $wphtml5playerclass->setFlowPlayerOption($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError() . " @ FlowPlayer Options";
    }
}

add_action("html5player_flowplayer_options", "wphtml5player_setFlowPlayerOptions",
        10, 1);

function wphtml5player_setFlowPlayerConfig($json) {
    global $wphtml5playerclass;
    $wphtml5playerclass->setFlowplayerConfig($json);
}

add_action("html5player_flowplayer_config", "wphtml5player_setFlowPlayerConfig",
        10, 1);

function wphtml5player_setVideoAttribute($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        foreach ($json as $key => $value) {
            $wphtml5playerclass->setVideoAttribute($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError() . " @ Video Attribute";
    }
}

add_action("html5player_video_attribute", "wphtml5player_setVideoAttribute",
        10, 1);

function wphtml5player_setAudioAttribute($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        foreach ($json as $key => $value) {
            $wphtml5playerclass->setAudioAttribute($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError() . " @ Audio Attribute";
    }
}

add_action("html5player_audio_attribute", "wphtml5player_setAudioAttribute",
        10, 1);

function wphtml5player_setObjectAttribute($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        foreach ($json as $key => $value) {
            $wphtml5playerclass->setUserObjectAttribute($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError() . " @ Attribute";
    }
}

add_action("html5player_ombed_object_attribute", "wphtml5player_setObjectAttribute",
        10, 1);

function wphtml5player_setObjectParameter($json) {
    global $wphtml5playerclass;
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        foreach ($json as $key => $value) {
            $wphtml5playerclass->setUserObjectParameter($key, $value);
        }
    } else {
        echo $wphtml5playerclass->jsonError() . " @ Param";
    }
}

add_action("html5player_oembed_object_param", "wphtml5player_setObjectParameter",
        10, 1);

$wphtml_host = $_SERVER['HTTP_HOST'];

function wphtml5player_oembed_video_handler($matches, $attr, $url, $rawattr) {
    global $wphtml5playerclass, $wphtml_host;
    $json = $attr;
    if (defined("USE_FLOWPLAYER_IN_WP_EMBED") && preg_match("#^(.ext|.main|.high)(mp4|m4v)$#i", $matches[3] . $matches[5])) {
        // Do nothing
    } else {
        $json['url'] = array($url);
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".m4v") && !preg_match("#^(mp4|m4v)$#i", $matches[3] . $matches[5])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".m4v";
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".mp4") && !preg_match("#^(mp4|m4v)$#i", $matches[3] . $matches[5])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".mp4";
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".ogv") && !preg_match("#ogv#i", $matches[5])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".ogv";
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".webm") && !preg_match("#webm#i", $matches[5])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".webm";
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".jpg")) {
        $json['poster'] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".jpg";
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".jpeg")) {
        $json['poster'] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".jpeg";
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".gif")) {
        $json['poster'] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".gif";
    }

    if (defined("USE_FLOWPLAYER_IN_WP_EMBED")) {
        if (preg_match('#(ogv|webm)#i', $matches[5])) {
            $code = $wphtml5playerclass->videoreplaceJSON(null, $json);
        } else {
            $flow['video'] = $json;
            $flow['video']['htmlvideo']['url'] = $flow['video']['url'];
            $flow['video']['url'] = $url;
            if (preg_match("#^(.ext|.main|.high)(mp4|m4v)$#i", $matches[3] . $matches[5])) {
                $flow['video']['plugins']['controls']['fullscreen'] = true;
            }
            $code = $wphtml5playerclass->flowPlayerJSON(null, $flow);
        }
    } else {
        $code = $wphtml5playerclass->videoreplaceJSON(null, $json);
    }
    return $code;
}

wp_embed_register_handler("wphtml5video", "#(http://|https://)" . $wphtml_host . "/(.{1,}?)((.ext|.main|.high){0,1}).(mp4|m4v|ogv|webm)$#i", "wphtml5player_oembed_video_handler");

function wphtml5player_oembed_audio_handler($matches, $attr, $url, $rawattr) {
    global $wphtml5playerclass, $wphtml_host;
    $json = $attr;
    $json['url'] = array($url);

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".m4a") && !preg_match("#(aac|m4a)#i", $matches[3])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".m4a";
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".aac") && !preg_match("#(aac|m4a)#i", $matches[3])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".m4a";
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".ogg") && !preg_match("#(ogg|oga)#i", $matches[3])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".ogg";
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".oga") && !preg_match("#(ogg|oga)#i", $matches[3])) {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".oga";
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $matches[2] . ".mp3") && $matches[3] != "mp3") {
        $json['url'][] = $matches[1] . $wphtml_host . "/" . $matches[2] . ".mp3";
    }

    unset($json['width']);
    unset($json['height']);
    if (defined("USE_FLOWPLAYER_IN_WP_EMBED")) {
        if (preg_match('#(ogg|oga|wav)#i', $matches[3])) {
            $code = $wphtml5playerclass->audioreplaceJSON(null, $json);
        } else {
            $flow['audio'] = $json;
            $flow['audio']['htmlaudio']['url'] = $flow['audio']['url'];
            $flow['audio']['url'] = $url;
            $code = $wphtml5playerclass->flowPlayerJSON(null, $flow);
        }
    } else {
        $code = $wphtml5playerclass->audioreplaceJSON(null, $json);
    }
    return $code;
}

wp_embed_register_handler("wphtml5audio", "#(http://|https://)" . $wphtml_host . "/(.{1,}?).(m4a|aac|ogg|oga|mp3|wav)$#i", "wphtml5player_oembed_audio_handler");

function wphtml5player_oembed_json_handler($matches, $attr, $url, $rawattr) {
    global $wphtml5playerclass;
    $baseUrl = substr($url, 0, -strlen(strrchr($url, "/")));
    $json = @file_get_contents($url);
    $json = str_replace('"/', '"' . $baseUrl . '/', $json);
    $json = json_decode($json, true);
    if ($wphtml5playerclass->is_assoc($json)) {
        foreach ($json as $key => $value) {
            $jsonTemp[strtolower($key)] = $value;
        }
        $data = "";
        remove_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);
        if (isset($jsonTemp['video'])) {
            $data = $wphtml5playerclass->videoreplaceJSON(null, $jsonTemp['video']);
        } elseif (isset($jsonTemp['audio'])) {
            $data = $wphtml5playerclass->audioreplaceJSON(null, $jsonTemp['audio']);
        } elseif (isset($jsonTemp['flowplayer'])) {
            $data = $wphtml5playerclass->flowPlayerJSON(null, $jsonTemp['flowplayer']);
        } elseif (isset($jsonTemp['oembed'])) {
            $data = $wphtml5playerclass->oEmbedJSON(null, $jsonTemp['oembed']);
        }
        add_filter('embed_oembed_html', 'wphtml5player_oembed', 10, 2);
        return $data;
    } else {
        return $wphtml5playerclass->jsonError();
    }
}

wp_embed_register_handler("wphtml5json", "#(http://|https://)" . $wphtml_host . "/(.{1,}?).h5.json$#i", "wphtml5player_oembed_json_handler");

function wphtml5player_setTag() {
    global $wphtml5playerclass;
    if (defined('WPHTML5_VIDEO_TAG')) {
        $wphtml5playerclass->setTag("video", WPHTML5_VIDEO_TAG);
    }
    if (defined('WPHTML5_AUDIO_TAG')) {
        $wphtml5playerclass->setTag("audio", WPHTML5_AUDIO_TAG);
    }
    if (defined('WPHTML5_FLOWPLAYER_TAG')) {
        $wphtml5playerclass->setTag("flowplayer", WPHTML5_FLOWPLAYER_TAG);
    }
    if (defined('WPHTML5_OEMBED_TAG')) {
        $wphtml5playerclass->setTag("oembed", WPHTML5_OEMBED_TAG);
    }
}

function wphtml5player_add_upload_ext($mimes='') {
    $mimes['json'] = 'application/json';
    $mimes['webm'] = 'application/webm';
    $mimes['ogv'] = 'video/ogg';
    $mimes['oga'] = 'audio/ogg';
    $mimes['m4v'] = 'video/m4v';
    return $mimes;
}

add_filter("upload_mimes", "wphtml5player_add_upload_ext");

function wphtml5player_admin_option() {
?>

    <div class="wrap">
        <h2>HTML5 Multimedia Framework Options</h2>

        <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>

        <h3>General Options</h3>
        <p><span>In which order? (applies to [embed] tag only)</span><br />
            <input type="radio" name="html5framework_order" value="0" <?php if (get_option('html5framework_order') == '0') {
            echo 'checked="checked"';
        }; ?> /> HTML5 first, Flowplayer as fallback. <input type="radio" name="html5framework_order" value="1" <?php if (get_option('html5framework_order') == '1') {
            echo 'checked="checked"';
        }; ?> /> Flowplayer first, HTML5 as fallback.
        </p>

        <h3>Flowplayer Options</h3>
        <p><span>Flowplayer File Location (Including filename of player, useful for commercial versions of flowplayer, Leave Blank to use GPLv3 version included with plugin):</span><br />
            <input id="html5framework_flowplayer_location" type="text" name="html5framework_flowplayer_location" style="width: 95%;" value="<?php echo get_option('html5framework_flowplayer_location'); ?>" />
        </p>

        <p><span>Flowplayer Configuration (in <a href="http://www.json.org/" target="_blank">JSON</a>, for experience users of Flowplayer, no need to wrap with curly brackets '{}', <a href="http://flowplayer.org/documentation/index.html" target="_blank">click here for flowplayer documentation</a> ):</span><br />
            <textarea id="html5framework_flowplayer_config" name="html5framework_flowplayer_config" style="width: 95%; height: 15em" ><?php echo get_option('html5framework_flowplayer_config'); ?></textarea>
        </p>

        <p><span>Flowplayer Video Class Name:</span><br />
            <input id="html5framework_flowplayer_videoClassName" type="text" name="html5framework_flowplayer_videoClassName" style="width: 95%;" value="<?php echo get_option('html5framework_flowplayer_videoClassName'); ?>" />
        </p>

        <p><span>Flowplayer Audio Class Name:</span><br />
            <input id="html5framework_flowplayer_audioClassName" type="text" name="html5framework_flowplayer_audioClassName" style="width: 95%;" value="<?php echo get_option('html5framework_flowplayer_audioClassName'); ?>" />
        </p>

        <p><span>Enable Flowplayer for: </span><br />
            <input type="checkbox" name="html5framework_flowplayer_videoEnable" value="true" <?php if (get_option('html5framework_flowplayer_videoEnable') == 'true') {
            echo 'checked="checked"';
        }; ?> /> Video <input type="checkbox" name="html5framework_flowplayer_audioEnable" value="true" <?php if (get_option('html5framework_flowplayer_audioEnable') == 'true') {
            echo 'checked="checked"';
        }; ?> /> Audio</p>

        <p><span>Flowplayer Enable Range Requests (experimental and untested):</span> <input type="checkbox" name="html5framework_flowplayer_rangeRequests" value="true" <?php if (get_option('html5framework_flowplayer_rangeRequests') == 'true') {
            echo 'checked="checked"';
        }; ?> /></p>

        <h3>Advanced HTML5 Audio and Video Options</h3>
        <p><span>HTML5 <a href="http://www.json.org/" target="_blank">JSON</a> Options: (<a href="http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions?ts=1296417494&updated=AdvancedOptions#HTML5_Options" target="_blank">Click here for instruction</a>, no need to wrap with curly brackets '{}' ):</span><br />
            <textarea id="html5framework_html5_config" name="html5framework_html5_config" style="width: 95%; height: 15em" ><?php echo get_option('html5framework_html5_config'); ?></textarea>
        </p>

        <p><span>HTML5 Video Attribute <a href="http://www.json.org/" target="_blank">JSON</a> Options: (<a href="http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions?ts=1296417494&updated=AdvancedOptions#Video_Attribute_Option" target="_blank">Click here for instruction</a>, no need to wrap with curly brackets '{}' ):</span><br />
            <textarea id="html5framework_html5_videoAttribute" name="html5framework_html5_videoAttribute" style="width: 95%; height: 15em" ><?php echo get_option('html5framework_html5_videoAttribute'); ?></textarea>
        </p>

        <p><span>HTML5 Audio Attribute <a href="http://www.json.org/" target="_blank">JSON</a> Options: (<a href="http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions?ts=1296417494&updated=AdvancedOptions#Audio_Attribute_Option" target="_blank">Click here for instruction</a>, no need to wrap with curly brackets '{}' ):</span><br />
            <textarea id="html5framework_html5_audioAttribute" name="html5framework_html5_audioAttribute" style="width: 95%; height: 15em" ><?php echo get_option('html5framework_html5_audioAttribute'); ?></textarea>
        </p>

        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="html5framework_flowplayer_location,html5framework_flowplayer_config,html5framework_flowplayer_videoClassName,html5framework_flowplayer_audioClassName,html5framework_flowplayer_videoEnable,html5framework_flowplayer_audioEnable,html5framework_order,html5framework_html5_config,html5framework_html5_videoAttribute,html5framework_html5_audioAttribute,html5framework_flowplayer_rangeRequests" />

        <p class="submit">
            <input type="submit" class="button-primary" value="Save Changes" />
        </p>
    </form>
</div>

<?php
    }

    /**
     * Post admin hooks
     */
    add_action('admin_menu', "wphtml5player_video_admin_init");

    /**
     * Add options page.
     */
    function wphtml5player_video_admin_init() {
        add_options_page('HTML5 Multimedia Framework Options', 'HTML5 Multimedia', 8, 'html5multimedia', 'wphtml5player_admin_option');
    }
?>