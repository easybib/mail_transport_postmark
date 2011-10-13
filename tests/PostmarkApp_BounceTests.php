<?php

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
require_once 'Services/PostmarkApp/BounceHook.php';
require_once 'Db.php';


class Services_PostmarkApp_Bounce_TestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    private $_dbAdapter = 'PDO_SQLITE';
    private $_dbName = 'test.db';


    public function testMissingDbTableThrowsException()
    {
        $this->setExpectedException('Exception');
        $hook = new Services_PostmarkApp_BounceHook();
    }


    public function testSaveData()
    {
        $this->_setupDb();
        $db = new Services_Tests_Db();

        $data = array(
            'ID'        => 42,
            'Type'      => 'HardBounce',
            'Email'     => 'jim@test.com',
            'BouncedAt' => '2010-04-01',
            'Details'   => 'test bounce',
        );
        $expected = array(
            'id'          => 1,
            'postmark_id' => 42,
            'type'        => 'HardBounce',
            'email'       => 'jim@test.com',
            'bounced_at'  => '2010-04-01',
            'details'     => 'test bounce',
        );

        $mapper = array(
            'ID'        => 'postmark_id',
            'Type'      => 'type',
            'Email'     => 'email',
            'BouncedAt' => 'bounced_at',
            'Details'   => 'details'
        );

        $this->request->setMethod('POST')->setPost(array())
            ->setRawBody(json_encode($data));

        $hook = new Services_PostmarkApp_BounceHook($db, $mapper);
        $hook->saveData($this->request);

        $fetched = $db->fetchRow($db->select()->where('id = ?', 1));

        $this->assertEquals($fetched->toArray(), $expected);
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
                . 'id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, postmark_id '
                . 'INTEGER NOT NULL, type VARCHAR(20) NOT NULL, email '
                . 'VARCHAR(50) NOT NULL, bounced_at VARCHAR(10) NOT NULL, '
                . 'details VARCHAR(50) NOT NULL);'
            );
            $db->query(
                'INSERT INTO tests (id, postmark_id, type, email, '
                . 'bounced_at, details) VALUES (1, 42, "HardBounce", '
                . '"jim@test.com", "2010-04-01", "test bounce");'
            );
        } catch (Exception $e) {
            throw $e;
        }

        Zend_Registry::set('tests', $db);
    }


    protected function setUp()
    {
        Zend_Session::$_unitTestEnabled = true;
        parent::setUp();
    }


    protected function tearDown()
    {
        $this->resetRequest()->resetResponse();
        parent::tearDown();
    }

}
