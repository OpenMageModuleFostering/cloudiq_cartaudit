<?php

class Cloudiq_Cartaudit_Model_Observer extends Varien_Object {

    /**
     * Save the cartAudit data when the cloud.IQ configuration is saved.
     *
     * @param $observer
     */
    public function observeCloudiqCoreConfigSave($observer) {
        $request = $observer->getRequest();

        /** @var Cloudiq_Cartaudit_Helper_Data $helper */
        $helper = Mage::helper('cloudiq_cartaudit');

        /** @var Mage_Adminhtml_Model_Session $admin_session */
        $admin_session = Mage::getSingleton('adminhtml/session');

        $form_data = new Varien_Object($request->getParam('cartaudit'));

        if (count($form_data->getData()) == 0) {
            // No form data, nothing to save
            return;
        }

        // Save the form data in session to repopulate the fields in case or errors
        $admin_session->setData('cartaudit_form_data', $form_data);

        /** @var Cloudiq_Cartaudit_Model_Config $config */
        $config = Mage::getModel('cloudiq_cartaudit/config');
        $config->load();

        // Update config with the form data
        $config->addData(array(
            'enabled' => $form_data->getData('enabled'),

            'app_name' => $form_data->getData('cart/app_name'),
            'industry' => $form_data->getData('cart/industry'),

            'website_url'           => $form_data->getData('cart/website_url'),
            'cart_confirmation_url' => $form_data->getData('cart/cart_confirmation_url'),

            'average_cart_value' => $form_data->getData('cart/average_cart_value'),

            'carts_started_per_month'   => $form_data->getData('cart/carts_started_per_month'),
            'carts_completed_per_month' => $form_data->getData('cart/carts_completed_per_month'),
        ));

        // Validate the new config values
        $errors = $config->validate();
        if (!empty($errors)) {
            foreach ($errors as $field => $message) {
                $admin_session->addError($helper->__("cartAudit: " . $message));
            }
            return;
        }

        // Submit the changes to the API
        /** @var Cloudiq_Cartaudit_Model_Api_Cartaudit_Store_Cartaudit $api */
        $api = Mage::getModel('cloudiq_cartaudit/api_cartaudit_store_cartaudit');

        $request = $api->buildRequest(array("config" => $config));
        $response = $request->send(Zend_Http_Client::POST);

        if ($api->isResponseSuccessful($response)) {
            // API call succeeded, update the appId and cloudiqTag
            $response_data = $response->getResponse()->cartAudit;
            if ($response_data->appId && is_numeric((string) $response_data->appId)) {
                $config->setAppId((string) $response_data->appId);
            }
            if ($response_data->cloudIqTag) {
                $config->setCloudiqTag(html_entity_decode((string) $response_data->cloudIqTag));
            }
        } else {
            $admin_session->addError("cartAudit API error: " . $api->getResponseErrorMessage());
            return;
        }

        $config->save();
        $admin_session->addSuccess($helper->__("cartAudit: Configuration saved."));
    }
}
