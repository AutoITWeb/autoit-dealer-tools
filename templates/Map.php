<?php
/**
 * A template that shows a map
 *
 *
 * @author 		AutoIT A/S
 * @package 	AutoIT-Dealer-Tools
 * @version     1.0.0
 */
if (!defined('ABSPATH')) exit; // Security check

$zoomLevel = !empty($this->_options_4['bdt_zoom_level']) ? intval($this->_options_4['bdt_zoom_level']) : 17;
$setTileLayer = !empty($this->_options_4['bdt_tile_layer']) ? esc_url($this->_options_4['bdt_tile_layer']) : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
$markerColor = !empty($this->_options_4['bdt_marker_color']) ? strtolower($this->_options_4['bdt_marker_color']) : 'red';
$marker = !empty($this->_options_4['bdt_custom_marker']) ? esc_url($this->_options_4['bdt_custom_marker']) : plugin_dir_url(dirname(__FILE__)) . "includes/img/marker-icon-$markerColor.png";

$options = get_option('bdt_options');
$options_6 = get_option('bdt_options_6');

$companiesFeed = null;
$setView = "";

if (!empty($atts['detailspage']) && $atts['detailspage'] == 'true') {
    $vehicle = $this->currentVehicle->company->coordinates ?? null;
    $setView = $vehicle ? "{$vehicle->latitude},{$vehicle->longitude}" : "56.2639,9.5018"; // Default Denmark coords
    $zoomLevel = !empty($this->_options_4['bdt_zoom_level_detailspage']) ? intval($this->_options_4['bdt_zoom_level_detailspage']) : 17;
} else {
    $companiesFeed = $this->biltorvetAPI->GetCompanies();
    if (!empty($companiesFeed->companies)) {
        $setView = "{$companiesFeed->companies[0]->coordinates->latitude},{$companiesFeed->companies[0]->coordinates->longitude}";
    }
}
?>

<div id="map" style="height: 400px;"></div>

<style>
    .leaflet-container a {
        color: <?= esc_attr($options['primary_color'] ?? '#007bff'); ?>;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mapElement = document.querySelector("#map");
        
        if (!mapElement) return;

        let map = L.map(mapElement, {
            center: <?= json_encode(explode(',', $setView)); ?>,
            zoom: <?= intval($zoomLevel); ?>,
            gestureHandling: true
        });

        L.tileLayer(<?= json_encode($setTileLayer); ?>, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let coloredIcon = new L.Icon({
            iconUrl: <?= json_encode($marker); ?>,
            shadowUrl: <?= json_encode(plugin_dir_url(dirname(__FILE__)) . 'includes/img/marker-shadow.png'); ?>,
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        <?php if(empty($atts)) : ?>
            <?php foreach ($companiesFeed->companies as $company) : ?>
                <?php if (!empty($company->coordinates->latitude)) : ?>
                    L.marker([<?= json_encode($company->coordinates->latitude); ?>, <?= json_encode($company->coordinates->longitude); ?>], {icon: coloredIcon}).addTo(map)
                        .bindPopup("<b><?= esc_js($company->name); ?></b><br><?= esc_js($company->address); ?><br><?= esc_js($company->postNumber . ' ' . $company->city); ?>");
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($atts['detailspage']) && $atts['detailspage'] == 'true' && !empty($this->currentVehicle->company->coordinates->latitude)) : ?>
            L.marker([<?= json_encode($this->currentVehicle->company->coordinates->latitude); ?>, <?= json_encode($this->currentVehicle->company->coordinates->longitude); ?>], {icon: coloredIcon}).addTo(map)
                .bindPopup("<b><?= esc_js($this->currentVehicle->company->name); ?></b><br><?= esc_js($this->currentVehicle->company->address); ?>");
        <?php endif; ?>
    });
</script>