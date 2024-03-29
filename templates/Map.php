<?php
/**
 * A template that shows a map
 *
 *
 * @author 		AutoIT A/S
 * @package 	AutoIT-Dealer-Tools
 * @version     1.0.0
 */
if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

$companiesFeed = null;
$setView = "";

$zoomLevel = isset($this->_options_4['bdt_zoom_level']) && $this->_options_4['bdt_zoom_level'] != '' ? $this->_options_4['bdt_zoom_level'] : 17;
$setTileLayer = isset($this->_options_4['bdt_tile_layer']) && $this->_options_4['bdt_tile_layer']!= '' ? $this->_options_4['bdt_tile_layer'] : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
$markerColor = isset($this->_options_4['bdt_marker_color']) && $this->_options_4['bdt_marker_color'] != '' ? $this->_options_4['bdt_marker_color'] : 'Red';
$marker = isset($this->_options_4['bdt_custom_marker']) && $this->_options_4['bdt_custom_marker']!= '' ? $this->_options_4['bdt_custom_marker'] : plugin_dir_url( dirname( __FILE__ ) ) . 'includes/img/marker-icon-' . strtolower($markerColor) . '.png';

$options = get_option( 'bdt_options' );
$options_6 = get_option( 'bdt_options_6' );

// Globalmap not to be used on VehicleDetailsPage as it will only show the map data for the current vehicle
if(isset($atts['detailspage']) && $atts['detailspage'] == 'true') {
    $setView = $this->currentVehicle->company->coordinates->latitude . "," . $this->currentVehicle->company->coordinates->longitude;
    $zoomLevel = isset($this->_options_4['bdt_zoom_level_detailspage']) && $this->_options_4['bdt_zoom_level_detailspage'] != '' ? $this->_options_4['bdt_zoom_level_detailspage'] : 17;

} else {

    $companiesFeed = $this->biltorvetAPI->GetCompanies();
    $setView = $companiesFeed->companies[0]->coordinates->latitude . "," . $companiesFeed->companies[0]->coordinates->longitude;

    if($companiesFeed->totalResults >= 2) {
        $setView = isset($this->_options_4['bdt_set_view']) && $this->_options_4['bdt_set_view'] != '' ? $this->_options_4['bdt_set_view'] : $setView;
    }
}
?>

<script>
    const lazyInit = (element, fn) => {
        const observer = new IntersectionObserver((entries) => {
            if (entries.some(({isIntersecting}) => isIntersecting)) {
                observer.disconnect();
                fn();
            }
        });
        observer.observe(element);
    };
</script>


<div id="map"></div>

<style>
    .leaflet-container a {
        color: <?= $options['primary_color']; ?>;
    }
</style>

<script>

    const mapElement = document.querySelector("#map");

    lazyInit(mapElement, () => {
        var map = L.map('map', {
            center:[<?= $setView; ?>],
            zoom: <?= $zoomLevel; ?>,
            gestureHandling: true
        });

        map.invalidateSize();

        L.tileLayer(<?= "'" . $setTileLayer ."'"; ?>, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var coloredIcon = new L.Icon({
            iconUrl: '<?= $marker; ?>',
            shadowUrl: '<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/img/marker-shadow.png'; ?>',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

    // Globalmap markers
    <?php if(empty($atts)) : ?>
    <?php foreach ($companiesFeed->companies as $company) : ?>

    <?php if($company->coordinates->latitude != null) : ?>

        L.marker([<?= $company->coordinates->latitude; ?>, <?= $company->coordinates->longitude; ?>], {icon: coloredIcon}).addTo(map)
            .bindPopup(" <b><?= $company->name; ?></b> <br> <?= $company->address; ?> <br> <?= $company->postNumber; ?> <?= $company->city; ?> <br> <?= $company->phone != 0 ? '<a href=' . 'tel:+45' . $company->phone . '>' . '+45 ' . $company->phone . '</a>' : ""; ?> <?= isset($options_6['departments_google_directions_' . $company->id . '']) && !empty($options_6['departments_google_directions_' . $company->id . '']) ? '<br><a href=' . $options_6['departments_google_directions_' . $company->id . ''] . ' target=_blank>Rutevejledning</a>' : ''; ?> ")

    <?php endif; ?>

    <?php endforeach; ?>
    <?php endif; ?>

    // VehicleDetailsPage marker
    <?php if(isset($atts['detailspage']) && $atts['detailspage'] == 'true') : ?>
        L.marker([<?= $this->currentVehicle->company->coordinates->latitude; ?>, <?=  $this->currentVehicle->company->coordinates->longitude; ?>], {icon: coloredIcon}).addTo(map)
            .bindPopup(" <b><?=  $this->currentVehicle->company->name; ?></b> <br> <?=  $this->currentVehicle->company->address; ?> <br> <?=  $this->currentVehicle->company->postNumber; ?> <?=  $this->currentVehicle->company->city; ?> <br> <?=  $this->currentVehicle->company->phone != 0 ? '<a href=' . 'tel:+45' .  $this->currentVehicle->company->phone . '>' . '+45 ' .  $this->currentVehicle->company->phone . '</a>' : ""; ?> <?= isset($options_6['departments_google_directions_' . $this->currentVehicle->company->id . '']) && !empty($options_6['departments_google_directions_' . $this->currentVehicle->company->id . '']) ? '<br><a href=' . $options_6['departments_google_directions_' . $this->currentVehicle->company->id . ''] . ' target=_blank>Rutevejledning</a>' : ''; ?> ")

    <?php endif; ?>

    });
</script>

