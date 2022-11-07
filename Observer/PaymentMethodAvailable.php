<?php
/**
 * Ksolves
 *
 * @category  Ksolves
 * @package   Ksolves_Bankpay
 * @author    Ksolves Team
 * @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
 * @license   https://store.ksolves.com/magento-license
 */

namespace Ksolves\Bankpay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * PaymentMethodAvailable Class
 */
class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var \Ksolves\Bankpay\Model\Config
     */
    protected $config;
    
    /**
     * @param  \Ksolves\Bankpay\Model\Config $config,
     */
    public function __construct(
        \Ksolves\Bankpay\Model\Config $config
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
        if($observer->getEvent()->getMethodInstance()->getCode()=="bankpay"){
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
