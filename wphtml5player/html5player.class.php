<?php

/**
 * HTML5 Player Class 0.1.0
 * Embed video using shortcodes, using flowplayer as fallback.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
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
    private $flowplayercount;

    public function  __construct($url, $root) {
        $this->url = $url;
        $this->root = $root;
        $this->flowplayer = "";
        $this->flowplayercount = 1;
    }

    public function videoreplace($data) {
        $data = $data[1];
        $data = explode(" ",$data);
        return $this->arrayToOrganisedArrays($data);
    }

    private function arrayToOrganisedArrays($matches) {
        $videourl = $matches[0];
        $videourl = explode("|", $videourl);
        $ifScore = 1;
        
        //Check for poster url.
        if(isset($matches[$ifScore]) && !is_numeric($matches[$ifScore])) {
            $videooption["poster"] = $matches[$ifScore];
            $ifScore++;
        } else {
            $videooption["poster"] = "";
        }
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
        if($poster != ""){
            $parms = ' poster="'.$poster.'"';
        }
        $output = '<div class="video-js-box"><video class="video-js" '.$parms.' controls>';
        foreach($videourl as $value) {
            $this->flowPlayerVideoCompatible($value, $width, $height, $poster);
            $output .='<source src="'.$value.'" '.$this->videoType($value).' />';
        }
        $output .= $this->flowplayer;
        $output .= '</video></div>';
        $this->flowplayer = "";
        return $output;
    }

    public function audioreplace($data) {
        $audiourl = $data[1];
        $audiourl = explode("|", $audiourl);
        return $this->audioCodeGenerator($audiourl);
    }

    private function audioCodeGenerator($audiourl) {
        $output = '<audio controls>';
        foreach($audiourl as $value) {
            $this->flowPlayerAudioCompatible($value);
            $output .='<source src="'.$value.'" '.$this->audioType($value).' />';
        }
        $output .= $this->flowplayer;
        $output .= '</audio>';
        $this->flowplayer = "";
        return $output;
    }

    private function flowPlayerVideoCompatible($url, $width, $height, $poster) {
        if(!(is_numeric($width) && is_numeric($height))) {
            $width = 480;
            $height = 320;
        }
        $flashvars = "";
        if($poster != ""){
            $flashvars = '<param name="flashvars" value=\'config={"playlist":[{"url":"'.$poster.'"},{"url":"'.$url.'","autoPlay":false}]}\' />';
        } else {
            $flashvars = '<param name="flashvars" value=\'config={"clip":{"url":"'.$url.'", "autoPlay":false}}\' />';
        }
        if(preg_match("#(mp4|m4v)$#i",$url)) {
            $flowplayer = array(
                    '<object class="vjs-flash-fallback" id="flowplayer-'.$this->flowplayercount.'" width="'.$width.'" height="'.$height.'" ',
                    'data="'.$this->url.'/inc/flowplayer.swf" type="application/x-shockwave-flash">',
                    '<param name="movie" value="'.$this->url.'/inc/flowplayer.swf" />',
                    '<param name="allowfullscreen" value="false" />',
                    $flashvars,
                    '</object>'
            );
            $this->flowplayer = implode("",$flowplayer);
            $this->flowplayercount++;
        }
    }

    private function flowPlayerAudioCompatible($url) {
        if(preg_match("#(mp3)$#i",$url)) {
            $flowplayer = array(
                    '<object id="flowplayer-'.$this->flowplayercount.'" width="300" height="30" ',
                    'data="'.$this->url.'/inc/flowplayer.swf" type="application/x-shockwave-flash">',
                    '<param name="movie" value="'.$this->url.'/inc/flowplayer.swf" />',
                    '<param name="allowfullscreen" value="false" />',
                    '<param name="cachebusting" value="true">',
                    '<param name="bgcolor" value="#000000">',
                    '<param name="flashvars" value=\'config={"plugins":{"controls":{"fullscreen":false,"height":30,"autoHide":false}},"clip":{"autoPlay":false,"url":"'.$url.'"},"playerId":"audio","playlist":[{"autoPlay":false,"url":"'.$url.'"}]}\' />',
                    '</object>'
            );
            $this->flowplayer = implode("",$flowplayer);
            $this->flowplayercount++;
        }
    }

    private function videoType($url) {
        if(preg_match("#(mp4|m4v)$#i", $url)) {
            return "type='video/mp4; codecs=\"avc1.42E01E, mp4a.40.2\"'";
        }
        if(preg_match("#(ogg|ogv)$#i",$url)) {
            return "type='video/ogg; codecs=\"theora, vorbis\"'";
        }
        if(preg_match("#(webm)$#i",$url)) {
            return "type='video/webm; codecs=\"vp8, vorbis\"'";
        }

        return "";
    }

    private function audioType($url) {
        if(preg_match("#(ogg|oga)$#i",$url)) {
            return 'type="audio/ogg"';
        }
        if(preg_match("#(mp4|m4a|aac)$#i",$url)) {
            return 'type="audio/aac"';
        }
        if(preg_match("#(mp3)$#i",$url)) {
            return 'type="audio/mpeg"';
        }
        if(preg_match("#(wav)$#i", $url)) {
            return 'type="audio/x-wav"';
        }

        return "";
    }

    public function httpHead() {
        $output = '<script src="'.$this->url.'/inc/video.js" type="text/javascript"></script>'.
                '<script type="text/javascript">window.onload = function(){ VideoJS.setup(); }</script>'.
                '<link rel="stylesheet" href="'.$this->url.'/inc/video-js.css" type="text/css" media="screen" title="Video JS">';

        return $output;
    }
}
?>
