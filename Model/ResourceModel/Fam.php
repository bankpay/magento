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

 namespace Ksolves\Fam\Model\ResourceModel;

 use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
* Class Fam
*/
class Fam extends AbstractDb
{
    //Initialising table.
    public function _construct()
    {
        $this->_init('fam_transaction', 'id');
    }
}
