<?php

class flowplayer {

    const THEID = "flowplayer-";

    private $object_attribs;
    private $object_params;
    private $option;
    private $count;
    private $location;

    public function __construct($location) {
        $this->location = $location;
        $this->defaultOption();
        $this->count = 1;
    }

    private function defaultOption() {
        $this->option = array(
                'flashIsSetup' => false,
                'swfobject' => false,
                'videoClassName' => false,
                'audioClassName' => false,
                'videoClassNameForTag' => false,
                'audioClassNameForTag' => false,
                'videoFlowPlayerEnabled' => true,
                'audioFlowPlayerEnabled' => true
        );
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

    public function flowPlayerJSON($json) {
        $jsonTemp = preg_replace('~(&#(8220|8221|8243);|“|”)~','"',$json[1]);
        $jsonTemp = preg_replace('#((,){0,1}<(.*?)>){0,}(("){1}(.){0,}(<(.*?)>){1,}(.){0,}("){1}){0,}#i', '$2$4', $jsonTemp);
        $jsonTemp = preg_replace('#("|}|]){1}(,){0,1}(<(.*?)>){0,}("|}|]){1}#i', '$1$2$5', $jsonTemp);
        $jsonTemp = preg_replace('#(.){1}(\n){1}(<(.*?)>){0,1}("){1}#i', '$1$3$5', $jsonTemp);
        $jsonTemp = json_decode($jsonTemp, true);
        global $wphtml5playerclass;
        if($wphtml5playerclass->is_assoc($jsonTemp)) {
            $json = $jsonTemp;
            unset($jsonTemp);
            $jsonTemp = array();
            foreach($json as $key => $value) {
                $jsonTemp[strtolower($key)] = $value;
            }
            if(isset($jsonTemp["video"])) {
                return $this->videoJSON($jsonTemp["video"]);
            } elseif(isset($jsonTemp["audio"])) {
                return $this->audioJSON($jsonTemp["audio"]);
            } elseif(isset($jsonTemp["full"])) {
                return $this->fullJSON($jsonTemp["full"]);
            } else {
                return "video or audio is not set.";
            }
        } else {
            return $wphtml5playerclass->jsonError();
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
            if($poster) {
                $temp[0] = $poster;
                $temp = $wphtml5playerclass->urlsCheck($temp);
                $poster = $temp[0];
                unset($temp);
            }
            unset($json);
            if($htmlvideo) {
                $fallback = $wphtml5playerclass->videoreplaceJSON(null, $htmlvideo, true);
            } else {
                $htmlvideo = array ("url" => $url, "poster" => $poster);
                $fallback = $wphtml5playerclass->videoreplaceJSON(null, $htmlvideo, true);
            }
            $this->videoCompatible($url, $width, $height, $poster, true, $plugins);
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
            unset($json);
            if($htmlaudio) {
                $fallback = $wphtml5playerclass->audioreplaceJSON(null, $htmlaudio, true);
            } else {
                $htmlaudio = array("url" => $url);
                $fallback = $wphtml5playerclass->audioreplaceJSON(null, $htmlaudio, true);
                //print_r($htmlaudio);
            }
            $this->audioCompatible($url, true, $plugins);
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
        unset($json["htmlvideo"]);
        unset($json["htmlaudio"]);
        unset($json["width"]);
        unset($json["height"]);
        $flashvars = $this->flowPlayerConfig($json);
        $flashvars = 'config='.json_encode($flashvars);
        if(defined("FLOWPLAYER_URL")) {
            $movie = FLOWPLAYER_URL;
        } else {
            $movie = $this->location."/inc/flowplayer.swf";
        }
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
                "flashvars" => $flashvars
        );
        if($this->option['videoClassNameForTag']) {
            $flashobject['attribs']['class'] = $this->option['videoClassNameForTag'];
        }
        $this->setUpFlash($flashobject);
        return $this->getFlashObject($fallback);
    }

    private function getSWFobject() {
        if($this->option['swfobject']) {
            $this->object_attribs['id'] = self::THEID.$this->count;
            $swfobject = '<script type="text/javascript">'.
                    'swfobject.registerObject("'.$this->object_attribs['id'].'", "9.0.115")</script>';
            $this->count++;
            return $swfobject;
        } else {
            return "";
        }
    }

    public function getFlashObject($fallback = "") {
        if($this->option['flashIsSetup']) {
            $object_attribs = $object_params = '';
            $swfobject = $this->getSWFobject();

            foreach ($this->object_attribs as $param => $value) {
                $object_attribs .= '  ' . $param . '="' . $value . '"';
            }

            foreach ($this->object_params as $param => $value) {
                $object_params .= '<param name="' . $param . '" value=\'' . $value . '\' />';
            }
            $this->option['flashIsSetup'] = false;
            return sprintf("%s<object %s> %s  %s</object>", $swfobject, $object_attribs, $object_params, $fallback);
        } else {
            return "";
        }
    }

    public function videoCompatible($url, $width, $height, $poster, $tag = false, $pluginConfig = false) {
        if(preg_match("#(mp4|m4v)$#i",$url) && !$this->option['flashIsSetup'] &&
                ($this->option['videoFlowPlayerEnabled'] || $tag)) {
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
                                        "autoPlay" => false
                                )
                        )
                );
            } else {
                $flashvars = array(
                        "clip" => array(
                                "url" => $url,
                                "autoPlay" => false
                        )
                );
            }
            $flashvars['plugins'] = array(
                    "controls" => array(
                            "fullscreen" => false
                    ),
            );
            $flashvars = $this->flowPlayerConfig($flashvars, $pluginConfig);
            $flashvars = 'config='.json_encode($flashvars);
            if(defined("FLOWPLAYER_URL")) {
                $movie = FLOWPLAYER_URL;
            } else {
                $movie = $this->location."/inc/flowplayer.swf";
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
                    "flashvars" => $flashvars
            );
            if($this->option['videoClassNameForTag'] && $tag) {
                $flashobject['attribs']['class'] = $this->option['videoClassNameForTag'];
            } elseif($this->option['videoClassName'] && !$tag) {
                $flashobject['attribs']['class'] = $this->option['videoClassName'];
            }
            $this->setUpFlash($flashobject);
        }
    }

    public function audioCompatible($url, $tag = false, $pluginConfig = false) {
        if(preg_match("#(mp3|aac|m4a)$#i",$url) && !$this->option['flashIsSetup'] &&
                ($this->option['audioFlowPlayerEnabled'] || $tag)) {
            $flashvars = array(
                    "plugins" => array(
                            "controls" => array(
                                    "fullscreen" => false,
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
            $flashvars = $this->flowPlayerConfig($flashvars, $pluginConfig);
            $flashvars = 'config='.json_encode($flashvars);
            if(defined("FLOWPLAYER_URL")) {
                $movie = FLOWPLAYER_URL;
            } else {
                $movie = $this->location."/inc/flowplayer.swf";
            }
            $flashobject['attribs'] = array(
                    "type" => "application/x-shockwave-flash",
                    "data" => $movie,
                    "width" => "300",
                    "height" => "30",
            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "true",
                    "cachebusting" => "true",
                    "bgcolor" => "#000000",
                    "flashvars" => $flashvars
            );
            if($this->option['audioClassNameForTag'] && $tag) {
                $flashobject['attribs']['class'] = $this->option['audioClassNameForTag'];
            } elseif($this->option['audioClassName'] && !$tag) {
                $flashobject['attribs']['class'] = $this->option['audioClassName'];
            }
            $this->setUpFlash($flashobject);
        }
    }

    private function flowPlayerConfig($flashvars, $pluginConfig = false) {
        global $wphtml5playerclass;
        if(defined("FLOWPLAYER_JSON")) {
            $flowPlayerJSON = json_decode(FLOWPLAYER_JSON, true);
            if(!$wphtml5playerclass->is_assoc($flowPlayerJSON)) {
                $flowPlayerJSON = false;
            } else {
                unset($flowPlayerJSON["clip"]);
                unset($flowPlayerJSON["playlist"]);
                if(isset($flowPlayerJSON["key"])) {
                    $flowPlayerJSON["key"] = preg_replace("# $#i", "", $flowPlayerJSON["key"]);
                }
            }
        } else {
            $flowPlayerJSON = false;
        }
        if($pluginConfig) {
            if(!$flowPlayerJSON) {
                $flowPlayerJSON = array();
            }
            foreach($pluginConfig as $key => $value) {
                $flowPlayerJSON["plugins"][$key] = $value;
            }
            unset($pluginConfig);
        }
        if ($flowPlayerJSON) {
            if(function_exists("array_replace_recursive")) {
                $temp = array_replace_recursive($flashvars, $flowPlayerJSON);
            } else {
                $temp = $this->array_replace_recursive($flashvars, $flowPlayerJSON);
            }
            $flashvars = $temp;
            unset($temp);
            unset($flowPlayerJSON);
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

?>