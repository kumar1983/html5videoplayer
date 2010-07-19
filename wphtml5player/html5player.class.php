<?php

/**
 * HTML5 Player Class 0.9.1
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
    private $flowplayer;
    private $downloadLinks;
    private $language;

    public function  __construct($url, $siteurl, $root) {
        $this->url['script'] = $url;
        $this->url['site'] = $siteurl;
        $this->root = $root;
        $this->downloadLinks['open'] = false;
        $this->downloadLinks['closed'] = false;
        require_once 'inc/flowplayer.class.php';
        $this->flowplayer = new flowplayer();
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
        $this->flowplayer->setOptions('swfobject', $bool);
    }

    public function videoreplace($data) {
        $data = $data[1];
        $data = explode(" ",$data);
        return $this->arrayToOrganisedArrays($data);
    }

    private function arrayToOrganisedArrays($matches) {
        $videourls = $matches[0];
        $videourls = explode("|", $videourls);
        $videourls = $this->urlsCheck($videourls);
        $ifScore = 1;

        //Check for poster url.
        if(isset($matches[$ifScore]) && !is_numeric($matches[$ifScore])) {
            $url[0] = $matches[$ifScore];
            $url = $this->urlsCheck($url);
            $videooption["poster"] = $url[0];
            $ifScore++;
        } else {
            $videooption["poster"] = false;
        }

        $resolutionset = false;
        //Check for resolution
        if(isset($matches[$ifScore]) && isset($matches[$ifScore+1])) {
            if(is_numeric($matches[$ifScore]) && is_numeric($matches[$ifScore+1])) {
                $videooption["width"] = $matches[$ifScore];
                $videooption["height"] = $matches[$ifScore+1];
                $ifScore+2;
                $resolutionset = true;
            }
        }
        if(!$resolutionset) {
            $videooption["width"] = false;
            $videooption["height"] = false;
        }


        return $this->videoCodeGenerator($videourls, $videooption);
    }

    private function videoCodeGenerator($videourls, $videooption) {
        $width = $videooption["width"];
        $height = $videooption["height"];
        $poster = $videooption["poster"];
        $source = '';
        foreach($videourls as $value) {
            $this->flowplayer->videoCompatible($value, $width, $height, $poster, $this->url['script']);
            $source .='<source src="'.$value.'" '.$this->videoType($value).' />';
        }
        $noVideo = $this->language['noVideo'].$this->language['downloadVideo'];
        $links = ""; $outside = "";
        if($this->operaMobileCheck()) {
            $outside = '<br />'.$this->linkGenerator();
        } else {
            $links = '<br />'.$this->linkGenerator();
        }
        $header = '<video '.$this->getPoster($poster).' controls preload="none" >';
        $footer = '</video>';
        return sprintf('%s %s %s %s %s', $header, $source,
                $this->getFallback($this->getPosterForFallback($poster).$noVideo.$links), $footer, $outside);
    }

    private function getPosterForFallback($poster) {
        if($poster) {
            return '<img src="'.$poster.'" /><br />';
        } else {
            return "";
        }
    }

    private function getPoster($poster) {
        if(!$poster) {
            return "";
        }
        $return = 'poster="'.$poster.'"';
        if(preg_match('#(iPod|iPhone|iPad)#',$_SERVER['HTTP_USER_AGENT'])) {
            if(preg_match("#AppleWebKit/([0-9]+)(\.|\+|:space:)#", $_SERVER['HTTP_USER_AGENT'],
            $matches)) {
                $WebKitVersion = (int)$matches[1];
                if($WebKitVersion >= 420 && $WebKitVersion < 532) {
                    $return = "";
                }
            }
        }
        return $return;
    }

    private function linkGenerator() {
        if(isset($this->downloadLinks)) {
            if($this->downloadLinks['closed']) {
                $links .= $this->language['closedFormat'].$this->downloadLinks['closed'];
            }
            if($this->downloadLinks['open']) {
                $links .= $this->language['openFormat'].$this->downloadLinks['open'];
            }
            $this->downloadLinks['open'] = false;
            $this->downloadLinks['closed'] = false;
            return $links;
        }
        return "";
    }

    public function audioreplace($data) {
        $audiourls = $data[1];
        $audiourls = explode("|", $audiourls);
        $audiourls = $this->urlsCheck($audiourls);
        return $this->audioCodeGenerator($audiourls);
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

    private function audioCodeGenerator($audiourls) {
        $source = '';
        foreach($audiourls as $value) {
            $this->flowplayer->audioCompatible($value, $this->url['script']);
            $source .='<source src="'.$value.'" '.$this->audioType($value).' />';
        }
        $noAudio = $this->language['noAudio'].$this->language['downloadAudio'];
        $links = ""; $outside = "";
        if($this->operaMobileCheck()) {
            $outside = '<br />'.$this->linkGenerator();
        } else {
            $links = '<br />'.$this->linkGenerator();
        };
        $header = '<audio controls>';
        $footer = '</audio>';
        return sprintf('%s %s %s %s %s', $header, $source, $this->getFallback($noAudio.$links), $footer, $outside);
    }

    private function getFallback($fallback) {
        if($this->flowplayer->getFlashIsSetup()) {
            $this->flowplayer->setFallback($fallback);
            return $this->flowplayer->getFlashObject();
        } else {
            return $fallback;
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

    private function operaMobileCheck() {
        if(preg_match("#(Opera Mini|Opera Mobi)#",$_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }
}
?>
