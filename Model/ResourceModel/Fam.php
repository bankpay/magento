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

 namespace Fam\Fam\Model\ResourceModel;

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
