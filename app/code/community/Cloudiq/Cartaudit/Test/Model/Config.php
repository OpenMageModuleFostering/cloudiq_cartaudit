<?php

class Cloudiq_Cartaudit_Test_Model_Config extends EcomDev_PHPUnit_Test_Case {

    /** @var Cloudiq_Cartaudit_Model_Config $_model */
    protected $_model;

    public function setUp() {
        $this->_model = Mage::getModel('cloudiq_cartaudit/config');
    }

    public function tearDown() {
        $this->_model = null;
    }

    /**
     * @test
     */
    public function testInstance() {
        $this->assertInstanceOf('Cloudiq_Cartaudit_Model_Config', $this->_model);
    }

    /**
     *
     * @test
     * @loadFixture ~/valid_config.yaml
     */
    public function testLoad() {
        $this->assertEquals('123', Mage::getStoreConfig('cloudiq/cartaudit/app_id'));
        $this->_model->load();
        $this->assertEquals('123', $this->_model->getAppId());
    }

    /**
     * @test
     * @loadFixture ~/empty_config.yaml
     * @dataProvider dataProvider
     */
    public function testSave($data) {
        $this->_model->setData($data);
        $this->_model->save();
        $this->assertEquals('123', Mage::getStoreConfig('cloudiq/cartaudit/app_id'));
    }

    /**
     * @test
     * @loadFixture ~/valid_config.yaml
     * @dataProvider dataProvider
     */
    public function testRequiredField($fieldname) {
        $this->_model->load();

        $this->_model->unsetData($fieldname);

        $validation_result = $this->_model->validate();

        $this->assertTrue(is_array($validation_result));
        $this->assertEquals(1, count($validation_result));
    }

    /**
     * @test
     * @fixture ~/valid_config.yaml
     * @dataProvider dataProvider
     */
    public function testOptionalField($fieldname) {
        $this->_model->load();

        $this->_model->unsetData($fieldname);

        $validation_result = $this->_model->validate();

        $this->assertTrue(is_array($validation_result));
        $this->assertTrue(empty($validation_result));
    }

    /**
     * @test
     * @fixture ~/valid_config.yaml
     */
    public function testCanEstimateRevenueLost() {
        $this->_model->load();

        $this->assertTrue($this->_model->canEstimateRevenueLost());
    }

    /**
     * @test
     * @fixture ~/valid_config.yaml
     * @dataProvider dataProvider
     */
    public function testCanEstimateRevenueLostMissingField($fieldname) {
        $this->_model->load();

        $this->_model->unsetData($fieldname);

        $this->assertFalse($this->_model->canEstimateRevenueLost());
        $this->assertNull($this->_model->calculateDropOffRate());
        $this->assertNull($this->_model->calculateRevenueLost());
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testCalculateDropOffRate($data, $expected_rate) {
        $this->_model->setData($data);

        $this->assertTrue($this->_model->canEstimateRevenueLost());
        $this->assertEquals($expected_rate, $this->_model->calculateDropOffRate());
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testCalculateRevenueLost($data, $expected_loss) {
        $this->_model->setData($data);

        $this->assertTrue($this->_model->canEstimateRevenueLost());
        $this->assertEquals($expected_loss, $this->_model->calculateRevenueLost());
    }

    /**
     * @test
     * @fixture ~/valid_config.yaml
     */
    public function testIsConfigured() {
        $this->_model->load();

        $this->assertTrue($this->_model->isConfigured());
    }

    /**
     * @test
     * @fixture ~/valid_config.yaml
     * @dataProvider dataProvider
     */
    public function testIsConfiguredMissingField($fieldname) {
        $this->_model->load();

        $this->_model->unsetData($fieldname);

        $this->assertFalse($this->_model->isConfigured());
    }
}
