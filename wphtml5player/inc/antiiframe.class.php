<?php

/*
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
                        'iframe-src' => 'http://www.dailymotion.com/embed/video/(.{1,})$',
                        'id-location' => 1,
                        'flash-src' => 'http://www.dailymotion.com/swf/video/<%ID%>?additionalInfos=0'
                ),
                array(
                        'iframe-src' => 'http://dotsub.com/media/(([0-9a-z]|-){1,})/e',
                        'id-location' => 1,
                        'flash-src' => 'http://dotsub.com/static/players/portalplayer.swf',
                        'flash-vars' => 'type=flv&plugins=dotsub&debug=none&tid=UA-3684979-1&uuid=<%ID%>&lang=eng'
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
