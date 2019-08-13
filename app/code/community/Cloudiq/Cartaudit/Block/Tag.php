<?php

class Cloudiq_Cartaudit_Block_Tag extends Mage_Core_Block_Abstract {

    /** @var Cloudiq_Cartaudit_Model_Config $_config */
    protected $_config;

    public function _construct() {
        $this->_config = Mage::getModel('cloudiq_cartaudit/config');
        $this->_config->load();
    }

    /**
     * Return the cloud.IQ tag as HTML.
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->_config->isConfigured()) {
            return $this->_config->getCloudiqTag();
        }
        return "";
    }
}
