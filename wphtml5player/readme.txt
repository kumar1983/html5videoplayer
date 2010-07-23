=== HTML5 Player Plugin for Wordpress ===
Contributors: CJ_Jackson
Author URI: http://cj-jackson.com/
Donate link: http://cj-jackson.com/donate/
Tags: audio, html5, quickcode, video, flowplayer
Requires at least: 2.6
Tested up to: 3.0
Stable tag: 0.9.3

Quickcode for HTML5 video and audio, fallback to flowplayer on fail

== Description ==

A WordPress plugin that allows blogger to embed video and audio using the respective
html 5 tags with Flowplayer as fallback, for example if a web browser doesn't support
HTML 5 video or audio, then it will use Flowplayer for unsupported browsers.

The plugin is based on [http://camendesign.com/code/video_for_everybody Video for Everybody],
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

See [http://cj-jackson.com/projects/autoembed-and-html-5-player-plugin-demo/ Demostration]
detail, but keep the code on one line, otherwise it won't work.

== Installation ==

1. Upload the unzipped folder `wphtml5player` to your `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. And then follow the usage instructions on the Description page

== Frequently Asked Questions ==
= How to enable use with SWFObject? =
Easy, just add the below to `header.php` template of your selected theme.

`<?php if(function_exists("html5player_enableSWFObject")) {
	html5player_enableSWFObject();
}
?>`

Note: As of 0.9.3, SWFobject is disabled by default as it was found to causes issues
with some setup, e.g. flowplayer shows up, 5 seconds after it disappears.

= Why Firefox won't fallback to Flowplayer(or Flash) when non-supported format is not detected? =
This is a problem with Firefox itself, to work round this problem include theora
and vorbis format within video and audio tag. e.g.

`[video:file.mp4|file.ogv]

[audio:file.mp3|file.ogg]`

Note: Bare in mind, that HTML5 is currently a working draft.

= Why Firefox won't play ogv although it's included? =
This sound like a problem with the web server, sending the incorrect mime-type, try adding
the following to `.htaccess`, if that does not work contact your server admin.

`AddType video/ogg .ogv
AddType video/mp4 .mp4
AddType video/webm .webm`

After doing this, perform a hard refresh with Firefox (Ctrl+F5).

== Screenshots ==
None

== Changelog ==

= 0.9.3 =
* Disabled SWFObject by default, as it was found to causes issues with some
installation.
* Added FAQ and Updated Description.

= 0.9.2 =
* Updated Plugin URI and Donate Link.

= 0.9.1 =
* Initial version.