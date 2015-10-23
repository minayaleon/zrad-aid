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
class ZradAid_Pdf_Html
{

    /**
     * 
     * 
     */
    public function getElements($text)
    {
        $result = array();
        
        // Listas <li></li>
        $text = preg_replace('/\n|\r|\n\r/','',$text);
        $text = strip_tags($text, '<li>');
        $text = preg_replace('/<\/li>.*?<li>/','</li><li>',$text);
        $text = str_replace('</li><li>', '|', $text);
        $text = strip_tags($text);
        $tagLi = explode('|', $text);
        // Limipamos
        for ($i = 0; $i < count($tagLi); $i++) {
            $tagLi[$i] = trim($tagLi[$i]);
        }        
        $result['li'] = $tagLi;
        
        // Retornamos el resultado
        return $result;
    }

}

