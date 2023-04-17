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
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * AdminWebhook Class
 */
class AdminWebhook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Ftl\Fam\Helper\Data
    */
    protected $_dataHelper;

    /**
     * @var \Ftl\Fam\Logger\Logger
    */
    protected $_logger;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

     /**
     * @var \Ftl\Fam\Model\Config
     */
    protected $config;

   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Ftl\Fam\Helper\Data $dataHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param \Magento\Sales\Model\Order $order
     * @param \Ftl\Fam\Logger\Logger $logger
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Ftl\Fam\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ftl\Fam\Helper\Data $dataHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Sales\Model\Order $order,
        \Ftl\Fam\Logger\Logger $logger,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Ftl\Fam\Model\Config $config,
        \Magento\Quote\Model\QuoteManagementFactory $quoteManagement
    ) {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->order = $order;
        $this->_logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
        $this->quoteManagement = $quoteManagement->create();
    }
    
    /**
     * invalid form key
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
    
    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
    
   /**
     * Success Response.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $this->_logger->info('Webhook start ---');
            $response = $this->getRequest()->getContent(); // get the data from fam side
            $json_decode = $this->jsonHelper->unserialize($response);
            $this->_logger->info(print_r($json_decode,true));

            $signature = $this->getRequest()->getHeader('Webhook-Signature');
            $this->_logger->info(print_r($signature,true));
            $this->_logger->info(print_r($this->config->getSecretKey(),true));

            if ($signature == $this->config->getSecretKey()) {
                if ($json_decode['event_type'] == "ORDER_CREATED") {
                    return;
                }
                if (!empty($json_decode)) {
                    $checkoutId = $json_decode['checkout_id'];
                    $transactionId = $json_decode['order_id'];
                    $transactionStatus = $json_decode['status'];
                }else{
                    $transactionId = null;
                    $transactionStatus = 'Pending';
                }
                $orderData = $this->_dataHelper->getOrderData($checkoutId); //get orderdata from fam_transaction table

                $this->_logger->info(print_r($orderData,true));
                // Check order is created or not
                if (!empty($orderData)) {
                    $this->_logger->info('Webhook-> Order data found ---');
                    $this->_logger->info(print_r($orderData,true));
                    $this->_logger->info(print_r($checkoutId,true));
                    // update the transaction table
                    $tableId = $this->_dataHelper->getRowId($checkoutId);
                    if ($tableId) {
                        $this->_logger->info('Webhook-> Transaction table updated ---');
                        $this->_dataHelper->updateTransactionData($tableId,$transactionStatus);
                        if($transactionStatus == 'COMPLETED'){
                            $this->_dataHelper->createInvoice($orderData['order_id']); // create invoice here                                
                        }
                    }else{
                        $this->_logger->info('Webhook-> Table Id not found ---');
                        $this->_logger->info($e->getMessage());
                    }
                }else{
                    $this->_logger->info('Webhook-> Order data not found ---');
                    $this->_logger->info(print_r($orderData,true));
                    //order create here using quoteId
                    $quoteId = $this->_dataHelper->getQuoteId($checkoutId);
                    if ($quoteId) {
                        $this->_logger->info('Webhook-> Order creation process start ---');
                        $this->_logger->info($quoteId);
                        // Create Order From Quote
                        $quote = $this->cartRepositoryInterface->get($quoteId);
                        $order = $this->quoteManagement->submit($quote);
                        $order->setEmailSent(1);
                        if ($order) {
                            $this->_dataHelper->saveTransactionHistory($transactionId,$order->getQuoteId(),$order->getId(),$transactionStatus,$checkoutId); //save in transaction table
                            $this->_logger->info('Webhook-> Order created successfully ---');
                            $this->_logger->info($order->getId());
                            if($transactionStatus == 'COMPLETED'){
                                $this->_dataHelper->createInvoice($order->getId()); // create invoice here                                
                            }
                        }
                    }else{
                        $this->_logger->info('Webhook-> Quote Id not found ---');
                    }
                }
            }else{
                $this->_logger->info('Webhook-> Fam-Signature did not match');
            }    
        } catch (\Exception $e) {
            $this->_logger->info('Webhook-> Catch section ---');
            $this->_logger->info($e->getMessage());
        }
    }  
}
