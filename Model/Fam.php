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
* Class Fam
*/
class Fam extends \Magento\Framework\Model\AbstractModel
{
    //constructor
    public function _construct()
    {
        $this->_init(\Fam\Fam\Model\ResourceModel\Fam::class);
    }
}
