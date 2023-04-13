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
namespace Ksolves\Fam\Plugin;

/**
* Class ConfigPlugin
*/
class ConfigPlugin 
{
    /**
     * @param \Ksolves\Fam\Model\Config $config
     */
    public function __construct(
        \Ksolves\Fam\Model\Config $config
    ) {
        $this->config = $config;
    }

    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    ) {
        $data = $subject->getData();
        if($data['section'] == "payment"){
            $famData = $data['groups']['fam']['fields'];
            $keyId = $this->config->getKeyId();
            $keySecret = $this->config->getSecretKey();
            if($famData['key_id']['value'] !== $keyId || $famData['key_secret']['value'] !== $keySecret){
                $subject->setDataByPath('payment/fam/active',0);
            }
        }
        return $proceed();
    }
}
