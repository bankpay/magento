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

 namespace Ksolves\Bankpay\Model\ResourceModel;

 use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
* Class Bankpay
*/
class Bankpay extends AbstractDb
{
    //Initialising table.
    public function _construct()
    {
        $this->_init('bankpay_transaction', 'id');
    }
}
