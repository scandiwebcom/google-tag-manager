<?php


namespace Scandi\Gtm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Index extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param JsonFactory $jsonFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        JsonFactory $jsonFactory,
        JsonHelper $jsonHelper
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|null
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            throw new NotFoundException(__('Usage is incorrect'));
        }
        $result = $this->jsonFactory->create();
        if ($this->customerSession->getAddToCart()) {
            $result->setData($this->customerSession->getAddToCart());
            $this->customerSession->unsAddToCart();
            return $result;
        } else if ($this->customerSession->getRemoveFromCart()) {
            $result->setData($this->customerSession->getRemoveFromCart());
            $this->customerSession->unsRemoveFromCart();
            return $result;
        }
        return null;
    }
}