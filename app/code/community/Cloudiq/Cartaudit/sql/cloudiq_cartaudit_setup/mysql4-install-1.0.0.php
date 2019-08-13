<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$adapter = $installer->getConnection();

/** @var Mage_Core_Model_Store $store */
$store = Mage::app()->getStore()->resetConfig();

$installer->startSetup();

/**
 * Set the default values for cartAudit configuration fields
 */

$installer->setConfigData('cloudiq/cartaudit/app_name', Mage::getStoreConfig('general/store_information/name', $store));

$installer->setConfigData('cloudiq/cartaudit/website_url', $store->getBaseUrl());

/** @var Mage_Sales_Model_Resource_Quote_Collection $quotes_collection */
$quotes_collection = Mage::getResourceModel('sales/quote_collection');
$quotes_collection
    ->removeAllFieldsFromSelect()
    ->addFieldToSelect(new Zend_Db_Expr('COUNT(DISTINCT(main_table.entity_id))'), 'quote_count')
    ->addFieldToFilter('created_at', array('from' => new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)')))
    ->load();

if ($quotes_collection->getFirstItem()) {
    $installer->setConfigData('cloudiq/cartaudit/carts_started_per_month', $quotes_collection->getFirstItem()->getQuoteCount());
}

/** @var Mage_Sales_Model_Resource_Order_Collection $orders_collection */
$orders_collection = Mage::getResourceModel('sales/order_collection')
    ->removeAllFieldsFromSelect()
    ->addFieldToSelect(new Zend_Db_Expr(sprintf('AVG((%s - %s - %s - (%s - %s - %s)) * %s)',
        'IFNULL(main_table.base_total_invoiced, 0)',
        'IFNULL(main_table.base_tax_invoiced, 0)',
        'IFNULL(main_table.base_shipping_invoiced, 0)',
        'IFNULL(main_table.base_total_refunded, 0)',
        'IFNULL(main_table.base_tax_refunded, 0)',
        'IFNULL(main_table.base_shipping_refunded, 0)',
        'main_table.base_to_global_rate'
    )), 'average_value')
    ->addFieldToSelect(new Zend_Db_Expr('COUNT(DISTINCT(main_table.entity_id))'), 'order_count')
    ->addFieldToFilter('created_at', array('from' => new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)')))
    ->addFieldToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
    ->load();

if ($orders_collection->getFirstItem()) {
    $installer->setConfigData('cloudiq/cartaudit/average_cart_value', round($orders_collection->getFirstItem()->getAverageValue(), 2));
    $installer->setConfigData('cloudiq/cartaudit/carts_completed_per_month', $orders_collection->getFirstItem()->getOrderCount());
}

$installer->endSetup();
