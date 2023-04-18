<?php

/**
 * Fam
 *
 * @category  Fam
 * @package   Fam_Fam
 * @author    Fam Team
 * @copyright Copyright (c) Frictionless Technologies Ltd (https://www.joinfam.com/)
 * @license   https://joinfam.com/legal
 */ 

namespace Fam\Fam\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

/**
 * Class OnsiteMessage
 */
class OnsiteMessage extends Template
{
  
    /**
     * @var \Fam\Fam\Model\Config
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
     * @param \Fam\Fam\Model\Config $config
     * @param Registry $registry
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Fam\Fam\Model\Config $config,
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
        if(!is_null($this->getQuote()->getGrandTotal())){
            $quoteTotal = number_format($this->getQuote()->getGrandTotal(), 2, ".", "");
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
        $currentProduct = $this->registry->registry('product');
        $finalprice = 0 ;

        if ($currentProduct->getTypeId() == "simple") {
            $finalprice = $currentProduct->getFinalPrice();
        } elseif ($currentProduct->getTypeId() == "configurable") {
            $_children = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct);
            foreach ($_children as $child) {
                $finalprice = $child->getFinalPrice();
                break;
            }
        } elseif ($currentProduct->getTypeId() == "bundle" || $currentProduct->getTypeId() == "grouped") {
            $finalprice = $currentProduct->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
        }

        return number_format($finalprice, 2, ".", "");
    }
}
