<?php
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    // Equivalent of WidgetTypeEnum in Biltorvets API.
    $WidgetType = array(
        'Consent', // Samtykke widget
        'ExchangePrice', // Byttepris widget
        'AutoDesktopLeads', // Book PrøvekørselsWidget
        'Santander', // Santander financing
        'ServiceOnline'
    );