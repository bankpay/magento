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

namespace Fam\Fam\Controller\Adminhtml\Payment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Cancel Controller
 */
class Cancel extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
    */
    protected $backendQuoteSession;

    /**
     * @var \Fam\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Fam\Fam\Logger\Logger
    */
    protected $_logger;

    /**
     * Initialize Controller
     *
     * @param Context $context
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Fam\Fam\Helper\Data $dataHelper
     * @param \Fam\Fam\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Fam\Fam\Helper\Data $dataHelper,
        \Fam\Fam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->backendQuoteSession = $backendQuoteSession; 
        $this->dataHelper = $dataHelper;
        $this->_logger = $logger;
    }

    /**
     * execute action
    */
    public function execute()
    {
        $this->_logger->info('Admin-> cancel controller call ***');
        try {
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
        } catch (\Exception $e) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/index');
            return $resultRedirect;
        }
    }
}
