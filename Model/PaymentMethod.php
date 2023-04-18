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
namespace Fam\Fam\Model;
 
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
    const METHOD_CODE = 'fam';

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