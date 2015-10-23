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
class ZradAid_Validate_Age extends Zend_Validate_Abstract
{

    const NOT_GREATER = 'notGreater';
    const NOT_LESS = 'notLess';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_GREATER => "La edad mínima es '%min%', usted tiene '%value%'",
        self::NOT_LESS => "La edad máxima es '%max%', ustd tiene '%value%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'max' => '_max',
        'min' => '_min'
    );

    /**
     * Edada maxima
     * 
     * @var integer
     */
    protected $_max;

    /**
     * Edad minima
     * 
     * @var integer
     */
    protected $_min;

    /**
     * @return void
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['min'] = array_shift($options);
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }
            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            $options['min'] = 0;
        }

        $this->setMin($options['min']);

        if (array_key_exists('max', $options)) {
            $this->setMax($options['max']);
        }
    }

    /**
     * Returns the min option
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  integer $min
     * @throws Zend_Validate_Exception
     * @return ZradAid_Validate_Age Provides a fluent interface
     */
    public function setMin($min)
    {
        if (null !== $this->_max && $min > $this->_max) {
            /**
             * @see Zend_Validate_Exception
             */
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("La edad minima debe sera menor o igual a la edad mayor, pero $min >"
                . " $this->_max");
        }
        $this->_min = max(0, (integer) $min);
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return integer|null
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  integer|null $max
     * @throws Zend_Validate_Exception
     * @return ZradAid_Validate_Age Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->_max = null;
        } else if ($max < $this->_min) {
            /**
             * @see Zend_Validate_Exception
             */
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("La edad mayor debe ser mayor o igual a la edad menor, pero "
                . "$max < $this->_min");
        } else {
            $this->_max = (integer) $max;
        }

        return $this;
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
        $age = 0;

        if (is_string($value)) {
            $birth = new Zend_Date($value);
            $today = new Zend_Date();
            $diff = $today->sub($birth)->toValue();
            $age = floor($diff / 3600 / 24 / 365);
            $value = $age;
        }

        if (is_int($value)) {
            
        }

        $this->_setValue($value);

        if ($age < $this->_min) {
            $this->_error(self::NOT_GREATER);
        }

        if (null !== $this->_max && $this->_max < $age) {
            $this->_error(self::NOT_LESS);
        }

        if (count($this->_messages)) {
            return false;
        } else {
            return true;
        }
    }

}
