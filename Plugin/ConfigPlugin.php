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
namespace Ksolves\Bankpay\Plugin;

/**
* Class ConfigPlugin
*/
class ConfigPlugin 
{
    /**
     * @param \Ksolves\Bankpay\Model\Config $config
     */
    public function __construct(
        \Ksolves\Bankpay\Model\Config $config
    ) {
        $this->config = $config;
    }

    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    ) {
        $data = $subject->getData();
        if($data['section'] == "payment"){
            $bankpayData = $data['groups']['bankpay']['fields'];
            $keyId = $this->config->getKeyId();
            $keySecret = $this->config->getSecretKey();
            if($bankpayData['key_id']['value'] !== $keyId || $bankpayData['key_secret']['value'] !== $keySecret){
                $subject->setDataByPath('payment/bankpay/active',0);
            }
        }
        return $proceed();
    }
}
