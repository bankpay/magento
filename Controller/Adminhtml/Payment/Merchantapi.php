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
 * Class Merchantapi Controller
 */
class Merchantapi extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\UrlInterface
    */
    protected $urlBuider;

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
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Fam\Fam\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Fam\Fam\Helper\Data $dataHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Fam\Fam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->backendQuoteSession = $backendQuoteSession; 
        $this->dataHelper = $dataHelper;
        $this->urlBuilder = $urlBuilder;
        $this->_logger = $logger;
    }

    /**
     * execute action
    */
    public function execute()
    {
        $this->_logger->info('Admin-> Merchantapi controller call ---');
        $email = $this->getRequest()->getParam('email');
        if (isset($email) && $email) {
           return $this->adminTransactionsApi($email);
        }else{
            return "Data not found";
        } 
    }


    /**
     * @return merchant Api Call
    */
    protected function adminTransactionsApi($email)
    {
        $successUrl = $this->urlBuilder->getUrl('fam/payment/success');
        $cancelUrl = $this->urlBuilder->getUrl('fam/payment/cancel');

        $admin_quote = $this->backendQuoteSession->getQuote();
        $payload_data = $this->dataHelper->merchantApiPayloadData($admin_quote,$email,$successUrl,$cancelUrl,true);
        $this->_logger->info('Admin-> payload data ---');
        $this->_logger->info("<pre>===");
        $this->_logger->info(print_r($payload_data,true));
        return $this->dataHelper->clientCurl("POST",$payload_data,$admin_quote->getId()); //api call
    }
}
