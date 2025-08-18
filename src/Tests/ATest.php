<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 18/08/2025 10:56
 */
namespace Monta\CheckoutApiWrapper\Tests;

use Monta\CheckoutApiWrapper\Helper\Address;
use PHPUnit\Framework\TestCase;

class ATest extends TestCase
{
    protected const string TEST_EXPECTATION = 'expected';

    protected const string TEST_INPUTS = 'inputs';

    /** These fields are less important for testing but are required.
     * @var string[] - Default values for the test address
     */
    protected const array TEST_ADDRESS_DEFAULTS = [
        'countryCode' => "NL",
        'city' => "Den Haag",
        'postalCode' => "2525LM",
    ];

    /**
     * @var array[] - Number of addresses and how they can possibly be formatted
     */
    protected array $testCases = [
        [
            self::TEST_EXPECTATION => [
                'street' => 'Hoefkade',
                'houseNumber' => '1156',
                'houseNumberAddition' => 'A',
            ],
            // Each of these inputs should result in the above output
            self::TEST_INPUTS => [
                [
                    'street' => 'Hoefkade',
                    'houseNumber' => '1156',
                    'houseNumberAddition' => 'A',
                ],
                [
                    // Autofill sometimes puts these in both fields
                    'street' => "Hoefkade 1156",
                    'houseNumber' => '1156',
                    'houseNumberAddition' => 'A',
                ],
                [
                    'street' => "Hoefkade 1156A",
                ]
            ]
        ],
    ];
    protected Address $helper;

    protected function setUp(): void
    {
        $this->helper = new Address();
    }

    public function testAddressConverter()
    {
        $this->assertTrue(true);
        foreach ($this->testCases as $testCase) {
            $expectation = $testCase[self::TEST_EXPECTATION];
            foreach ($testCase[self::TEST_INPUTS] as $index => $input) {
                $testCode = "TC" . ($index + 1);
                // Add required defaults
                $input += self::TEST_ADDRESS_DEFAULTS;
                // Convert this address in Helper
                $address = $this->helper->convertAddress($input);
                // Compare each field to the expectation
                $this->assertEquals($address->street, $expectation['street'], message: $testCode . " failed on street");
                $this->assertEquals($address->houseNumber, $expectation['houseNumber'], message: $testCode . " failed on houseNumber");
                $this->assertEquals($address->houseNumberAddition, $expectation['houseNumberAddition'], message: $testCode . " failed on houseNumberAddition");
            }
        }
    }

}
