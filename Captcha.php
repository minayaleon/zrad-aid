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
class ZradAid_Captcha
{

    /**
     * @var string
     */
    private $_imgdir = 'resources/captcha/images/';

    /**
     * @var string
     */
    private $_font = 'resources/captcha/fonts/ROCKB.TTF';

    /**
     * @var string
     */
    private $_baseUrl = '';

    /**
     * @param string $baseUrl
     */
    public function setbaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
    }

    /**
     * @param string $word
     */
    public function isValid($word)
    {
        $result = false;
        $captcha = new Zend_Session_Namespace('captcha');

        if ($word == $captcha->word) {
            $result = true;
            unset($captcha->id);
            unset($captcha->word);
        }
        return $result;
    }

    public function generate($wordLen, $width, $height)
    {
        //limipamos la carpeta
        $this->_recursiveDelete($this->_imgdir);
        $captchaImage = new Zend_Captcha_Image(array(
                'name' => "captcha",
                'wordLen' => $wordLen,
                'timeout' => 600,
                'dotNoiseLevel' => 20,
                'width' => $width,
                'height' => $height,
                'lineNoiseLevel' => 0,
                'font' => $this->_font,
                'fontSize' => 16,
                'imgdir' => $this->_imgdir,
                'imgurl' => $this->_baseUrl . '/' . $this->_imgdir
            ));
        //generamos la imagen
        $id = $captchaImage->generate();
        $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $id);
        //guardamos en ession
        $captcha = new Zend_Session_Namespace('captcha');
        $captcha->id = $id;
        $captcha->word = $captchaSession->word;
        //retornamos la imagen
        return $id;
    }

    private function _recursiveDelete($str)
    {
        if (is_file($str)) {
            return @unlink($str);
        } else if (is_dir($str)) {
            $scan = glob(rtrim($str, '/') . '/*');
            foreach ($scan as $path) {
                $this->_recursiveDelete($path);
            }
        }
    }

}