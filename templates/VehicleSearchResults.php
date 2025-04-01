<?php
    /**
     * A template that shows all the vehicle search results.
     *
     * This template can be overriden by copying this file to your-theme/biltorvet-dealer-tools/VehicleSearchResults.php
     *
     * @author 		Biltorvet A/S
     * @package 	Biltorvet Dealer Tools
     * @version     1.0.0
     */
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    use Biltorvet\Factory\VehicleFactory;
    use Biltorvet\Helper\DataHelper;
    use Biltorvet\Model\Vehicle;

    global $wp;
    $root = dirname(dirname(dirname(plugin_dir_url( __FILE__ ))));
    $currentPage = get_query_var('bdt_page', -1);
    $bdt_root_url = null;

    if(isset($this->_options) && isset($this->_options['vehiclesearch_page_id'])) {
        $bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']),'/');
    } else {
        return '<!-- Cannot load Biltorvet vehicle search results: no root page (Vehicle search) has been set! -->';
    }

    if($currentPage === -1)
    {
        $currentPage = 1;
    }

    if(isset($atts) && isset($atts['relativepositiontosearch']))
    {
        $bdt_root_url .= $atts['relativepositiontosearch'];
    }

    if(!$this->biltorvetAPI)
    {
        die('API not set.');
    }

  try {

        $start = ($currentPage-1) * intval($this->biltorvetAPI->GetVehicleResultsPageLimit());
        $limit = intval($this->biltorvetAPI->GetVehicleResultsPageLimit());

        $filterObject = new BDTFilterObject();

        if(isset($_SESSION['bdt_filter']) && trim($_SESSION['bdt_filter']) !== '')
        {
            $filterObject = new BDTFilterObject(json_decode($_SESSION['bdt_filter'], true));
        }

        if(isset($atts) && isset($atts['makes']) && trim($atts['makes']) !== '')
        {
            $filterObject->Makes = explode(',', $atts['makes']);
        }

        if ($filterObject->OrderBy === null && isset($this->_options_2['default_sorting_value'])) {
            $filterObject->OrderBy = $this->_options_2['default_sorting_value'];
        }

        if($filterObject->Ascending === null && isset($this->_options_2['default_sorting_order'])) {
            $filterObject->Ascending = $this->_options_2['default_sorting_order'] === 'Ascending' ? 'true' : 'false';
        }

        $filterObject->Start = $start;
        $filterObject->Limit = $limit;

        $filterObject->HideSoldVehicles = isset($this->_options_2['hide_sold_vehicles']) && $this->_options_2['hide_sold_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideLeasingVehicles = isset($this->_options_2['hide_leasing_vehicles']) && $this->_options_2['hide_leasing_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideFlexLeasingVehicles = isset($this->_options_2['hide_flexleasing_vehicles']) && $this->_options_2['hide_flexleasing_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideWarehousesaleVehicles = isset($this->_options_2['hide_warehousesale_vehicles']) && $this->_options_2['hide_warehousesale_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideCarLiteDealerLabelVehicles = isset($this->_options_2['hide_carlite_dealer_label_vehicles']) && $this->_options_2['hide_carlite_dealer_label_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideRentalVehicles = isset($this->_options_2['hide_rental_vehicles']) && $this->_options_2['hide_rental_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideUpcomingVehicles = isset($this->_options_2['hide_upcoming_vehicles']) && $this->_options_2['hide_upcoming_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideWholesaleVehicles = isset($this->_options_2['hide_wholesale_vehicles']) && $this->_options_2['hide_wholesale_vehicles'] === 'on' ? 'true' : null;
		//jlk
        $filterObject->HideOnlyWholesaleVehicles = isset($this->_options_2['hide_only_wholesale_vehicles']) && $this->_options_2['hide_only_wholesale_vehicles'] === 'on' ? 'true' : null;
        $filterObject->ShowOnlyWholesaleVehicles = isset($this->_options_2['show_only_wholesale_vehicles']) && $this->_options_2['show_only_wholesale_vehicles'] === 'on' ? 'true' : null;		
        $filterObject->HideTrailerVehicles = isset($this->_options_2['hide_trailer_vehicles']) && $this->_options_2['hide_trailer_vehicles'] === 'on' ? 'true' : null;
		$filterObject->HideClassicVehicles = isset($this->_options_2['hide_classic_vehicles']) && $this->_options_2['hide_classic_vehicles'] === 'on' ? 'true' : null;
		$filterObject->HideTractorVehicles = isset($this->_options_2['hide_tractor_vehicles']) && $this->_options_2['hide_tractor_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideCommissionVehicles = isset($this->_options_2['hide_commission_vehicles']) && $this->_options_2['hide_commission_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideExportVehicles = isset($this->_options_2['hide_export_vehicles']) && $this->_options_2['hide_export_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideByTypeCar = isset($this->_options_2['hide_typecar_vehicles']) && $this->_options_2['hide_typecar_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideByTypeVan = isset($this->_options_2['hide_typevan_vehicles']) && $this->_options_2['hide_typevan_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideByTypeMotorcycle = isset($this->_options_2['hide_typemotorcycle_vehicles']) && $this->_options_2['hide_typemotorcycle_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideByTypeTruck = isset($this->_options_2['hide_typetruck_vehicles']) && $this->_options_2['hide_typetruck_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideByTypeBus = isset($this->_options_2['hide_typebus_vehicles']) && $this->_options_2['hide_typebus_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideBrandNewVehicles = isset($this->_options_2['hide_brandnew_vehicles']) && $this->_options_2['hide_brandnew_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideADVehicles = isset($this->_options_2['hide_ad_vehicles']) && $this->_options_2['hide_ad_vehicles'] === 'on' ? 'true' : null;
        $filterObject->HideBIVehicles = isset($this->_options_2['hide_bi_vehicles']) && $this->_options_2['hide_bi_vehicles'] === 'on' ? 'true' : null;
		//jlk
		$filterObject->HideInternalVehiclesBilInfo = isset($this->_options_2['hide_internal_vehicles_bilinfo']) && $this->_options_2['hide_internal_vehicles_bilinfo'] === 'on' ? 'true' : null;

        if(isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] !== '-1')
        {
            $filterObject->PriceTypes = array($this->_options_2['bdt_pricetypes']);
        }
		//jlk
        if(isset($this->_options_2['bdt_propellanttypes']) && $this->_options_2['bdt_propellanttypes'] !== '-1')
        {
            //$filterObject->PropellantTypes = array($this->_options_2['bdt_propellanttypes']);
			$filterObject->Propellants = array($this->_options_2['bdt_propellanttypes']);
        }

        $vehicleFeed = $this->biltorvetAPI->GetVehicles($filterObject);
        $orderByValues = $this->biltorvetAPI->GetOrderByValues();
    } catch(Exception $e) {
        die($e->getMessage());
    }

    $amountOfPages = ceil($vehicleFeed->totalResults / $limit);

    $customVehicleTypesFilterHasValue = $filterObject->CustomVehicleTypes != null ? $filterObject->CustomVehicleTypes[0] : "";

?>
    <div class="bdt" id="bdt_vehicle_search_results">
        <div id="vehicle_search_results" class="vehicle_search_results hide-bdt" data-totalResults="<?= $vehicleFeed->totalResults ?>">
            <div class="row resultsTitle">
                <div class="col-md-6">
                    <h4>
                        <?php printf(__('Your search returned <span class="bdt_color">%d cars</span>', 'biltorvet-dealer-tools'), $vehicleFeed->totalResults); ?>
                    </h4>
                </div>
                <div class="col-md-6 searchFilter">
                    <div class="row">
                        <div class="col">
                            <select class="results_order_by" name="orderBy" style="">
                                <option value=""><?php _e('- Order by -', 'biltorvet-dealer-tools'); ?></option>
                                <?php
                                foreach($orderByValues as $orderBy) : ?>
                                    <option value="<?php echo $orderBy; ?>"
                                        <?php if (isset($filterObject->OrderBy) && $filterObject->OrderBy === $orderBy) : ?>
                                            selected="selected"
                                        <?php endif; ?>
                                    ><?= _e($orderBy, 'biltorvet-dealer-tools'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <select name="ascDesc" style="">
                                <option value="desc"<?php echo $filterObject->Ascending !== 'true' || isset($this->_options_2['default_sorting_order']) && isset($this->_options_2['default_sorting_order']) === "Descending" ? ' selected="selected"' : '';  ?>><?php _e('Descending', 'biltorvet-dealer-tools'); ?></option>
                                <option value="asc"<?php echo $filterObject->Ascending === 'true' || isset($this->_options_2['default_sorting_order']) && isset($this->_options_2['default_sorting_order']) === "Ascending" ? ' selected="selected"' : '';  ?>><?php _e('Ascending', 'biltorvet-dealer-tools'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="lds-ring-paging d-done" style="display: none; opacity: 0;"><div></div><div></div><div></div><div></div></div>
                <div class="clearfix"></div>
            </div>
            <div class="results">
                <div id="vehicle-row" class="row vehicle-row">
                    <?php
                    foreach($vehicleFeed->vehicles as $oVehicle)
                    {
                        $link = $bdt_root_url . '/' . $oVehicle->uri;

                        // @TODO: Refactor.
                        // For new we convert the old vehicle object to the new, so it works with the new templates
                        // PLUGIN_ROOT refers to the v2 root.

                        /** @var Vehicle $vehicle */
                        $vehicle = VehicleFactory::create(json_decode(json_encode($oVehicle), true));
                        $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
                        $basePage = $bdt_root_url;
                        require PLUGIN_ROOT . 'templates/partials/_vehicleCard.php';
                    }
                    ?>
                </div>
            </div>
            <div class="paging">
                <?php
                $buttonCurrentPage = $currentPage;

                $newEnd = $currentPage * $limit;

                if($buttonCurrentPage < $amountOfPages)
                {
                    echo '<button class="paging-button et_pb_button bdt_bgcolor" id="paging-button" data-current-page="' . $buttonCurrentPage . '" data-amount-of-pages="' . $amountOfPages . '" data-end="' . $newEnd .'" data-limit="' . $limit .'">Indlæs flere...</button>';
                }
                ?>
                <div class="lds-ring-paging d-done" style="display: none; opacity: 0;"><div></div><div></div><div></div><div></div></div>
            </div>
        </div>
    </div>