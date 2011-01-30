<?php
/*
Plugin Name: HTML5 Multimedia Framework Prevent Flash Lite.
Plugin URI: http://code.google.com/p/html5videoplayer/
Description: Only activated this if you don't like the idea of using flash on mobile devices.
Version: 3.0.0
Author: Christopher John Jackson
Author URI: http://cj-jackson.com/
License: MIT License

    Copyright (C) 2011 by Christopher John Jackson

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
*/

add_action('init', 'wphtml5preventflashlite_call');

function wphtml5preventflashlite_call() {
    define('WPHTML5_PREVENT_FLASH_LITE', true);
}

?>