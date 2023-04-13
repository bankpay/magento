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

namespace Ksolves\Fam\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Config\Storage\WriterInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
* Class Config
*/
class Config
{
    const KEY_ACTIVE = 'active';
    const KEY_PUBLIC_KEY = 'key_id';
    const KEY_SECRET_KEY = 'key_secret';
    const KEY_MERCHANT_NAME = 'merchant_name';
    const KEY_THEME = 'theme';
    const CHECKOUT_METHOD_KEY_THEME = 'checkout_method_theme';
    const KEY_CLIENT_URL = 'client_url';
    const KEY_ONSITE_MESSAGE_ENABLE = 'onsite_message_enable';
    const KEY_ONSITE_MESSAGE_OPTION = 'onsite_message_option';
    const KEY_ASSOSCIATED_WEBSITE = 'website_list';
    const KEY_MODE = 'mode';
    const KEY_LOGO_THEME = 'logo_theme';

    /**
     * @var string
     */
    protected $methodCode = 'fam';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var WriterInterface
    */
    protected $configWriter;

    /**
     * @var int
     */
    protected $storeId = null;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getMerchantName()
    {
        return $this->getConfigData(self::KEY_MERCHANT_NAME);
    }

    /**
     * @return string
     */
    public function getKeyId()
    {
        return $this->getConfigData(self::KEY_PUBLIC_KEY);
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getConfigData(self::KEY_SECRET_KEY);
    }

    /**
     * @return string
     */
    public function getClientUrl()
    {
        $mode = $this->getMode();
        if ($mode == 'sandbox') {
            return "https://api-staging-sandbox.joinfam.com";
        }elseif ($mode == 'live') {
           return "https://api-staging-live.joinfam.com";
        }
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->getConfigData(self::KEY_THEME);
    }

    /**
     * @return string
     */
    public function getCheckoutMethodTheme()
    {
        return $this->getConfigData(self::CHECKOUT_METHOD_KEY_THEME);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->getConfigData(self::KEY_MODE);
    }

    /**
     * @return string
     */
    public function getLogoTheme()
    {
        return $this->getConfigData(self::KEY_LOGO_THEME);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param null|string $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeId;
        }

        $code = $this->methodCode;

        $path = 'payment/' . $code . '/' . $field;
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Set information from payment configuration
     *
     * @param string $field
     * @param string $value
     * @param null|string $storeId
     *
     * @return mixed
     */
    public function setConfigData($field, $value)
    {
        $code = $this->methodCode;

        $path = 'payment/' . $code . '/' . $field;

        return $this->configWriter->save($path, $value);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) (int) $this->getConfigData(self::KEY_ACTIVE, $this->storeId);
    }

    /**
     * @return bool
     */
    public function isEnabledConfig($location)
    {
    
        return $this->getConfigData('fam_onsite_message/'.self::KEY_ONSITE_MESSAGE_ENABLE.'_'.$location);
        if (!$enabled)
            return false;

        $activeLocations = explode(',', $this->getConfigData(self::KEY_ONSITE_MESSAGE_OPTION));
        if (!in_array($location, $activeLocations))
            return false;

        return true;
    }

    /**
     * @return bool
     */
    public function osmTheme($location)
    {
        return $this->getConfigData('fam_onsite_message/'.$location.'_theme');    
    }
    
    /**
     * @return bool
     */
    public function osmLogoTheme($location)
    {
        return $this->getConfigData('fam_onsite_message/'.$location.'_logo_theme');    
    }
        
    /**
     * @return bool
     */
    public function isOSMEnabled()
    {    
        return $this->getConfigData(self::KEY_ONSITE_MESSAGE_ENABLE);
    }

    /**
     * @return string
     */
    public function getWebsiteId()
    {
        return $this->getConfigData(self::KEY_ASSOSCIATED_WEBSITE);
    }

    /**
     * Get website code
     *
     * @return int|null
     */
    public function getWebsiteCode(): ?int
    {
        try {
            $websiteCode = $this->storeManager->getWebsite()->getId();
        } catch (LocalizedException $localizedException) {
            $websiteCode = null;
            $this->logger->error($localizedException->getMessage());
        }
        return $websiteCode;
    }
}
