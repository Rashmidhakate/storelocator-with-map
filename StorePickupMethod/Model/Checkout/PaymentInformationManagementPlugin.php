<?php
namespace Brainvire\StorePickupMethod\Model\Checkout;

use \Magento\Sales\Model\OrderFactory;
/**
 * One page checkout processing model
 */
class PaymentInformationManagementPlugin
{

    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }


    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $result = $proceed($cartId, $paymentMethod, $billingAddress);
        if($result){
            $extAttributes = $paymentMethod->getExtensionAttributes();
            $storeAddress = $extAttributes->getStoreAddress();
            if($storeAddress != "please select source"){
                $history = $this->orderRepository->get($result);
                $history->setStoreAddress($storeAddress);
                $history->save();
            }
        }

        return $result;
    }
}