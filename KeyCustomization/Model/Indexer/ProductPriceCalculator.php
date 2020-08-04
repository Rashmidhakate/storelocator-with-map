<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Brainvire\KeyCustomization\Model\Indexer;

/**
 * Product price calculation according rules settings.
 */
class ProductPriceCalculator extends \Magento\CatalogRule\Model\Indexer\ProductPriceCalculator
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Calculates product price.
     *
     * @param array $ruleData
     * @param null $productData
     * @return float
     */
    public function calculate($ruleData, $productData = null)
    {
        if ($productData !== null && isset($productData['rule_price'])) {
            $productPrice = $productData['rule_price'];
        } else {
            $productPrice = $ruleData['default_price'];
        }

        switch ($ruleData['action_operator']) {
            case 'two_key':
                $productPrice = $productPrice / 2;
                break;
            case 'three_key':
                $productPrice = $productPrice / 3;
                break;
            case 'four_key':
                $productPrice = $productPrice / 4;
                break;
            case 'to_fixed':
                $productPrice = min($ruleData['action_amount'], $productPrice);
                break;
            case 'to_percent':
                $productPrice = $productPrice * $ruleData['action_amount'] / 100;
                break;
            case 'by_fixed':
                $productPrice = max(0, $productPrice - $ruleData['action_amount']);
                break;
            case 'by_percent':
                $productPrice = $productPrice * (1 - $ruleData['action_amount'] / 100);
                break;
            default:
                $productPrice = 0;
        }

        return $this->priceCurrency->round($productPrice);
    }
}
