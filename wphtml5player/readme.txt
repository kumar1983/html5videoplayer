=== HTML5 Player Plugin for Wordpress ===
Contributors: CJ_Jackson
Author URI: http://cj-jackson.com/
Donate link: http://cj-jackson.com/donate/
Tags: audio, html5, quickcode, video, flowplayer
Requires at least: 2.6
Tested up to: 3.0
Stable tag: 0.9.2

Quickcode for HTML5 video and audio, fallback to flowplayer on fail

== Description ==

A WordPress plugin that allows blogger to embed video and audio using the respective
html 5 tags with Flowplayer as fallback, for example if a web browser doesn?t support
HTML 5 video or audio, then it will use Flowplayer for unsupported browsers.

The plugin is based on Video for Everybody, except that this plugin takes full
advantage of the PHP scripting language, while Video for Everybody is pure HTML.
For example version 0.9.1 can detect the iPad or any iPhone below 4.0, if detected
it will not include the poster attribute and if Opera Mobile/Mini is detected it will
move the link outside the video and audio tag.

= Instructions =

The syntax is easy to remember, the syntax is either
`[video:url.mp4|url.ogv|url.webm image.jpg width height]` or
`[audio:url.ogg|url.aac|url.mp3]`, image, width and height parameters are optional,
but if width is defined then height becomes mandatory.

== Installation ==

1. Upload the unzipped folder `wphtml5player` to your `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. And then follow the usage instructions on the Description page

== Frequently Asked Questions ==



== Screenshots ==



== Changelog ==

= 0.9.2 =
* Updated Plugin URI and Donate Link.

= 0.9.1 =
* Initial version.