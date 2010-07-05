<?php

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
        if(isset($matches[1]) && isset($matches[2])) {
            $videooption["width"] = $matches[1];
            $videooption["height"] = $matches[2];
        }
        if(!isset($videooption)) {
            $videooption = "notset";
        }
        return $this->videoCodeGenerator($videourl, $videooption);
    }

    private function videoCodeGenerator($videourl, $videooption) {
        $width = $videooption["width"];
        $height = $videooption["height"];

        $output = '<video controls="true">';
        foreach($videourl as $value) {
            $this->flowPlayerVideoCompatible($value, $width, $height);
            $output .='<source src="'.$value.'" />';
        }
        $output .= $this->flowplayer;
        $output .= '</video>';
        $this->flowplayer = "";
        return $output;
    }

    public function audioreplace($data) {
        $audiourl = $data[1];
        $audiourl = explode("|", $audiourl);
        return $this->audioCodeGenerator($audiourl);
    }

    private function audioCodeGenerator($audiourl) {
        $output = '<audio controls="true">';
        foreach($audiourl as $value) {
            $this->flowPlayerAudioCompatible($value);
            $output .='<source src="'.$value.'" />';
        }
        $output .= $this->flowplayer;
        $output .= '</audio>';
        $this->flowplayer = "";
        return $output;
    }

    private function flowPlayerVideoCompatible($url, $width, $height) {
        if($width == "n"){
            $width = 480;
            $height = 320;
        }
        if(preg_match("#(mp4|m4v)$#i",$url)) {
            $flowplayer = array(
                    '<object id="flowplayer-'.$this->flowplayercount.'" width="'.$width.'" height="'.$height.'" ',
                    'data="'.$this->url.'/inc/flowplayer.swf" type="application/x-shockwave-flash">',
                    '<param name="movie" value="'.$this->url.'/inc/flowplayer.swf" />',
                    '<param name="allowfullscreen" value="false" />',
                    '<param name="flashvars" value=\'config={"clip":{"url":"'.$url.'", "autoPlay":false}}\' />',
                    '</object>'
            );
            $this->flowplayer = implode("",$flowplayer);
            $this->flowplayercount++;
        }
    }

    private function flowPlayerAudioCompatible($url) {
        if(preg_match("#(mp3)$#i",$url)) {
            $flowplayer = array(
                    '<object id="flowplayer-'.$this->flowplayercount.'" width="100%" height="30" ',
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
}
?>
