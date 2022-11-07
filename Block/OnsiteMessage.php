<?php

/**
 * Ksolves
 *
 * @category    Ksolves
 * @package     Ksolves_Bankpay
 * @author      Ksolves Team
 * @copyright   Copyright (c) Ksolves India Ltd.(https://www.ksolves.com/)
 * @license     https://store.ksolves.com/magento-license
 */

namespace Ksolves\Bankpay\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

/**
 * Class OnsiteMessage
 */
class OnsiteMessage extends Template
{
  
    /**
     * @var \Ksolves\Bankpay\Model\Config
     */
    protected $config;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Checkout\Model\Session
    */
    protected $checkoutSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
    */
    protected $_product;


    /**
     * OnsiteMessage constructor.
     *
     * @param Template\Context $context
     * @param \Ksolves\Bankpay\Model\Config $config
     * @param Registry $registry
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Ksolves\Bankpay\Model\Config $config,
        Registry $registry,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->registry = $registry;
        $this->checkoutHelper = $checkoutHelper;
        $this->checkoutSession = $checkoutSession;
        $this->productFactory = $productFactory;
    }

    /**
     * Check Is Block enabled
     * @return bool
     */
    public function isEnabled($location)
    {
        return $this->config->isEnabledConfig($location);
    }

    /**
     * Check if OSM is enabled
     * @return bool
     */
    public function isOSMEnabled()
    {
        return $this->config->isOSMEnabled();
    }

    /**
     * Returns merchant id
     *
     * @return mixed
    */
    public function getMerchantId()
    {
        return $this->config->getKeyId();
    }

    /**
     * Returns theme colour
     *
     * @return string
    */
    public function getThemeColour($location)
    {
        return $this->config->osmTheme($location);
    }

    /**
     * Returns payment mode
     *
     * @return string
    */
    public function getPaymentMode()
    {
        return $this->config->getMode();
    }

    /**
     * Returns logo theme colour
     *
     * @return string
    */
    public function getLogoThemeColour($location)
    {
        return $this->config->osmLogoTheme($location);
    }

    /**
     * Return website id
     *
     * @return string
    */
    public function getWebsiteId()
    {
        $currentWebsiteId = $this->config->getWebsiteCode();
        $selectedWebsiteId = $this->config->getWebsiteId();
        if ((int) $currentWebsiteId === (int) $selectedWebsiteId) {
           return true;
        }
        return false;
    }

    /**
     * Get Quote
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        $quote = $this->checkoutHelper->getCheckout()->getQuote();
        if (!$quote->getId()) {
            $quote = $this->checkoutSession->getQuote();;
        }
        return $quote;
    }

    /**
     * Get quote total
     * @return int
     */
    public function getQuoteTotal()
    {
        if(!is_null($this->getQuote()->getSubtotal())){
            $quoteTotal = (int) number_format($this->getQuote()->getSubtotal(), 2, ".", "");
        } else {
            $quoteTotal = 0;
        }
        return $quoteTotal;
    }
    
    /**
     * Get product price
     * @return int
    */
    public function getPriceById()
    {
        $productId = $this->registry->registry('product')->getId(); //Product ID
        $product = $this->productFactory->create();
        $productPriceById = $product->load($productId)->getPrice();
        return (int) number_format($productPriceById, 2, ".", "");
    }
}
