<?php

class flowplayer {

    const THEID = "flowplayer-";

    private $object_attribs;
    private $object_params;
    private $fallback;
    private $option;
    private $count;

    public function __construct() {
        $this->defaultOption();
        $this->count = 1;
    }

    private function defaultOption() {
        $this->option = array(
            'flashIsSetup' => false,
            'swfobject' => false,
            'videoClassName' => false,
            'audioClassName' => false,
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

    public function setFallback($fallback) {
        $this->fallback = $fallback;
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

    public function getFlashObject() {
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
            return sprintf("%s<object %s> %s  %s</object>", $swfobject, $object_attribs, $object_params, $this->fallback);
        } else {
            return "";
        }
    }

    public function videoCompatible($url, $width, $height, $poster, $root) {
        if(preg_match("#(mp4|m4v)$#i",$url) && !$this->option['flashIsSetup'] &&
                $this->option['videoFlowPlayerEnabled']) {
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
            $flashvars = 'config='.json_encode($flashvars);
            $movie = $root."/inc/flowplayer.swf";
            $flashobject['attribs'] = array(
                    "type" => "application/x-shockwave-flash",
                    "data" => $movie,
                    "width" => $width,
                    "height" => $height

            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "false",
                    "flashvars" => $flashvars
            );
            if($this->option['videoClassName']) {
                $flashobject['attribs']['class'] = $this->option['videoClassName'];
            }
            $this->setUpFlash($flashobject);
        }
    }

    public function audioCompatible($url, $root) {
        if(preg_match("#(mp3)$#i",$url) && !$this->option['flashIsSetup'] &&
                $this->option['audioFlowPlayerEnabled']) {
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
                    "playerId" => "audio",
                    "playlist" => array(
                            array(
                                    "autoPlay" => false,
                                    "url" => $url
                            )
                    )
            );
            $flashvars = 'config='.json_encode($flashvars);
            $movie = $root."/inc/flowplayer.swf";
            $flashobject['attribs'] = array(
                    "type" => "application/x-shockwave-flash",
                    "data" => $movie,
                    "width" => "300",
                    "height" => "30",
            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "false",
                    "cachebusting" => "true",
                    "bgcolor" => "#000000",
                    "flashvars" => $flashvars
            );
            if($this->option['audioClassName']) {
                $flashobject['attribs']['class'] = $this->option['audioClassName'];
            }
            $this->setUpFlash($flashobject);
        }
    }
}

?>
