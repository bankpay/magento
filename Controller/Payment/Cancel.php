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
 * Cancel Class
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
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
     * @param \Ftl\Fam\Helper\Data $dataHelper
     * @param \Ftl\Fam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ftl\Fam\Helper\Data $dataHelper,
        \Ftl\Fam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->_logger = $logger;
    }
    
    /**
     * Marchant Api Cancel Response.
     *
     * @return data
    */
    public function execute()
    {
        $this->_logger->info('Frontend-> cancel controller call ===');
        try {
            // $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                'checkout/cart/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                'checkout/cart/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
