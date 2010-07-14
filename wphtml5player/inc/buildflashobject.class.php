<?php

class buildflashobject {

    private $before;
    private $object_attribs;
    private $object_params;
    private $fallback;
    private $flashIsSetup;

    public function __construct() {
        $this->flashIsSetup = false;
    }

    public function setUpFlash($object) {
        $this->before = $object['before'];
        $this->object_attribs = $object['attribs'];
        $this->object_params = $object['params'];
        $this->flashIsSetup = true;
    }

    public function getFlashIsSetup() {
        return $this->flashIsSetup;
    }

    public function setFallback($fallback) {
        $this->fallback = $fallback;
    }

    public function getFlashObject() {
        if($this->flashIsSetup) {
            $object_attribs = $object_params = '';

            foreach ($this->object_attribs as $param => $value) {
                $object_attribs .= '  ' . $param . '="' . $value . '"';
            }

            foreach ($this->object_params as $param => $value) {
                $object_params .= '<param name="' . $param . '" value="' . $value . '" />';
            }
            $this->flashIsSetup = false;
            return sprintf("%s<object %s> %s  %s</object>", $this->before, $object_attribs, $object_params, $this->fallback);
        } else {
            return "";
        }
    }

}

?>
