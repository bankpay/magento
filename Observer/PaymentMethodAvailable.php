<?php
/**
 * Fam
 *
 * @category  Fam
 * @package   Ftl_Fam
 * @author    Fam Team
 * @copyright Copyright (c) Frictionless Technologies Ltd (https://www.joinfam.com/)
 * @license   https://joinfam.com/legal
 */ 

namespace Ftl\Fam\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * PaymentMethodAvailable Class
 */
class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var \Ftl\Fam\Model\Config
     */
    protected $config;
    
    /**
     * @param  \Ftl\Fam\Model\Config $config,
     */
    public function __construct(
        \Ftl\Fam\Model\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($observer->getEvent()->getMethodInstance()->getCode()=="fam"){
            if (!$this->getWebsiteId()) {
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page
            }
        }
    }

    /**
     * Returns website
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
}
