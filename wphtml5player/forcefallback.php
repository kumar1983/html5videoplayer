<?php
/*
Plugin Name: HTML5 Video and Audio Framework Force-Fallback to Flash.
Plugin URI: http://code.google.com/p/html5videoplayer/
Description: Only activated this if your intention is to only use one format or if you want force fallback.
Version: 1.7.2
Author: Christopher John Jackson
Author URI: http://cj-jackson.com/
License: New BSD License (GPLv2 and v3 Compatible)

    Copyright (c) 2010, Christopher John Jackson
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification,
    are permitted provided that the following conditions are met:

     * Redistributions of source code must retain the above copyright notice,
     this list of conditions and the following disclaimer.
     * Redistributions in binary form must reproduce the above copyright notice,
     this list of conditions and the following disclaimer in the documentation and/or
     other materials provided with the distribution.
     * Neither the name of the cj-jackson.com nor the names of its contributors may
     be used to endorse or promote products derived from this software without specific
     prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
    AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
    IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
    INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
    NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
    OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
    WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
    OF SUCH DAMAGE.
*/

add_action('init', 'wphtml5forcefallback_call');
add_action('wp_footer', 'wphtml5forcefallback_footer');

function wphtml5forcefallback_call() {
    wp_enqueue_script('jquery');
}

function wphtml5forcefallback_footer() {
    $scriptUrl = WP_PLUGIN_URL."/wphtml5player/inc";
    echo "<script type='text/javascript' src='".$scriptUrl."/forcefallback.js'></script>\n";
}

?>
