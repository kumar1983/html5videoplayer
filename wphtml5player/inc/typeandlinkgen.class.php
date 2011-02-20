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
class TypeAndLinkGenclass {

    private $typeList;
    private $links;
    private $message;
    private $count;

    const video = 'video';
    const audio = 'audio';

    public function __construct($audioDownload, $videoDownload, $open, $closed, $ME_enabled) {
        $this->message = array(
            self::video => array(
                'download' => $videoDownload,
                'open' => $open,
                'closed' => $closed
            ),
            self::audio => array(
                'download' => $audioDownload,
                'open' => $open,
                'closed' => $closed
            )
        );

        $this->typeList = array(
            self::video => array(
                array(
                    'name' => 'MP4 (Extended)',
                    'exp' => "#.ext.(mp4|m4v)$#i",
                    'mime' => 'video/mp4',
                    'codec' => 'avc1.58A01E, mp4a.40.2',
                    'open' => false
                ),
                array(
                    'name' => 'MP4 (Main)',
                    'exp' => "#.main.(mp4|m4v)$#i",
                    'mime' => 'video/mp4',
                    'codec' => 'avc1.4D401E, mp4a.40.2',
                    'open' => false
                ),
                array(
                    'name' => 'MP4 (High)',
                    'exp' => "#.high.(mp4|m4v)$#i",
                    'mime' => 'video/mp4',
                    'codec' => 'avc1.64001E, mp4a.40.2',
                    'open' => false
                ),
                array(
                    'name' => 'MP4',
                    'exp' => "#.(mp4|m4v)$#i",
                    'mime' => 'video/mp4',
                    'codec' => 'avc1.42E01E, mp4a.40.2',
                    'open' => false
                ),
                array(
                    'name' => 'OGG',
                    'exp' => "#.(ogg|ogv)$#i",
                    'mime' => 'video/ogg',
                    'codec' => 'theora, vorbis',
                    'open' => true
                ),
                array(
                    'name' => 'WebM',
                    'exp' => "#.(webm)$#i",
                    'mime' => 'video/webm',
                    'codec' => 'vp8, vorbis',
                    'open' => true
                )
            ),
            self::audio => array(
                array(
                    'name' => 'OGG',
                    'exp' => "#.(ogg|oga)$#i",
                    'mime' => 'audio/ogg',
                    'codec' => false,
                    'open' => true
                ),
                array(
                    'name' => 'AAC',
                    'exp' => "#.(mp4|m4a|aac)$#i",
                    'mime' => 'audio/aac',
                    'codec' => false,
                    'open' => false
                ),
                array(
                    'name' => 'MP3',
                    'exp' => "#.(mp3)$#i",
                    'mime' => 'audio/mpeg',
                    'codec' => false,
                    'open' => false
                ),
                array(
                    'name' => 'WAV',
                    'exp' => "#.(wav)$#i",
                    'mime' => 'audio/x-wav',
                    'codec' => false,
                    'open' => false
                ),
                array(
                    'name' => 'WebM',
                    'exp' => "#.(webm)$#i",
                    'mime' => 'audio/webm',
                    'codec' => false,
                    'open' => true
                ),
            )
        );

        if ($ME_enabled) {
            $typeAltList = array(
                self::video => array(
                    array(
                        'name' => 'FLV',
                        'exp' => "#.(flv|f4v)$#i",
                        'mime' => 'video/x-flv',
                        'codec' => false,
                        'open' => false
                    ),
                    array(
                        'name' => 'WMV',
                        'exp' => "#.(wmv)$#i",
                        'mime' => 'video/x-ms-wmv',
                        'codec' => false,
                        'open' => true
                    )
                ),
                self::audio => array(
                    array(
                        'name' => 'WMA',
                        'exp' => "#.(wma)$#i",
                        'mime' => 'audio/x-ms-wma',
                        'codec' => false,
                        'open' => true
                    )
                )
            );

            $this->typeList = $this->array_replace_recursive($this->typeList, $typeAltList);
        }

        $this->resetLinksAndCount();
    }

    private function resetLinksAndCount() {
        $this->links = array(
            'open' => array(),
            'closed' => array()
        );

        $this->count = array('open' => 0, 'closed' => 0);
    }

    private function getType($type, $url) {
        foreach ($this->typeList[$type] as $key) {
            if (preg_match($key['exp'], $url)) {
                if ($key['open']) {
                    $this->count['open']++;
                    if ($this->count['open'] == 1) {
                        $this->links['open'][] = $this->message[$type]['open'];
                    }
                    $this->links['open'][] = '<a href="' . $url . '">' . $key['name'] . '</a>';
                } else {
                    $this->count['closed']++;
                    if ($this->count['closed'] == 1) {
                        $this->links['closed'][] = $this->message[$type]['closed'];
                    }
                    $this->links['closed'][] = '<a href="' . $url . '">' . $key['name'] . '</a>';
                }
                $codec = "";
                if ($key['codec']) {
                    $codec = '; codecs="' . $key['codec'] . '"';
                }
                return sprintf("type='%s%s'", $key['mime'], $codec);
            }
        }
        return "";
    }

    public function getVideoType($url) {
        return $this->getType(self::video, $url);
    }

    public function getAudioType($url) {
        return $this->getType(self::audio, $url);
    }

    private function getLinks($type) {
        $links = $this->message[$type]['download'] . ' ' . implode(' ', $this->links['closed']) .
                ' ' . implode(' ', $this->links['open']);
        $this->resetLinksAndCount();
        return $links;
    }

    public function getVideoLinks() {
        return $this->getLinks(self::video);
    }

    public function getAudioLinks() {
        return $this->getLinks(self::audio);
    }

    private function array_replace_recursive() {
        $arrays = func_get_args();
        $original = array_shift($arrays);
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $original[$key] = $this->array_replace_recursive($original[$key], $array[$key]);
                } else {
                    $original[$key] = $value;
                }
            }
        }
        return $original;
    }

}
