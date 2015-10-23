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
class ZradAid_Validate_Captcha extends Zend_Validate_Abstract
{
	const MSG_CAPTCHA = 'msgCaptcha';

	protected $_messageTemplates = array(
        self::MSG_CAPTCHA => "El codigo de verificación no era el mismo",
    );

	public function isValid($value)
	{
		$isValid = true;
		$this->_setValue($value);
		
		$captcha = new Zend_Session_Namespace('captcha');
		$word = $captcha->word;
		
		if($word != $value){
			$this->_error(self::MSG_CAPTCHA);
			$isValid = false;
		}

		return $isValid;
	}
}
