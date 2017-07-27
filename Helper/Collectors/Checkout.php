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

class Checkout
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * Checkout constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
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
}
