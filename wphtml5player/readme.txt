=== HTML5 Audio and Video Framework for Wordpress ===
Contributors: CJ_Jackson
Author URI: http://cj-jackson.com/
Donate link: http://cj-jackson.com/donate/
Tags: audio, html5, quickcode, video, flowplayer
Requires at least: 2.6
Tested up to: 3.0
Stable tag: 1.3.2

A Highly Customisable HTML5 Audio and Video Framework for Wordpress

== Description ==

HTML5 Audio and Video Framework is designed to be a highly customisable plugin for
wordpress that allows advanced users to add their own JavaScript Libraries, or other
JavaScript Libraries such as VideoJS and JW Player for HTML5 within the Wordpress theme
file `header.php` via [advanced options](http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions).
You may want to have a look at the [JavaScript Library page](http://code.google.com/p/html5videoplayer/wiki/JavaScriptLibraries)
on the project wiki.

The framework is designed to be compatible with mobile devices that use WebKit or
Opera Mobile/Mini and XML feeds such as RSS, for example if that mobile device is
detected or RSS feed are used the framework will not load any JavaScript and will
use the [Video for Everybody](http://cj-jackson.com/projects/autoembed-and-html-5-player-plugin-demo/)
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
[FlowPlayer](http://flowplayer.org/) 3.2 the perfect choice and that why it got
included with the framework.  Adobe has already announced support for webm will be
included into the next version of Flash.

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

= 1.3.2 =
* Fixed bug with flowplayer `"audio":{}`.

= 1.3.1 =
* Fixed relative url issue with `[flowplayer]` tag.
* A Few minor fixes.

= 1.3.0 =
* Added flowplayer syntax tag `[flowplayer]` uses html5 video or audio as fallback.

= 1.2.6 =
* Added further intergration support for Autoembed Plugin for Wordpress.
* Framework deactivate if function "json_encode" or "json_decode" does not exists in PHP.

= 1.2.5 =
* Fixed Fatal Error caused by function "json_last_error" not existing on some servers

= 1.2.4 =
* Added experimental intergration support for [Autoembed Plugin for Wordpress](http://wordpress.org/extend/plugins/wpautoembed/)
1.2.1 and above, allowing video sharing site such as YouTube to be used as fallback
rather then just flowplayer.

= 1.2.3 =
* Fixed case-sensitive issue.

= 1.2.2 =
* Added localisation support.
* A few minor fixes.

= 1.2.1 =
* Added hooks for Video For Everybody.
* Added stronger contraints for JSON Advanced Syntax.

= 1.2.0 =
* Renamed from "HTML5 Player Plugin for Wordpress" to "HTML5 Audio and Video Framework 
for Wordpress" because that what it is and it sound better.
* Advanced options have been changed and replace with JSON Advanced Options and
Wordpress hooks.  Older advanced options will no longer work.

= 1.1.3 =
* Improved iPad support.

= 1.1.2 =
* Escape special html characters form title, video and audio url, and poster url.
* Output JSON error, if error is made or otherwise returns the html code.

= 1.1.1 =
* Fixed bug with advanced audio syntax, Sorry about that.

= 1.1.0 =
* Added robust advanced syntax using [JSON](http://www.json.org/)

= 1.0.5 =
* Added options to disable FlowPlayer for either video, audio or both. Useful for
some JavaScript Libraries.

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