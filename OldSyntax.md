# Syntax #

Absolute and Relative URL's can used with `url`, `url` can also be referred as `src` in advanced syntax.

## Simple Syntax ##

`[video:url.mp4|url.ogv|url.webm image.jpg width height]`

Image, width and height parameters are optional, but if width is defined then height becomes mandatory.

`[audio:url.ogg|url.aac|url.mp3]`

### Pros ###

  * Wordpress Visual Editor Friendly.
  * Very Easy to use.
  * Will not change in the future.

### Cons ###

  * Not robust, can't use URL's with spaces, as they are used as delimiters.
  * Will not support more options such as title in the future because it hard to maintain with PHP.
  * Does not support multiple lines, only single line.
  * Not a recognised standard.
  * Does not return error.

## Advanced Syntax ([JSON](http://www.json.org/)) ##

Use only double quote, JSON doesn't like single quote and no trailing commas neither.  To make it easier use HTML editor view in Wordpress.  The code below will also produce a flash (FlowPlayer) fallback if m4v/mp4 is provided.

```
[video]
{"url":[
"url.m4v",
"url.webm",
"url.ogv"
],
"poster":"url.jpg",
"width":640,
"height":368,
"title":"Title",
"attribute":{"controls":null,"preload":"none"}
}
[/video]
```

Video URL(s) are/is mandatory, others are optional, but if width is defined then height becomes mandatory.  Don't put quote round width and height values as they are numbers, otherwise it will return a JSON Error.

For single video use JSON value e.g. `"url.m4v"`.
For multiple video sources use JSON array e.g. `["url.m4v","url.webm","url.ogv"]`

```
[audio]
{"url":[
"url.ogg",
"url.aac",
"url.mp3"
],
"title":"Title",
"attribute":{"controls":null}
}
[/audio]
```

Audio URL(s) are/is mandatory, title is optional.

For single audio use JSON value e.g. `"url.ogg"`.
For multiple audio sources use JSON array e.g. `["url.ogg","url.aac","url.mp3"]`

If [Autoembed Plugin for Wordpress](http://wordpress.org/extend/plugins/wpautoembed/) is activated, then `"autoembed":{}` becomes available, which uses the same principle as the `[aembed]` syntax at http://code.google.com/p/autoembedplugin/wiki/Syntax. `"htmlvideo":{}` and `"htmlaudio":{}` are both disabled when used with `"autoembed":{}`, also `"autoembed":{}` will override the default fallback.

Note: In attribute `"preload":null` would return just `preload` in html, while `"preload":"none"` would return `preload="none"` in html.  Remember use `null` without quotes if you are not going to specify the value to the attribute. Also `id`, `width`, `height`, `poster` and `title` are not allowed inside the attribute object.  For `id` see [Advanced Options](http://code.google.com/p/html5videoplayer/wiki/AdvancedOptions).

### Flowplayer ###

Video syntax!

```
[flowplayer]
{"video":{
"url":"example.m4v",
"width":640,
"height":368,
"poster":"example.jpg"}
}
[/flowplayer]
```

Audio syntax!

```
[flowplayer]
{"audio":{
"url":"example.m4a"}
}
[/flowplayer]
```

Plus both of those syntax will also produce a html5 audio/video fallback as default fallback, enhancing support for mobile devices. There also other additional options, `"htmlvideo":{}` for flowplayer video only and `"htmlaudio":{}` for flowplayer audio only, those options use the same principles as `[video]` and `[audio]` syntax respectively and it will override the default fallback. Note: `"autoembed":{}` is always disabled when used with `"htmlvideo":{}` or `"htmlaudio":{}`.

Full Control Mode!

```
[flowplayer]
{"full":{"clip":{
"baseUrl":"http://example.com/get",
"duration":10,
"playlist":[
{"url":"example1.flv", "duration":2, "position": 0},
{"url":"example2.flv", "duration":3, "position": 5},
{"url":"example3.flv", "duration":4, "position": -1}
]}}}
[/flowplayer]
```

Full Control Mode uses the same principle as [flowplayer itself](http://flowplayer.org/demos/configuration/index.html), with additional options which are `"width":123`, `"height":123`, `"htmlvideo":{}` and `"htmlaudio:{}"`.

Note: Full Control Mode will not use fallback by default, also make sure you know the different between jQuery syntax and JSON.

## Pros ##

  * Very robust, can use URL's with spaces and won't break.
  * Will support more options in the future because it easy to maintain with PHP, thanks to `json_decode();`
  * JSON works with multi-dimensional array, while Wordpress shortcode hook only support one dimension.
  * easily support multiple lines, can also be done on one line if you very clever.
  * JSON is easier then HTML and XML.
  * JSON is a recognised Internet standard ([RCF 4627](http://tools.ietf.org/html/rfc4627))

## Cons ##

  * Not as easy as Simple Syntax.
  * Not Wordpress Visual Editor Friendly.

See also http://www.json.org/ and http://jsonformatter.curiousconcept.com/ for validation.