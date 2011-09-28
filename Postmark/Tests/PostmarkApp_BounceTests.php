<?php

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
require_once dirname(__DIR__) . '/Services/PostmarkApp/BounceHook.php';
require_once 'Db.php';


class Services_PostmarkApp_Bounce_TestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    private $_dbAdapter = 'PDO_SQLITE';
    private $_dbName = 'test.db';


    public function testMissingDbTableThrowsException()
    {
        $this->setExpectedException('Exception');
        $hook = new Postmark_Services_PostmarkApp_BounceHook();
    }


    public function testSaveData()
    {
        $this->_setupDb();
        $db = new Postmark_Services_Tests_Db();

        $data = array(
            'id'         => 1,
            'type'       => 'HardBounce',
            'email'      => 'jim@test.com',
            'bounced_at' => '2011-09-28',
            'details'    => 'test bounce',
        );

        $this->request->setMethod('POST')->setPost(array())
            ->setRawBody(json_encode($data));

        $hook = new Postmark_Services_PostmarkApp_BounceHook($db);
        $hook->saveData($this->request);

        $fetched = $db->fetchRow($db->select()->where('id = ?', 1));

        $this->assertEquals($fetched->toArray(), $data);
    }


    private function _setupDb()
    {
        include_once 'Zend/Db/Table.php';

        $dbName = __DIR__ . '/_files/' . $this->_dbName;

        $db = Zend_Db::factory(
            $this->_dbAdapter, array('dbname' => $dbName)
        );

        Zend_Db_Table::setDefaultAdapter($db);

        try {
            $db->query('DROP TABLE IF EXISTS tests');
            $db->query(
                'CREATE TABLE IF NOT EXISTS tests ('
                . 'id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, '
                . 'type VARCHAR(20) NOT NULL, email VARCHAR(50) NOT NULL, '
                . 'bounced_at VARCHAR(10) NOT NULL, details VARCHAR(50) NOT '
                . 'NULL);'
            );
        } catch (Exception $e) {
            throw $e;
        }

        Zend_Registry::set('tests', $db);
    }

}
