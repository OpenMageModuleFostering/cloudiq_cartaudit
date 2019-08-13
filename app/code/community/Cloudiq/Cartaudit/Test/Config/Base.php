<?php

class Cloudiq_Cartaudit_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config {

    /**
     * @test
     */
    public function testBasicConfiguration() {
        $this->assertModuleCodePool('community');
        $this->assertModuleVersion('1.0.0');
    }

    /**
     * @test
     */
    public function testClassAliases() {
        $this->assertHelperAlias('cloudiq_cartaudit', 'Cloudiq_Cartaudit_Helper_Data');
        $this->assertModelAlias('cloudiq_cartaudit/test', 'Cloudiq_Cartaudit_Model_Test');
        $this->assertBlockAlias('cloudiq_cartaudit/test', 'Cloudiq_Cartaudit_Block_Test');
    }

    /**
     * @test
     */
    public function testDataHelperExists() {
        $this->assertInstanceOf('Cloudiq_Cartaudit_Helper_Data', Mage::helper('cloudiq_cartaudit'));
    }
}
