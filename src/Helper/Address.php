<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 15:31
 */
namespace Monta\CheckoutApiWrapper\Helper;

use Monta\CheckoutApiWrapper\Objects\Address as WrapperAddress;

class Address
{
    protected const string RETURN_TYPE_STREET = 'street';

    protected const string RETURN_TYPE_HOUSE_NUMBER = 'house_number';

    protected const string RETURN_TYPE_HOUSE_NUMBER_EXT = 'house_number_ext';

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

    /** Get normalized address from array
     *
     * @param array $address
     * @return WrapperAddress
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function convertAddress(array $address): WrapperAddress
    {
        $countryCode = $address['countryCode'] ?? $address['country_id']
            ?? $address['country_code'] ?? $address['country'] ?? null;

        // Normalize street
        $street = $address['street'] ?? $address['fullstreet'] ?? '';
        if (is_array($street)) {
            // If it's an array, glue with spaces
            // Rest of the code expects a string
            $street = implode(' ', $street);
        }

        // When street is a string, presumably the housenr and addition were included.
        $houseNr = $address['housenumber'] ?? $address['housenr']
            ?? $address['house_number'] ?? $address['houseNumber']
            // or extract it from street
            ?? self::getAddressParts($street, self::RETURN_TYPE_HOUSE_NUMBER) ?? '';
        $houseNrAddition = $address['housenumberaddition'] ??
            $address['housenraddition'] ?? $address['house_number_addition'] ??
            $address['houseNumberAddition'] ?? $address['houseNrAddition'] ??
            $address['addition'] ?? $address['houseNumberExt'] ?? $address['house_number_ext'] ??
            // if not passed directly, extract it from street
            self::getAddressParts($street, self::RETURN_TYPE_HOUSE_NUMBER_EXT) ?? '';

        // Street at the end because it replaces the variable
        $convertedStreet = self::getAddressParts($street, self::RETURN_TYPE_STREET, $countryCode) ?? $street;
        // Occasionally street is converted empty, so we use the original street
        if ($convertedStreet) {
            $street = $convertedStreet;
        }
        $street = trim($street);
        // Extract values out of any possible array fields
        $postCode = $address['postcode'] ?? $address['postal_code'] ??
            $address['postalCode'] ?? $address['zipcode'] ?? $address['zip'] ?? '';
        $city = $address['city'] ?? $address['city_id'] ?? '';
        $state = $address['state'] ?? $address['region'] ?? '';
        // Return address as array, exactly in the shape of an Address object (to splat into constructor)
        return new WrapperAddress(
            street: $street,
            houseNumber: $houseNr,
            houseNumberAddition: $houseNrAddition,
            postalCode: $postCode,
            city: $city,
            state: $state,
            countryCode: $countryCode,
        );
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
        $countryCode = strtolower($countryCode);

        // Get street, house number and extension via preg match
        preg_match(
            $countryCode == 'nl' ? static::PREG_MATCH_ADDRESS_NL : static::PREG_MATCH_ADDRESS_BE,
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
                $return = $houseNumber;
                break;
            case self::RETURN_TYPE_HOUSE_NUMBER_EXT:
                $return = $houseNumberExtension;
                break;
            default:
                $return = $street;
                break;
        }
        // Always return string
        return (string)$return;
    }
}
