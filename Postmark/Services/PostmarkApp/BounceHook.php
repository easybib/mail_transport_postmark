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

class Postmark_Services_PostmarkApp_BounceHook
{

    /**
     * @var Zend_Db_Table_Abstract $_table
     */
    private $_table;


    /**
     * __construct
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function __construct(Zend_Db_Table_Abstract $table)
    {
        $this->_table = $table;
    }

    /**
     * saveData
     *
     * @param Zend_Controller_Request_Http $request
     * @return void
     */
    public function saveData(Zend_Controller_Request_Http $request)
    {
        $body = $request->getRawBody();
        $data = json_decode($body);
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        try {
            $this->_table->insert($data);
        } catch (Exception $e) {
            throw $e;
        }
    }

}