=== HTML5 Multimedia Framework for Wordpress ===
Contributors: CJ_Jackson
Author URI: http://cj-jackson.com/
Donate link: http://cj-jackson.com/donate/
Tags: audio, html5, quickcode, video, flowplayer
Requires at least: 2.6
Tested up to: 3.1
Stable tag: 3.2.5

A Highly Customisable HTML5 Multimedia Framework for Wordpress

== Description ==

HTML5 Multimedia Framework is designed to be a highly customisable plugin for
wordpress that comes with [MediaElement.js](http://mediaelementjs.com/).

The framework currently support mp4(h.264,aac), ogg(theora,vorbis), mp3(audio only)
and also webm (vp8,vorbis).  It can also support wmv, flv and wma while MediaElement.js
is enabled.

Please read the [FAQ](http://code.google.com/p/html5videoplayer/wiki/FAQ) before
asking questions; also look at [Demonstration](http://cj-jackson.com/projects/autoembed-and-html-5-player-plugin-demo/)
for instructions.

== Installation ==

1. Upload the unzipped folder `wphtml5player` to your `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. And then follow the usage instructions on the [syntax page](http://code.google.com/p/html5videoplayer/wiki/Syntax)

== Frequently Asked Questions ==
See http://code.google.com/p/html5videoplayer/wiki/FAQ

== Screenshots ==
None

== Changelog ==

= 3.2.5 =
* Updated mediaelementjs to 2.1.1.

= 3.2.4 =
* Modified mediaelement-and-player.min.js to prevent mod_security from triggering with flash.
* Using modified version of flashmediaelement.swf for improved fullscreen support.
* Added 'wmode="transparent"' to all object to prevent overlapping of css and html.

= 3.2.3 =
* Prevent errors caused by human error.
* MediaElement.js is now the default method.
* Flowplayer will no longer be updated, always include 3.2.5.
* Updated MediaElement.js to 2.1.0
* Added "enable" and "disable" attribute for MediaElement.js features.

= 3.2.2 =
* Fixed serious <track> bug, forgot to unset $attribute in foreach loop.

= 3.2.1 =
* Fixed minor bug.

= 3.2.0 =
* Added support for Windows Media Video (wmv) and Audio (wma), and Flash Video (flv),
but only works when MediaElement.js is enabled.
* Added support for external URL via embed tag, auto-detection with poster and
other video files (except wmv, wma and flv) only works with internal URL.
* Added attribute to embed tag, for video: poster, mp4, ogv, webm, wmv and flv, and for audio:
aac, ogg, mp3 and wma.  Work with both external and internal URL.
* Added support for chapters and subtitles for video only via the embed attribute, subtitle,
slang, chapter and clang.  Only works with MediaElement.js at present as no browser
support track element, yet.

= 3.1.0 =
* Added support for mediaelement.js

= 3.0.1 =
* Prevent Flash Light and Force Fallback now works as options in admin panel,
they no longer operate as a plugin.
* Added Video for Everybody Compliant option in admin panel.
* Added oEmbed Options in admin panel.
* Flowplayer now has dedicated configuration for video, audio and full control, (
What the point of using liverail plugin while playing audio file?).

= 3.0.0 =
* Added Admin Panel (Settings -> HTML5 Multimedia).
* Added FlowPlayers' pseudostreaming plugin and range requests support (experimental and untested)

= 2.2.3 =
* MIT Licensed Release.