<?php


namespace AlsaifGallery\Orders\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Magento\Sales\Setup\SalesSetupFactory;
class UpgradeData implements UpgradeDataInterface
{

     /**
     * Custom Order-Status code
     */
    const PACKED_CODE = 'packed';
    /**
     * Custom Order-Status label
     */
    const PACKED_LABEL = 'Packed';
    /**
     * Custom Order-Status code
     */
    const SHIPPED_CODE = 'shipped';
    /**
     * Custom Order-Status label
     */
    const SHIPPED_LABEL = 'Shipped';
    /**
     * Custom Order-Status code
     */
    const DELIVERED_CODE = 'delivered';
    /**
     * Custom Order-Status label
     */
    const DELIVERED_LABEL = 'Delivered';

    protected $salesSetupFactory;

    protected $statusResourceFactory;

    protected $statusFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        StatusResourceFactory $statusResourceFactory,
        SalesSetupFactory $salesSetupFactory,
        StatusFactory $statusFactory) {
        $this->statusResourceFactory = $statusResourceFactory;
        $this->statusFactory = $statusFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.0.0", "<")) {
            $this->addNewOrderStateAndStatus(self::SHIPPED_CODE, self::SHIPPED_LABEL, Order::STATE_PROCESSING);
            $this->addNewOrderStateAndStatus(self::PACKED_CODE, self::PACKED_LABEL, Order::STATE_PROCESSING);
            $this->addNewOrderStateAndStatus(self::DELIVERED_CODE, self::DELIVERED_LABEL, Order::STATE_COMPLETE);
        
            
            
        }
    }
    protected function addNewOrderStateAndStatus($code,$label,$state)
    {
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => $code,
            'label' => $label,
            'is_balleh'=> 1,
        ]);
        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            return;
        }
        $status->assignState($state, false, true);
        
    }
}