<?php

class wphtml5_antiIframe {
    private $dom;
    private $iframelist;

    public function  __construct() {
        include_once 'simple_html_dom.php';
        $this->dom = new wphtml5_simple_html_dom();
        $this->iframelist = array(
                array(
                        'iframe-src' => 'http://www.youtube.com/embed/(.{1,})$',
                        'id-location' => 1,
                        'flash-src' => 'http://www.youtube.com/v/<%ID%>?fs=1'
                ),
                array(
                        'iframe-src' => 'http://player.vimeo.com/video/([0-9]{1,})$',
                        'id-location' => 1,
                        'flash-src' => 'http://vimeo.com/moogaloop.swf?clip_id=<%ID%>&server=vimeo.com&show_title=1&show_byline=1&show_portrait=1&fullscreen=1&autoplay=0&loop=0'
                )
        );
    }

    public function checkIframe($html) {
        $this->dom->load($html);
        $iframe_src = '';
        $embed = array();
        foreach($this->dom->find('iframe') as $data) {
            $iframe_src = $data->src;
            if(isset($data->width)) {
                $embed['width'] = $data->width;
            }
            if(isset($data->height)) {
                $embed['height'] = $data->height;
            }
        }
        $this->dom->__destruct();
        foreach($this->iframelist as $key) {
            if(preg_match('#'.$key['iframe-src'].'#', $iframe_src, $matches)) {
                $match = $matches[$key['id-location']];
                $embed['src'] = preg_replace('#<%ID%>#', $match, $key['flash-src']);
                if(isset($key['flash-vars'])) {
                    $embed['flashvars'] = preg_replace('#<%ID%>#', $match, $key['flash-vars']);
                }
                $embed['type'] = "application/x-shockwave-flash";
                return $this->getNewCode($embed);
            }
        }
        return $html;
    }

    private function getNewCode($embed) {
        $embedAttribute = "";
        foreach($embed as $key => $value) {
            $embedAttribute .= " ".$key.'="'.$value.'" ';
        }
        return sprintf("<embed %s ></embed>", $embedAttribute);
    }
}
?>
