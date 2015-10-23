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
class ZradAid_Db
{
    /**
     * @param string $entidad Nombre de la entidad
     * @param string $campo Nombre del campo donde se guarda la posicion
     * @param array $condicion condicionales
     * @return int Siguiente Orden
     */
    public static function getPosicion($entidad, $campo, $condicion = null)
    {        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from($entidad, array(new Zend_Db_Expr("MAX($campo) AS maxOrden")));        
        if (!empty($condicion) && is_array($condicion)) {
            foreach ($condicion as $clave => $valor)
                $select->where($clave, $valor);
        }
        
        $row = $db->fetchOne($select);
        //Zend_Registry::get('log')->log($row, Zend_log::DEBUG);
        return ((null === $row) ? 1 : (((int) $row) + 1));
    }

}
