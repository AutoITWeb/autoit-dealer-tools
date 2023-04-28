<?php

namespace Biltorvet\Controller;

use Biltorvet\Factory\PriceFactory;
use Biltorvet\Factory\PropertyFactory;
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
    private $hideCashCards;

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
    private $primaryPriceType;
    /**
     * @var string
     */
    private $secondaryPriceType;

    /**
     * @var string
     */
    private $tertiaryPriceType;

    /**
     * @var string
     */
    private $prioritizedPriceFromOptions;
    /**
     * @var string
     */
    private $customNoCashPriceLabel;

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

        // Get settings
        $this->hideCashCards =
            WordpressHelper::getOption(2,'bdt_hide_cashprices_card') == 'on' ? true : false;

        $this->hideFinancingCards =
            WordpressHelper::getOption(2,'bdt_hide_financing_prices_card') == 'on' ? true : false;

        $this->hideFinancingDetails =
            WordpressHelper::getOption(3,'bdt_hide_financing_prices_details') == 'on' ? true : false;

        $this->hideLeasingCards =
            WordpressHelper::getOption(2,'bdt_hide_leasing_prices_card') == 'on' ? true : false;

        $this->hideLeasingDetails =
            WordpressHelper::getOption(3,'bdt_hide_leasing_prices_details') == 'on';

        // Set prioritized custom no-price label from options
        $getCustomNoPriceLabel =
            WordpressHelper::getOption(2,'bdt_no_price_label');

        // Set prioritized price from options
        $getPrioritizedPrice = WordpressHelper::getOption(2, 'bdt_prioritized_price');

        // Set custom no-price label
        switch($getCustomNoPriceLabel)
        {
            case '':
                $this->customNoCashPriceLabel = 'Ring for pris';
                break;
            default:
                $this->customNoCashPriceLabel = $getCustomNoPriceLabel;
        }

        // Set prioritized price from options
        switch($getPrioritizedPrice)
        {
            case '-1':
                $this->prioritizedPriceFromOptions = null;
                break;
            case "Kontant":
                $this->prioritizedPriceFromOptions = "cashPrice";
                break;
            case "Finansiering":
                $this->prioritizedPriceFromOptions = "financingPrice";
                break;
            case "Leasing":
                $this->prioritizedPriceFromOptions = "leasingPrice";
                break;
            default:
                $this->prioritizedPriceFromOptions = null;
        }
    }

    /**
     * Decides which price to show as the primary price
     * @param  string $type
     * @return string
     */
    public function GetPrimaryPrice(string $type, string $primaryPriceTypeFromStatusCode = null)
    {
        // Set classes
        $priceCssClass = $type === 'card' ? 'price bdt_color primary-price-card' : 'bdt_price_big primary-price-details';
        $priceLabelCssClass = $type === 'card' ? 'priceLabel primary-price-label-card' : 'bdt_price_mainlabel primary-price-label-details';

        /*
         * A prioritized price has been selected from the plugin settings or via a shortcode
         * Basic logic:
         * 1. Selected prioritized price will be selected
         * 2. If pricetype is null cash price -> leasing price -> financing price -> no price will be selected in that order
         */

        if($primaryPriceTypeFromStatusCode !== null)
        {
            $this->prioritizedPriceFromOptions = $primaryPriceTypeFromStatusCode;
        }

        if($this->prioritizedPriceFromOptions !== null)
        {
            if($this->prioritizedPriceFromOptions === 'cashPrice')
            {
                // Cash price set as prioritized price
                return $this->CashPriceSelectedAsPrioritizedPrice($priceCssClass, $priceLabelCssClass, $type);
            }
            else if($this->prioritizedPriceFromOptions === 'leasingPrice')
            {
                // Leasing price set as prioritized price
                return $this->LeasingPriceSelectedAsPrioritizedPrice($priceCssClass, $priceLabelCssClass, $type);
            }
            else
            {
                // Financing price set as prioritized price
                return $this->FinancingPriceSelectedAsPrioritizedPrice($priceCssClass, $priceLabelCssClass, $type);
            }
        }
        else
        {
            /*
             * Basic prio:
             * 1. Leasing price
             * 2. Financing price
             * 3. Cash price
             * 4. No price (Set custom "no price" label in the plugin settings
             */

            // Prioritized price has NOT been selected in the plugin settings
            if (!$this->hideLeasingCards && $this->price->getHasLeasingPrice())
            {
                // Show leasing price as prioritized price
                $this->primaryPriceType = 'leasingPrice';
                $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setLeasingPrice;
            }
            else if(!$this->hideFinancingCards && $this->price->getHasFinancingPrice())
            {
                // Show financing price as prioritized price
                $this->primaryPriceType = 'financingPrice';
                $setFinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setFinancingPrice;
            }
            else
            {
                if(!$this->hideCashCards && $this->price->getHasCashPrice())
                {
                    // Show cash price as prioritized price
                    $this->primaryPriceType = 'cashPrice';
                    $setCashPrice = $this->ReturnCashPrice($priceCssClass, $priceLabelCssClass, $type);
                    return $setCashPrice;
                }
                else
                {
                    // No price available for the current vehicle
                    $setNoPrice = $this->ReturnNoCashPrice($priceCssClass, $priceLabelCssClass, $type);
                    return $setNoPrice;
                }
            }
        }
    }

    /**
     * Decides whether to show and which price to show as the secondary price
     * @param  string $type
     * @return string
     */
    public function GetSecondaryPrice(string $type, $hideSecondaryPrice = false)
    {
        // Return empty string
        if($hideSecondaryPrice === true)
        {
            return '';
        }

        // This is what we'll eventually return
        $noSecondaryPriceToReturn = '';
        $priceCssClass = $type === 'card' ? 'bdt_price_small_cashprice_vehicle_card secondary-price-card' : 'bdt_price_big secondary-price-details';
        $priceLabelCssClass = $type === 'card' ? 'bdt_price_small_cashprice_vehicle_card_label secondary-price-label-card' : 'bdt_price_mainlabel secondary-price-label-details';

        // Leasing / financing is the prioritized price
        if (!$this->hideLeasingCards && $this->primaryPriceType === 'leasingPrice' || !$this->hideFinancingCards && $this->primaryPriceType === 'financingPrice')
        {
            if($this->price->getHasCashPrice() && !$this->hideCashCards)
            {
                // Show cash price as secondary price
                $this->secondaryPriceType = 'cashPrice';
                $setCashPrice = $this->ReturnCashPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setCashPrice;
            }
            else if(!$this->primaryPriceType === 'leasingPrice' && !$this->hideLeasingCards && $this->price->getHasLeasingPrice())
            {
                // Show leasing price as secondary price
                $this->secondaryPriceType = 'leasingPrice';
                $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setLeasingPrice;
            }
            else if(!$this->primaryPriceType === 'financingPrice' && !$this->hideFinancingCards && $this->price->getHasFinancingPrice())
            {
                // Show financing price as secondary price
                $this->secondaryPriceType = 'financingPrice';
                $setFinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setFinancingPrice;
            }
            else
            {
                // No secondary price to show
                return $noSecondaryPriceToReturn;
            }
        }
        else
        {
            // Cash price is prioritized price
            if(!$this->hideLeasingCards && $this->price->getHasLeasingPrice())
            {
                // Show leasing price as secondary price
                $this->secondaryPriceType = 'leasingPrice';
                $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setLeasingPrice;
            }
            else if(!$this->hideFinancingCards && $this->price->getHasFinancingPrice())
            {
                // Show cash price as secondary price
                $this->secondaryPriceType = 'financingPrice';
                $setFinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
                return $setFinancingPrice;
            }
            else
            {
                // No secondary price to show
                return $noSecondaryPriceToReturn;
            }
        }
    }

    public function GetTertiaryPrice(string $type)
    {
        // This is what we'll eventually return
        $noTertiaryPriceToReturn = '';
        $priceCssClass = $type === 'card' ? 'bdt_price_small_cashprice_vehicle_card tertiary-price-card' : 'bdt_price_big tertiary-price-details';
        $priceLabelCssClass = $type === 'card' ? 'bdt_price_small_cashprice_vehicle_card_label tertiary-price-label-card' : 'bdt_price_mainlabel tertiary-price-label-details';

        if($this->primaryPriceType !== 'cashPrice' && $this->secondaryPriceType !== 'cashPrice' && $this->price->getHasCashPrice())
        {
            // Return cash price if any
            $this->tertiaryPriceTypePriceType = 'cashPrice';
            $setCashPrice = $this->ReturnCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setCashPrice;
        }
        else if($this->primaryPriceType !== 'leasingPrice' && $this->secondaryPriceType !== 'leasingPrice' && $this->price->getHasLeasingPrice())
        {
            // Return leasing price if any
            $this->tertiaryPriceTypePriceType = 'leasingPrice';
            $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setLeasingPrice;
        }
        else if($this->primaryPriceType !== 'financingPrice' && $this->secondaryPriceType !== 'financingPrice' && $this->price->getHasfinancingPrice())
        {
            // Return financing price if any
            $this->tertiaryPriceTypePriceType = 'financingPrice';
            $setFinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setFinancingPrice;
        }
        else
        {
            // No tertiary price
            return $noTertiaryPriceToReturn;
        }
    }

    /**
     * Creates the price and pricelabel markup
     * @param  string $cssClass
     * @param  string $data
     * @return string
     */
    public function CreateHtmlMarkUp(string $cssClass, string $data, string $type)
    {
        $htmlMarkUpToReturn = '';

        switch ($type)
        {
            case 'card':
                $htmlMarkUpToReturn = '<span class="' . $cssClass . '">' . $data . '</span>';
                break;
            case 'details':
                $htmlMarkUpToReturn = '<span class="' . $cssClass . '">' . $data . '</span>';
                break;
            default:
                $htmlMarkUpToReturn = '<span class="' . $cssClass . '">' . $data . '</span>';
        }

        return $htmlMarkUpToReturn;
    }

    /**
     * Cash price as prioritized price logic
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function CashPriceSelectedAsPrioritizedPrice(string $priceCssClass, string $priceLabelCssClass, string $type)
    {
        if($this->price->getHasCashPrice())
        {
            $this->primaryPriceType = 'cashPrice';
            $setCashPrice = $this->ReturnCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setCashPrice;
        }
        else if($this->price->getHasLeasingPrice())
        {
            $this->primaryPriceType = 'leasingPrice';
            $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setLeasingPrice;
        }
        else if($this->price->getHasfinancingPrice())
        {
            $this->primaryPriceType = 'financingPrice';
            $setfinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setfinancingPrice;
        }
        else
        {
            // No price available for the current vehicle
            $setNoPrice = $this->ReturnNoCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setNoPrice;
        }
    }

    /**
     * Leasing price as prioritized price logic
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function LeasingPriceSelectedAsPrioritizedPrice(string $priceCssClass, string $priceLabelCssClass, string $type)
    {
        if($this->price->getHasLeasingPrice())
        {
            $this->primaryPriceType = 'leasingPrice';
            $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setLeasingPrice;
        }
        else if($this->price->getHasCashPrice())
        {
            $this->primaryPriceType = 'cashPrice';
            $setCashPrice = $this->ReturnCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setCashPrice;
        }
        else if($this->price->getHasfinancingPrice())
        {
            $this->primaryPriceType = 'financingPrice';
            $setfinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setfinancingPrice;
        }
        else
        {
            // No price available for the current vehicle
            $setNoPrice = $this->ReturnNoCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setNoPrice;
        }
    }

    /**
     * Cash price as prioritized price logic
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function FinancingPriceSelectedAsPrioritizedPrice(string $priceCssClass, string $priceLabelCssClass, string $type)
    {
        if($this->price->getHasfinancingPrice())
        {
            $this->primaryPriceType = 'financingPrice';
            $setfinancingPrice = $this->ReturnFinancingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setfinancingPrice;
        }
        else if($this->price->getHasCashPrice())
        {
            $this->primaryPriceType = 'cashPrice';
            $setCashPrice = $this->ReturnCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setCashPrice;
        }
        else if($this->price->getHasLeasingPrice())
        {
            $this->primaryPriceType = 'leasingPrice';
            $setLeasingPrice = $this->ReturnLeasingPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setLeasingPrice;
        }
        else
        {
            // No price available for the current vehicle
            $setNoPrice = $this->ReturnNoCashPrice($priceCssClass, $priceLabelCssClass, $type);
            return $setNoPrice;
        }
    }

    /**
     * Creates the no-price and no-pricelabel markup
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function ReturnNoCashPrice(string $priceCssClass, string $priceLabelCssClass, string $type) : string
    {
        $prioritizedCardPriceToReturn = $this->CreateHtmlMarkUp($priceCssClass, '-', $type);
        $prioritizedCardPriceToReturn .= $this->CreateHtmlMarkUp($priceLabelCssClass, $this->customNoCashPriceLabel, $type);

        return $prioritizedCardPriceToReturn;
    }

    /**
     * Creates the no-price and no-pricelabel markup
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function ReturnCashPrice(string $priceCssClass, string $priceLabelCssClass, string $type) : string
    {
        // We want to display a special price label on the details page
        $selectedPriceLabel = $type === 'details' ? $this->price->getCashPriceLabelDetailsPage() : $this->price->getCashPriceLabelVehicleCards();

        $prioritizedCardPriceToReturn = $this->CreateHtmlMarkUp($priceCssClass, $this->price->getCashPriceFormatted(), $type);
        $prioritizedCardPriceToReturn .= $this->CreateHtmlMarkUp($priceLabelCssClass, $selectedPriceLabel, $type);

        return $prioritizedCardPriceToReturn;
    }

    /**
     * Creates the no-price and no-pricelabel markup
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function ReturnFinancingPrice(string $priceCssClass, string $priceLabelCssClass, string $type) : string
    {
        $prioritizedCardPriceToReturn = $this->CreateHtmlMarkUp($priceCssClass, $this->price->getfinancingPriceFormatted(), $type);
        $prioritizedCardPriceToReturn .= $this->CreateHtmlMarkUp($priceLabelCssClass, $this->price->getFinancingPriceLabelVehicleCards(), $type);

        return $prioritizedCardPriceToReturn;
    }

    /**
     * Creates the no-price and no-pricelabel markup
     * @param  string $priceCssClass
     * @param  string $priceLabelCssClass
     * @return string
     */
    public function ReturnLeasingPrice(string $priceCssClass, string $priceLabelCssClass, string $type) : string
    {
        $prioritizedCardPriceToReturn = $this->CreateHtmlMarkUp($priceCssClass, $this->price->getLeasingPriceFormatted(), $type);
        $prioritizedCardPriceToReturn .= $this->CreateHtmlMarkUp($priceLabelCssClass, $this->price->getLeasingPriceLabelVehicleCards(), $type);

        return $prioritizedCardPriceToReturn;
    }

    /**
     * @return string
     */
    public function getStructuredDataPrice()
    {
        if($this->price->getPriceValue())
        {
            return $this->price->getPriceValue();
        }

        if (!$this->hideFinancingCards && $this->price->getFinancingValue()) {
            $this->primaryPriceType = 'financing';
            return $this->price->getFinancingValue();
        } else {
            if (!$this->hideLeasingCards && $this->price->getLeasingPriceValue()) {
                $this->primaryPriceType = 'leasing';
                if ($this->price->getIsBusinessLeasing()) {
                    return $this->price->getLeasingPriceValue();
                } else if($this->price->getIsPrivateLeasing()) {
                    return $this->price->getLeasingPriceValue();
                } else {
                    return $this->price->getLeasingPriceValue();
                }
            } else {
                if ($this->price->getPriceValue()) {
                    $this->primaryPriceType = 'cash';
                    return $this->price->getPriceValue();
                }
            }
        }

        return null;
    }

    /**
     * @param float $value
     * @return string
     */
    private function formatValue(float $value): string
    {
        return (string)number_format($value, 0, ',', '.') . ',-';
    }
}
