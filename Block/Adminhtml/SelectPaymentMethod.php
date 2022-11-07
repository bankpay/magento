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

namespace Ksolves\Bankpay\Block\Adminhtml;

use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;
use Magento\Backend\Model\Session\Quote as AdminCheckoutSession;

/**
 * Class SelectPaymentMethod
 */
class SelectPaymentMethod extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var AdminCheckoutSession
     */
    private $adminCheckoutSession;

    /**
     * @var \Ksolves\Bankpay\Model\Config
    */
    protected $config;
    
    /**
     * @param AdminCheckoutSession $adminCheckoutSession
     * @param \Ksolves\Bankpay\Model\Config $config
    */
    public function __construct(
        AdminCheckoutSession $adminCheckoutSession,
        \Ksolves\Bankpay\Model\Config $config
    ) {
        $this->adminCheckoutSession = $adminCheckoutSession;
        $this->config = $config;
    }

    /**
     * Returns the Quote object made in adminpanel area
     *
     * @return Quote
    */
    public function getCurrentAdminQuote(): Quote
    {
        $quote = $this->adminCheckoutSession->getQuote();
        return $quote;
    }

    /**
     * Returns merchant id
     *
     * @return mixed
    */
    public function getMerchantId()
    {
        return $this->config->getKeyId();
    }

    /**
     * Returns theme colour
     *
     * @return string
    */
    public function getThemeColour()
    {
        return $this->config->getTheme();
    }

    /**
     * Returns mode
     *
     * @return string
    */
    public function getMode()
    {
        return $this->config->getMode();
    }
}
