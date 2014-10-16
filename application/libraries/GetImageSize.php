<?php

class ImageSize {

    public $url;
    public $size;

    public function __construct($url) {
        $this->url = $url;
    }

    public function getWithHeader($url) {
        $fp = fsockopen($url['host'], 80, $errno, $errstr, 30);
        if ($fp) {
            $out = "HEAD " . $url['path'] . " HTTP/1.1\r\n";
            $out .= "Host: " . $url['host'] . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            while (!feof($fp)) {
                $header = fgets($fp);
                if (stripos($header, 'Content-Length') !== false) {
                    $size = trim(substr($header, strpos($header, ':') + 1));
                    break;
                }
            }
            fclose($fp);
        } else {
            $size = false;
        }
        return $size;
    }

    public function getWithGet($url) {
        $headers = get_headers($url);
        while($header = next($headers)) {
            if(stripos($header, 'Content-Length') !== false) {
                $size = trim(substr($header, strpos($header, ':') + 1));
                break;
            } else {
                $size = false;
            }
        }
        return $size;
    }

    public function get($url = '') {
        if(!empty($url)) {
            $this->url = $url;
        }
        $parsed_url = parse_url($this->url);
        if(!$parsed_url) {
            throw new Exception('Invalid image url to load.');
        }
        $this->size = $this->getWithHeader($parsed_url);
        if(empty($this->size)) {
            $this->size = $this->getWithGet($this->url);
        }
    }
}