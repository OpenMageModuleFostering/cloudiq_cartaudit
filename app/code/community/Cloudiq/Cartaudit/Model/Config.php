<?php

/**
 * Class Cloudiq_Cartaudit_Model_Config
 *
 * @method getEnabled()
 * @method getAppId()
 * @method setAppId()
 * @method getCloudiqTag()
 * @method setCloudiqTab()
 * @method getAppName()
 * @method getIndustry()
 * @method getWebsiteUrl()
 * @method getCartConfirmationUrl()
 * @method hasAverageCartValue()
 * @method getAverageCartValue()
 * @method hasCartsStartedPerMonth()
 * @method getCartsStartedPerMonth()
 * @method hasCartsCompletedPerMonth()
 * @method getCartsCompletedPerMonth()
 */
class Cloudiq_Cartaudit_Model_Config extends Varien_Object {

    const CONFIG_KEY_PREFIX = "cloudiq/cartaudit";

    /**
     * Load the config data from the database.
     *
     * @return Cloudiq_Cartaudit_Model_Config
     */
    public function load() {
        $config_data = Mage::getStoreConfig(self::CONFIG_KEY_PREFIX);

        $this->unsetData();

        if (is_array($config_data) && !empty($config_data)) {
            foreach ($config_data as $key => $value) {
                $this->setData($key, $value);
            }
        }

        return $this;
    }

    /**
     * Save the config data to the database.
     *
     * @return Cloudiq_Cartaudit_Model_Config
     */
    public function save() {
        $errors = $this->validate();

        if (empty($errors)) {
            $data = $this->getData();

            $system_config = Mage::getConfig();

            foreach ($data as $key => $value) {
                $system_config->saveConfig(self::CONFIG_KEY_PREFIX . "/" . $key, $value);
            }

            $system_config->reinit();
            Mage::app()->reinitStores();
        } else {
            Mage::throwException("Could not save Cloudiq_Cartaudit_Model_Config due to validation errors: " . implode(", ", $errors));
        }

        return $this;
    }

    /**
     * Validate the config data. Return an array of validation errors, or an empty array if validation passed.
     *
     * @return array validation error messages.
     */
    public function validate() {
        $errors = array();

        // Enabled = 0|1
        if (!Zend_Validate::is($this->getEnabled(), "InArray", array("haystack" => array("0", "1")))) {
            $errors["enabled"] = "Enabled must be set to 0 or 1.";
        }

        // App Name must not be empty
        if (!Zend_Validate::is($this->getAppName(), "NotEmpty")) {
            $errors["appName"] = "App Name can not be empty.";
        }

        // Industry must match one of the available options
        if ($this->getIndustry() && !Zend_Validate::is($this->getIndustry(), "InArray", array("haystack" => array_map(function ($el) { return $el["value"]; }, Mage::helper('cloudiq_cartaudit/options')->getIndustryOptions())))) {
            $errors["industry"] = "Invalid Industry value.";
        }

        // Site URL must be valid
        if (!filter_var($this->getWebsiteUrl(), FILTER_VALIDATE_URL)) {
            $errors["websiteUrl"] = "Website URL must be a valid URL.";
        }

        // Cart Confirmation URL must be valid
        if ($this->getCartConfirmationUrl() && !filter_var($this->getCartConfirmationUrl(), FILTER_VALIDATE_URL)) {
            $errors["cartConfirmationUrl"] = "Cart Confirmation URL must be a valid URL.";
        }

        // Average Cart Value must be present
        if (!Zend_Validate::is($this->getAverageCartValue(), "NotEmpty")) {
            $errors["averageCartValue"] = "Average Cart Value can not be empty.";
        }

        // Carts Started Per Month must be an integer
        if (!Zend_Validate::is($this->getCartsStartedPerMonth(), "Int")) {
            $errors["cartsStartedPerMonth"] = "Carts Started Per Month must be an number.";
        }

        // Carts Completed Per Month must be an integer
        if (!Zend_Validate::is($this->getCartsCompletedPerMonth(), "Int")) {
            $errors["cartsCompletedPerMonth"] = "Cars Completed Per Month must be an number.";
        }

        return $errors;
    }

    /**
     * Check if the config contains the data needed to estimate the revenue lost due to uncompleted carts.
     *
     * @return bool
     */
    public function canEstimateRevenueLost() {
        return (
            $this->hasAverageCartValue()
            && $this->hasCartsStartedPerMonth()
            && $this->hasCartsCompletedPerMonth()
        );
    }

    /**
     * Calculate the drop off rate based on the number of carts started and completed per month.
     *
     * @return float The drop off rate as a percentage.
     */
    public function calculateDropOffRate() {
        if (!$this->canEstimateRevenueLost()) {
            return null;
        }

        $carts_started = intval($this->getCartsStartedPerMonth());
        $carts_completed = intval($this->getCartsCompletedPerMonth());

        if ($carts_started == $carts_completed) {
            return 0;
        }

        return (100 / $carts_started) * ($carts_started - $carts_completed);
    }

    /**
     * Calculate the estimated revenue lost based on the number of incomplete carts per month.
     *
     * @return float
     */
    public function calculateRevenueLost() {
        if (!$this->canEstimateRevenueLost()) {
            return null;
        }

        $carts_started = intval($this->getCartsStartedPerMonth());
        $carts_completed = intval($this->getCartsCompletedPerMonth());
        $average_cart = floatval($this->getAverageCartValue());

        return ($carts_started - $carts_completed) * $average_cart;
    }

    /**
     * Check if the model is configured to print the cloud.IQ tag.
     *
     * @return bool
     */
    public function isConfigured() {
        return ($this->getEnabled() && $this->getCloudiqTag() != "");
    }

    /**
     * Convert all the attributes to an array with camelCase keys
     *
     * @param array $attributes array of attributes to include.
     * @return array
     */
    public function toCamelCaseArray(array $attributes = array()) {
        $data = $this->__toArray($attributes);
        $camelData = array();
        foreach ($data as $key => $value) {
            $camelData[lcfirst($this->_camelize($key))] = $value;
        }
        return $camelData;
    }
}
