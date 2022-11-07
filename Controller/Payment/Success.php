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

namespace Ksolves\Bankpay\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

/**
 * Success Class
 */
class Success extends \Ksolves\Bankpay\Controller\BaseController
{
    /**
     * @var \Magento\Quote\Model\Quote
    */
    protected $quote;
    
    /**
     * @var \Magento\Checkout\Model\Session
    */
    protected $checkoutSession;

    /**
     * @var \Ksolves\Bankpay\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ksolves\Bankpay\Logger\Logger
    */
    protected $_logger;
   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ksolves\Bankpay\Helper\Data $dataHelper
     * @param Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param Magento\Sales\Model\Order $order
     * @param \Ksolves\Bankpay\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ksolves\Bankpay\Helper\Data $dataHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Sales\Model\Order $order,
        \Ksolves\Bankpay\Logger\Logger $logger
    ) {
        parent::__construct($context,$checkoutSession);
        $this->dataHelper = $dataHelper;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->order = $order;
        $this->_logger = $logger;
    }
    
   /**
     * Success Response.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams(); // get the data from bankpay side
        $this->_logger->info('Success controller start ***');
        $this->_logger->info(print_r($data,true));
        try {
            if (!empty($data)) {
                $transactionId = $data['transaction_id'];
                $transactionStatus = $data['status'];
            }else{
                $transactionId = null;
                $transactionStatus = 'Pending';
            }
            $orderData = $this->dataHelper->getOrderData($transactionId); //get orderdata from bankpay_transaction table
            // Check order is created or not (comparision with webhook url)
            if (empty($orderData)) {
                $this->_logger->info('success-> Order data not found ***');
                $this->_logger->info(print_r($orderData,true));
                // Create Order using quoteid
                $quoteId = $this->dataHelper->getQuoteId($transactionId);
                if ($quoteId) {
                    $this->_logger->info('success-> Order creation process start ***');
                    $this->_logger->info($quoteId);
                    $quote = $this->cartRepositoryInterface->get($quoteId);
                    $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
                    $order = $this->order->load($orderId);
                    $order->setEmailSent(1);

                    if ($order) {
                        $this->dataHelper->saveTransactionHistory($transactionId,$order->getQuoteId(),$order->getId(),$transactionStatus); //save in transaction table
                        $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                        $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                        $this->checkoutSession->setLastOrderId($order->getId());
                        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                        $this->checkoutSession->setLastOrderStatus($order->getStatus());
                        if ($transactionStatus == 'COMPLETED') {
                            $this->dataHelper->createInvoice($order->getId()); // create invoice
                        }
                        $this->_logger->info('success-> Order created successfully ***');
                        $this->_logger->info($order->getId());
                        $this->_redirect('checkout/onepage/success');
                    }
                }else{
                    $this->_logger->info('success-> Quote Id not found ***');
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $this->resultRedirectFactory->create()->setPath(
                        'checkout/cart/index',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            }else{
                $this->_logger->info('success-> Order data found ***');
                $this->_logger->info(print_r($orderData,true));
                if($transactionStatus == 'COMPLETED'){
                    $this->dataHelper->createInvoice($orderData['order_id']); // create invoice here                                
                }
                $this->checkoutSession->setLastQuoteId($orderData['quote_id']);
                $this->checkoutSession->setLastSuccessQuoteId($orderData['quote_id']);
                $this->checkoutSession->setLastOrderId($orderData['order_id']);
                $this->checkoutSession->setLastRealOrderId($this->dataHelper->getOrderIncrementId($orderData['order_id']));
                $this->checkoutSession->setLastOrderStatus($this->dataHelper->getOrderStatus($orderData['order_id']));
                $this->_redirect('checkout/onepage/success');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                'checkout/cart/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
