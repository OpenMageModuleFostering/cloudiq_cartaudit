<?php

class Cloudiq_Cartaudit_Test_Model_Api_Cartaudit_Store_Cartaudit extends EcomDev_PHPUnit_Test_Case {

    /** @var Cloudiq_Cartaudit_Model_Api_Cartaudit_Store_Cartaudit $_model */
    protected $_model;

    public function setUp() {
        $this->_model = Mage::getModel('cloudiq_cartaudit/api_cartaudit_store_cartaudit');
    }

    public function tearDown() {
        $this->_model = null;
    }

    /**
     * @test
     * @loadFixture ~/valid_config.yaml
     */
    public function testRequestEndpoint() {
        /** @var Cloudiq_Cartaudit_Model_Config $config */
        $config = Mage::getModel('cloudiq_cartaudit/config');
        $config->load();

        /** @var Cloudiq_Core_Model_Api_Request $request */
        $request = $this->_model->buildRequest(array('config' => $config));
        $request_parameters = $request->getParameters();

        $this->assertTrue(is_array($request_parameters), 'Request parameters missing.');
        $this->assertArrayHasKey("mode", $request_parameters);
        $this->assertEquals("store", $request_parameters["mode"]);
        $this->assertArrayHasKey("action", $request_parameters);
        $this->assertEquals("cartaudit", $request_parameters["action"]);
    }

    /**
     * @test
     * @loadFixture ~/valid_config.yaml
     * @dataProvider dataProvider
     */
    public function testRequestRequiredParameter($parameter) {
        /** @var Cloudiq_Cartaudit_Model_Config $config */
        $config = Mage::getModel('cloudiq_cartaudit/config');
        $config->load();

        /** @var Cloudiq_Core_Model_Api_Request $request */
        $request = $this->_model->buildRequest(array('config' => $config));
        $request_parameters = $request->getParameters();

        $this->assertTrue(is_array($request_parameters), 'Request parameters missing.');
        $this->assertArrayHasKey($parameter, $request_parameters);
    }

    /**
     * @test
     */
    public function testResponseSuccessful() {
        /** @var Cloudiq_Core_Model_Api_Response $response */
        $response = Mage::getModel('cloudiq_core/api_response');
        $response->populate($this->_loadHttpResponseFromFile("testResponseSuccessful.txt"));

        $this->assertTrue($this->_model->isResponseSuccessful($response));
    }

    /**
     * @test
     */
    public function testResponseUnsuccessful() {
        /** @var Cloudiq_Core_Model_Api_Response $response */
        $response = Mage::getModel('cloudiq_core/api_response');
        $response->populate($this->_loadHttpResponseFromFile("testResponseUnsuccessful.txt"));

        $this->assertFalse($this->_model->isResponseSuccessful($response));
    }

    /**
     * @test
     */
    public function testResponseMissing() {
        $this->assertFalse($this->_model->isResponseSuccessful(null));
    }

    /**
     * @test
     */
    public function testCartauditResponseUnsuccessful() {
        /** @var Cloudiq_Core_Model_Api_Response $response */
        $response = Mage::getModel('cloudiq_core/api_response');
        $response->populate($this->_loadHttpResponseFromFile("testCartauditResponseUnsuccessful.txt"));

        $this->assertFalse($this->_model->isResponseSuccessful($response));
    }

    /**
     * @test
     */
    public function testCartauditResponseMissing() {
        /** @var Cloudiq_Core_Model_Api_Response $response */
        $response = Mage::getModel('cloudiq_core/api_response');
        $response->populate($this->_loadHttpResponseFromFile("testCartauditResponseMissing.txt"));

        $this->assertFalse($this->_model->isResponseSuccessful($response));
    }

    /**
     * Create a Zend_Http_Response object using raw HTTP response data from the specified file.
     *
     * @param $filename Raw response file, relative to Test/Model/Api/Cartaudit/Store/Cartaudit/data/
     *
     * @return Zend_Http_Response
     */protected function _loadHttpResponseFromFile($filename) {
        $directory_tree = array(
            Mage::getModuleDir('', 'Cloudiq_Cartaudit'),
            'Test', 'Model', 'Api', 'Cartaudit', 'Store', 'Cartaudit',
            'data',
            $filename
        );
        $file_path = join(DS, $directory_tree);

        return Zend_Http_Response::fromString(file_get_contents($file_path));
    }
}
