<?php
/**
 * Ksolves
 *
 * @category  Ksolves
 * @package   Ksolves_Fam
 * @author    Ksolves Team
 * @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
 * @license   https://store.ksolves.com/magento-license
 */

namespace Ksolves\Fam\Controller\Adminhtml\Payment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Success Controller
 */
class Success extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
    */
    protected $backendQuoteSession;

    /**
     * @var \Ksolves\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ksolves\Fam\Logger\Logger
    */
    protected $_logger;

    /**
     * Initialize Controller
     *
     * @param Context $context
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Ksolves\Fam\Helper\Data $dataHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Ksolves\Fam\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Ksolves\Fam\Helper\Data $dataHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Sales\Model\Order $order,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Ksolves\Fam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->backendQuoteSession = $backendQuoteSession; 
        $this->dataHelper = $dataHelper;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->quoteManagement = $quoteManagement;
        $this->_logger = $logger;
    }

    /**
     * execute action
    */
    public function execute()
    {
        $data = $this->getRequest()->getParams(); // get the data from fam side
        $this->_logger->info('Admin Success controller start +++');
        $this->_logger->info(print_r($data,true));
        try {
            if (!empty($data)) {
                $transactionId = $data['fam_order_id'];
                $checkoutId = $data['fam_checkout_id'];
                $transactionStatus = "Pending";
            }else{
                $transactionId = null;
                $transactionStatus = 'Pending';
            }
            $orderData = $this->dataHelper->getOrderData($checkoutId); //get orderdata from fam_transaction table
            // Check order is created or not
            if (empty($orderData)) {
                $this->_logger->info('Admin success-> Order data not found +++');
                $this->_logger->info(print_r($orderData,true));
                $quoteId = $this->dataHelper->getQuoteId($checkoutId);
                if ($quoteId) {
                    $this->_logger->info('Admin success-> Order creation process start +++');
                    $this->_logger->info($quoteId);
                    // Create Order From Quote
                    $quote = $this->cartRepositoryInterface->get($quoteId);
                    $order = $this->quoteManagement->submit($quote);
                    if ($order) {
                        $this->dataHelper->saveTransactionHistory($transactionId,$quoteId,$order->getEntityId(),$transactionStatus,$checkoutId); //save in transaction table
                        $this->dataHelper->createInvoice($order->getEntityId()); // create invoice 
                        $this->_logger->info('Admin success-> Order created successfully +++');
                        $this->_logger->info($order->getEntityId());
                    }
                    $this->messageManager->addSuccess(__('Order created successfully.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getEntityId(), '_current' => true]);
                }else{
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $this->messageManager->addError(__('Quote Id not found.'));
                    $this->_logger->info('Admin success-> Quote Id not found');
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('sales/order/index');
                    return $resultRedirect;
                }
            }else{
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderData['order_id'], '_current' => true]);
            }
        } catch (\Exception $e) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $this->_logger->info($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/index');
            return $resultRedirect;
        }
    }
}
