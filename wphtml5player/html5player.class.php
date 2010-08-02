<?php

/**
 * HTML5 Audio and Video Framework Class 1.2.1
 * A Highly Customisable HTML5 Audio and Video Framework for Wordpress
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
    const HTML5_TAG = '<!-- Generated by HTML5 Player Plugin (http://code.google.com/p/html5videoplayer) -->';

    private $url;
    private $root;
    private $flowplayer;
    private $downloadLinks;
    private $language;
    private $option;
    private $count;
    private $htmlAttribute;
    private $tag;

    public function  __construct($url, $siteurl, $root) {
        $this->url['script'] = $url;
        $this->url['site'] = $siteurl;
        $this->root = $root;
        $this->downloadLinks['open'] = false;
        $this->downloadLinks['closed'] = false;
        require_once 'inc/flowplayer.class.php';
        $this->flowplayer = new flowplayer();
        $this->defaultLanguage();
        $this->defaultOption();
        $this->defaultHtmlAttribute();
        $this->count = array("video" => 0, "audio" => 0);
        $this->tag = array("video" => "video", "audio" => "audio");
    }

    private function defaultOption() {
        $this->option = array(
                'xmlMode' => false,
                'videoID' => false,
                'beforeVideo' => '',
                'afterVideo' => '',
                'videoScript' => false,
                'audioID' => false,
                'beforeAudio' => '',
                'afterAudio' => '',
                'audioScript' => false,
                'videoLinkOutside' => false,
                'audioLinkOutside' => false,
                'videoLinkOutsideBefore' => '<p>',
                'videoLinkOutsideAfter' => '</p>',
                'audioLinkOutsideBefore' => '<p>',
                'audioLinkOutsideAfter' => '</p>'
        );
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

    private function defaultHtmlAttribute() {
        $this->htmlAttribute = array(
            'video' => array(
                'controls' => null,
                'preload' => "none"
            ),
            'audio' => array(
                'controls' => null
            )
        );
    }

    public function setVideoAttribute($key, $value) {
        $this->htmlAttribute["video"][strtolower($key)] = $value;
    }

    public function setAudioAttribute($key, $value) {
        $this->htmlAttribute["audio"][strtolower($key)] = $value;
    }

    public function setLanguage($key, $value) {
        $this->language[$key] = $value;
    }

    public function setOption($key, $value) {
        $this->option[$key] = $value;
    }

    public function setTag($key, $value) {
        $this->tag[$key] = $value;
    }

    public function getTag($key) {
        return $this->tag[$key];
    }

    public function setFlowPlayerOption($key, $value) {
        $this->flowplayer->setOptions($key, $value);
    }

    public function videoreplace($data) {
        $data = $data[1];
        $data = explode(" ",$data);
        return $this->arrayToOrganisedArrays($data);
    }

    public function videoreplaceJSON($data) {
        $json = str_replace('&#8220;','"',$data[1]);
        $json = str_replace('&#8221;','"',$json);
        $json = str_replace('<br />','',$json);
        $json = json_decode($json, true);
        if(is_array($json)) {
            if(!isset($json["url"])) {
                return "ERROR: url not specified";
            } elseif ($this->is_assoc($json["url"]) && !is_string($json["url"])) {
                return "ERROR: url as object is not allowed";
            }
            if(!is_array($json["url"])) {
                $array[0] = $json['url'];
                $json['url'] = $array;
            }
            if(!(isset($json["width"]) && isset($json["height"]))) {
                $json["width"] = false;
                $json["height"] = false;
            } elseif (!(is_numeric($json["width"]) && is_numeric($json["height"]))) {
                $json["width"] = false;
                $json["height"] = false;
            } else {
                $json["width"] = (int)$json["width"];
                $json["height"] = (int)$json["height"];
            }
            if(!isset($json["poster"]) || is_array($json["poster"])) {
                $json["poster"] = false;
            } elseif (!preg_match("#.(jpg|jpeg|png|gif)$#i", $json["poster"])) {
                $json["poster"] = false;
            }
            if($json["poster"]) {
                $url[0] = $json["poster"];
                $url = $this->urlsCheck($url);
                $json["poster"] = $url[0];
            }
            if(!isset($json["title"]) || is_array($json["title"])) {
                $json["title"] = false;
            } else {
                $json["title"] = htmlspecialchars($json["title"]);
            }
            if(!isset($json["attribute"])) {
                $json["attribute"] = false;
            } elseif(!$this->is_assoc($json["attribute"])) {
                $json["attribute"] = false;
            }
            return $this->videoCodeGenerator("", "", $json);
        } else {
            return $this->jsonError()." @ Video Tag";
        }
    }

    private function is_assoc($var) {
        return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
    }

    public function jsonError() {
        $json_errors = array(
                JSON_ERROR_NONE => 'No error has occurred',
                JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
                JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
                JSON_ERROR_SYNTAX => 'Syntax error',
        );
        return "JSON ERROR: ".$json_errors[json_last_error()];
    }

    private function arrayToOrganisedArrays($matches) {
        $videourls = $matches[0];
        $videourls = explode("|", $videourls);
        $videourls = $this->urlsCheck($videourls);
        $ifScore = 1;

        $videooption["poster"] = false;
        //Check for poster url.
        if(isset($matches[$ifScore]) && !is_numeric($matches[$ifScore])) {
            if(preg_match("#.(jpg|jpeg|png|gif)$#i", $matches[$ifScore])) {
                $url[0] = $matches[$ifScore];
                $url = $this->urlsCheck($url);
                $videooption["poster"] = $url[0];
            }
            $ifScore++;
        }

        $videooption["width"] = false;
        $videooption["height"] = false;
        //Check for resolution
        if(isset($matches[$ifScore]) && isset($matches[$ifScore+1])) {
            if(is_numeric($matches[$ifScore]) && is_numeric($matches[$ifScore+1])) {
                $videooption["width"] = (int)$matches[$ifScore];
                $videooption["height"] = (int)$matches[$ifScore+1];
                $ifScore+2;
            }
        }
        return $this->videoCodeGenerator($videourls, $videooption, false);
    }

    private function videoCodeGenerator($videourls, $videooption, $JSON) {
        if($JSON) {
            $videourls = $this->urlsCheck($JSON["url"]);
            $width = $JSON["width"];
            $height = $JSON["height"];
            $poster = $JSON["poster"];
            $title = $JSON["title"];
            $attribute = $JSON["attribute"];
            unset($JSON);
        } else {
            $width = $videooption["width"];
            $height = $videooption["height"];
            $poster = $videooption["poster"];
            $title = false;
            $attribute = false;
        }
        $sources = '';
        foreach($videourls as $value) {
            $this->flowplayer->videoCompatible($value, $width, $height, $poster, $this->url['script']);
            $source ='<source src="'.$value.'" '.$this->videoType($value).' />';
            if(((preg_match("#.(ext|main).(mp4|m4v)$#i", $value) && $this->buggyiOS() &&
                    !preg_match('#iPad#',$_SERVER['HTTP_USER_AGENT'])) ||
                    (preg_match("#.(ogv|ogg|webm|high\.(mp4|m4v))$#i", $value) && $this->buggyiOS()))
                            && !$this->option['xmlMode']) {
                $source = '';
            }
            $sources .= $source;
        }
        $noVideo = $this->language['noVideo'].$this->language['downloadVideo'];
        $links = $outside = "";
        if($this->mobileCheck() || $this->option['videoLinkOutside']) {
            $outside = '<br />'.$this->option['videoLinkOutsideBefore'].$this->linkGenerator().
                    $this->option['videoLinkOutsideAfter'];
        } else {
            $links = '<br />'.$this->linkGenerator();
        }
        $header = sprintf("%s<video %s %s %s %s %s>", $this->option['beforeVideo'],
                $this->getID($this->option['videoID'], "video"),
                $this->getPoster($poster), $this->getResolutionCode($width, $height),
                $this->getTitle($title), $this->getHtmlAttribute($attribute, "video"));
        $footer = "</video>";
        return sprintf('%s %s %s %s %s %s %s %s', $this->getJavaScriptCall($this->option['videoScript'], "video"), $header, $sources,
                $this->getFallback($this->getPosterForFallback($poster).$noVideo.$links), self::HTML5_TAG, $footer, $outside,
                $this->option['afterVideo']);
    }

    private function getID($name, $type) {
        if($name) {
            $this->count[$type]++;
            return 'id="'.$name.'-'.$this->count[$type].'"';
        } else {
            return "";
        }
    }

    private function getJavaScriptCall($script, $type) {
        if($script && $this->option[$type.'ID']) {
            $output = sprintf($script, $this->option[$type.'ID'].'-'.$this->count[$type]);
            return sprintf("<script type='text/javascript'>%s</script>",$output);
        } else {
            return "";
        }
    }

    private function getHtmlAttribute($attribute, $type) {
        $htmlAtrribute = $this->htmlAttribute[$type];
        if($attribute) {
            foreach($attribute as $key => $value) {
                unset($htmlAtrribute[strtolower($key)]);
                $htmlAtrribute[strtolower($key)] = $value;
            }
        }

        unset($attribute);
        // Unset banned Attribute Start
        unset($htmlAtrribute['id']);
        unset($htmlAtrribute['title']);
        unset($htmlAtrribute['width']);
        unset($htmlAtrribute['height']);
        unset($htmlAtrribute['poster']);
        // Unset banned Attrbute End
        
        $htmlAttri = "";
        foreach($htmlAtrribute as $key => $value) {
            if($value == null) {
                $htmlAttri .= $key." ";
            } else {
                $htmlAttri .= $key.'="'.htmlspecialchars($value).'" ';
            }
        }
        return $htmlAttri;
    }

    private function getPosterForFallback($poster) {
        if($poster) {
            return '<img src="'.$poster.'" /><br />';
        } else {
            return "";
        }
    }

    private function getPoster($poster) {
        if(!$poster || $this->buggyiOS()) {
            return "";
        }
        return 'poster="'.$poster.'"';
    }

    private function getTitle($title) {
        if($title) {
            return 'title="'.$title.'"';
        }
        return "";
    }

    private function buggyiOS() {
        if(preg_match('#(iPod|iPhone|iPad)#',$_SERVER['HTTP_USER_AGENT'])) {
            if(preg_match("#AppleWebKit/([0-9]+)(\.|\+|:space:)#", $_SERVER['HTTP_USER_AGENT'],
            $matches)) {
                $WebKitVersion = (int)$matches[1];
                if($WebKitVersion >= 420 && $WebKitVersion < 532) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getResolutionCode($width, $height) {
        if(preg_match('#((webOS|SymbianOS|Nokia)+?AppleWebKit|AppleWebKit(.*?)Mobile)#',$_SERVER['HTTP_USER_AGENT']) &&
                !preg_match('#iPad#',$_SERVER['HTTP_USER_AGENT'])) {
            return 'width="270"';
        }
        if(!($width && $height)) {
            return "";
        }
        return 'width="'.$width.'" height="'.$height.'"';
    }

    private function linkGenerator() {
        if(isset($this->downloadLinks)) {
            $links = "";
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
        return $this->audioCodeGenerator($audiourls, false);
    }

    public function audioreplaceJSON($data) {
        $json = str_replace('&#8220;','"',$data[1]);
        $json = str_replace('&#8221;','"',$json);
        $json = str_replace('<br />','',$json);
        $json = json_decode($json, true);
        if($json) {
            if(!isset($json["url"])) {
                return "ERROR: url not specified";
            } elseif ($this->is_assoc($json["url"]) && !is_string($json["url"])) {
                return "ERROR: url as object is not allowed";
            }
            if(!is_array($json["url"])) {
                $array[0] = $json['url'];
                $json['url'] = $array;
            }
            if(!isset($json["title"]) || is_array($json["title"])) {
                $json["title"] = false;
            } else {
                $json["title"] = htmlspecialchars($json["title"]);
            }
            if(!isset($json["attribute"])) {
                $json["attribute"] = false;
            } elseif(!$this->is_assoc($json["attribute"])) {
                $json["attribute"] = false;
            }
            return $this->audioCodeGenerator("", $json);
        } else {
            return $this->jsonError()." @ Audio Tag";
        }
    }

    private function urlsCheck($urls) {
        $arrayCount = 0;
        foreach($urls as $value) {
            if(!preg_match("#^(http|https)://#i", $value)) {
                $data = $this->url['site']."/".$value;
                $array[$arrayCount] = htmlspecialchars($data);
            } else {
                $array[$arrayCount] = htmlspecialchars($value);
            }
            $arrayCount++;
        }
        return $array;
    }

    private function audioCodeGenerator($audiourls, $JSON) {
        if($JSON) {
            $audiourls = $this->urlsCheck($JSON['url']);
            $title = $JSON['title'];
            $attribute = $JSON["attribute"];
            unset($JSON);
        } else {
            $title = false;
            $attribute = false;
        }
        $source = '';
        foreach($audiourls as $value) {
            $this->flowplayer->audioCompatible($value, $this->url['script']);
            $source .='<source src="'.$value.'" '.$this->audioType($value).' />';
        }
        $noAudio = $this->language['noAudio'].$this->language['downloadAudio'];
        $links = $outside = "";
        if($this->mobileCheck() || $this->option['audioLinkOutside']) {
            $outside = '<br />'.$this->option['audioLinkOutsideBefore'].$this->linkGenerator().
                    $this->option['audioLinkOutsideAfter'];
        } else {
            $links = '<br />'.$this->linkGenerator();
        };
        $header = sprintf("%s<audio %s %s %s>", $this->option['beforeAudio'],
                $this->getID($this->option['audioID'], "audio"),
                $this->getTitle($title), $this->getHtmlAttribute($attribute, "audio"));
        $footer = "</audio>";
        return sprintf('%s %s %s %s %s %s %s %s', $this->getJavaScriptCall($this->option['audioScript'], "audio"),
                $header, $source, $this->getFallback($noAudio.$links), self::HTML5_TAG, $footer, $outside,
                $this->option['afterAudio']);
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
        if(preg_match("#.ext.(mp4|m4v)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP4 (Extended)</a> ';
            return "type='video/mp4; codecs=\"avc1.58A01E, mp4a.40.2\"'";
        }
        if(preg_match("#.main.(mp4|m4v)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP4 (Main)</a> ';
            return "type='video/mp4; codecs=\"avc1.4D401E, mp4a.40.2\"'";
        }
        if(preg_match("#.high.(mp4|m4v)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP4 (High)</a> ';
            return "type='video/mp4; codecs=\"avc1.64001E, mp4a.40.2\"'";
        }
        if(preg_match("#.(mp4|m4v)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP4</a> ';
            return "type='video/mp4; codecs=\"avc1.42E01E, mp4a.40.2\"'";
        }
        if(preg_match("#.(ogg|ogv)$#i",$url)) {
            $this->downloadLinks['open'] .= '<a href="'.$url.'">OGG</a> ';
            return "type='video/ogg; codecs=\"theora, vorbis\"'";
        }
        if(preg_match("#.(webm)$#i",$url)) {
            $this->downloadLinks['open'] .= '<a href="'.$url.'">WebM</a> ';
            return "type='video/webm; codecs=\"vp8, vorbis\"'";
        }
        return "";
    }

    private function audioType($url) {
        if(preg_match("#.(ogg|oga)$#i",$url)) {
            $this->downloadLinks['open'] .= '<a href="'.$url.'">OGG</a> ';
            return 'type="audio/ogg"';
        }
        if(preg_match("#.(mp4|m4a|aac)$#i",$url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">AAC</a> ';
            return 'type="audio/aac"';
        }
        if(preg_match("#.(mp3)$#i",$url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">MP3</a> ';
            return 'type="audio/mpeg"';
        }
        if(preg_match("#.(wav)$#i", $url)) {
            $this->downloadLinks['closed'] .= '<a href="'.$url.'">WAV</a> ';
            return 'type="audio/x-wav"';
        }
        return "";
    }

    private function mobileCheck() {
        if(preg_match("#(Opera Mini|Opera Mobi)#",$_SERVER['HTTP_USER_AGENT']) ||
                (preg_match('#((webOS|SymbianOS|Nokia)+?AppleWebKit|AppleWebKit(.*?)Mobile)#',$_SERVER['HTTP_USER_AGENT']) &&
                        !preg_match('#iPad#',$_SERVER['HTTP_USER_AGENT']))) {
            return true;
        } else {
            return false;
        }
    }
}
?>
