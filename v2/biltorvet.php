<?php
/**
 * Plugin Name:       Biltorvet
 * Plugin URI:        http://www.biltorvet.as/hjemmesider
 * Description:       Tools providing connection to AutoDesktop, and other Biltorvet services.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Biltorvet A/S
 * Author URI:        http://www.biltorvet.as
 * License:           Proprietary
 * License URI:       https://www.biltorvet.as/media/1385/betingelser-biltorvetas.pdf
 * Text Domain:       biltorvet
 * Domain Path:       /languages
 *
 * Biltorvet Dealer Tools is free software: you can redistribute it, but NOT modify it.
 * Changes to the code or attempts on rebranding are NOT allowed.
 * Biltorvet Dealer Tools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use Biltorvet\Controller\ApiController;
use Biltorvet\Controller\PluginController;

define( 'PLUGIN_ROOT', plugin_dir_path( __FILE__ ) );
define('LABEL_SOLD', 5);
define('LABEL_NEW', 11);
define('LABEL_FEATURED', 10);
define('LABEL_LEASING', 12);
define('LABEL_WAREHOUSESALE', 26);
define('LABEL_FLEXLEASING', 198);
define('LABEL_EXPORT', 382);
define('LABEL_UPCOMING', 4);
define('LABEL_RENTAL', 2);
define('LABEL_COMMISION', 27);
define('LABEL_ENGROS', 9);

// @TODO: Refactor.
//  As the data for price calculations are pretty messy we try to map it more logically before working with it.
define('RELATED_PRICE_PROPERTY_KEYS', [
        'VAT', // Bruges til eksl momslabel på price
        'LeasingBusiness', // bool for om det er erhverv leasing eller ej. skal momsens trækkes fra LeasingMonthlyPayment eller ej. afgør leasing moms label
        'LeasingMonthlyPaymentTotal', // DO NOT USE
        'LeasingMonthlyPaymentVAT', // ONLY moms
        'LeasingMonthlyPayment', // Use this for leasing price. HVIS LeasingBusiness == true leasing price er LeasingMonthlyPayment - LeasingMonthlyPaymentVAT
        'Price', // Vare type V inkl moms, v+ er ekskl moms bliver beregenet i API
        'FinancingMonthlyPrice', // Use this for financing price
    ]);

require __DIR__ . '/vendor/autoload.php';

$plugin = new PluginController();