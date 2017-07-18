<?php
/**
 * Payment CC Types Source Model
 *
 * @category
 * @package
 * @author
 * @copyright
 * @license
 */

namespace Helcim\HelcimAPI\Model\Adminhtml\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DI');
    }
}
