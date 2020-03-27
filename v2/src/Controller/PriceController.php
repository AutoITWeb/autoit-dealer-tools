<?php

namespace Biltorvet\Controller;

use Biltorvet\Factory\PriceFactory;
use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Helper\WordpressHelper;
use Biltorvet\Model\Price;
use Biltorvet\Model\Vehicle;

class PriceController
{
    /**
     * @var Price
     */
    private $price;

    /**
     * @var boolean
     */
    private $hideFinancingCards;

    /**
     * @var boolean
     */
    private $hideFinancingDetails;

    /**
     * @var boolean
     */
    private $hideLeasingCards;

    /**
     * @var boolean
     */
    private $hideLeasingDetails;

    /**
     * @var string
     */
    private $noPriceValue;

    /**
     * @var string
     */
    private $monthlyPostFix;

    /**
     * @var string
     */
    private $prioritizedPriceType;

    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * PriceController constructor.
     * @param Vehicle $vehicle
     * @param string $noPriceValue
     * @param string $monthlyPostFix
     */
    public function __construct(Vehicle $vehicle, string $noPriceValue = '-', string $monthlyPostFix = ' /mdr.')
    {
        $this->noPriceValue = $noPriceValue;
        $this->monthlyPostFix = $monthlyPostFix;
        $this->vehicle = $vehicle;

        $this->price = PriceFactory::create($vehicle);
        $this->hideFinancingCards =
            WordpressHelper::getOption(2,'bdt_hide_financing_prices_card') == 'on' ? true : false;
        $this->hideFinancingDetails =
            WordpressHelper::getOption(3,'bdt_hide_financing_prices_details') == 'on' ? true : false;
        $this->hideLeasingCards =
            WordpressHelper::getOption(2,'bdt_hide_leasing_prices_card') == 'on' ? true : false;
        $this->hideLeasingDetails =
            WordpressHelper::getOption(3,'bdt_hide_leasing_prices_details') == 'on' ? true : false;
    }

    /**
     * @return string
     */
    public function getCardPrioritizedPrice()
    {

        if (!$this->hideFinancingCards && $this->price->getFinancingValue()) {
            $this->prioritizedPriceType = 'financing';
            return $this->formatValue($this->price->getFinancingValue()) . $this->monthlyPostFix;
        } else {
            if (!$this->hideLeasingCards && $this->price->getLeasingPriceValue()) {
                $this->prioritizedPriceType = 'leasing';
                if ($this->price->getIsBusinessLeasing()) {
                    return $this->formatValue(
                        $this->price->getLeasingPriceValue()
                    ) . $this->monthlyPostFix;
                } else {
                    return $this->formatValue($this->price->getLeasingPriceValue()) . $this->monthlyPostFix;
                }
            } else {
                if ($this->price->getPriceValue()) {
                    $this->prioritizedPriceType = 'cash';
                    return $this->formatValue($this->price->getPriceValue());
                }
            }
        }

        return $this->noPriceValue;
    }

    /**
     * @return string
     */
    public function showCashPriceFinance()
    {
        if (!$this->hideFinancingCards && $this->price->getFinancingValue()) {

            if($this->prioritizedPriceType = 'financing' && $this->formatValue($this->price->getPriceValue()) != null)
            {
                return _e('Cash price', 'biltorvet-dealer-tools') . ': ' . $this->formatValue($this->price->getPriceValue());
            }
            else {
                return "<br>";
            }
        }
        else {
            return "<br>";
        }
    }

    /**
     * @param float $value
     * @return string
     */
    private function formatValue(float $value): string
    {
        return (string)number_format($value, 0, ',', '.') . ',-';
    }

    /**
     * @return string
     */
    public function getCardLabel()
    {
        switch ($this->prioritizedPriceType) {
            case 'financing':
                return __('Financing price', 'biltorvet-dealer-tools');
                break;
            case 'leasing':
                if ($this->price->getIsBusinessLeasing()) {
                    return __('Leasing price (ex. VAT)', 'biltorvet-dealer-tools');
                } else {
                    return __('Leasing price', 'biltorvet-dealer-tools');
                }
                break;
            default:
                if ($this->price->getIsBusinessPrice() && $this->vehicle->getType() === 'Varebil') {
                    return __('Excl. VAT', 'biltorvet-dealer-tools');
                }
                break;
        }
    }

    /**
     * @TODO: refactor
     *
     * @return mixed
     */
    public function getDetailsPrioritizedPrices()
    {
        if (!$this->hideFinancingDetails && $this->price->getFinancingValue()) {
            $prices['financing'] = [
                'price' => $this->formatValue($this->price->getFinancingValue()),
                'label' => $this->getDetailsLabel('financing')
            ];
        }

        if (!$this->hideLeasingDetails && $this->price->getLeasingPriceValue()) {
            $prices['leasing'] = [
                'price' => $this->formatValue($this->price->getLeasingPriceValue()),
                'label' => $this->getDetailsLabel('leasing')
            ];
        }

        if ($this->price->getPriceValue()) {
            $prices['cash'] = [
                'price' => $this->formatValue($this->price->getPriceValue()),
                'label' => $this->getDetailsLabel('cash')
            ];
        }


        if (!$this->hideFinancingDetails && $this->price->getFinancingValue()) {
            $new_value = $prices['financing'];
            unset($prices['financing']);
            array_unshift($prices, $new_value);
        } else {
            if (!$this->hideLeasingDetails && $this->price->getLeasingPriceValue()) {
                $new_value = $prices['leasing'];
                unset($prices['leasing']);
                array_unshift($prices, $new_value);
            } else {
                if ($this->price->getPriceValue()) {
                    $new_value = $prices['cash'];
                    unset($prices['cash']);
                    array_unshift($prices, $new_value);
                }
            }
        }

        return $prices ?? [0 => ['label' => '', 'price' => '-']];
    }

    public function getDetailsLabel(string $prioritizedPriceType)
    {

        switch ($prioritizedPriceType) {
            case 'financing':
                return __('Financing price pr. m.', 'biltorvet-dealer-tools');
                break;
            case 'leasing':
                if ($this->price->getIsBusinessLeasing()) {
                    return __('Leasing price pr. m. (ex. VAT)', 'biltorvet-dealer-tools');
                } else {
                    return __('Leasing price pr. m.', 'biltorvet-dealer-tools');
                }
                break;
            default:
                if ($this->price->getIsBusinessPrice() && $this->vehicle->getType() === 'Varebil') {
                    return __('Excl. VAT', 'biltorvet-dealer-tools');
                } else {
                    return __('Cash price', 'biltorvet-dealer-tools');
                }
                break;
        }
    }
}
