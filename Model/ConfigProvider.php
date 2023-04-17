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

namespace Ftl\Fam\Model;

use Magento\Payment\Helper\Data as PaymentHelper;
use Ftl\Fam\Model\PaymentMethod;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

/**
* Class ConfigProvider
*/
class ConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Ftl\Fam\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;

    /**
     * @var Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Url $urlBuilder
     * @param \Ftl\Fam\Model\Config $config
     * @param LayoutInterface $layout
    */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Url $urlBuilder,
        \Psr\Log\LoggerInterface $logger,
        PaymentHelper $paymentHelper,
        \Ftl\Fam\Model\Config $config,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        LayoutInterface $layout
    ) {
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
        $this->methodCode = PaymentMethod::METHOD_CODE;
        $this->method = $paymentHelper->getMethodInstance(PaymentMethod::METHOD_CODE);
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->layout = $layout;
    }

    /**
     * @return array|void
     */
    public function getConfig()
    {
        if (!$this->config->isActive()) {
            return [];
        }
        
        $config = [
            'payment' => [
                'fam' => [
                    'merchant_name' => $this->config->getMerchantName(),
                    'key_id'    => $this->config->getKeyId(),
                    'theme' => $this->config->getTheme(),
                    'checkout_method_theme' => $this->config->getCheckoutMethodTheme(),
                    'mode' => $this->config->getMode()
                ],
            ],
        ];
        $config['onsite_message'] = $this->layout
                ->createBlock("Ftl\Fam\Block\OnsiteMessage")
                ->setTemplate("Ftl_Fam::checkout/onsite-message-content.phtml")
                ->toHtml();

        return $config;
    }
}
