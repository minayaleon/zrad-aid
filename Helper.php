<?php

/**
 * Zend Rad Aid
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
class ZradAid_Helper
{
    /**
     * Return protocol http o https
     * 
     * @return string
     */
    public static function getProtocol()
    {
        $protocol = 'http://';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $protocol = 'https://';
        }

        return $protocol;
    }

    /**
     * Retorna el numero de ip
     * 
     * @return string
     */
    public static function getIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function nombreCampo($campo)
    {
        $nombreCampo = $campo;

        switch ($nombreCampo) {
            case 'clave': $nombreCampo = 'La clave';
                break;
            case 'verifica tus datos': $nombreCampo = '';
                break;
        }

        return $nombreCampo . ' ';
    }

    public static function associativePush($arr, $tmp)
    {
        if (is_array($tmp)) {
            foreach ($tmp as $key => $value) {
                $arr[$key] = $value;
            }
            return $arr;
        }
        return false;
    }

    /**
     * Obtiene el primer nombre y el primer apellido
     *
     * @param String $names Nombres
     * @param String $lastName Apellidos
     * @return String
     */
    public static function shortName($firstNames, $lastName = '')
    {
        $firstName = explode(' ', $firstNames);
        $firstName = $firstName[0];

        if (!empty($lastName)) {
            $lastName = explode(' ', $lastName);
            $lastName = $lastName[0];
        }

        return trim($firstName . ' ' . $lastName);
    }

    /**
     * Obtiene el nombre corto, opciones:
     * Primer Nombre + Primer Apellido
     * Primer Nombre
     * 
     * @param string $nombres 
     * @param string $apellidos Puede ser vacio
     * @return string
     */
    public static function nombreCorto($nombres, $apellidos = '')
    {
        $nombreCorto = current(explode(' ', $nombres));
        if (!empty($apellidos))
            $nombreCorto .= ' ' . current(explode(' ', $apellidos));

        return trim($nombreCorto);
    }

    public static function getDateName()
    {
        $fecha = date("d-m-Y H-i-s");
        $name = str_replace("-", "", (string) $fecha);
        $name = str_replace(" ", "_", $name);
        return $name;
    }

    /**
     * @param string $extension extension a generar
     * @return $string
     */
    public static function generateName($extension = '')
    {
        $name = str_replace(' ', '', str_replace('.', '', microtime()));
        return (empty($extension)) ? $name : $name . '.' . $extension;
    }

    /**
     * Descargar Archivo
     *
     * @param $file
     */
    public static function download($file)
    {
        if (is_file($file)) {
            // requerido para IE
            if (ini_get('zlib.output_compression')) {
                ini_set('zlib.output_compression', 'Off');
            }

            if (function_exists('mime_content_type')) {
                $type = mime_content_type($file);
            } else if (function_exists('finfo_file')) {
                $info = finfo_open(FILEINFO_MIME);
                $type = finfo_file($info, $file);
                finfo_close($info);
            } else {
                $parts = explode('.', $file);
                $ext = end($parts);
                switch (strtolower($ext)) {
                    case 'pdf': $type = 'application/pdf';
                        break;
                    case 'zip': $type = 'application/zip';
                        break;
                    case 'jpeg':
                    case 'jpg': $type = 'image/jpg';
                        break;
                    default: $type = 'application/force-download';
                }
            }

            header('Pragma: public');     // required
            header('Expires: 0');        // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $type);
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($file));
            header('Connection: close');
            readfile($file);
            exit();
        } else {
            return false;
        }
    }

    public function pluralize($str)
    {
        $rule1 = array('a', 'e', 'o');
        $rule2 = array('z');
        //obtenemos la ultima letra
        $last = substr($str, -1, 1);
        //terminacion a,e,o
        if (in_array($last, $rule1)) {
            return $str . 's';
        }
        //terminacion o
        if (in_array($last, $rule2)) {
            //eliminamos la ultima letra
            $str = substr($str, 0, strlen($str) - 1);
            return $str . 'ces';
        }
        return $str . 'es';
    }

    /**
     * Convierte a Mayusculas
     * 
     * @param string $cadena
     * @return string
     */
    public static function toUpper($cadena)
    {
        setlocale(LC_CTYPE, 'es');
        $cadena = strtr(strtoupper($cadena), "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        return $cadena;
    }

    /**
     * @param float|int $number Numero a redondear
     * @param int $decimal Numero de decimales 
     * @return float|int
     */
    public function round($number, $decimal)
    {
        $factor = pow(10, $decimal);
        return (round($number * $factor) / $factor);
    }

    /**
     * Devuleve una porcion de texto
     * 
     * @param string $string
     * @param int $length
     */
    public static function lengthText($string, $length)
    {
        return (strlen($string) <= $length) ? $string : mb_strcut($string, 0, $length, 'UTF-8') . '...';
    }

    /**
     * Devuelve una cantidad limitada de palabras
     * 
     * @param string $string
     * @param int $length
     */
    public static function cutText($string, $length)
    {
        $palabras = explode(' ', $string); //Partimos la cadena en palabras
        $result = $string;

        if (count($palabras) > $length) {//Verificamos que la cantidad de palabras
            $result = '';
            for ($i = 0; $i <= $length - 1; $i++)
                $result .= $palabras[$i] . ' ';
            $result .= '...';
        }

        return $result;
    }

    public static function nombrePropio($nombres)
    {
        $fieldParts = explode(' ', mb_convert_case($nombres, MB_CASE_LOWER, "UTF-8"));
        if (count($fieldParts) > 1) {
            $n = ucfirst($fieldParts[0]);
            for ($i = 1; $i < count($fieldParts); $i++)
                $n .= ' ' . ucfirst($fieldParts[$i]);
        } else {
            $n = ucfirst($n);
        }
        return $n;
    }

    /**
     * @param String $field
     * @param Integer $case
     */
    public static function format($field, $case = 1)
    {
        //$field = strtolower($field);
        $output = array();
        switch ($case) {
            case 1://input:producto_detalle , output: productoDetalle
                $fieldParts = explode('_', $field);
                if (count($fieldParts) > 1) {
                    $field = $fieldParts[0];
                    for ($i = 1; $i < count($fieldParts); $i++)
                        $field .= ucfirst($fieldParts[$i]);
                }
                break;
            case 2://input:productoDetalle , output: producto-detalle
                preg_match_all("/([A-Z]|[a-z])[a-z]+/", $field, $output);
                $output = $output[0];
                $controllerLink = '';
                foreach ($output as $result) {
                    $controllerLink .= strtolower($result) . '-';
                }
                $controllerLink = substr($controllerLink, 0, -1);
                $field = $controllerLink;
                break;
            case 3://input:productoDetalle , output: PRODUCTO DETALLE
                preg_match_all("/[A-Z][a-z]+/", $field, $output);
                $output = $output[0];
                $controllerTitle = '';
                foreach ($output as $result) {
                    $controllerTitle .= strtoupper($result) . ' ';
                }
                $controllerTitle = substr($controllerTitle, 0, -1);
                $field = $controllerTitle;
                break;
            case 4://input:producto_detalle , output: Producto Detalle
                $fieldParts = explode('_', $field);
                if (count($fieldParts) > 1) {
                    $field = ucfirst($fieldParts[0]);
                    for ($i = 1; $i < count($fieldParts); $i++)
                        if ($fieldParts[$i] != 'id') {
                            $field .= ' ' . ucfirst($fieldParts[$i]);
                        }
                } else {
                    $field = ucfirst($field);
                }
                break;
            case 5://input:productoDetalle , output: Producto Detalle
                preg_match_all("/[A-Z][a-z]+/", $field, $output);
                $output = $output[0];
                $label = '';
                foreach ($output as $result) {
                    $label .= ucfirst($result) . ' ';
                }
                $label = substr($label, 0, -1);
                $field = $label;
                break;
            case 6://input:producto-detalle , output: ProductoDetalle
                $fieldParts = explode('-', $field);
                if (count($fieldParts) > 1) {
                    $field = '';
                    for ($i = 0; $i < count($fieldParts); $i++)
                        $field .= ucfirst($fieldParts[$i]);
                } else {
                    $field = ucfirst($field);
                }
                break;
            case 7://input:producto , output: PRODUCTOS
                $field = $this->pluralize($field, 'es');
                preg_match_all("/[A-Z][a-z]+/", $field, $output);
                $output = $output[0];
                $controllerTitle = '';
                foreach ($output as $result) {
                    $controllerTitle .= strtoupper($result) . ' ';
                }
                $controllerTitle = substr($controllerTitle, 0, -1);
                $field = $controllerTitle;
                break;
            case 8://input:producto_detalle , output: ProductoDetalle
                $fieldParts = explode('_', $field);
                if (count($fieldParts) > 1) {
                    $field = '';
                    for ($i = 0; $i < count($fieldParts); $i++)
                        $field .= ucfirst($fieldParts[$i]);
                } else {
                    $field = ucfirst($field);
                }
                break;
            case 9://input:producto-detalle , output: Producto Detalle
                $fieldParts = explode('-', $field);
                if (count($fieldParts) > 1) {
                    $field = ucfirst($fieldParts[0]);
                    for ($i = 1; $i < count($fieldParts); $i++)
                        if ($fieldParts[$i] != 'id') {
                            $field .= ' ' . ucfirst($fieldParts[$i]);
                        }
                } else {
                    $field = ucfirst($field);
                }
                break;
            case 10://input:producto_detalle , output: producto-detalle
                $fieldParts = explode('_', $field);
                if (count($fieldParts) > 1) {
                    $field = '';
                    foreach ($fieldParts as $result) {
                        $field .= strtolower($result) . '-';
                    }
                    $field = substr($field, 0, -1);
                } else {
                    $field = strtolower($field);
                }
                break;
            case 11://input:producto-detalle , output: productoDetalle
                $fieldParts = explode('-', $field);
                if (count($fieldParts) > 1) {
                    $field = $fieldParts[0];
                    for ($i = 1; $i < count($fieldParts); $i++)
                        $field .= ucfirst($fieldParts[$i]);
                }
                break;
        }
        return $field;
    }

    public static function showErrors($validations)
    {
        $errors = array();
        foreach ($validations as $element => $validation) {
            array_push($errors, $element . ' ' . current($validation));
        }
        return current($errors);
    }

    public static function generatePass($length = 8)
    {
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $caracteres{rand() % strlen($caracteres)};
        }
        return $str;
    }

    /**
     * Crea un array tomando como clave el parametro $field, usado genralmente con datos obtenidos de una tabla
     * 
     * @param array $inputs Array para ordenar
     * @param string $field Clave del array asociativo
     * @return array Array creado
     */
    public static function createArray($inputs, $field = 'id')
    {
        $array = array();
        foreach ($inputs as $key => $value) {
            // Verificamos si existe el campo/clave
            if (!isset($value[$field]))
                throw new Exception('No existe el campo "' . $field . '" / $inputs[' . $key . ']');
            // Creamos nuevo elemento
            $array[$value[$field]] = $value;
        }
        return $array;
    }
    
    public static function resAS($r)
    {
        // En flash no hay error
        unset($r['errors']);
        if (empty($r['info'])) {
            unset($r['info']);
        }
        if (isset($r['estado'])) {
            $r['estado'] = ($r['estado']) ? 'ok' : 'error';
        }
        // Resultado
        return implode('&', array_map(create_function('$k,$v', 'return "$k=$v";'), array_keys($r), array_values($r)));
    }

    public static function byteFormat($bytes, $unit = "", $decimals = 2)
    {
        $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4,
            'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

        $value = 0;
        if ($bytes > 0) {
            // Generate automatic prefix by bytes 
            // If wrong prefix given
            if (!array_key_exists($unit, $units)) {
                $pow = floor(log($bytes) / log(1024));
                $unit = array_search($pow, $units);
            }

            // Calculate byte value by prefix
            $value = ($bytes / pow(1024, floor($units[$unit])));
        }

        // If decimals is not numeric or decimals is less than 0 
        // then set default value
        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }

        // Format output
        return sprintf('%.' . $decimals . 'f ' . $unit, $value);
    }
    
    public static function obtenerExtension($ruta)
    {
        return substr($ruta, -4);
    }
    
    public static function obtenerParametrosFb($parametrosFb, $token, $union = ':')
    {
        $parametros = array();
        $variables = explode($token, $parametrosFb);
        foreach ($variables as $valor) {
            $temp = explode($union, $valor);            
            $parametros[$temp[0]] = $temp[1];
        }
        return $parametros;
    }
    
    public static function existeEnArray($buscar, $array, $clave) 
    {
        $i = -1;
        foreach ($array as $indice => $valor) {
            if ($valor[$clave] == $buscar) {
                $i = $indice;
                break;
            }
        }
        return $i;
    }
}
