# Monta Checkout API wrapper magento2-checkout

Wrapper for connecting webshops or frameworks to the Monta backend. Used by Monta modules for Magento, WooCommerce and Shopware.

### Usage

Instantiate and use the API like thus:

```php
use Monta\CheckoutApiWrapper\Objects\Settings;

/** @var Monta\CheckoutApiWrapper\Service\ApiFactory $apiFactory */
$api = $this->apiFactory->create(
    settings: new Settings(
        origin: $this->montaConfiguration->getOrigin(),
        user: $this->montaConfiguration->getUsername(),
        password: $this->montaConfiguration->getPassword(),
        googleKey: $this->montaConfiguration->getGoogleKey(),
    ),
    systemInfo: [
        Settings::CORE_SOFTWARE => "Magento",
        Settings::CORE_VERSION => "2.4.8",
        Settings::MODULE_NAME => "YourCustom_Checkout",
        Settings::MODULE_VERSION => "1.2",
    ]);

// Set destination address
$api->setAddressFromArray([
    'street' => "Main Street",
    'housenr' => "1",
    'city' => "Star City",
    'country' => 'NL'
]);

// Add quote items for costs calculation
foreach ($quoteItems as $item) {
    $api->addProductFromArray($item->getData());
}

// Retrieve shipping options
$shippingOptions = $api->getShippingOptions(computeKm: true);
```