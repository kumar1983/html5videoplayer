<?php

/**
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

class mediaelement {

    private $object_attribs;
    private $object_params;
    private $mediaelement_dir;
    private $option;

    public function __construct($location) {
        $this->mediaelement_dir = $location . "/mediaelement/";
        $this->option['flashIsSetup'];
    }

    private function setUpFlash($object) {
        $this->object_attribs = $object['attribs'];
        $this->object_params = $object['params'];
        $this->option['flashIsSetup'] = true;
    }

    public function getFlashObject($fallback = "") {
        if ($this->option['flashIsSetup']) {
            $object_attribs = $object_params = '';

            foreach ($this->object_attribs as $param => $value) {
                $object_attribs .= '  ' . $param . '="' . $value . '"';
            }

            foreach ($this->object_params as $param => $value) {
                $object_params .= '<param name="' . $param . '" value=\'' . $value . '\' />';
            }
            $this->option['flashIsSetup'] = false;
            return sprintf("<object %s> %s  %s</object>", $object_attribs, $object_params, $fallback);
        } else {
            return $fallback;
        }
    }

    public function setMediaElementDir($dir) {
        $this->mediaelement_dir = $dir;
    }

    public function setOptions($param, $value) {
        $this->option[$param] = $value;
    }

    public function videoCompatible($data, $tag = false) {
        $url = $data["url"];
        $width = $data["width"];
        $height = $data["height"];
        $poster = $data["poster"];
        $title = $data["title"];
        if (preg_match("#\.(mp4|m4v)$#i", $url) && !$this->option['flashIsSetup']) {
            $movie = $this->mediaelement_dir . 'flashmediaelement.swf';
            $flashobject['attribs'] = array(
                "type" => "application/x-shockwave-flash",
                "data" => $movie,
                "width" => $width,
                "height" => $height
            );
            $flashobject['params'] = array(
                "movie" => $movie,
                "allowfullscreen" => "true",
                "cachebusting" => "true",
                "bgcolor" => "#000000",
                "flashvars" => 'controls=true&poster='.$poster.'&file='.$url
            );
            if ($title) {
                $flashobject['attribs']['title'] = $title;
            }
            $this->setUpFlash($flashobject);
        }
    }

    public function audioCompatible($data, $tag = false) {
        $url = $data['url'];
        $title = $data['title'];
        if (preg_match("#\.(mp3|aac|m4a)$#i", $url) && !$this->option['flashIsSetup']) {
            $movie = $this->mediaelement_dir . 'flashmediaelement.swf';
            $flashobject['attribs'] = array(
                "type" => "application/x-shockwave-flash",
                "data" => $movie,
                "width" => "400",
                "height" => "30",
            );
            $flashobject['params'] = array(
                "movie" => $movie,
                "allowfullscreen" => "false",
                "cachebusting" => "true",
                "bgcolor" => "#000000",
                "flashvars" => 'controls=true&file='.$url
            );
            if ($title) {
                $flashobject['attribs']['title'] = $title;
            }
            $this->setUpFlash($flashobject);
        }
    }

    public function getFlashIsSetup() {
        return $this->option['flashIsSetup'];
    }
}
