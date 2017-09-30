<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Scandi\Gtm\Helper\Config;

/**
 * Class Checkout
 * @package Scandi\Gtm\Helper\Collectors
 */
class Checkout
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * Checkout constructor.
     * @param Config $config
     * @param Cart $cart
     */
    public function __construct(
        Config $config,
        Cart $cart
    )
    {
        $this->config = $config;
        $this->cart = $cart;
    }

    /**
     * @return string
     */
    public function getCheckoutSteps()
    {
        return $this->makeCheckoutObject($this->config->getCheckoutSteps(), 'checkoutLayerSteps');
    }

    /**
     * @return string
     */
    public function getOptionWrappers()
    {
        return $this->makeCheckoutObject($this->config->getCheckoutWrappers(), 'checkoutWrappers');
    }

    /**
     * @param $configurations
     * @param $objectName
     * @return string
     */
    protected function makeCheckoutObject($configurations, $objectName)
    {
        $items = '';
        foreach($configurations as $configuration) {
            $configuration = trim($configuration, ' ');
            $items .= "'$configuration',";
        }
        $items = rtrim($items, ',');
        return "<script>$objectName = [" . $items . "]</script>";
    }

    /**
     * @return string
     */
    public function getCart()
    {
        return "<script>cartData = " . json_encode($this->cart->collectProducts($this->cart->quote)) . ";</script>";
    }
}
