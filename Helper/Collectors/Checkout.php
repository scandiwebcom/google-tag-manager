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
        $steps = '';
        foreach ($this->config->getCheckoutSteps() as $step) {
            $step = trim($step, ' ');
            $steps .= "'$step',";
        }
        $steps = rtrim($steps, ',');
        return "<script>checkoutLayerSteps = [" . $steps . "]</script>";
    }

    public function getCart()
    {
        return "<script>cartData = " . json_encode($this->cart->collectCart()) . ";</script>";
    }
}
