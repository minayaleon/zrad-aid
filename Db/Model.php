<?php

/**
 * Description of Model
 *
 * @author jminaya
 */

class ZradAid_Db_Model
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db = null;
    
    public function __construct()
    {        
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /**
     * Devuelve si existe una URL
     *
     * @param string $url URL creado del nombre
     * @param array $condicion contiene los IDs de la fila a obviar de la busqueda
     * @param string $entidad Nombre de la tabla a validar
     * @param string $campo Nombre del campo a validar por defecto "url"
     * @return boolean 
     */
    public function existeUrlAmigable($url, $condicion, $entidad, $campo)
    {
        //$cond = (!empty($id)) ? " AND id <> $id" : "";
        $cond = "";
        if (!empty($condicion) && is_array($condicion)) {
            foreach ($condicion as $indice => $valor) {
               $cond .= " AND $indice <> $valor";
            }
        }
        $url = $this->_db->query("SELECT $campo FROM $entidad WHERE $campo = '$url'" . $cond)->fetchColumn();
        return (false !== $url) ? true : false;
    }
    
    /**
     * Devuelve todas las coincidencias
     *
     * @param string $url URL creado del nombre
     * @param string $entidad Nombre de la tabla a validar
     * @param string $campo Nombre del campo a validar por defecto "url"
     * @return array coincidencias
     */
    public function buscarUrlsAmigables($url, $entidad, $campo)
    {
        return $this->_db->query("SELECT $campo FROM $entidad WHERE $campo LIKE '$url%' ORDER BY $campo DESC")->fetchAll();
    }
    
    /**
     * @param string $entidad Nombre de la entidad
     * @param string $campo Nombre del campo donde se guarda la posicion
     * @param array $condicion condicionales
     * @return int Siguiente Orden
     */
    public function obtenerOrden($entidad, $campo, $condicion = null)
    {
        $select = $this->_db->select()->from($entidad, array(new Zend_Db_Expr("MAX($campo) AS maxOrden")));        
        if (!empty($condicion) && is_array($condicion)) {
            foreach ($condicion as $clave => $valor)
                $select->where($clave, $valor);
        }
        
        $row = $this->_db->fetchRow($select);
        Zend_Registry::get('log')->log($row, Zend_log::DEBUG);
        return ((null === $row) ? 1 : (int) ($row->maxOrden + 1));
    }
        
}