<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Plugin;

use Magento\Checkout\Controller\Sidebar\RemoveItem;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\Helper\Data;
use Scandi\Gtm\Helper\Config;

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
     * @var Config
     */
    protected $config;

    /**
     * RemoveFromCartPlugin constructor.
     * @param Data $jsonHelper
     * @param Session $customerSession
     * @param Config $config
     */
    public function __construct(
        Data $jsonHelper,
        Session $customerSession,
        Config $config
    )
    {
        $this->config = $config;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param RemoveItem $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(RemoveItem $subject, $result)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }
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
