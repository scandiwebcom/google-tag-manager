<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Json\Helper\Data;

class RemoveFromCartPlugin
{

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * RemoveFromCartPlugin constructor.
     * @param Data $jsonHelper
     * @param Session $customerSession
     */
    public function __construct(
        Data $jsonHelper,
        Session $customerSession
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Checkout\Controller\Sidebar\RemoveItem $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\Checkout\Controller\Sidebar\RemoveItem $subject, $result)
    {
        $content = $this->jsonHelper->jsonDecode($subject->getResponse()->getContent());
        if (!array_key_exists('success', $content)) {
            return $result;
        }
        if ($content['success'] !== true) {
            return $result;
        }
        $content['eventPush'] = $this->jsonHelper->jsonDecode($this->customerSession->getRemoveFromCart());
        $this->customerSession->unsRemoveFromCart();
        $subject->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($content)
        );
        return $result;
    }
}
?>
