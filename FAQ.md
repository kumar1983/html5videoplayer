# Frequently Asked Questions #

## Does it upholds the same principle as [Video for Everybody](http://camendesign.com/code/video_for_everybody)? ##
By default, it upholds only on xml feeds and mobile devices that uses webkit or opera excluding iPad, on other type of devices the link will be inside the video tag rather then outside which the majority of theme editors may prefer.  If you want to use that principle on your theme, add the code below to `header.php`.

```
<?php
do_action("html5player_videoForEverybody");
?>
```

## Does it support H.264, Extended, Main and High Profiles? ##
Yes, as of 1.0.4 by using special extensions in file names which is currently a proof of concept, so here the table.

| Extensions | Profile | Video Codec `**` | Audio Codec `**` |
|:-----------|:--------|:-----------------|:-----------------|
| mp4 or m4v`*` | Baseline | avc1.42E01E      | mp4a.40.2        |
| ext.mp4 or ext.m4v | Extended | avc1.58A01E      | mp4a.40.2        |
| main.mp4 or main.m4v | Main    | avc1.4D401E      | mp4a.40.2        |
| high.mp4 or high.m4v | High    | avc1.64001E      | mp4a.40.2        |
| webm`*`    | --      | vp8              | vorbis           |
| ogv or ogg`*` | --      | theora           | vorbis           |
`*` Not special extensions.
`**` According to [HTML5 specification](http://www.w3.org/TR/html5/video.html) and [DiveInToHTML5](http://diveintohtml5.org/video.html)

A file using a baseline would be `example.m4v`, but if happens to be using main profile then rename it to `example.main.m4v`.

If iPhoneOS or iOS is detected and is below 4.0, the plugin will automatically cancel them off the html source list except baseline.

Note: This is a proof of concept, it is recommended to stick to `Baseline` profile to avoid issues.  The iOS 4 does honour the codec type but other mobile devices may not.

## Why Firefox or Safari won't fallback to Flowplayer(or Flash) when non-supported format is detected? (Shows `X` on Firefox) ##
Version 1.7.0 and above comes with a force-fallback script that can be activated as a separate plug-in, `HTML5 Video and Audio Framework Force-Fallback to Flash.`

As a alternative, you can also use a [JavaScript Library](http://code.google.com/p/html5videoplayer/wiki/JavaScriptLibraries) such as [VideoJS](http://videojs.com/) which will force fallback when a non-supported format is detected.

Note: Bare in mind, that HTML5 is currently a working draft.

## Why Firefox won't play ogv although it's included? ##
This sound like a problem with the web server, sending the incorrect mime-type, try adding
the following to `.htaccess`, if that does not work contact your server admin.

```
AddType video/ogg .ogv
AddType video/mp4 .mp4
AddType video/webm .webm
```

After doing this, perform a hard refresh with Firefox (Ctrl+F5).

## Opera hangs on large ogg containers? ##
Opera does not seems to like ogg containers that contain text streams, some encoders such
as Miro Video Converter add text streams to the container.  Windows, Mac and Linux users can
use [MediaInfo](http://mediainfo.sourceforge.net) to check if the containers has text streams.

Note: This was tested with Opera 10.6 and may not apply to later versions.

## Any good encoding tutorials? ##
You might to check out [DiveIntoHTML5](http://diveintohtml5.org/video.html) they have some very good tutorials.

Tip: I use Handbrake to encode m4v for the web and mkv (h.264 60% constant quality and Vorbis(AoTuV) 64kbit) for the base for FFMpeg to encode from, just replace `-acodec libvorbis` (Example from DiveIntoHTML5) with `-acodec copy` because there is no need to reencode the audio from the base. The very latest FFMpeg can be obtained from [Automated FFmpeg Builds](http://ffmpeg.arrozcru.org/autobuilds/) for Windows and Mac. Take no notice of ffmpeg2theora, as the standard ffmpeg can do Theora anyway.  You may want to remove the aspect and size option as well.

## What the default attributes for video and audio? ##
The default for video is `controls preload="none"`.
The default for audio is `controls`.

## Is there a way to overwrite the default attributes for video and audio? ##
Yes, the following examples below will overwrite the default attributes.

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

```
<?php
$json = '{
"controls":null
}';
do_action("html5player_audio_attribute", $json);
unset($json);
?>
```

Place the code on top of `header.php` template of your selected theme.

Note: The attribtues `src`, `poster`, `width` and `height` are already covered by the syntax,
therefore no need to add them, there also no need to add `type` neither as that covered
as well.

## Any other advanced options? ##

Yes, see http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions

## Any future plans to include Video and Audio JS Library? ##
Sorry No, but the advanced option should allow you to add your own JS library or
the other libraries.  See [JavaScript Libraries](http://code.google.com/p/html5videoplayer/wiki/JavaScriptLibraries).

## Is it possible to change the name of the syntax? ##
There is a time, when a plug in will conflict with other plug ins, usually because of syntax name, so yes it is possible to change the name of the syntax, just add the below to `wp-config.php`

```
define("WPHTML5_VIDEO_TAG", "html5video");
define("WPHTML5_AUDIO_TAG", "html5audio");
define("WPHTML5_FLOWPLAYER_TAG", "flash");
define("WPHTML5_OEMBED_TAG", "aembed");
```

After you put this into `wp-config.php,` `[video]`, `[audio]` and `[flowplayer]` will become `[html5video]`, `[html5audio]` and `[flash]` respectively.

## How to change location and configure FlowPlayer? ##
By adding the following options to `wp-config.php`.

```
define("FLOWPLAYER_URL", "http://example.com/flowplayer/flowplayer.swf");
define("FLOWPLAYER_JSON", '{"key":"key goes here."}'); /* Useful for using commercial version of flowplayer or adding plugins to the player such as Google Analytics. */
```

Flowplayer plugin setting can also be changed at syntax level `[flowplayer]` using `"plugins":{}`.

Note: if you are going to change the URL make sure you also include audio plugin in the same location as flowplayer.swf.