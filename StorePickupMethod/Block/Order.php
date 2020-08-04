<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Brainvire\StorePickupMethod\Block;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
/**
 * Sales order view block
 *
 * @api
 * @since 100.0.2
 */
class Order extends \Magento\Sales\Block\Order\Info
{

    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->paymentHelper = $paymentHelper;
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context,$registry,$paymentHelper,$addressRenderer,$data);
    }
    
    public function getQuote(){
        return $this->quoteRepository;
    }
   
}
