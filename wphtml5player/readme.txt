=== HTML5 Multimedia Framework for Wordpress ===
Contributors: CJ_Jackson
Author URI: http://cj-jackson.com/
Donate link: http://cj-jackson.com/donate/
Tags: audio, html5, quickcode, video, flowplayer
Requires at least: 2.6
Tested up to: 3.0
Stable tag: 2.0.0

A Highly Customisable HTML5 Multimedia Framework for Wordpress

== Description ==

HTML5 Multimedia Framework is designed to be a highly customisable plugin for
wordpress that allows advanced users to add their own JavaScript Libraries, or other
JavaScript Libraries such as VideoJS and JW Player for HTML5 within the Wordpress theme
file `header.php` via [advanced options](http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions).
You may want to have a look at the [JavaScript Library page](http://code.google.com/p/html5videoplayer/wiki/JavaScriptLibraries)
on the project wiki.

The framework is designed to be compatible with mobile devices that use WebKit or
Opera Mobile/Mini and XML feeds such as RSS, for example if that mobile device is
detected or RSS feed are used the framework will not load any JavaScript and will
use the [Video for Everybody](http://camendesign.com/code/video_for_everybody)
principle, if iOS or iPhoneOS is detected and below 4.0 the framework will also
cancel out all incompatible format from video source list but leave the download
links behind, so it will only show the extensions `mp4` or `m4v` (`.ext.mp4` or
`.main.mp4` for iPad with iOS 3.2 or above).

The framework is also designed to be compatible with browsers that do not render
HTML5 Audio or Video such as Internet Explorer 8 or below, because the framework
will also include the fallback flash player, [FlowPlayer](http://flowplayer.org/)
3.2 for both video and audio provided that you include either mp4 for video or mp3
for audio within the syntax, [FlowPlayer](http://flowplayer.org/) 3.2 and HTML5 video
have both things in common, that would be the overlay video control bar, making
[FlowPlayer](http://flowplayer.org/) 3.2 the perfect choice.

The framework use two kinds of syntax, the simple syntax which is limited to URL's,
poster, width and height, and the advanced syntax which uses [JSON](http://www.json.org/)
which support all kinds of attributes for video and audio even if it not on the
HTML5 specification, see [syntax page](http://code.google.com/p/html5videoplayer/wiki/Syntax)
for details.

The framework currently support mp4(h.264,aac), ogg(theora,vorbis), mp3(audio only)
and also webm (vp8,vorbis).

Please read the [FAQ](http://code.google.com/p/html5videoplayer/wiki/FAQ) before
asking questions; also look at [Demonstration](http://cj-jackson.com/projects/autoembed-and-html-5-player-plugin-demo/).

== Installation ==

1. Upload the unzipped folder `wphtml5player` to your `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. And then follow the usage instructions on the [syntax page](http://code.google.com/p/html5videoplayer/wiki/Syntax)

== Frequently Asked Questions ==
See http://code.google.com/p/html5videoplayer/wiki/FAQ

== Screenshots ==
None

== Changelog ==

= 2.0.0 =
* Enhanced support for Wordpress oEmbed.
* Producers XHTML1.1/HTML5 complient code from oEmbed output
thanks to [PHP Simple HTML DOM Parser](http://simplehtmldom.sourceforge.net) for
analysing and extracting attribute from oEmbed output. (Only works with flash object)
* Renamed simple HTML DOM classes to prevent potential conflict with other
Wordpress plugins.
* Return oEmbed output if flash object is not detected.
* Automatically add fallback to oEmbed therefore perseving iPhone support.
(Only works with flash object).
* Added JSON interface for Wordpress oEmbed.

= 1.8.0 =
* Removed Autoembed intergration support.
* Added support for Wordpress oembed as fallback to html5.