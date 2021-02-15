<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Test\Model;

/**
 * Class ImportTest
 * @package Amasty\Xcoupon\Test\Model
 * @author Artem Brunevski
 */
class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }


    /**
     * @return array
     */
    public function initDataProvider()
    {
        return [
            [
                'data' => [
                    'Coupon Code',
                    'Created',
                    'Times Used'
                ],
                'result' => [
                    'code' => 0,
                    'created_at' => 1,
                    'times_used' => 2
                ]
            ],
            [
                'data' => [
                    'Created',
                    'Coupon Code',
                    'Times Used'
                ],
                'result' => [
                    'created_at' => 0,
                    'code' => 1,
                    'times_used' => 2
                ]
            ],
            [
                'data' => [
                    'Coupon Code'
                ],
                'result' => [
                    'code' => 0,
                    'created_at' => null,
                    'times_used' => null
                ]
            ]
        ];
    }

    /**
     * @param array $data
     * @param bool $result
     *
     * @dataProvider initDataProvider
     */
    public function testInit(array $data, $result)
    {
        /** @var \Amasty\Xcoupon\Model\Import $import */
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Import');

        $class = new \ReflectionClass($massgenerator);

        $method = $class->getMethod('init');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($massgenerator, [$data]));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testExceptionInit()
    {
        /** @var \Amasty\Xcoupon\Model\Import $import */
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Import');

        $class = new \ReflectionClass($massgenerator);

        $method = $class->getMethod('init');
        $method->setAccessible(true);

        $method->invokeArgs($massgenerator, [
            [
                'Created',
                'Times Used'
            ]
        ]);
    }

    /**
     * @return array
     */
    public function buildRowDataProvider()
    {
        return [
            [
                'init' => [
                    'Coupon Code',
                    'Created',
                    'Times Used'
                ],
                'data' => [
                    'Test Code',
                    'Oct 6, 2016, 1:07:06 PM',
                    '1'
                ],
                'result' => [
                    'code' => 'Test Code',
                    'created_at' => '2016-10-06 13:07:06',
                    'times_used' => 1,
                    'rule_id' => 1,
                    'type' => 1
                ]
            ],
            [
                'init' => [
                    'Coupon Code',
                    'Created',
                    'Times Used'
                ],
                'data' => [
                    'Test Code',
                    '2016-10-06 13:07:06'
                ],
                'result' => [
                    'code' => 'Test Code',
                    'created_at' => '2016-10-06 13:07:06',
                    'times_used' => null,
                    'rule_id' => 1,
                    'type' => 1
                ]
            ]
        ];
    }

    /**
     * @param array $init
     * @param array $data
     * @param array $result
     *
     * @dataProvider buildRowDataProvider
     */
    public function testBuildRow(array $init, array $data, array $result)
    {
        /** @var \Amasty\Xcoupon\Model\Import $import */
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Import');

        $class = new \ReflectionClass($massgenerator);

        $initMethod = $class->getMethod('init');
        $initMethod->setAccessible(true);
        $initMethod->invokeArgs($massgenerator, [$init]);

        $buildRowMethod = $class->getMethod('buildRow');
        $buildRowMethod->setAccessible(true);

        $this->assertEquals($result, $buildRowMethod->invokeArgs($massgenerator, [$data, 1]));
    }

    /**
     * @return array
     */
    public function createdAtBuildRowDataProvider()
    {
        return [
            [
                'init' => [
                    'Coupon Code',
                    'Created',
                    'Times Used'
                ],
                'data' => [
                    'Test Code',
                    'Oct 6, 2016, 1:07:06 PM',
                    '1'
                ]
            ],
            [
                'init' => [
                    'Coupon Code',
                    'Created',
                    'Times Used'
                ],
                'data' => [
                    'Test Code',
                ]
            ],
            [
                'init' => [
                    'Coupon Code',
                    'Created',
                    'Times Used'
                ],
                'data' => [
                    'Test Code',
                    ''
                ]
            ],
            [
                'init' => [
                    'Coupon Code'
                ],
                'data' => [
                    'Test Code',
                ]
            ]
        ];
    }

    /**
     * @param array $init
     * @param array $data
     * @dataProvider createdAtBuildRowDataProvider
     */
    public function testDataIntegrityBuildRow(array $init, array $data)
    {
        /** @var \Amasty\Xcoupon\Model\Import $import */
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Import');

        $class = new \ReflectionClass($massgenerator);

        $initMethod = $class->getMethod('init');
        $initMethod->setAccessible(true);
        $initMethod->invokeArgs($massgenerator, [$init]);

        $buildRowMethod = $class->getMethod('buildRow');
        $buildRowMethod->setAccessible(true);

        $row = $buildRowMethod->invokeArgs($massgenerator, [$data, 1]);

        $this->assertArrayHasKey('created_at', $row);
        $this->assertNotNull($row['created_at']);
        $this->assertNotEmpty($row['created_at']);

        $this->assertArrayHasKey('times_used', $row);
        $this->assertNotNull($row['times_used']);
    }
}