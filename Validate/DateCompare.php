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
class ZradAid_Validate_DateCompare extends Zend_Validate_Abstract
{
    /**
     * Error codes
     * @const string
     */

    const NOT_SAME = 'notSame';
    const MISSING_TOKEN = 'missingToken';
    const NOT_LATER = 'notLater';
    const NOT_EARLIER = 'notEarlier';
    const NOT_BETWEEN = 'notBetween';

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SAME => "The date '%token%' does not match the given '%value%'",
        self::NOT_BETWEEN => "The date '%token%' is not in the valid range",
        self::NOT_LATER => "La fecha '%token%' es mas reciente que '%value%'",
        self::NOT_EARLIER => "La fecha '%token%' no es menor que '%value%'",
        self::MISSING_TOKEN => 'No date was provided to match against',
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'token' => '_tokenString'
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_tokenString;
    protected $_token;
    protected $_compare;
    protected $_lang;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @param  mixed $compare
     * @return void
     */
    public function __construct($token = null, $compare = true, $lang = 'en')
    {        
        $this->_lang = $lang;
        if (null !== $token) {
            $this->setToken($token);
            $this->setCompare($compare);
        }
    }

    /**
     * Set token against which to compare
     *
     * @param  mixed $token
     * @return Zend_Validate_Identical
     */
    public function setToken($token)
    {
        $this->_tokenString = (string) $token;
        $this->_token = $token;
        return $this;
    }

    /**
     * Retrieve token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Set compare against which to compare
     *
     * @param  mixed $compare
     * @return Zend_Validate_Identical
     */
    public function setCompare($compare)
    {
        $this->_compareString = (string) $compare;
        $this->_compare = $compare;
        return $this;
    }

    /**
     * Retrieve compare
     *
     * @return string
     */
    public function getCompare()
    {
        return $this->_compare;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($this->_lang == 'es') {
            $date = new Zend_Date($value, 'yyyy-MM-dd');
            $value = $date->get('dd/MM/yyyy');
        }        
        
        $this->_setValue((string) $value);
        $token = $this->getToken();

        if ($token === null) {
            $this->_error(self::MISSING_TOKEN);
            return false;
        }                
        
        // Falta ajustarlo para cualquier idioma
        $date1 = new Zend_Date($value, 'd/M/yyyy');
        $date2 = new Zend_Date($token, 'd/M/yyyy');
        
        if ($this->getCompare() === true) {
            if ($date1->compare($date2) < 0 || $date1->equals($date2)) {
                //echo $token . ' es mas reciente ' . $value;
                $this->_error(self::NOT_LATER);
                return false;
            }
        } else if ($this->getCompare() === false) {
            if ($date1->compare($date2) > 0 || $date1->equals($date2)) {
                $this->_error(self::NOT_EARLIER);
                return false;
            }
        } else if ($this->getCompare() === null) {
            if (!$date1->equals($date2)) {
                $this->_error(self::NOT_SAME);
                return false;
            }
        } else {
            $date3 = new Zend_Date($this->getCompare());
            if ($date1->compare($date2) < 0 || $date1->compare($date3) > 0) {
                $this->_error(self::NOT_BETWEEN);
                return false;
            }
        }
        return true;
    }

}
