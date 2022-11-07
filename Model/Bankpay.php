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
* Class Bankpay
*/
class Bankpay extends \Magento\Framework\Model\AbstractModel
{
    //constructor
    public function _construct()
    {
        $this->_init(\Ksolves\Bankpay\Model\ResourceModel\Bankpay::class);
    }
}
