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
class ZradAid_Uri
{

    /**
     * Retorna la URL enmascarada
     * @param string url
     * @return string
     */
    public function getWebPageUrl($url)
    {
        $go = curl_init($url);
        curl_setopt($go, CURLOPT_URL, $url);
        //follow on location problems
        $last = $this->_curlRedirExec($go);
        curl_close($go);
        return $last;
    }
    
    /**
     * Procesa una cadena y lo convierte en URL 
     * Vestidos de Moda 2013 -> vestidos-de-moda-2013
     * 
     * @return string URL Amigable     
     */
    public static function urlAmigable($palabra)
    {
        //Buscamos caracteres no deseados (/,-) y los borramos        
        $partes = array_map('trim', explode(' ',$palabra));
        $r = array();
        foreach ($partes as $parte) {            
            if (!empty($parte) && !in_array($parte, array('-', '/', '+', '(', ')')))
                array_push($r, $parte);
        }
        $url = implode(' ', $r);
        $dirty = array("Á", "É", "Í", "Ó", "Ú", "Ñ","á", "é", "í", "ó", "ú", "ñ", "'", '"', 'ü', ' ', '.', '?', '¿', ',', '(', ')');
        $clean = array("A", "E", "I", "O", "U", "N","a", "e", "i", "o", "u", "n", "", "", 'u', '-', '', '', '', '', '', '');        
        return mb_strtolower(str_replace($dirty, $clean, $url),'UTF-8');
    }
    
    /**
     * @param string $texto Texto a convertir en URL
     * @param string $entidad Nombre de la tabla a validar
     * @param array $condicion Array de condicionas de la fila a obviar de la busqueda
     * @param string $campo Nombre del campo a validar por defecto "url"     
     * @param bool $convertido Indica si ya uso la funcion ZradAid_Uri::urlAmigable, por defecto FALSE
     * @return string url
     */
    public static function urlAmigableEntidad($texto, $entidad, $condicion = null, $campo = 'url', $convertido = false)
    {
        $model = new ZradAid_Db_Model();
        // Vemos si ya esta cambiado a URL amigable o no
        $url = (!$convertido) ? ZradAid_Uri::urlAmigable($texto) : $texto;
        // Buscamos si ya existe la URL
        if ($model->existeUrlAmigable($url, $condicion, $entidad, $campo)) {
            // Contamos todas las existencias, No aplica un simple count ya que pueden eliminarse productos
            $urls = $model->buscarUrlsAmigables($url, $entidad, $campo);
            if (count($urls) > 0) {
                // Vemos en que numero esta
                $uUrl = current($urls);
                $uNumero = (int) str_replace($url . '-', '', $uUrl['url']);
                $uNumero++;
                $url .= '-' . $uNumero;
            }
        }
        return $url;
    }

    private function _curlRedirExec($ch)
    {
        static $curl_loops = 0;
        static $curl_max_loops = 10;
        if ($curl_loops++ >= $curl_max_loops) {
            $curl_loops = 0;
            return false;
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $data = curl_exec($ch);
        list($header, $data) = explode("\n\n", $data, 2);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code >= 301 && $http_code <= 305) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = @parse_url(trim(array_pop($matches)));
            if (!$url) {
                //couldn't process the url to redirect to
                $curl_loops = 0;
                return false;
            }
            $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
            if (!$url['scheme'])
                $url['scheme'] = $last_url['scheme'];
            if (!$url['host'])
                $url['host'] = $last_url['host'];
            if (!$url['path'])
                $url['path'] = $last_url['path'];
            $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query'] ? '?' . $url['query'] : '');
            curl_setopt($ch, CURLOPT_URL, $new_url);
            return $this->_curlRedirExec($ch);
        }
        else {
            $curl_loops = 0;
            $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
            return $last_url['scheme'] . '://' . $last_url['host'] . $last_url['path'] . ($last_url['query'] ? '?' . $last_url['query'] : '');
        }
    }

    public static function serverUrl()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        return $request->getScheme() . '://' . $request->getHttpHost();
    }
    
}
