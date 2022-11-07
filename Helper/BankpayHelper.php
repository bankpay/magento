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

namespace Ksolves\Bankpay\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * BankpayHelper Class
 */
class BankpayHelper extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
	protected $_scopeConfig;
	
    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * get config value
     * @param string $configPath
     * @return string
    */
	public function getConfig($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}