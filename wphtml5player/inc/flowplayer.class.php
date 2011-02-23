<?php

/**
 * Copyright (C) 2011 by Christopher John Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class flowplayer {
    private $object_attribs;
    private $object_params;
    private $option;
    private $count;
    private $flowplayer;
    private $flowplayerConfig;
    private $jsonCommand;

    public function __construct($location) {
        if(defined("FLOWPLAYER_URL")) {
            $this->flowplayer = FLOWPLAYER_URL;
        } else {
            $this->flowplayer = $location."/inc/flowplayer.swf";
        }
        $this->defaultOption();
        $this->count = 1;
        $this->flowplayerConfig = false;
        $this->jsonCommand = array(
                array(
                        "name" => "video",
                        "cmd" => "videoJSON"
                ),
                array(
                        "name" => "audio",
                        "cmd" => "audioJSON"
                ),
                array(
                        "name" => "full",
                        "cmd" => "fullJSON"
                )
        );
    }

    private function defaultOption() {
        $this->option = array(
                'flashIsSetup' => false,
                'videoClassName' => false,
                'audioClassName' => false,
                'videoClassNameForTag' => false,
                'audioClassNameForTag' => false,
                'videoFlowPlayerEnabled' => true,
                'audioFlowPlayerEnabled' => true
        );
    }

    public function setFlowplayerConfig($json) {
        if(!$this->flowplayerConfig) {
            $this->flowplayerConfig = array();
        }
        $this->flowplayerConfig = $this->array_replace_recursive($this->flowplayerConfig, $json);
    }

    public function setFlowLocation($url) {
        $this->flowplayer = $url;
    }

    public function setUpFlash($object) {
        $this->object_attribs = $object['attribs'];
        $this->object_params = $object['params'];
        $this->option['flashIsSetup'] = true;
    }

    public function getOptions($param) {
        return $this->option[$param];
    }

    public function setOptions($param, $value) {
        $this->option[$param] = $value;
    }

    public function getFlashIsSetup() {
        return $this->option['flashIsSetup'];
    }

    public function flowPlayerJSON($json, $array = false) {
        if($this->flowplayer) {
            if($array) {
                $jsonTemp = $array;
            } else {
                $jsonTemp = json_decode($json, true);
            }
            global $wphtml5playerclass;
            if($wphtml5playerclass->is_assoc($jsonTemp)) {
                $json = $jsonTemp;
                unset($jsonTemp);
                $jsonTemp = array();
                foreach($json as $key => $value) {
                    $jsonTemp[strtolower($key)] = $value;
                }
                foreach($this->jsonCommand as $value) {
                    if(isset($jsonTemp[$value["name"]])) {
                        return $this->$value["cmd"]($jsonTemp[$value["name"]]);
                    }
                }
                return "video, audio or full mode is not set.";
            } else {
                return $wphtml5playerclass->jsonError();
            }
        } else {
            return "";
        }
    }

    private function videoJSON($jsonTemp) {
        global $wphtml5playerclass;
        if($wphtml5playerclass->is_assoc($jsonTemp)) {
            $json = array();
            foreach($jsonTemp as $key => $value) {
                $json[strtolower($key)] = $value;
            }
            if(isset($json["src"])) {
                $json["url"] = $json["src"];
                unset($json["src"]);
            }
            if(isset($json["url"])) {
                if(!is_string($json["url"])) {
                    return "ERROR: URL is not string.";
                } else {
                    $url = $json["url"];
                }
            } else {
                return "ERROR: URL is not specified.";
            }
            if($url) {
                $temp[0] = $url;
                $temp = $wphtml5playerclass->urlsCheck($temp);
                $url = $temp[0];
                unset($temp);
            }
            if(!(isset($json["width"]) && isset($json["height"]))) {
                $width = false;
                $height = false;
            } elseif (!(is_numeric($json["width"]) && is_numeric($json["height"]))) {
                $width = false;
                $height = false;
            } else {
                $width = (int)$json["width"];
                $height = (int)$json["height"];
            }
            if(!isset($json["htmlvideo"])) {
                $htmlvideo = false;
            } elseif(!$wphtml5playerclass->is_assoc($json["htmlvideo"])) {
                $htmlvideo = false;
            } else {
                $htmlvideo = $json["htmlvideo"];
            }
            if(!isset($json["poster"]) || is_array($json["poster"])) {
                $poster = false;
            } elseif (!preg_match("#.(jpg|jpeg|png|gif)$#i", $json["poster"])) {
                $poster = false;
            } else {
                $poster = $json["poster"];
            }
            if(!isset($json["plugins"])) {
                $plugins = false;
            } elseif(!$wphtml5playerclass->is_assoc($json["plugins"])) {
                $plugins = false;
            } else {
                $plugins = $json["plugins"];
            }
            if(!isset($json["title"]) || is_array($json["title"])) {
                $title = false;
            } else {
                $title = htmlspecialchars($json["title"]);
            }
            if($poster) {
                $temp[0] = $poster;
                $temp = $wphtml5playerclass->urlsCheck($temp);
                $poster = $temp[0];
                unset($temp);
            }
            unset($json);
            if($htmlvideo) {
                $temp = array();
                foreach($htmlvideo as $key => $value) {
                    $temp[strtolower($key)] = $value;
                }
                $htmlvideo = $temp;
                unset($temp);
                if(isset($htmlvideo["src"])) {
                    //do nothing.
                } elseif(!isset($htmlvideo["url"])) {
                    $htmlvideo["url"] = $url;
                }
                if(!isset($htmlvideo["poster"]))
                    $htmlvideo["poster"] = $poster;
                if(!isset($htmlvideo["width"]))
                    $htmlvideo["width"] = $width;
                if(!isset($htmlvideo["height"]))
                    $htmlvideo["height"] = $height;
                if(!isset($htmlvideo["title"]))
                    $htmlvideo["title"] = $title;
                $fallback = $wphtml5playerclass->videoreplaceJSON(null, $htmlvideo, true);
            } else {
                $htmlvideo = array ("url" => $url, "poster" => $poster,
                        "width" => $width, "height" => $height, "title" => $title);
                $fallback = $wphtml5playerclass->videoreplaceJSON(null, $htmlvideo, true);
            }
            if(preg_match("#(webOS|SymbianOS|Nokia|Android)#i", $_SERVER['HTTP_USER_AGENT']) &&
                    (defined('WPHTML5_PREVENT_FLASH_LITE') && !$wphtml5playerclass->getOption('xmlMode'))) {
                return $fallback;
            }
            $array = array ("url" => $url, "poster" => $poster, "width" => $width, "height" => $height,
                    "title" => $title, "plugins" => $plugins);
            $this->videoCompatible($array, true);
            return $this->getFlashObject($fallback);
        } else {
            return "";
        }
    }

    private function audioJSON($jsonTemp) {
        global $wphtml5playerclass;
        if($wphtml5playerclass->is_assoc($jsonTemp)) {
            $json = array();
            foreach($jsonTemp as $key => $value) {
                $json[strtolower($key)] = $value;
            }
            if(isset($json["src"])) {
                $json["url"] = $json["src"];
                unset($json["src"]);
            }
            if(isset($json["url"])) {
                if(!is_string($json["url"])) {
                    return "ERROR: URL is not string.";
                } else {
                    $url = $json["url"];
                }
            } else {
                return "ERROR: URL is not specified.";
            }
            if($url) {
                $temp[0] = $url;
                $temp = $wphtml5playerclass->urlsCheck($temp);
                $url = $temp[0];
                unset($temp);
            }
            if(!isset($json["htmlaudio"])) {
                $htmlaudio = false;
            } elseif(!$wphtml5playerclass->is_assoc($json["htmlaudio"])) {
                $htmlaudio = false;
            } else {
                $htmlaudio = $json["htmlaudio"];
            }
            if(!isset($json["plugins"])) {
                $plugins = false;
            } elseif(!$wphtml5playerclass->is_assoc($json["plugins"])) {
                $plugins = false;
            } else {
                $plugins = $json["plugins"];
            }
            if(!isset($json["title"]) || is_array($json["title"])) {
                $title = false;
            } else {
                $title = htmlspecialchars($json["title"]);
            }
            unset($json);
            if($htmlaudio) {
                $temp = array();
                foreach($htmlaudio as $key => $value) {
                    $temp[strtolower($key)] = $value;
                }
                $htmlaudio = $temp;
                unset($temp);
                if(isset($htmlaudio["src"])) {
                    //do nothing.
                } elseif(!isset($htmlaudio["url"])) {
                    $htmlaudio["url"] = $url;
                }
                if(!isset($htmlaudio["title"]))
                    $htmlaudio["title"] = $title;
                $fallback = $wphtml5playerclass->audioreplaceJSON(null, $htmlaudio, true);
            } else {
                $htmlaudio = array("url" => $url, "title" => $title);
                $fallback = $wphtml5playerclass->audioreplaceJSON(null, $htmlaudio, true);
                //print_r($htmlaudio);
            }
            if(preg_match("#(webOS|SymbianOS|Nokia|Android)#i", $_SERVER['HTTP_USER_AGENT']) &&
                    (defined('WPHTML5_PREVENT_FLASH_LITE') && !$wphtml5playerclass->getOption('xmlMode'))) {
                return $fallback;
            }
            $array = array("url" => $url, "title" => $title, "plugins" => $plugins);
            $this->audioCompatible($array, true);
            return $this->getFlashObject($fallback);
        } else {
            return "";
        }
    }

    private function fullJSON($json) {
        $fallback = "";
        if(isset($json["htmlvideo"]) || isset($json["htmlaudio"])) {
            global $wphtml5playerclass;
        }
        if(isset($json["htmlvideo"])) {
            $fallback = $wphtml5playerclass->videoreplaceJSON(null, $json["htmlvideo"], true);
        } elseif(isset($json["htmlaudio"])) {
            $fallback = $wphtml5playerclass->audioreplaceJSON(null, $json["htmlaudio"], true);
        }
        if(isset($json["htmlvideo"]) || isset($json["htmlaudio"])) {
            if(preg_match("#(webOS|SymbianOS|Nokia|Android)#i", $_SERVER['HTTP_USER_AGENT']) &&
                    (defined('WPHTML5_PREVENT_FLASH_LITE') && !$wphtml5playerclass->getOption('xmlMode'))) {
                return $fallback;
            }
        }
        if(!(isset($json["width"]) && isset($json["height"]))) {
            $width = false;
            $height = false;
        } elseif (!(is_numeric($json["width"]) && is_numeric($json["height"]))) {
            $width = false;
            $height = false;
        } else {
            $width = (int)$json["width"];
            $height = (int)$json["height"];
        }
        if(!isset($json["title"]) || is_array($json["title"])) {
            $title = false;
        } else {
            $title = htmlspecialchars($json["title"]);
        }
        unset($json["htmlvideo"]);
        unset($json["htmlaudio"]);
        unset($json["width"]);
        unset($json["height"]);
        unset($json["title"]);
        $flashvars = $this->flowPlayerConfig($json, null, '_FULL');
        $flashvars = 'config='.json_encode($flashvars);
        $movie = $this->flowplayer;
        if(!($width && $height)) {
            $width = 480;
            $height = 320;
        }
        $flashobject['attribs'] = array(
                "type" => "application/x-shockwave-flash",
                "data" => $movie,
                "width" => $width,
                "height" => $height

        );
        $flashobject['params'] = array(
                "movie" => $movie,
                "allowfullscreen" => "true",
                "cachebusting" => "true",
                "bgcolor" => "#000000",
                "flashvars" => $flashvars
        );
        if($this->option['videoClassNameForTag']) {
            $flashobject['attribs']['class'] = $this->option['videoClassNameForTag'];
        }
        if($title) {
            $flashobject['attribs']['title'] = $title;
        }
        $this->setUpFlash($flashobject);
        return $this->getFlashObject($fallback);
    }

    public function getFlashObject($fallback = "") {
        if($this->option['flashIsSetup']) {
            $object_attribs = $object_params = '';

            foreach ($this->object_attribs as $param => $value) {
                $object_attribs .= '  ' . $param . '="' . $value . '"';
            }

            foreach ($this->object_params as $param => $value) {
                $object_params .= '<param name="' . $param . '" value=\'' . $value . '\' />';
            }
            $this->option['flashIsSetup'] = false;
            return sprintf("<object %s> %s  %s</object>", $object_attribs, $object_params, $fallback);
        } else {
            return $fallback;
        }
    }

    public function videoCompatible($data, $tag = false) {
        $url = $data["url"];
        $width = $data["width"];
        $height = $data["height"];
        $poster = $data["poster"];
        $pluginConfig = $data["plugins"];
        $title = $data["title"];
        if(preg_match("#\.(mp4|m4v)$#i",$url) && !$this->option['flashIsSetup'] &&
                ($this->option['videoFlowPlayerEnabled'] || $tag) && $this->flowplayer) {
            if(!($width && $height)) {
                $width = 480;
                $height = 320;
            }
            $flashvars = "";
            if($poster) {
                $flashvars = array(
                        "playlist" => array(
                                array(
                                        "url" => $poster
                                ),
                                array(
                                        "url" => $url,
                                        "autoPlay" => false,
                                        "scaling" => "fit"
                                )
                        )
                );
            } else {
                $flashvars = array(
                        "clip" => array(
                                "url" => $url,
                                "autoPlay" => false,
                                "scaling" => "fit"
                        )
                );
            }
            $flashvars['plugins']['controls']['fullscreen'] = false;
            if(defined('FLOWPLAYER_RANGE_REQUESTS')) {
                $flashvars['plugins']['pseudo'] = array(
                    'url' => 'flowplayer.pseudostreaming.swf',
                    'rangeRequests' => true
                );
                if(isset($flashvars['playlist'])) {
                    $flashvars['playlist'][1]['provider'] = 'pseudo';
                } else {
                    $flashvars['clip']['provider'] = 'pseudo';
                }
            }
            $flashvars['canvas']['backgroundGradient'] = "none";
            $flashvars = $this->flowPlayerConfig($flashvars, $pluginConfig);
            $flashvars = 'config='.json_encode($flashvars);
            $movie = $this->flowplayer;
            $flashobject['attribs'] = array(
                    "type" => "application/x-shockwave-flash",
                    "data" => $movie,
                    "width" => $width,
                    "height" => $height
            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "true",
                    "cachebusting" => "true",
                    "bgcolor" => "#000000",
                    "flashvars" => $flashvars
            );
            if($this->option['videoClassNameForTag'] && $tag) {
                $flashobject['attribs']['class'] = $this->option['videoClassNameForTag'];
            } elseif($this->option['videoClassName'] && !$tag) {
                $flashobject['attribs']['class'] = $this->option['videoClassName'];
            }
            if($title) {
                $flashobject['attribs']['title'] = $title;
            }
            $this->setUpFlash($flashobject);
        }
    }

    public function audioCompatible($data, $tag = false) {
        $url = $data['url'];
        $pluginConfig = $data['plugins'];
        $title = $data['title'];
        if(preg_match("#\.(mp3|aac|m4a)$#i",$url) && !$this->option['flashIsSetup'] &&
                ($this->option['audioFlowPlayerEnabled'] || $tag) && $this->flowplayer) {
            $flashvars = array(
                    "plugins" => array(
                            "controls" => array(
                                    "height" => 30,
                                    "autoHide" => false
                            )
                    ),
                    "clip" => array(
                            "autoPlay" => false,
                            "url" => $url
                    ),
                    "playlist" => array(
                            array(
                                    "autoPlay" => false,
                                    "url" => $url
                            )
                    )
            );
            $flashvars = $this->flowPlayerConfig($flashvars, $pluginConfig, '_AUDIO');
            unset($flashvars["play"]);
            $flashvars["play"]["opacity"] = 0;
            $flashvars["plugins"]["controls"]["fullscreen"] = false;
            $flashvars = 'config='.json_encode($flashvars);
            $movie = $this->flowplayer;
            $flashobject['attribs'] = array(
                    "type" => "application/x-shockwave-flash",
                    "data" => $movie,
                    "width" => "400",
                    "height" => "30",
            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "false",
                    "cachebusting" => "true",
                    "bgcolor" => "#000000",
                    "flashvars" => $flashvars
            );
            if($this->option['audioClassNameForTag'] && $tag) {
                $flashobject['attribs']['class'] = $this->option['audioClassNameForTag'];
            } elseif($this->option['audioClassName'] && !$tag) {
                $flashobject['attribs']['class'] = $this->option['audioClassName'];
            }
            if($title) {
                $flashobject['attribs']['title'] = $title;
            }
            $this->setUpFlash($flashobject);
        }
    }

    private function flowPlayerConfig($flashvars, $pluginConfig = false, $type = '') {
        global $wphtml5playerclass;
        if(defined("FLOWPLAYER_JSON".$type)) {
            $flowPlayerJSON = json_decode(constant("FLOWPLAYER_JSON".$type), true);
            if(!$wphtml5playerclass->is_assoc($flowPlayerJSON)) {
                $flowPlayerJSON = false;
                if($type == "_AUDIO") {
                    echo $wphtml5playerclass->jsonError() . " @ Flowplayer Configuration for Audio";
                } elseif($type == "_FULL") {
                    echo $wphtml5playerclass->jsonError() . " @ Flowplayer Configuration for Full Control Mode";
                } else {
                    echo $wphtml5playerclass->jsonError() . " @ Flowplayer Configuration for Video";
                }
            } else {
                unset($flowPlayerJSON["clip"]);
                unset($flowPlayerJSON["playlist"]);
            }
        } else {
            $flowPlayerJSON = false;
        }
        if($this->flowplayerConfig) {
            $flashvars = $this->array_replace_recursive($flashvars, $this->flowplayerConfig);
        }
        if($flowPlayerJSON) {
            $flashvars = $this->array_replace_recursive($flashvars, $flowPlayerJSON);
            unset($flowPlayerJSON);
        }
        if($pluginConfig) {
            $flashvars["plugins"] = $this->array_replace_recursive($flashvars["plugins"], $pluginConfig);
            unset($pluginConfig);
        }
        return $flashvars;
    }

    private function array_replace_recursive() {
        $arrays = func_get_args();
        $original = array_shift($arrays);
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $original[$key] = $this->array_replace_recursive($original[$key], $array[$key]);
                } else {
                    $original[$key] = $value;
                }
            }
        }
        return $original;
    }
}