<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 18/08/2025 10:56
 */
namespace Monta\CheckoutApiWrapper\Tests;

use Monta\CheckoutApiWrapper\Helper\Address;
use PHPUnit\Framework\TestCase;

class AddressConverter extends TestCase
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
            'street' => 'Hoefkade',
            'houseNumber' => '1156',
            'houseNumberAddition' => 'A',
            // Each of these inputs should result in the above output
            self::TEST_INPUTS => [
                [ // TC101
                    'street' => 'Hoefkade',
                    'houseNumber' => '1156',
                    'houseNumberAddition' => 'A',
                ],
                [
                    // with whitespaces
                    'street' => ' Hoefkade   ',
                    'houseNumber' => '1156',
                    'houseNrExt' => ' A',
                ],
                [
                    // alternative keys
                    'fullStreet' => 'Hoefkade',
                    'houseNr' => '1156',
                    'HouseNrExt' => 'A',
                ],
                [
                    // Magento sometimes puts street as array
                    'street' => [
                        'Hoefkade',
                        '1156',
                        'A'
                    ]
                ],
//                [
//                    // TODO 4 addresslines not currently supported
//                        'street' => [
//                            'Hoefkade',
//                            '1154',
//                            'A',
//                            'achterom',
//                        ]
//                ],
                [
                    'street' => [
                        'Hoefkade 1156A',
                    ]
                ],
                [
                    // Autofill sometimes puts these in both fields
                    'street' => "Hoefkade 1156",
                    'houseNumber' => '1156',
                    'houseNumberAddition' => 'A',
                ],
                [
                    // Some systems pass only one string
                    'street' => "Hoefkade 1156A",
                ]
            ]
        ],
        [
            'street' => "Papland",
            'houseNumber' => "1",
            'houseNumberAddition' => "",
            self::TEST_INPUTS => [
                [ // TC201
                    'street' => "Papland",
                    'houseNumber' => "1",
                ], [
                    'street' => "Papland 1",
                    'houseNumber' => "1",
                ],
            ]
        ],
        [
            'street' => null,
            self::TEST_INPUTS => [
                [],
                [ // TC302
                    'street' => []
                ],
                [
                    'street' => ['', '', '']
                ],
                ['houseNrExt' => null],
                ['fullStreet' => ''],
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
        foreach ($this->testCases as $caseIndex => $testCase) {
            foreach ($testCase[self::TEST_INPUTS] as $index => $input) {
                $testCode = "TC" . ($caseIndex + 1) . "0" . ($index + 1);
                // Add required defaults
                $input += self::TEST_ADDRESS_DEFAULTS;
                // Convert this address in Helper
                $address = $this->helper->convertAddress($input);
                // Compare each field to the expectation
                foreach (['street', 'houseNumber', 'houseNumberAddition'] as $field) {
                    $this->assertEquals($address->$field, $testCase[$field] ?? null, message: $testCode . " failed on " . $field);
                }
            }
        }
    }

}
