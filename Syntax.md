# Simple Syntax #

Only absolute URL can be used inside the syntax, also the URL within the syntax must be on the same domain as your blog.

## HTML Video and Audio ##
By default it will return html5 audio or video with flowplayer flash object as fallback, unless `define('USE_FLOWPLAYER_IN_WP_EMBED', true);` is specified in `wp-config.php` file, if specified it return flowplayer with html5 audio or video as fallback.

### HTML Video ###
```
[embed width="640" height="480"]http://www.example.com/video.m4v[/embed]
```

### HTML Audio ###
```
[embed]http://www.example.com/audio.m4a[/embed]
```

## Video Sharing Site ##
```
[embed]http://www.youtube.com/watch?v=3G4h28qlJhY&feature=fvhl[/embed]
```

# Advanced Approach (JSON) #

The advanced approach involves using external .h5.json file, the file must also be stored on the same domain (excludes sub-domain) as you blog otherwise it will not work.

Use only double quote, JSON doesn't like single quote and no trailing commas neither. To make it easier use HTML editor view in Wordpress. The code below will also produce a flash (FlowPlayer?) fallback if m4v/mp4 is provided.

## Pointing to .h5.json file ##
```
[embed]http://cj-jackson.com/example.h5.json[/embed]
```

## HTML Video Example ##
```
{"video"{"url":[
"url.m4v",
"url.webm",
"url.ogv"
],
"poster":"url.jpg",
"width":640,
"height":368,
"title":"Title",
"attribute":{"controls":null,"preload":"none"}
}}
```

Video URL(s) are/is mandatory, others are optional, but if width is defined then height becomes mandatory. Don't put quote round width and height values as they are numbers, otherwise it will return a JSON Error.

For single video use JSON value e.g. "url.m4v". For multiple video sources use JSON array e.g. ["url.m4v","url.webm","url.ogv"]

## HTML Audio Example ##
```
{"url":[
"url.ogg",
"url.aac",
"url.mp3"
],
"title":"Title",
"attribute":{"controls":null}
}
```

Audio URL(s) are/is mandatory, title is optional.

For single audio use JSON value e.g. "url.ogg". For multiple audio sources use JSON array e.g. ["url.ogg","url.aac","url.mp3"]

## Flowplayer Examples ##

### Video Mode ###
```
{"flowplayer":{"video":{
"url":"example.m4v",
"width":640,
"height":368,
"poster":"example.jpg"}
}}
```

### Audio Mode ###
```
{"flowplayer":{"audio":{
"url":"example.m4a"}
}}
```

### Note for Video or Audio Mode ###

Plus both of those syntax will also produce a html5 audio/video fallback as default fallback, enhancing support for mobile devices. There also other additional options, "htmlvideo":{} for flowplayer video only and "htmlaudio":{} for flowplayer audio only, those options use the same principles as [video](video.md) and [audio](audio.md) syntax respectively and it will override the default fallback. Note: "autoembed":{} is always disabled when used with "htmlvideo":{} or "htmlaudio":{}.

### Full Control Mode ###
```
{"flowplayer":{"full":{"clip":{
"baseUrl":"http://example.com/get",
"duration":10,
"playlist":[
{"url":"example1.flv", "duration":2, "position": 0},
{"url":"example2.flv", "duration":3, "position": 5},
{"url":"example3.flv", "duration":4, "position": -1}
]}}}}
```

Full Control Mode uses the same principle as flowplayer itself, with additional options which are "width":123, "height":123, "htmlvideo":{} and "htmlaudio:{}".

Note: Full Control Mode will not use fallback by default, also make sure you know the different between jQuery syntax and JSON.

# Other Syntax #

Can be found at http://code.google.com/p/html5videoplayer/wiki/OldSyntax