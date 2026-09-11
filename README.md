# Monta Checkout API wrapper magento2-checkout

Wrapper for connecting webshops or frameworks to the Monta backend. Used by Monta modules for Magento, WooCommerce and Shopware.

### Usage

Instantiate and use the API like this example from Magento:

```php
use Monta\CheckoutApiWrapper\Objects\Settings;

/** @var Monta\CheckoutApiWrapper\Service\ApiFactory $apiFactory */
$api = $this->apiFactory->create(
    settings: new Settings(
        origin: $this->config->getOrigin(),
        user: $this->config->getUsername(),
        password: $this->config->getPassword(),
        googleKey: $this->config->getGoogleKey(),
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
/** @var array[] $shippingOptions - Array with all the delivery options, pickup points etc. */
$shippingOptions = $api->getShippingOptions(computeKm: true);
```