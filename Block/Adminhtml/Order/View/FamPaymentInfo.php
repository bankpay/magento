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

namespace Ftl\Fam\Block\Adminhtml\Order\View;

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
     * @var \Ftl\Fam\Helper\Data
    */
    protected $dataHelper;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Ftl\Fam\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Ftl\Fam\Helper\Data $dataHelper,
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
