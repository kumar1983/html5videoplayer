=== HTML5 Player Plugin for Wordpress ===
Contributors: CJ_Jackson
Author URI: http://cj-jackson.com/
Donate link: http://cj-jackson.com/donate/
Tags: audio, html5, quickcode, video, flowplayer
Requires at least: 2.6
Tested up to: 3.0
Stable tag: 1.0.4

Quickcode for HTML5 video and audio, fallback to flowplayer on fail

== Description ==

A WordPress plugin that allows blogger to embed video and audio using the respective
html 5 tags with Flowplayer as fallback, for example if a web browser doesn't render
HTML 5 video or audio, it will use Flowplayer (Flash) for that web browsers.

The plugin is based on [Video for Everybody](http://camendesign.com/code/video_for_everybody) concept,
except that this plugin takes full advantage of the PHP scripting language, while
Video for Everybody is pure HTML. For example version 0.9.1 can detect the iPad or
any iPhone below 4.0, if detected it will not include the poster attribute and if
Opera Mobile/Mini is detected it will move the link outside the video and audio tag.

= Instructions =

The syntax is easy to remember, the syntax is either
`[video:url.mp4|url.ogv|url.webm image.jpg width height]` or
`[audio:url.ogg|url.aac|url.mp3]`, image, width and height parameters are optional,
but if width is defined then height becomes mandatory.  It is recommended that you
include theora and vorbis, as firefox won't fallback to flowplayer.

See [Demostration](http://cj-jackson.com/projects/autoembed-and-html-5-player-plugin-demo/)
and [FAQ](http://code.google.com/p/html5videoplayer/wiki/FAQ) for details, but keep
the code on one line, otherwise it won't work.

== Installation ==

1. Upload the unzipped folder `wphtml5player` to your `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. And then follow the usage instructions on the Description page

== Frequently Asked Questions ==
See http://code.google.com/p/html5videoplayer/wiki/FAQ

== Screenshots ==
None

== Changelog ==

= 1.0.4 =
* Added support for H.264 Extended, Main and High Profile using special extentions
(.ext.m4v, .main.m4v and .high.m4v) as proof of concept.
* A few minor improvement.

= 1.0.3 =
* Added ability to force links outside the html video or audio tag.

= 1.0.2 =
* Added type safety to video attribute "poster", to prevent errors with FlowPlayer.

= 1.0.1 =
* Added ability to define class name for FlowPlayers object.

= 1.0.0 =
* Added more advanced options.

= 0.9.4 =
* Added ability to override default attributes of video or audio.

= 0.9.3 =
* Disabled SWFObject by default, as it was found to cause issues with some
installation.
* Added FAQ and Updated Description.

= 0.9.2 =
* Updated Plugin URI and Donate Link.

= 0.9.1 =
* Initial version.