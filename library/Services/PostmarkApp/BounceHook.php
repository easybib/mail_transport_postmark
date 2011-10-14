<?php
/**
 * EasyBib Copyright 2008-2011
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Imagine Easy Solutions LLC ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by EasyBib. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.easybib.com/company/terms
 *
 * PHP Version 5
 *
 * @category
 * @package
 * @author   Yvan Volochine <yvan.volochine@gmail.com>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @version  GIT: $Id$
 * @link     http://www.easybib.com
 */

class Services_PostmarkApp_BounceHook
{

    /**
     * @var Zend_Db_Table_Abstract $_table
     * @see __construct
     */
    private $_table;

    /**
     * @var array $mapper Used for mapping response keys to DB column names
     * @see __construct
     */
    private $_mapper;


    /**
     * __construct
     *
     * @param Zend_Db_Table_Abstract $table  DB table used for storage
     * @param array                  $mapper Mapping of response keys to DB column names
     * @return void
     */
    public function __construct(Zend_Db_Table_Abstract $table, array $mapper)
    {
        $this->_table  = $table;
        $this->_mapper = $mapper;
    }

    /**
     * insertData Insert data into database
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     * @throws Exception if database cannot be written
     */
    public function insertData(Zend_Controller_Request_Http $request)
    {
        $data = $this->_parseRequest($request);

        try {
            $this->_table->insert($data);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * updateData Update data into database
     *
     * @param Zend_Controller_Request_Http $request Http_Request from Postmark
     * @param string                       $column  DB column for the WHERE statement
     * @return bool
     * @throws Exception if database cannot be written
     */
    public function updateData(Zend_Controller_Request_Http $request, $column)
    {
        $data = $this->_parseRequest($request);
        if (! is_string($column) || ! in_array($column, $this->_mapper)) {
            throw new Exception('Incorrect column name.');
        }

        try {
            $where = $this->_table->getAdapter()
                ->quoteInto($column . ' = ?', $data[$column]);

            $this->_table->update($data, $where);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * _parseRequest Parse HTTP request into an array using self::$_mapper
     *               to match database column names.
     *
     * @param Zend_Controller_Request_Http $request ''
     * @return array
     */
    private function _parseRequest(Zend_Controller_Request_Http $request)
    {
        $body = $request->getRawBody();
        $data = json_decode($body);

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        return $this->_mapKeys($data);
    }

    /**
     * _mapKeys
     * Adapt array given by Postmark to fit to DB column names
     *
     * @param array $arr ''
     * @return array
     */
    private function _mapKeys(array $arr)
    {
        $result = array();
        foreach ($arr as $key => $value) {
            if (isset($this->_mapper[$key])) {
                $result[$this->_mapper[$key]] = $value;
            }
        }
        return $result;
    }

}
