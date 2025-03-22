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
namespace Fam\Fam\Plugin;

/**
* Class ConfigPlugin
*/
class ConfigPlugin 
{
    /**
     * \Fam\Fam\Model\Config
     */
    protected $config;

    /**
     * @param \Fam\Fam\Model\Config $config
     */
    public function __construct(
        \Fam\Fam\Model\Config $config
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
