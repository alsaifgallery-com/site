<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Test\Model;

/**
 * Class MassgeneratorTest
 * @package Amasty\Xcoupon\Test\Model
 * @author Artem Brunevski
 */
class MassgeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testGeneratePool()
    {
        $qty = 10;
        $data = [
            'qty' => $qty,
            'pattern' => 'test-coupon-LLDD'
        ];

        $couponMock = $this->getMock(
            'Magento\SalesRule\Model\Coupon',
            [
                '__wakeup',
                'setId',
                'setRuleId',
                'setUsageLimit',
                'setUsagePerCustomer',
                'setExpirationDate',
                'setCreatedAt',
                'setType',
                'setCode',
                'save'
            ],
            [],
            '',
            false
        );

        $couponMock->expects($this->any())->method('setId')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setRuleId')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setUsageLimit')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setUsagePerCustomer')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setExpirationDate')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setCreatedAt')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setType')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('setCode')->will($this->returnSelf());
        $couponMock->expects($this->any())->method('save')->will($this->returnSelf());

        $couponFactoryMock = $this->getMock('Magento\SalesRule\Model\CouponFactory', ['create'], [], '', false);
        $couponFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($couponMock));

        $dateTimeMock = $this->getMock('Magento\Framework\Stdlib\DateTime', ['formatDate'], [], '', false);
        $dateMock = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', ['gmtTimestamp'], [], '', false);
        $resourceMock = $this->getMock(
            'Magento\SalesRule\Model\ResourceModel\Coupon',
            ['exists', '__wakeup', 'getIdFieldName'],
            [],
            '',
            false
        );
        $salesRuleCouponMock = $this->getMock('Magento\SalesRule\Helper\Coupon', ['getCharset'], [], '', false);

        /** @var \Amasty\Xcoupon\Model\Massgenerator $massgenerator */
        $massgenerator = $this->objectManager->getObject(
            'Amasty\Xcoupon\Model\Massgenerator',
            [
                'couponFactory' => $couponFactoryMock,
                'dateTime' => $dateTimeMock,
                'date' => $dateMock,
                'resource' => $resourceMock,
                'data' => $data,
                'salesRuleCoupon' => $salesRuleCouponMock
            ]
        );

        $this->assertEquals($massgenerator, $massgenerator->generatePool());
        $this->assertEquals($qty, $massgenerator->getGeneratedCount());
    }


    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The pattern must be set up.
     */
    public function testGenerateCodeException()
    {
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Massgenerator');
        $massgenerator->generateCode();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The rule must be set up.
     */
    public function testCleanException()
    {
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Massgenerator');
        $massgenerator->clean();
    }

    /**
     * Data for validate test
     *
     * @return array
     */
    public function validateDataProvider()
    {
        return [
            [
                'data' => [
                    'qty' => 20,
                    'rule_id' => 1,
                    'pattern' => 'LLDD',
                ],
                'result' => true,
            ],
            [
                'data' => [
                    'rule_id' => 1,
                    'pattern' => 'LLDD'
                ],
                'result' => false,
            ],
            [
                'data' => [
                    'qty' => 20,
                    'pattern' => 'LLDD'
                ],
                'result' => false,
            ],
            [
                'data' => [
                    'qty' => 20,
                    'rule_id' => 1
                ],
                'result' => false,
            ]
        ];
    }

    /**
     * Run test validateData method
     *
     * @param array $data
     * @param bool $result
     *
     * @dataProvider validateDataProvider
     */
    public function testValidateData(array $data, $result)
    {
        /** @var \Amasty\Xcoupon\Model\Massgenerator $massgenerator */
        $massgenerator = $this->objectManager->getObject('Amasty\Xcoupon\Model\Massgenerator');

        $this->assertEquals($result, $massgenerator->validateData($data));
    }
}