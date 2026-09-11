<?php
/**
 * @created 11/18/2025 12:36
 */

namespace Monta\CheckoutApiWrapper\Objects;

abstract class Objectable
{
    /** @var string - Shipper images are located here, grouped on ShipperGroupName */
    protected const string SHIPPER_IMAGE_URL = "https://cdn.monta.nl/PublicFiles/Images/shippers/%s/icon.svg";

    /** @var string[] - Fallback mapping while shipperGroupName is not returned by the API */
    protected const SHIPPER_GROUP_BY_SHORT = [
        'PostNLPakjesUntracked' => 'PostNL',
        'PostNLPakjesUntrackedBoxable' => 'PostNL',
        'PostNLPakjesTracked' => 'PostNL',
        'PostNLPakjesTrackedBoxable' => 'PostNL',
        'MyHairAfhaalpuntHilversum' => 'PostNL',
        'PostNlPakjesTrackedEu' => 'PostNL',
        'PostNlPakjesTrackedNo' => 'PostNL',
        'PostNlPakjesTrackedIs' => 'PostNL',
        'PostNlPakjesTrackedCh' => 'PostNL',
        'PostNlPakjesTrackedWorld' => 'PostNL',
        'PostNLfood' => 'PostNL',
        'PostNlBuspakje' => 'PostNL',
        'PostNLGroot' => 'PostNL',
        'PostNlBuspostPartijen01' => 'PostNL',
        'PostNlBuspostPartijen02' => 'PostNL',
        'PostNlBuspostPartijen03' => 'PostNL',
        'PostNlBuspostPartijen04' => 'PostNL',
        'PostNlSameDay' => 'PostNL',
        'PostNlBuspostBE' => 'PostNL',
        'PostNlBuspostAangetekend' => 'PostNL',
        'PAK' => 'PostNL',
        'PostNlBuspostEU' => 'PostNL',
        'EHM' => 'PostNL',
        'EHD' => 'PostNL',
        'EHT' => 'PostNL',
        'PostNlBuspostNl24hPartijenPost' => 'PostNL',
        'PostNlBuspostWorld' => 'PostNL',
        'PostNlBuspostNl24h' => 'PostNL',
        'PostNlBuspostNl72h' => 'PostNL',
        'PostNL' => 'PostNL',
        'PostNlBuspakje24h' => 'PostNL',
        'B2cEuropeMailPlus' => 'B2C',
        'B2cEuropeParcelPlus' => 'B2C',
        'Landmark' => 'BPost',
        'BpackPickupPoint' => 'BPost',
        'Bpack24hPro' => 'BPost',
        'BpostBuspostBe' => 'BPost',
        'LandmarkPickupPoint' => 'BPost',
        'SELGroot' => 'DHL',
        'DHLParcelConnectGroot' => 'DHL',
        'DHLDEWarenpost' => 'DHL',
        'DHLEP' => 'DHL',
        'DHLDEGroot' => 'DHL',
        'DHLExpress' => 'DHL',
        'DHLDEPickupPoint' => 'DHL',
        'DHLParcelUKPickupPoint' => 'DHL',
        'DHLParcelUK' => 'DHL',
        'DHLParcelConnectUK' => 'DHL',
        'DHLservicepunt' => 'DHL',
        'SEL' => 'DHL',
        'SELBuspakje' => 'DHL',
        'DHL' => 'DHL',
        'DHLParcelConnectPickupPoint' => 'DHL',
        'DHLParcelConnect' => 'DHL',
        'DHLservicepuntGroot' => 'DHL',
        'DHLpallet' => 'DHL',
        'DHLDE' => 'DHL',
        'DPD' => 'DPD',
        'DPDparcelstore' => 'DPD',
        'DPDKlein' => 'DPD',
        'DPDGroot' => 'DPD',
        'DPDparcelstoreGroot' => 'DPD',
        'DPDPL' => 'DPD',
        'DPDGroup' => 'DPD',
        'DPDGroupPickuppoint' => 'DPD',
        'UPSAP' => 'UPS',
        'UPSES' => 'UPS',
        'UPS' => 'UPS',
        'FedEx' => 'FedEx',
        'FedExICP' => 'FedEx',
        'RED' => 'RedjePakketje',
        'ParcelNlHomeDelivery' => 'ParcelNL',
        'ParcelNlParcelletter' => 'ParcelNL',
        'ParcelNlFreight' => 'ParcelNL',
        'OegemaPallet' => 'Oegema',
        'AFH' => 'Afhalen',
        'Trunkrs' => 'Trunkrs',
        'TrunkrsGroot' => 'Trunkrs',
        'Asendia' => 'Asendia',
        'DeutschePost' => 'DeutschePost',
        'DeutschePostBuspost' => 'DeutschePost',
        'Dynalogic' => 'Dynalogic',
        'Budbee' => 'Budbee',
        'BudbeePickupPoint' => 'Budbee',
        'Reviva' => 'Reviva',
        'Packs' => 'Packs',
        'DHLFYPickupPoint' => 'DHLForYou',
        'DHLFYPakket' => 'DHLForYou',
        'DHLFYBuspakje' => 'DHLForYou',
        'DHLFYSameDay' => 'DHLForYou',
        'DHLFYBuspakje500gram' => 'DHLForYou',
        'GLSInternational' => 'GLS',
        'GLSPickupPoint' => 'GLS',
        'GLSGroot' => 'GLS',
        'GLS' => 'GLS',
        'Swift' => 'Swift',
        'COT' => 'COT',
        'INT' => 'INT',
        'Cycloon' => 'Cycloon',
        'Izipack' => 'Izipack',
        'PLX' => 'PLX',
        'TransmissionPallet' => 'Transmission',
        'TransmissionHST' => 'Transmission',
        'Transmission' => 'Transmission',
        'TransmissionHSTPallet' => 'Transmission',
        'ColisPrivePickupPoint' => 'ColisPrive',
        'ColisPrive' => 'ColisPrive',
        'Seabourne' => 'Seabourne',
        'Colissimo' => 'Colissimo',
        'ColissimoPickupPoint' => 'Colissimo',
        'InPost' => 'InPost',
        'InPostPickupPoint' => 'InPost',
        'Hoef' => 'VanDeHoef',
        'HoefPallet' => 'VanDeHoef',
        'GEL' => 'GEL',
        'GELPallet' => 'GEL',
        'MondialRelay' => 'MondialRelay',
        'MondialRelayPickupPoint' => 'MondialRelay',
        'PostNord' => 'PostNord',
        'Veldhuizen' => 'Veldhuizen',
        'Mainfreight' => 'Mainfreight',
        'RoyalMail' => 'RoyalMail',
        'RoyalMailBuspost' => 'RoyalMail',
        'Bol' => 'Bol',
        'Amazon' => 'Amazon',
        'Cancelled' => 'Cancelled',
        'DAC' => 'Dachser',
        'Hermes2Man' => 'Hermes2man',
        'HeyWorld' => 'HeyWorld',
        'DeliveryMatchPallet' => 'DeliveryMatch',
        'DeliveryMatch' => 'DeliveryMatch',
        'Evri' => 'Evri',
        'EvriPickupPoint' => 'Evri',
        'XXL Pakket' => 'XXL Pakket',
        'Raben' => 'Raben',
        'Rijssen' => 'KoeriersdienstRijssen',
    ];

    /** @var array - Original data from API, packed and unpacked to JSON by frontend */
    protected array $originalData = [];

    /**
     * @param string|null $key
     * @return mixed
     */
    protected function getOriginalData(?string $key = null): mixed
    {
        if ($key) {
            return $this->originalData[$key] ?? null;
        } else {
            // Otherwise return the entire array
            return $this->originalData;
        }
    }

    /**
     * @param array $originalData
     * @return $this
     */
    protected function setOriginalData(array $originalData): static
    {
        // Remove any nested data
        unset($originalData['originalData']);
        $this->originalData = $originalData;
        return $this;
    }

    /** Update data
     *
     * @param array $originalData
     * @return $this
     * @deprecated - No longer used anywhere and is illogical. Real properties can be updated
     */
    public function updateOriginalData(array $originalData): static
    {
        // update array, overwrite existing keys
        $this->originalData = array_merge($this->originalData, $originalData);
        return $this;
    }

    /** Many child classes have a code property
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /** TODO is this not just getVars()?
     *
     * @return array
     */
    public function toArray(): array
    {
        $option = null;
        foreach ($this as $key => $value) {
            $option[$key] = $value;
        }

        return $option;
    }

    /** Generic function for converting object to JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    protected static function resolveShipperImageKey(?string $shipperGroupName, ?string $shipperShort): ?string
    {
        $shipperGroupName = self::normalizeShipperIdentifier($shipperGroupName);
        if ($shipperGroupName) {
            return $shipperGroupName;
        }

        $shipperShort = self::normalizeShipperIdentifier($shipperShort);
        if (!$shipperShort) {
            return null;
        }

        return self::SHIPPER_GROUP_BY_SHORT[$shipperShort] ?? $shipperShort;
    }

    protected static function normalizeShipperIdentifier(?string $identifier): ?string
    {
        if ($identifier === null) {
            return null;
        }

        $identifier = trim($identifier);

        return $identifier !== '' ? $identifier : null;
    }

    /** Public function for getting all properties (including protected) of this class
     * Call as `static` instead of `self` for child class
     *
     * @return string[]
     */
    public static function getVars(): array
    {
        // call static because we want the child class
        return get_class_vars(static::class);
    }

    /**
     * @param string $json - JSON-encoded array of properties
     * @return static|null|ShippingOption|PickupPoint
     */
    public static function constructFromJson(string $json): static|null|ShippingOption|PickupPoint
    {
        // Convert JSON into array
        return static::construct(json_decode($json, true));
    }

    /** Construct object from array
     *
     * @param array $data
     * @param string|null $className
     * @return static|null
     */
    public static function construct(array $data, ?string $className = null): ?static
    {
        // Use the passed className or use the class that was called
        if (!$className) {
            $className = static::class;
        }

        // Get array with only the keys that are a property (to splat into constructor)
        $props = array_intersect_key(
            $data,
            // get all properties from specific child class
            $className::getVars(),
        );

        return !empty($props) ?
            // construct class
            (new $className(...$props))
                // keep the source data
                ->setOriginalData($data) : null;
    }
}
