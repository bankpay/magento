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

namespace Ksolves\Bankpay\Block\Sales\Order\View;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class BankpayPaymentInfo
 */
class BankpayPaymentInfo extends Template
{
    /**
     * @type Registry|null
     */
    protected $registry = null;

    /**
     * @var \Ksolves\Bankpay\Helper\Data
    */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Ksolves\Bankpay\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Ksolves\Bankpay\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context,$data);
    }

    /**
     * Get bankpay information 
     *
     * @return DataObject
     */
    public function getBankpayInformation()
    {
        $result = [];

        if ($order = $this->getOrder()) {
            $result = $this->dataHelper->getTransactionData($order->getId());
        }
        return new DataObject($result);
    }

    /**
     * Get current order
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }
}
