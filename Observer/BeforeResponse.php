<?php
namespace Scandi\Gtm\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Scandi\Gtm\Block\DataLayer;

/**
 * Class BeforeResponse
 * @package Scandi\Gtm\Observer
 */
class BeforeResponse implements ObserverInterface
{

    /**
     * Page header is <!doctype html>
     */
    const LENGTH_OF_PAGE_HEADER = 16;

    /**
     * @var DataLayer
     */
    protected $dataLayer;

    /**
     * BeforeResponse constructor.
     * @param DataLayer $dataLayer
     */
    public function __construct(
        DataLayer $dataLayer
    )
    {
        $this->dataLayer = $dataLayer;
    }

    /**
     * @param Observer $observer
     * @return self
     */
    public function execute(Observer $observer)
    {
        $response = $observer->getResponse();
        $result = substr($response->getContent(), 0, self::LENGTH_OF_PAGE_HEADER);

        // TODO implement another way to detect if it is a page
        if (strpos($result, 'doctype html') && $response->getStatusCode() === 200) {
            $layer = "<script>dataLayer.push(" . json_encode($this->dataLayer->collectLayer()) . ")</script>pushed";
            $response->setContent($response->getContent() . $layer);
        }
    }
}