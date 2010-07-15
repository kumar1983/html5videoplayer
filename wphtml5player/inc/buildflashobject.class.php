<?php

class buildflashobject {

    const THEID = "flowplayer-";

    private $object_attribs;
    private $object_params;
    private $fallback;
    private $option;
    private $count;

    public function __construct() {
        $this->option['flashIsSetup'] = false;
        $this->option['swfobject'] = false;
        $this->count = 1;
    }

    public function setUpFlash($object) {
        $this->object_attribs = $object['attribs'];
        $this->object_params = $object['params'];
        $this->option['flashIsSetup'] = true;
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
                $object_params .= '<param name="' . $param . '" value="' . $value . '" />';
            }
            $this->option['flashIsSetup'] = false;
            return sprintf("%s<object %s> %s  %s</object>", $swfobject, $object_attribs, $object_params, $this->fallback);
        } else {
            return "";
        }
    }

}

?>
