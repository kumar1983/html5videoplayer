A list of advanced options!

# Introduction #

Advanced options are there to give users, especially wordpress theme editors the flexibility such as changing the way how the players looks or adding the ability to allow users to add their own JavaScript library or other libraries.

# Instruction #
Place the options on top of `header.php` of your selected wordpress theme.

# JSON Options (1.2.0 or newer) #

## HTML5 Options ##
```
<?php
$json = '{
"videoID":"video",
"beforeVideo":"",
"afterVideo":"",
"videoScript":"helloworld(%s)",
"audioID":"audio",
"beforeAudio":"",
"afterAudio":"",
"audioScript":"helloworld(%s)",
"videoLinkOutside":false,
"audioLinkOutside":false",
"videoLinkOutsideBefore":"<p>",
"videoLinkOutsideAfter":"</p>",
"audioLinkOutsideBefore":"<p>",
"audioLinkOutsideAfter":"</p>"
}';
do_action("html5player_options", $json);
unset($json);
?>
```

  * **videoID** set video id attribute with number appended (e.g. video-1, video-2).
  * **beforeVideo** any html code going before `<video>`.
  * **afterVideo** any html code going after `</video>` or outside link if set.
  * **videoScript** add JavaScript call to each video tag, place %s where id goes, videoID must be set first.
  * **audioID** set audio id attribute with number appended (e.g. audio-1, audio-2).
  * **beforeAudio** any html code going before `<audio>`.
  * **afterAudio** any html code going after `</audio>` or outside link if set.
  * **audioScript** add JavaScript call to each audio tag, place %s where id goes, audioID must be set first.
  * **videoLinkOutside** set video link to go after `</video>` rather then before, if true.
  * **audioLinkOutside** set audio link to go after `</audio>` rather then before, if true.
  * **videoLinkOutsideBefore** any html code going before outside video link.
  * **videoLinkOutsideAfter** any html code going after outside video link.
  * **audioLinkOutsideBefore** any html code going before outside audio link.
  * **audioLinkOutsideAfter** any html code going after outside audio link.

## FlowPlayer Options ##
```
<?php
$json = '{
"videoClassName":"videoclass",
"audioClassName":"audioclass",
"videoClassNameForTag":"videoclass",
"audioClassNameForTag":"audioclass",
"videoFlowPlayerEnabled":true,
"audioFlowPlayerEnabled":true
}';
do_action("html5player_flowplayer_options", $json);
unset($json);
?>
```

  * **videoClassName** add class attribute to flowplayer object for video, does not apply to `[flowplayer]` syntax.
  * **audioClassName** add class attribute to flowplayer object for audio, does not apply to `[flowplayer]` syntax.
  * **videoClassNameForTag** as `videoClassName` but applies to `[flowplayer]` syntax.
  * **audioClassNameForTag** as `audioClassName` but applies to `[flowplayer]` syntax.
  * **videoFlowPlayerEnabled** enabled FlowPlayer fallback for HTML5 Video, if set to true.
  * **audioFlowPlayerEnabled** enabled FlowPlayer fallback for HTML5 Audio, if set to true.

## Attribute ##

### Video Attribute Option ###
```
<?php
$json = '{
"controls":null,
"preload":"none"
}';
do_action("html5player_video_attribute", $json);
unset($json);
?>
```

### Audio Attribute Option ###
```
<?php
$json = '{
"controls":null
}';
do_action("html5player_audio_attribute", $json);
unset($json);
?>
```

Note: Don't use video and audio attribute option to add `id`, `title`, `width`, `height` and `poster`. For `id` use HTML5 Option, for others use syntax.

## oEmbed ##

### Attribute ###

```
<?php
$json = '{"global":{"class":"media"},
"youtube.com":{"append":{"data":"&color1=0xe1600f&color2=0xfebd01"}}}';
do_action("html5player_ombed_object_attribute", $json);
?>
```

### Param ###

```
<?php
$json = '{"youtube.com":{"append":{"movie":"&color1=0xe1600f&color2=0xfebd01"}}}';
do_action("html5player_oembed_object_param", $json);
?>
```

# Old Options (Older than 1.2.0, Deprecated) #
The example below will enable SWFObject.

```
<?php if(function_exists("html5player_enableSWFObject")) {
    html5player_enableSWFObject();
}
?>
```

The examples below will change the attributes of the `<video>` and `<audio>` tag.

```
<?php if(function_exists("html5player_videoParam")) {
    html5player_videoParam('controls preload="none"');
}
if(function_exists("html5player_audioParam")) {
    html5player_audioParam('controls');
}
?>
```

The examples below allow you to set videos and audios ID, plus it will automatically append a counter to the ID (e.g. video-1, video-2) so it does not fail w3c validation.

```
<?php
if(function_exists("html5player_setVideoID")) {
    html5player_setVideoID('video');
}
if(function_exists("html5player_setAudioID")) {
    html5player_setAudioID('audio');
}
?>
```

The examples below allow you to place html code, before and after video and audio tags.

```
<?php
if(function_exists("html5player_setVideoWrap")) {
    html5player_setVideoWrap('<!-- before -->','<!-- after -->');
}
if(function_exists("html5player_setAudioWrap")) {
    html5player_setAudioWrap('<!-- before -->','<!-- after -->');
}
?>
```
If you need to register your id's with a JavaScript library, use the examples below, there no need for `<script>` tag that done automatically, place `%s` where id's goes. Make sure you set the id's first.

```
<?php
if(function_exists("html5player_setVideoJSCall")) {
    html5player_setVideoJSCall('test("%s")');
}
if(function_exists("html5player_setAudioJSCall")) {
    html5player_setAudioJSCall('test("%s")');
}
?>
```
The examples below allows you to set class name of FlowPlayers object.

```
<?php
if(function_exists("html5player_setFlowplayerVideoClass")) {
    html5player_setFlowplayerVideoClass('media');
}
if(function_exists("html5player_setFlowplayerAudioClass")) {
    html5player_setFlowplayerAudioClass('media');
}
?>
```
The examples below will force links to go outside the video tag.

```
<?php
if(function_exists("html5player_setVideoLinkOutside")) {
    html5player_setVideoLinkOutside();
}
if(function_exists("html5player_setAudioLinkOutside")) {
    html5player_setAudioLinkOutside();
}
?>
```
The examples below will set add html code before and after the link.

```
<?php
if(function_exists("html5player_setVideoLinkOutsideWrap")) {
    html5player_setVideoLinkOutsideWrap('<!-- before -->','<!-- after -->');
}
if(function_exists("html5player_setAudioLinkOutsideWrap")) {
    html5player_setAudioLinkOutsideWrap('<!-- before -->','<!-- after -->');
}
?>
```

Some JavaScript Libraries comes with it own Fallback Player such as `JW Player for HTML5`, so there also a option to disable FlowPlayer for either video or audio.

```
<?php
if(function_exists("html5player_disableFlowPlayerVideo")) {
    html5player_disableFlowPlayerVideo();
}
if(function_exists("html5player_disableFlowPlayerAudio")) {
    html5player_disableFlowPlayerVideo();
}
?>
```

Note: Place the codes on top of `header.php` template of your selected theme.