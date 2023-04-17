<?php
/**
 * Fam
 *
 * @category  Fam
 * @package   Ftl_Fam
 * @author    Fam Team
 * @copyright Copyright (c) Frictionless Technologies Ltd (https://www.joinfam.com/)
 * @license   https://joinfam.com/legal
 */ 
namespace Ftl\Fam\Model;

/**
* Class Fam
*/
class Fam extends \Magento\Framework\Model\AbstractModel
{
    //constructor
    public function _construct()
    {
        $this->_init(\Ftl\Fam\Model\ResourceModel\Fam::class);
    }
}
