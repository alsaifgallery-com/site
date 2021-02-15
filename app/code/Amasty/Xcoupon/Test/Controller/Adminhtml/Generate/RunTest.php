<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Test\Controller\Adminhtml\Generate;

/**
 * Class RunTest
 * @package Amasty\Xcoupon\Test\Controller\Adminhtml\Generate
 * @author Artem Brunevski
 */
class RunTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\Registry |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Amasty\Xcoupon\Controller\Adminhtml\Generate\Run
     */
    protected $run;

    /**
     * @var \Magento\SalesRule\Model\Rule |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleMock;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            true,
            true,
            ['getParam', 'getPost', 'getPostValue', 'isAjax']
        );

        $this->requestMock->expects($this->any())->method('isAjax')->willReturn(true);

        $this->responseMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\ResponseInterface',
            [],
            '',
            false,
            true,
            true,
            ['representJson']
        );

        $contextMock = $this->getMock(
            'Magento\Backend\App\Action\Context',
            [],
            [],
            '',
            false
        );

        $objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with('Magento\Framework\Json\Helper\Data')
            ->willReturn($this->objectManager->getObject('Magento\Framework\Json\Helper\Data'));

        $contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $contextMock->expects($this->any())->method('getResponse')->willReturn($this->responseMock);
        $contextMock->expects($this->any())->method('getObjectManager')->willReturn($objectManagerMock);

        $this->registryMock = $this->getMock(
            'Magento\Framework\Registry',
            ['register', 'registry'],
            [],
            '',
            false
        );

        $this->ruleMock = $this->getMock(
            'Magento\SalesRule\Model\Rule',
            ['__wakeup', 'load'],
            [],
            '',
            false
        );

        $this->registryMock->expects($this->atLeastOnce())
            ->method('registry')
            ->willReturn($this->ruleMock);

        $this->run = $this->objectManager->getObject(
            'Amasty\Xcoupon\Controller\Adminhtml\Generate\Run',
            [
                'context' => $contextMock,
                'coreRegistry' => $this->registryMock
            ]
        );
    }

    public function testExecute()
    {
        // Optional: Test anything here, if you want.
        $this->assertTrue(true, 'This should already work.');

        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}