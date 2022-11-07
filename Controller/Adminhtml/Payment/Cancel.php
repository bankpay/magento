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

namespace Ksolves\Bankpay\Controller\Adminhtml\Payment;

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
     * @var \Ksolves\Bankpay\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ksolves\Bankpay\Logger\Logger
    */
    protected $_logger;

    /**
     * Initialize Controller
     *
     * @param Context $context
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Ksolves\Bankpay\Helper\Data $dataHelper
     * @param \Ksolves\Bankpay\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Ksolves\Bankpay\Helper\Data $dataHelper,
        \Ksolves\Bankpay\Logger\Logger $logger
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
