<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 15:31
 */
namespace Monta\CheckoutApiWrapper\Helper;

class Address
{
    protected const string RETURN_TYPE_STREET = 'street';

    protected const string RETURN_TYPE_HOUSE_NUMBER = 'house_number';

    protected const string RETURN_TYPE_HOUSE_NUMBER_EXT = 'house_number_ext';

    // Each field of Address, these must match the constructor parameters of Address
    public const string FIELD_STREET = 'street';

    public const string FIELD_HOUSE_NUMBER = 'houseNumber';

    public const string FIELD_HOUSE_NUMBER_ADDITION = 'houseNumberAddition';

    public const string FIELD_POSTAL_CODE = 'postalCode';

    public const string FIELD_CITY = 'city';

    public const string FIELD_STATE = 'state';

    public const string FIELD_COUNTRY_CODE = 'countryCode';

    public const string PREG_MATCH_ADDRESS_NL =
        '~(?P<street>.*?)' .              // The rest belongs to the street
        '\s?' .                           // Separator between street and number
        '(?P<number>\d{1,4})' .           // Number can contain a maximum of 4 numbers
        '[/\s\-]{0,2}' .                  // Separators between number and addition
        '(?P<number_suffix>' .
        '[a-zA-Z]{1}\d{1,3}|' .           // Numbers suffix starts with a letter followed by numbers or
        '-\d{1,4}|' .                     // starts with - and has up to 4 numbers or
        '\d{2}\w{1,2}|' .                 // starts with 2 numbers followed by letters or
        '[a-zA-Z]{1}[a-zA-Z\s]{0,3}' .    // has up to 4 letters with a space
        ')?$~';

    public const string PREG_MATCH_ADDRESS_BE =
        '~(?P<street>.*?)\s(?P<street_suffix>(?P<number>\S{1,8})\s?(?P<box_separator>bus?)?\s?(?P<box_number>\d{0,8}$))$~';

    /** Normalize address from array
     *
     * @param array $address
     * @return array
     */
    public static function convertAddress(array $address): array
    {
        // Normalize street
        $street = $address['street'] ?? $address['fullstreet'] ?? '';
        if (is_array($street)) {
            // If it's an array, glue with spaces
            $street = implode(' ', $address['street']);
        }
        $street = trim($street);

        // Extract values out of any possible array fields
        $countryCode = $address['countryCode'] ?? $address['country_id']
            ?? $address['country_code'] ?? $address['country'] ?? null;
        $postCode = $address['postcode'] ?? $address['postal_code'] ??
            $address['postalCode'] ?? $address['zipcode'] ?? $address['zip'] ?? '';
        $city = $address['city'] ?? $address['city_id'] ?? '';
        $state = $address['state'] ?? $address['region'] ?? '';
        // Return address as array, exactly in the shape of an Address object (to splat into constructor)
        return [
            self::FIELD_STREET => self::getAddressParts($street, self::RETURN_TYPE_STREET, $countryCode),
            self::FIELD_HOUSE_NUMBER => self::getAddressParts($street, self::RETURN_TYPE_HOUSE_NUMBER, $countryCode),
            self::FIELD_HOUSE_NUMBER_ADDITION => self::getAddressParts($street, self::RETURN_TYPE_HOUSE_NUMBER_EXT, $countryCode),
            self::FIELD_POSTAL_CODE => $postCode,
            self::FIELD_CITY => $city,
            self::FIELD_STATE => $state,
            self::FIELD_COUNTRY_CODE => $countryCode,
        ];
    }

    /**
     * Get the house number or its addition from a full street.
     * Caution: Only works for NL and BE addresses! Created for Monta/Shopware plugin but inexhaustive.
     *
     * @param string $fullStreet
     * @param string $returnType
     * @param string $countryCode
     * @return string
     */
    protected static function getAddressParts(string $fullStreet, string $returnType, string $countryCode = 'nl'): string
    {
        // Variables
        $houseNumber = null;
        $houseNumberExtension = null;

        // Get street, house number and extension via preg match
        preg_match(
            $countryCode === 'nl' ? static::PREG_MATCH_ADDRESS_NL : static::PREG_MATCH_ADDRESS_BE,
            $fullStreet,
            $matches
        );

        $street = $matches['street'] ?? null;

        if (!empty($matches['number']) && is_numeric($matches['number'])) {
            $houseNumber = $matches['number'];
        }

        $houseNumberExtension = $matches['number_suffix'] ?? null;

        // Return value depending on requested return type
        switch ($returnType) {
            case self::RETURN_TYPE_HOUSE_NUMBER:
                return (string)$houseNumber;
            case self::RETURN_TYPE_HOUSE_NUMBER_EXT:
                return (string)$houseNumberExtension;
            default:
                return $street;
        }
    }
}
