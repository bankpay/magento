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

/**
* Class Fam
*/
class Fam extends \Magento\Framework\Model\AbstractModel
{
    //constructor
    public function _construct()
    {
        $this->_init(\Ksolves\Fam\Model\ResourceModel\Fam::class);
    }
}
