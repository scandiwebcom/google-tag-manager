<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Magento\Framework\Json\Helper\Data;
use Magento\Sales\Model\OrderRepository;
use Scandi\Gtm\Helper\Configurable;

/**
 * Class Success
 * @package Scandi\Gtm\Helper\Collectors
 */
class Success
{

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var OrderRepository
     */
    protected $order;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * Success constructor.
     * @param Session $checkoutSession
     * @param OrderRepository $order
     * @param ProductRepository $productRepository
     * @param Category $category
     * @param Data $jsonHelper
     * @param Configurable $configurable
     */
    public function __construct(
        Session $checkoutSession,
        OrderRepository $order,
        ProductRepository $productRepository,
        Category $category,
        Data $jsonHelper,
        Configurable $configurable
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->productRepository = $productRepository;
        $this->category = $category;
        $this->jsonHelper = $jsonHelper;
        $this->configurable = $configurable;
    }

    /**
     * @return mixed
     */
    public function collectSuccess()
    {
        $order = $this->gatherSuccessData($this->jsonHelper->jsonDecode($this->checkoutSession->getGtmSuccess()));
        $this->checkoutSession->unstGtmSuccess();
        return "<script>dataLayer.push(" . $this->jsonHelper->jsonEncode($order) . ")</script>";
    }

    /**
     * @param $orderData
     * @return bool|\Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder($orderData)
    {
        if (array_key_exists('order_id', $orderData)) {
            try {
                return $this->order->get($orderData['order_id']);
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @param $orderData
     * @return array
     */
    public function gatherSuccessData($orderData)
    {
        $order = $this->getOrder($orderData);
        if (!$order) {
            return array();
        }
        $successData['event'] = 'purchase';
        $successData['ecommerce']['currencyCode'] = $order->getOrderCurrencyCode();
        $successData['ecommerce']['purchase']['actionField'] = $this->gatherActionField($order);
        $successData['ecommerce']['purchase']['products'] = array($this->gatherProducts($order));
        return $successData;
    }

    /**
     * @param $order
     * @return mixed
     */
    public function gatherActionField($order)
    {
        $actionField['id'] = $order->getIncrementId();
        $actionField['revenue'] = $order->getGrandTotal();
        $actionField['tax'] = $order->getTaxAmount();
        $actionField['shipping'] = $order->getShippingAmount();
        if ($order->getCouponCode()) {
            $actionField['coupon_code'] = $order->getCouponCode();
        } else {
            $actionField['coupon_code'] = '';
        }
        $actionField['coupon_discount_amount'] = $order->getDiscountAmount();
        $actionField['coupon_discount_amount_abs'] = $this->gatherDiscountAmount($order->getDiscountAmount());
        return $actionField;
    }

    /**
     * @param $order
     * @return array|bool
     */
    public function gatherProducts($order)
    {
        $brand = $this->category->config->getBrand();
        foreach ($order->getAllItems() as $product) {
            if ($product->getProductType() === Configurable::CONFIGURABLE_TYPE_ID) {
                $product = $this->configurable->extendConfigurable($product);
                $attributes = ['variant' => 'size', 'dimension1' => 'color'];
            }
            if ((int)$product->getPrice() == 0) {
                continue;
            }
            $categories = $this->productRepository->get($product->getSku())->getCategoryIds();
            $productData = [
                "name" => $product->getName(),
                "id" => $product->getSku(),
                "price" => $product->getPrice(),
                "category" => $this->category->getCategoryName($categories),
                "quantity" => $product->getQtyOrdered(),
                "dimension2" => "child_sku",
                "brand" => $brand,
                "affiliate" => "to be requested"
            ];
            if (!isset($attributes)) {
                $productsData[] = $productData;
                continue;
            }
            foreach($attributes as $key => $value) {
                if ($product->getData($value)) {
                    $productData[$key] = $product->getData($value);
                }
            }
            $productsData[] = $productData;
        }
        return isset($productsData) ? $productsData : false;
    }

    /**
     * @param $discount
     * @return mixed
     */
    public function gatherDiscountAmount($discount)
    {
        return ($discount < 0) ? $discount * -1 : $discount;
    }
}
