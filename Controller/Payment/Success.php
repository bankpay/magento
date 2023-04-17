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

namespace Ftl\Fam\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

/**
 * Success Class
 */
class Success extends \Ftl\Fam\Controller\BaseController
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
     * @var \Ftl\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ftl\Fam\Logger\Logger
    */
    protected $_logger;
   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ftl\Fam\Helper\Data $dataHelper
     * @param Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param Magento\Sales\Model\Order $order
     * @param \Ftl\Fam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ftl\Fam\Helper\Data $dataHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Sales\Model\Order $order,
        \Ftl\Fam\Logger\Logger $logger,
        \Magento\Quote\Model\QuoteManagement $quoteManagement
    ) {
        parent::__construct($context,$checkoutSession);
        $this->dataHelper = $dataHelper;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->order = $order;
        $this->_logger = $logger;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->quoteManagement = $quoteManagement;
    }
    
   /**
     * Success Response.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams(); // get the data from fam side
        $this->_logger->info('Success controller start ***');
        $this->_logger->info(print_r($data,true));
        try {
            if (!empty($data)) {
                $checkout_id = $data['fam_checkout_id'];
                $transactionId = $data['fam_order_id'];
                $transactionStatus = 'Pending';
            }else{
                $transactionId = null;
                $transactionStatus = 'Pending';
            }
            $orderData = $this->dataHelper->getOrderData($checkout_id); //get orderdata from fam_transaction table
            // Check order is created or not (comparision with webhook url)
            if (empty($orderData)) {
                $this->_logger->info('success-> Order data not found ***');
                $this->_logger->info(print_r($orderData,true));
                // Create Order using quoteid
                $quoteId = $this->dataHelper->getQuoteId($checkout_id);
                if ($quoteId) {
                    $this->_logger->info('success-> Order creation process start ***');
                    $this->_logger->info($quoteId);
                    $quote = $this->cartRepositoryInterface->get($quoteId);
                    $this->_logger->info(print_r($quoteId,true));
                    $orderId =$this->cartManagementInterface->placeOrder($quoteId);
                    $order = $this->order->load($orderId);
                    //$order = $this->quoteManagement->submit($quote);
                    $order->setEmailSent(1);

                    if ($order) {
                        $this->dataHelper->saveTransactionHistory($transactionId,$order->getQuoteId(),$order->getId(),$transactionStatus,$checkout_id); //save in transaction table
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
                        $updateParams = [
                            "merchant_order_id" => $order->getId()
                        ];
                        $this->dataHelper->updateCurl("PUT",$updateParams,$transactionId);
                        $this->_redirect('checkout/onepage/success?fam_checkout_id='.$checkout_id.'&');
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
                $this->_redirect('checkout/onepage/success?fam_checkout_id='.$checkout_id.'&');
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
