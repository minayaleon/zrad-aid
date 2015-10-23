<?php

/**
 * Zend Rad
 *
 * LICENCIA
 *
 * Este archivo está sujeta a la licencia CC(Creative Commons) que se incluye
 * en LICENCIA.txt.
 * Tambien esta disponible a traves de la Web en la siguiente direccion
 * http://www.zend-rad.com/licencia/
 * Si usted no recibio una copia de la licencia por favor envie un correo
 * electronico a <licencia@zend-rad.com> para que podamos enviarle una copia
 * inmediatamente.
 *
 * @author Juan Minaya Leon <info@juanminaya.com>
 * @copyright Copyright (c) 2011-2012 , Juan Minaya Leon
 * (http://www.zend-rad.com)
 * @licencia http://www.zend-rad.com/licencia/   CC licencia Creative Commons
 */
class ZradAid_Youtube
{

    public function esValido($url, $estricto = false)
    {
        $youtubeRegexp1 = "/^http:\/\/(?:www\.)?(?:youtube.com|youtu.be)\/(?:watch\?(?=.*v=([\w\-]+))(?:\S+)?|([\w\-]+))$/";
        $youtubeRegexp2 = "/^(?:https?:\/\/)?(?:www\.)?(?:youtube.com|youtu.be)\/(?:watch\?(?=.*v=([\w\-]+))(?:\S+)?|([\w\-]+))$/";        
        $youtubeRegexp = ($estricto) ? $youtubeRegexp1 : $youtubeRegexp2;
        // Validamos
        preg_match($youtubeRegexp, $url, $coincidencias);

        // Remove empty values from the array (regexp shit).
        $coincidencias = array_filter($coincidencias, function($var) {
            return($var !== '');
        });

        // If we have 2 elements in array, it means we got a valid url!
        // $matches[2] is the youtube ID!
        return (sizeof($coincidencias) == 2) ? true : false;
    }

    public function esLista($url)
    {
        return (strpos($url, 'list=') !== false) ? true : false;
    }

    public function getId($url)
    {
        return (!$this->esLista($url)) ? preg_replace('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i', '$1', $url) : null;
    }

    public function getEmbed($url)
    {
        // Check if youtube link is a playlist
        if (strpos($url, 'list=') !== false) {
            // Generate the embed code
            $url = preg_replace('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{12,})[a-z0-9;:@#?&%=+\/\$_.-]*~i', 'https://www.youtube.com/embed/videoseries?list=$1', $url);
            return $url;
        }
        // Check if youtube link is not a playlist but a video [with time identifier]
        if (strpos($url, 'list=') === false && strpos($url, 't=') !== false) {
            // Get the time in seconds from the time function
            $timeInSecs = $this->convertTimeToSeconds($url);
            // Generate the embed code
            $url = preg_replace('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i', 'https://www.youtube.com/embed/$1?start=' . $timeInSecs, $url);
            return $url;
        }
        // If the above conditions were false then the youtube link is probably just a plain video link. So generate the embed code already.
        $url = preg_replace('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i', 'https://www.youtube.com/embed/$1', $url);
        return $url;
    }

    public function convertTimeToSeconds($url)
    {
        $time = null;
        $hours = null;
        $minutes = null;
        $seconds = null;

        $pattern_time_split = "([0-9]{1-2}+[^hms])";

        // Regex to check for youtube video link with time identifier
        $youtube_time = '~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*(t=((\d+h)?(\d+m)?(\d+s)?))~i';

        // Check for time identifier in the youtube video link, extract it and convert it to seconds
        if (preg_match($youtube_time, $url, $matches)) {
            // Check for hours
            if (isset($matches[4])) {
                $hours = $matches[4];
                $hours = preg_split($pattern_time_split, $hours);
                $hours = substr($hours[0], 0, -1);
            }

            // Check for minutes
            if (isset($matches[5])) {
                $minutes = $matches[5];
                $minutes = preg_split($pattern_time_split, $minutes);
                $minutes = substr($minutes[0], 0, -1);
            }

            // Check for seconds
            if (isset($matches[6])) {
                $seconds = $matches[6];
                $seconds = preg_split($pattern_time_split, $seconds);
                $seconds = substr($seconds[0], 0, -1);
            }

            // Convert time to seconds
            $time = (($hours * 3600) + ($minutes * 60) + $seconds);
        }

        return $time;
    }

}
