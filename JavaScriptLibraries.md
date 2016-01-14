# JavaScript Libraries for HTML5 Video Instructions #

Choose one!

## [VideoJS](http://videojs.com/) (Highly Recommended) ##

Place in header.php of theme file.

```
<?php
wp_enqueue_script('videojs', get_bloginfo('url').'/script/video.js');
wp_enqueue_style('videojs', get_bloginfo('url').'/script/video-js.css');
function videoJSJavaScriptCall() {
        echo '<script type="text/javascript">VideoJS.setupAllWhenReady();</script>';
}
add_action('wp_head', videoJSJavaScriptCall);
$json = '
{"beforeVideo":"<div class=\"video-js-box\">",
"afterVideo":"</div>",
"videoLinkOutside":true,
"videoLinkOutsideBefore":"<p class=\"vjs-no-video\">",
"videoLinkOutsideAfter":"<br>\n<a href=\"http://videojs.com\">HTML5 Video Player</a> by <a href=\"http://videojs.com\">VideoJS</a>\n</p>"}
';
do_action("html5player_options", $json);
$json = '
{"class":"video-js"}
';
do_action("html5player_video_attribute", $json);
$json = '
{"videoClassName":"vjs-flash-fallback"}
';
do_action("html5player_flowplayer_options", $json);
unset($json);
?>
```

Note: You may want to also disable the force fallback plugin.

# JavaScript Libraries for HTML5 Audio Instructions #
None at the Moment.