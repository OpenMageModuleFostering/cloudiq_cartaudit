<?php

class Cloudiq_Cartaudit_Helper_Options extends Mage_Core_Helper_Abstract {

    /**
     * Return the list of available options for the Industry drop down.
     *
     * @return array
     */
    public function getIndustryOptions() {
        return array(
            array('value' => 'automotive',             'label' => $this->__("Automotive")),
            array('value' => 'agriculture',            'label' => $this->__("Agriculture")),
            array('value' => 'banking_finance',        'label' => $this->__("Banking & Finance")),
            array('value' => 'books_stationary',       'label' => $this->__("Books & Stationary")),
            array('value' => 'construction',           'label' => $this->__("Construction")),
            array('value' => 'consulting',             'label' => $this->__("Consulting")),
            array('value' => 'education',              'label' => $this->__("Education")),
            array('value' => 'electronics',            'label' => $this->__("Electronics")),
            array('value' => 'engineering',            'label' => $this->__("Engineering")),
            array('value' => 'entertainment',          'label' => $this->__("Entertainment")),
            array('value' => 'environmental',          'label' => $this->__("Environmental")),
            array('value' => 'fashion',                'label' => $this->__("Fashion")),
            array('value' => 'food_beverage',          'label' => $this->__("Food & Beverage")),
            array('value' => 'government',             'label' => $this->__("Government")),
            array('value' => 'healthcare_beauty',      'label' => $this->__("Healthcare & Beauty")),
            array('value' => 'hospitality',            'label' => $this->__("Hospitality")),
            array('value' => 'household_goods',        'label' => $this->__("Household Goods")),
            array('value' => 'insurance',              'label' => $this->__("Insurance")),
            array('value' => 'jewellery',              'label' => $this->__("Jewellery")),
            array('value' => 'kids_toys',              'label' => $this->__("Kids/Toys")),
            array('value' => 'lifestyle_sports_goods', 'label' => $this->__("Lifestyle/Sports Goods")),
            array('value' => 'leisure_tourism',        'label' => $this->__("Leisure & Tourism")),
            array('value' => 'machinery',              'label' => $this->__("Machinery")),
            array('value' => 'manufacturing',          'label' => $this->__("Manufacturing")),
            array('value' => 'media',                  'label' => $this->__("Media")),
            array('value' => 'not_for_profit',         'label' => $this->__("Not For Profit")),
            array('value' => 'other',                  'label' => $this->__("Other")),
            array('value' => 'public_sector',          'label' => $this->__("Public Sector")),
            array('value' => 'property',               'label' => $this->__("Property")),
            array('value' => 'retail',                 'label' => $this->__("Retail")),
            array('value' => 'security',               'label' => $this->__("Security")),
            array('value' => 'shipping',               'label' => $this->__("Shipping")),
            array('value' => 'technology',             'label' => $this->__("Technology")),
            array('value' => 'telecommunications',     'label' => $this->__("Telecommunications")),
            array('value' => 'utilities',              'label' => $this->__("Utilities"))
        );
    }
}
