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
class ZradAid_Texto
{

    /**
     * @param string $cadena banner, producto, banner_lateral, producto_detalle
     * @param int $tipo tipo de cadena (entidad,objeto,etc)
     * @return string banner,producto, banner-lateral, producto-detalle     
     */
    public static function aRuta($cadena, $tipo = 1)
    {
        /**
         * 1: entidad
         * 2: objeto
         */
        $ruta = '';

        switch ($tipo) {
            case 1:
                $partes = explode('_', $cadena);
                if (count($partes) >= 0) {
                    foreach ($partes as $parte) {
                        $ruta .= strtolower($parte) . '-';
                    }
                    $ruta = substr($ruta, 0, -1);
                }
                break;
        }
        return $ruta;
    }
    
    public static function limpiar($cadena)
    {
        $dirty = array("Á", "É", "Í", "Ó", "Ú", "Ñ","á", "é", "í", "ó", "ú", "ñ",'ü');
        $clean = array("A", "E", "I", "O", "U", "N","a", "e", "i", "o", "u", "n",'u');        
        return mb_strtolower(str_replace($dirty, $clean, $cadena),'UTF-8');
    }
}
