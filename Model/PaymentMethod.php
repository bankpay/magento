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
namespace Ksolves\Bankpay\Model;
 
/**
* Class PaymentMethod
*/
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    const METHOD_CODE = 'bankpay';

    /**
     * @var string
    */
    protected $_code = self::METHOD_CODE;

    /**
     * Availability option
     *
     * @var bool
    */
    protected $_isOffline = true;
}