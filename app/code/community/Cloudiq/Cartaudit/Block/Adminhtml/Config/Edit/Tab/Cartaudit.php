<?php

class Cloudiq_Cartaudit_Block_Adminhtml_Config_Edit_Tab_Cartaudit extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /** @var Cloudiq_Cartaudit_Model_Config $_config */
    protected $_config;

    /** @var Cloudiq_Cartaudit_Helper_Options $_options */
    protected $_options;

    /** @var Varien_Object $_input */
    protected $_input;

    public function _construct() {
        parent::_construct();

        $this->_config = Mage::getModel('cloudiq_cartaudit/config');
        $this->_config->load();

        $this->_options = Mage::helper('cloudiq_cartaudit/options');

        $input_data = Mage::getSingleton('adminhtml/session')->getData('cartaudit_form_data', true);
        if ($input_data && count($input_data->getData()) > 0) {
            $this->_input = $input_data;
        }

        $this->setTemplate('cloudiq/cartaudit/tab/config.phtml');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();

        $this->_addServiceFieldset($form);
        $this->_addCartFieldset($form);

        $this->setForm($form);
    }

    protected function _addServiceFieldset($form) {
        $fieldset = $form->addFieldset('service', array('legend' => $this->__('Service')));

        $fieldset->addField('cartaudit[enabled]', 'select', array(
            'label'  => $this->__('Enable?'),
            'title'  => $this->__('Enable?'),
            'name'   => 'cartaudit[enabled]',
            'value'  => ($this->_input) ? $this->_input->getData('enabled') : $this->_config->getEnabled(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
        ));
    }

    protected function _addCartFieldset($form) {
        $fieldset = $form->addFieldset('cart', array('legend' => $this->__('Tell us about your cart')));

        $fieldset->addField('cartaudit[cart][app_name]', 'text', array(
            'label'    => $this->__('Company Name'),
            'title'    => $this->__('Company Name'),
            'name'     => 'cartaudit[cart][app_name]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/app_name') : $this->_config->getAppName(),
            'required' => true
        ));

        $industry_options = $this->_options->getIndustryOptions();
        array_unshift($industry_options, array('value' => '', 'label' => 'Please select...'));

        $fieldset->addField('cartaudit[cart][industry]', 'select', array(
            'label'    => $this->__('Industry'),
            'text'     => $this->__('Industry'),
            'name'     => 'cartaudit[cart][industry]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/industry') : $this->_config->getIndustry(),
            'values'   => $industry_options,
            'required' => false,
        ));

        $fieldset->addField('cartaudit[cart][website_url]', 'text', array(
            'label'    => $this->__('Website URL'),
            'title'    => $this->__('Website URL'),
            'note'     => $this->__("Please tell us your site's URL."),
            'name'     => 'cartaudit[cart][website_url]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/website_url') : $this->_config->getWebsiteUrl(),
            'required' => true,
            'class'    => 'validate-url'
        ));

        $fieldset->addField('cartaudit[cart][cart_confirmation_url]', 'text', array(
            'label'    => $this->__('Confirmation page URL'),
            'title'    => $this->__('Confirmation page URL'),
            'name'     => 'cartaudit[cart][cart_confirmation_url]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/cart_confirmation_url') : $this->_config->getCartConfirmationUrl(),
            'required' => false,
            'class'    => 'validate-url'
        ));

        $fieldset->addField('cartaudit[cart][average_cart_value]', 'text', array(
            'label'    => $this->__('Average cart value'),
            'title'    => $this->__('Average cart value'),
            'note'     => $this->__("Estimate the average value of a cart transaction completed on your site. We use this to estimate the revenue that could be recovered."),
            'name'     => 'cartaudit[cart][average_cart_value]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/average_cart_value') : $this->_config->getAverageCartValue(),
            'required' => true,
            'class'    => 'validate-number'
        ));

        $fieldset->addField('cartaudit[cart][carts_started_per_month]', 'text', array(
            'label'    => $this->__('Carts started per month'),
            'title'    => $this->__('Carts started per month'),
            'note'     => $this->__("How many users start but don't complete a cart in a month? We use this for reporting purposes."),
            'name'     => 'cartaudit[cart][carts_started_per_month]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/carts_started_per_month') : $this->_config->getCartsStartedPerMonth(),
            'required' => true,
            'class'    => 'validate-digits'
        ));

        $fieldset->addField('cartaudit[cart][carts_completed_per_month]', 'text', array(
            'label'    => $this->__('Carts completed per month'),
            'title'    => $this->__('Carts completed per month'),
            'note'     => $this->__("Estimate the number of carts successfully completed in a month. We use this to calculate drop out rates."),
            'name'     => 'cartaudit[cart][carts_completed_per_month]',
            'value'    => ($this->_input) ? $this->_input->getData('cart/carts_completed_per_month') : $this->_config->getCartsCompletedPerMonth(),
            'required' => true,
            'class'    => 'validate-digits'
        ));
    }

    public function getTabLabel() {
        return $this->__('cartAudit Settings');
    }

    public function getTabTitle() {
        return $this->__('cartAudit Settings');
    }

    public function canShowTab() {
        return Mage::helper('cloudiq_core/config')->hasBeenSetUp();
    }

    public function isHidden() {
        return !(Mage::helper('cloudiq_core/config')->hasBeenSetUp());
    }
}
