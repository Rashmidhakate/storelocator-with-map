var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/shipping':'Brainvire_StorePickupMethod/js/view/shipping'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Brainvire_StorePickupMethod/js/order/set-shipping-information-mixin': true
            },
			'Magento_Checkout/js/action/place-order': {
				'Brainvire_StorePickupMethod/js/order/place-order-mixin': true
			},
			'Magento_Checkout/js/action/set-payment-information': {
				'Brainvire_StorePickupMethod/js/order/set-payment-information-mixin': true
			},
        }
    }
};