<?php

    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class BiltorvetAPI {
//        private $endpoint = 'https://vehicle-api.autoitweb.dk'; // Prod
        private $endpoint = 'https://vehicle-api-dev.autoitweb.dk'; // Dev
//        private $endpoint = 'http://localhost:5085'; // Local
        private $apiKey;
        private $vehicleResultsPageLimit = 24;
        private $errLogFile;
        private $_makes;

        public function __construct($apiKey = null)
        {
            $this->errLogFile = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'log'. DIRECTORY_SEPARATOR .'errors.log';
            $this->apiKey = $apiKey;
        }

        public function GetKeyEndpointsForCachePreload()
        {
            $endpoints = array(
                $this->endpoint . '/v2/vehicle' . '?a=' . $this->apiKey,
                $this->endpoint . '/v1/vehicle/filteroptions' . '?a=' . $this->apiKey,
                $this->endpoint . '/v1/vehicle/orderbyvalues' . '?a=' . $this->apiKey,
                $this->endpoint . '/v1/vehicle/count' . '?a=' . $this->apiKey,
                $this->endpoint . '/v1/companies' . '?a=' . $this->apiKey,
                $this->endpoint . '/v1/products' . '?a=' . $this->apiKey,
            );

            return $endpoints;
        }

        public function GetFilterOptions($filter = null)
        {
            return $this->Request('/v1/vehicle/filteroptions', (isset($filter) && $filter !== null ? array('filter' => json_encode($filter)) : null));
        }

        public function GetMakes()
        {
            return $this->Request('/v1/vehicle/make');
        }

        public function GetBodyTypes()
        {
            return $this->Request('/v1/vehicle/bodytypes');
        }

        public function GetCompanies()
        {
            return $this->Request('/v1/companies');
        }

        public function GetModels($make = null)
        {
            return $this->Request('/v1/vehicle/make' . (isset($make) && trim($make) !== '' ? '/' . TextUtils::Sanitize($make) : '') );
        }

        public function GetPropellantTypes() {
            $propellantsRaw = $this->Request('/v1/vehicle/propellant');
            $propellants = array();
            foreach($propellantsRaw as $propellant)
            {
                $propellants[$propellant->key] = $propellant->value;
            }
            return $propellants;
        }

        public function GetTypes()
        {
            return $this->Request('/v1/vehicle/type');
        }

        public function GetVehicle($id)
        {
            return $this->Request('/v2/vehicle/detail/' . TextUtils::Sanitize($id));
        }

        public function SendInfluxDbVehicleData($id)
        {
            return $this->Request('/v1/influxdb/vehicledetail/' . TextUtils::Sanitize($id));
        }

        public function GetBiltorvetBmsDealerInfo()
        {
            return $this->Request('/v1/bms/company');
        }

        public function GetVehicleTotalCount($filter)
        {
            return $this->Request('/v1/vehicle/count', isset($filter) && $filter !== null ? array('filter' => json_encode($filter)) : null);
        }

        public function GetVehicles($filter)
        {
            if($filter->Limit == null)
            {
                $filter->Limit = $this->vehicleResultsPageLimit;
            }

            return $this->Request('/v2/vehicle', array('filter' => json_encode($filter)));
        }

        public function GetVehiclesQuickSearch($filter)
        {
            return $this->Request('/v1/vehicle/quicksearch', array('filter' => json_encode($filter)));
        }

        public function AutodesktopSendLead($lead, $emailReciept = false)
        {
            if(!isset($lead))
            {
                throw new Exception("BT API: No lead specified");
            }

            return $this->Request('/v1/autodesktop/sendlead', array('leadInput' => json_encode($lead), 'emailReciept' => $emailReciept === true ? 'true' : 'false'), 'POST');
        }

        // New simple lead creator!
        public function CreateSimpleLead($lead, $companyId)
        {
            $endpoint = $this->endpoint . '/v1/leadimporter/createsimplelead/' . $companyId . '?a=' . $this->apiKey;

            $body = wp_json_encode( $lead );

            $options = [
                'method' => 'POST',
                'body'        => $body,
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'timeout'     => 10,
                'data_format' => 'body',
            ];

            $response = wp_remote_post( $endpoint, $options );

            return $response;
        }

        public function CreateLead($lead, $companyId)
        {
            if(!isset($lead))
            {
                throw new Exception("BT API: No lead specified");
            }

            return $this->Request('/v1/leadimporter/createlead/' . $companyId, array('leadObject' => json_encode($lead)));
        }

        public function GetVehicleResultsPageLimit()
        {
            return $this->vehicleResultsPageLimit;
        }

        public function GetOrderByValues()
        {
            return $this->Request('/v1/vehicle/orderbyvalues');
        }

        public function GetPropertyValue($vehicle, $propertyName, $raw = false)
        {
            if(isset($vehicle) && $vehicle !== null)
            {
                if(property_exists($vehicle, $propertyName))
                {
                    $value = $vehicle->{lcfirst($propertyName)};
                    if(trim($value) === '')
                    {
                        return null;
                    }
                    return $value;
                }
                foreach($vehicle->properties as $property) {
                    if(strtolower($property->id) == strtolower($propertyName))
                    {
                        return $raw === false && isset($property->valueFormatted) && trim($property->valueFormatted) !== '' ? $property->valueFormatted : $property->value;
                    }
                }
            }
            return null;
        }

        public function GetMakeFromSlug($slug)
        {
            foreach($this->GetMakes() as $make)
            {
                if(strtolower(TextUtils::Sanitize($make->name)) == strtolower(TextUtils::Sanitize($slug)))
                {
                    return $make->id;
                }
            }
        }

        public function GetBodyTypeFromSlug($slug)
        {
            foreach($this->GetBodyTypes() as $bodyType)
            {
                if(strtolower(TextUtils::Sanitize($bodyType->name)) == strtolower(TextUtils::Sanitize($slug)))
                {
                    return $bodyType->id;
                }
            }
        }

        public function GetRecommendedVehicles($vehicleId, $amount)
        {
            return $this->Request('/v2/vehicle/recommended' . (isset($vehicleId) && $vehicleId !== null ? '/' . TextUtils::Sanitize($vehicleId) : ''), isset($amount) ? array('amount' => intval($amount)) : null);
        }

        public function GetFeaturedVehicles($amount, $vehicleType)
        {
            $return =  $this->Request('/v2/vehicle/featured', isset($amount) ? array('amount' => intval($amount), 'vehicleType' => $vehicleType) : null);

            return $return;
        }

        public function GetProducts()
        {
            return $this->Request('/v1/products');
        }

        private function Request($method, $query = null, $requestType = 'GET')
        {
            try{
                if($method === null || trim($method) === '')
                {
                    throw new Exception('BT API: No method specified.');
                }

                $data = false;
                $transientName = $method . (isset($query) ? json_encode($query) : '');

/*                if($requestType === 'GET' && !strpos($method, 'influxdb') && !strpos($method, 'createlead'))
                {
                    $data = get_transient( $transientName );
                }*/

                //delete_transient($transientName);

                if( false === $data ) {
                    $ch = curl_init($this->endpoint . $method . '?' . ($requestType === 'GET' && isset($query) ? http_build_query($query) . '&'  : '') .'a=' . $this->apiKey );

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                    if($requestType === 'POST')
                    {
                        curl_setopt($ch, CURLOPT_POST, true);
                        if(isset($query))
                        {
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
                        }
                    }
                    $body = curl_exec($ch);

                    $curl_errno = curl_errno($ch);

                    if($curl_errno > 0)
                    {
                        throw new Exception(sprintf( __('Biltorvet API: can not connect to the server (%u)', 'biltorvet-dealer-tools'), intval($curl_errno)));
                    }
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE );
                    curl_close($ch);

                    if($httpCode !== 200)
                    {
                        switch($httpCode):
                            case 401:
                                throw new Exception(__('Biltorvet API: not authorized','biltorvet-dealer-tools'));
                            case 404:
                                throw new Exception(__('Biltorvet API: method not found', 'biltorvet-dealer-tools'));
                            default:
                                throw new Exception(sprintf( __('Biltorvet API: unexpected response code (%u)', 'biltorvet-dealer-tools'), intval($httpCode)));
                                //throw new Exception(sprintf( __('Biltorvet API: unexpected response code (%u), %s', 'biltorvet-dealer-tools'), intval($httpCode), $this->endpoint . $method . '?a=' . $this->apiKey . '&' . http_build_query($query)));
                        endswitch;
                    }

                    $response = json_decode($body);

                    if(!property_exists($response, 'status'))
                    {
                        throw new Exception(__('Malformed API response', 'biltorvet-dealer-tools'));
                    }

                    if(intval($response->status) !== 1)
                    {
                        if(property_exists($response, 'errors' ))
                        {
                            throw new Exception( sprintf(__('Biltorvet API returned following error(s):\r\n%s', 'biltorvet-dealer-tools'), implode(',\r\n', $response->errors)));
                        }

                        throw new Exception(__('Biltorvet API: Unexpected API error', 'biltorvet-dealer-tools'));
                    }

                    if(!property_exists($response, 'result'))
                    {
                        throw new Exception(__('Biltorvet API: Response missing', 'biltorvet-dealer-tools'));
                    }

                    $data = $response->result;

                    //set_transient( $transientName, $data, 300);
                }

                return $data;
            } catch(Exception $e) {
                $requestEnd = microtime(true);

                throw $e;
            }
        }
    }
