<?php
/**
 * Ksolves
 *
 * @category    Ksolves
 * @package     Ksolves_Fam
 * @author      Ksolves Team
 * @copyright   Copyright (c) Ksolves India Ltd.(https://www.ksolves.com/)
 * @license     https://store.ksolves.com/magento-license
 */

namespace Ksolves\Fam\Block\Adminhtml\Order\View;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class FamPaymentInfo
 */
class FamPaymentInfo extends \Magento\Backend\Block\Template
{
    /**
     * @type Registry|null
     */
    protected $registry = null;

    /**
     * @var \Ksolves\Fam\Helper\Data
    */
    protected $dataHelper;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Ksolves\Fam\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Ksolves\Fam\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context,$data);
    }

    /**
     * Get fam information 
     *
     * @return DataObject
     */
    public function getFamInformation()
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
