<?php

/**
 * Description of html5playerclass
 *
 * @author cjackson
 */
class html5player {
    private $url;
    private $root;
    private $flowplayer;

    public function  __construct($url, $root) {
        $this->url = $url;
        $this->root = $root;
        $this->flowplayer = "";
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
        if($videooption != "notset") {
            $videooption["width"] = 'width="'.$videooption["width"].'"';
            $videooption["height"] = 'height="'.$videooption["height"].'"';
            $videooption = implode(" ",$videooption);
        } else {
            $videooption = "";
        }

        $output = '<video '.$videooption.' controls="true">';
        foreach($videourl as $value) {
            $this->flowPlayerCompatible($value, $width, $height);
            $output .='<source src="'.$value.'" />';
        }
        $output .= $this->flowplayer;
        $output .= '</video>';
        return $output;
    }

    public function audioreplace($data) {
        $audiourl = $data[1];
        $audiourl = explode("|", $audiourl);
        return $this->audioCodeGenerator($audiourl);
    }

    private function audioCodeGenerator($audiourl) {
        $output = '<audio>';
        foreach($audiourl as $value) {
            $output .='<source src="'.$value.' />';
        }
        $output .= '</audio>';
        return $output;
    }

    private function flowPlayerCompatible($url, $width, $height) {
        if($width == "n"){
            $width = 480;
            $height = 320;
        }
        if(preg_match("#(mp4|m4v)$#i",$url)) {
            $flowplayer = array(
                    '<object id="flowplayer" width="'.$width.'" height="'.$height.'" ',
                    'data="'.$this->url.'/inc/flowplayer.swf" type="application/x-shockwave-flash">',
                    '<param name="movie" value="'.$this->url.'/inc/flowplayer.swf" />',
                    '<param name="allowfullscreen" value="false" />',
                    '<param name="flashvars" value=\'config={"clip":{"url":"'.$url.'", "autoPlay":false}}\' />',
                    '</object>'
            );
            $this->flowplayer = implode("",$flowplayer);
        }
    }
}
?>
