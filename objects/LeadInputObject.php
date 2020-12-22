<?php
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    class LeadInputObject {
        public $Name;
        public $PostalCode; // int
        public $City;
        public $CellPhoneNumber; // string
        public $Email; // string
        public $Type; // vehicle type
        public $Model; // vehicle make, model and variant
        public $FirstRegistrationDate; // DateTime
        public $RequestedTestdriveDateTime; // DateTime
        public $body; // Additional info
        public $ActivityType; // ActivityType enum
        public $CompanyId;
    }