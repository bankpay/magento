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
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * Webhook Class
 */
class Webhook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Ksolves\Bankpay\Helper\Data
    */
    protected $_dataHelper;

    /**
     * @var \Ksolves\Bankpay\Logger\Logger
    */
    protected $_logger;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

     /**
     * @var \Ksolves\Bankpay\Model\Config
     */
    protected $config;

   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Ksolves\Bankpay\Helper\Data $dataHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param \Magento\Sales\Model\Order $order
     * @param \Ksolves\Bankpay\Logger\Logger $logger
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Ksolves\Bankpay\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ksolves\Bankpay\Helper\Data $dataHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Sales\Model\Order $order,
        \Ksolves\Bankpay\Logger\Logger $logger,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Ksolves\Bankpay\Model\Config $config
    ) {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->order = $order;
        $this->_logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
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
            $response = $this->getRequest()->getContent(); // get the data from bankpay side
            $json_decode = $this->jsonHelper->unserialize($response);
            $this->_logger->info(print_r($json_decode,true));

            $signature = $this->getRequest()->getHeader('Bankpay-Signature');
            $this->_logger->info(print_r($signature,true));

            if ($signature == $this->config->getSecretKey()) {
                if ($json_decode['event_type'] == "TRANSACTION_CREATED") {
                    return;
                }
                if (!empty($json_decode)) {
                    $transactionId = $json_decode['txn_id'];
                    $transactionStatus = $json_decode['status'];
                }else{
                    $transactionId = null;
                    $transactionStatus = 'Pending';
                }
                $orderData = $this->_dataHelper->getOrderData($transactionId); //get orderdata from bankpay_transaction table

                // Check order is created or not
                if (!empty($orderData)) {
                    $this->_logger->info('Webhook-> Order data found ---');
                    $this->_logger->info(print_r($orderData,true));
                    // update the transaction table
                    $tableId = $this->_dataHelper->getRowId($transactionId);
                    if ($tableId) {
                        $this->_logger->info('Webhook-> Transaction tabel updated ---');
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
                    $quoteId = $this->_dataHelper->getQuoteId($transactionId);
                    if ($quoteId) {
                        $this->_logger->info('Webhook-> Order creation process start ---');
                        $this->_logger->info($quoteId);
                        // Create Order From Quote
                        $quote = $this->cartRepositoryInterface->get($quoteId);
                        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
                        $order = $this->order->load($orderId);
                        $order->setEmailSent(1);
                        if ($order) {
                            $this->_dataHelper->saveTransactionHistory($transactionId,$order->getQuoteId(),$order->getId(),$transactionStatus); //save in transaction table
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
                $this->_logger->info('Webhook-> Bankpay-Signature did not match');
            }    
        } catch (\Exception $e) {
            $this->_logger->info('Webhook-> Catch section ---');
            $this->_logger->info($e->getMessage());
        }
    }  
}
