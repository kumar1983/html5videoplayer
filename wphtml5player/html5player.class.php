<?php

/**
 * HTML5 Player Class 0.9.0
 * Embed video using shortcodes, using flowplayer as fallback.
 * Copyright (C) 2010, Christopher John Jackson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Embed Videos and Audios into respective HTML5 tags, on fail it falls back to
 * FlowPlayer
 *
 * @author cjackson
 */
class html5player {
    private $url;
    private $root;
    private $flashbuilder;
    private $downloadLinks;
    private $language;

    public function  __construct($url, $siteurl, $root) {
        $this->url['script'] = $url;
        $this->url['site'] = $siteurl;
        $this->root = $root;
        require_once 'inc/buildflashobject.class.php';
        $this->flashbuilder = new buildflashobject();
        $this->defaultLanguage();
    }

    private function defaultLanguage() {
        $this->language = array(
                'noVideo' => "No video playback capabilities, please download the video below\n",
                'noAudio' => "No audio playback capabilities, please download the audio below\n",
                'downloadVideo' => '<strong>Download Video: </strong>',
                'downloadAudio' => '<strong>Download Audio: </strong>',
                'closedFormat' => 'Closed Format: ',
                'openFormat' => 'Open Format: '
        );
    }

    public function setLanguage($param, $value) {
        $this->language[$param] = $value;
    }

    public function setSWFObject($bool) {
        $this->flashbuilder->setOptions('swfobject', $bool);
    }

    public function videoreplace($data) {
        $data = $data[1];
        $data = explode(" ",$data);
        return $this->arrayToOrganisedArrays($data);
    }

    private function arrayToOrganisedArrays($matches) {
        $videourl = $matches[0];
        $videourl = explode("|", $videourl);
        $videourl = $this->urlsCheck($videourl);
        $ifScore = 1;

        //Check for poster url.
        if(isset($matches[$ifScore]) && !is_numeric($matches[$ifScore])) {
            $videooption["poster"] = $matches[$ifScore];
            $ifScore++;
        } else {
            $videooption["poster"] = "";
        }

        //Check for resolution
        if(isset($matches[$ifScore]) && isset($matches[$ifScore+1])) {
            if(is_numeric($matches[$ifScore]) && is_numeric($matches[$ifScore+1])) {
                $videooption["width"] = $matches[$ifScore];
                $videooption["height"] = $matches[$ifScore+1];
                $ifScore+2;
            }
        }
        if(!isset($videooption)) {
            $videooption = "notset";
        }
        return $this->videoCodeGenerator($videourl, $videooption);
    }

    private function videoCodeGenerator($videourl, $videooption) {
        $width = $videooption["width"];
        $height = $videooption["height"];
        $poster = $videooption["poster"];
        $output = '<div class="video-js-box"><video class="video-js" controls>';
        foreach($videourl as $value) {
            $this->flowPlayerVideoCompatible($value, $width, $height, $poster);
            $output .='<source src="'.$value.'" '.$this->videoType($value).' />';
        }
        $links = $this->linkGenerator($this->language['noVideo'].$this->language['downloadVideo']);
        $output .= $this->getFlashObject($links);
        $output .= '</video></div>';
        return $output;
    }

    private function linkGenerator($message) {
        if(isset($this->downloadLinks)) {
            $links = $message;
            if(isset($this->downloadLinks['closed'])) {
                $links .= $this->language['closedFormat'].$this->downloadLinks['closed'];
            }
            if(isset($this->downloadLinks['open'])) {
                $links .= $this->language['openFormat'].$this->downloadLinks['open'];
            }
            unset($this->downloadLinks);
            return $links;
        }
        return "";
    }

    public function audioreplace($data) {
        $audiourl = $data[1];
        $audiourl = explode("|", $audiourl);
        $audiourl = $this->urlsCheck($audiourl);
        return $this->audioCodeGenerator($audiourl);
    }

    private function urlsCheck($urls) {
        $arrayCount = 0;
        foreach($urls as $value) {
            if(!preg_match("#^(http|https)://#i", $value)) {
                $data = $this->url['site']."/".$value;
                $array[$arrayCount] = $data;
            } else {
                $array[$arrayCount] = $value;
            }
            $arrayCount++;
        }
        return $array;
    }

    private function audioCodeGenerator($audiourl) {
        $output = '<audio controls>';
        foreach($audiourl as $value) {
            $this->flowPlayerAudioCompatible($value);
            $output .='<source src="'.$value.'" '.$this->audioType($value).' />';
        }
        $links = $this->linkGenerator($this->language['noAudio'].$this->language['downloadAudio']);
        $output .= $this->getFlashObject($links);
        $output .= '</audio>';
        return $output;
    }

    private function getFlashObject($links) {
        if($this->flashbuilder->getFlashIsSetup()) {
            $this->flashbuilder->setFallback($links);
            return $this->flashbuilder->getFlashObject();
        } else {
            return $links;
        }
    }

    private function flowPlayerVideoCompatible($url, $width, $height, $poster) {
        if(preg_match("#(mp4|m4v)$#i",$url)) {
            if(!(is_numeric($width) && is_numeric($height))) {
                $width = 480;
                $height = 320;
            }
            $flashvars = "";
            if($poster != "") {
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
            $flashvars = 'config='.json_encode($flashvars);
            $movie = $this->url['script']."/inc/flowplayer.swf";
            $flashobject['attribs'] = array(
                    "class" => "vjs-flash-fallback",
                    "width" => $width,
                    "height" => $height,
                    "data" => $movie,
                    "type" => "application/x-shockwave-flash"
            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "false",
                    "flashvars" => $flashvars
            );
            $this->flashbuilder->setUpFlash($flashobject);
        }
    }

    private function flowPlayerAudioCompatible($url) {
        if(preg_match("#(mp3)$#i",$url)) {
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
            $movie = $this->url['script']."/inc/flowplayer.swf";
            $flashobject['attribs'] = array(
                    "width" => "300",
                    "height" => "30",
                    "data" => $movie,
                    "type" => "application/x-shockwave-flash"
            );
            $flashobject['params'] = array(
                    "movie" => $movie,
                    "allowfullscreen" => "false",
                    "cachebusting" => "true",
                    "bgcolor" => "#000000",
                    "flashvars" => $flashvars
            );
            $this->flashbuilder->setUpFlash($flashobject);
        }
    }

    private function videoType($url) {
        if(preg_match("#(mp4|m4v)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP4</a> ';
            return "type='video/mp4; codecs=\"avc1.42E01E, mp4a.40.2\"'";
        }
        if(preg_match("#(ogg|ogv)$#i",$url)) {
            $this->downloadLinks['open'] .= '<a href="'.$url.'">OGG</a> ';
            return "type='video/ogg; codecs=\"theora, vorbis\"'";
        }
        if(preg_match("#(webm)$#i",$url)) {
            $this->downloadLinks['open'] .= '<a href="'.$url.'">WebM</a> ';
            return "type='video/webm; codecs=\"vp8, vorbis\"'";
        }

        return "";
    }

    private function audioType($url) {
        if(preg_match("#(ogg|oga)$#i",$url)) {
            $this->downloadLinks['open'] .= '<a href="'.$url.'">OGG</a> ';
            return 'type="audio/ogg"';
        }
        if(preg_match("#(mp4|m4a|aac)$#i",$url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">AAC</a> ';
            return 'type="audio/aac"';
        }
        if(preg_match("#(mp3)$#i",$url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP3</a> ';
            return 'type="audio/mpeg"';
        }
        if(preg_match("#(wav)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">WAV</a> ';
            return 'type="audio/x-wav"';
        }

        return "";
    }

    public function script($name) {
        $script = array(
                'videojs' => $this->url['script'].'/inc/video.js',
                'videojscss' => $this->url['script'].'/inc/video-js.css'
        );

        return $script[$name];
    }

    public function httpHead() {
        $output = '<script type="text/javascript">window.onload = function(){ VideoJS.setup(); }</script>';
        return $output;
    }
}
?>
