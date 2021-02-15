<?php
namespace Phoenix\CashOnDelivery\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Currency;
class ChangeCurrencyPosition implements ObserverInterface
{
    /**
     * currency_symbol_change_position
     *
     * @param Observer $observer
     * @return CurrencySymbolChangePositionObserver
     */
    public function execute(Observer $observer)
    {
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData('position', Currency::RIGHT);
        return $this;
    }
}
?>
