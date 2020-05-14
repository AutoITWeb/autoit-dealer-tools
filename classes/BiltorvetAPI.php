<?php
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class BiltorvetAPI {
        private $endpoint = 'https://api-v1.autoit.dk';
//        private $endpoint = 'http://localhost:53871';
        private $apiKey;
        private $vehicleResultsPageLimit = 30;
        private $errLogFile;
        private $_makes;

        public function __construct($apiKey = null)
        {
            $this->errLogFile = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'log'. DIRECTORY_SEPARATOR .'errors.log';
            $this->apiKey = $apiKey;
        }

        public function GetFilterOptions($filter = null)
        {
            return $this->Request('/vehicle/filteroptions', (isset($filter) && $filter !== null ? array('filter' => json_encode($filter)) : null));
        }

        public function GetMakes()
        {
            return $this->Request('/vehicle/make');
        }

        public function GetModels($make = null)
        {
            return $this->Request('/vehicle/make' . (isset($make) && trim($make) !== '' ? '/' . TextUtils::Sanitize($make) : '') );
        }

        public function GetPropellantTypes() {
            $propellantsRaw = $this->Request('/vehicle/propellant');
            $propellants = array();
            foreach($propellantsRaw as $propellant)
            {
                $propellants[$propellant->key] = $propellant->value;
            }
            return $propellants;
        }

        public function GetTypes()
        {
            return $this->Request('/vehicle/type');
        }

        public function GetVehicle($id)
        {
            return $this->Request('/vehicle/detail/' . TextUtils::Sanitize($id));
        }

        public function GetVehicleTotalCount($filter)
        {
            return $this->Request('/vehicle/count', isset($filter) && $filter !== null ? array('filter' => json_encode($filter)) : null);
        }

        public function GetVehicles($filter)
        {
            if($filter->Limit == null)
            {
                $filter->Limit = $this->vehicleResultsPageLimit;
            }
            
            return $this->Request('/vehicle', array('filter' => json_encode($filter)));
        }

        public function AutodesktopSendLead($lead, $emailReciept = false) 
        {   
            if(!isset($lead))
            {
                throw new Exception("BT API: No lead specified");
            }
            
            return $this->Request('/autodesktop/sendlead', array('leadInput' => json_encode($lead), 'emailReciept' => $emailReciept === true ? 'true' : 'false'), 'POST');
        }

        public function GetVehicleResultsPageLimit()
        {
            return $this->vehicleResultsPageLimit;
        }

        public function GetOrderByValues()
        {
            return $this->Request('/vehicle/orderbyvalues');
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

        public function GetRecommendedVehicles($vehicleId, $amount)
        {
            return $this->Request('/vehicle/recommended' . (isset($vehicleId) && $vehicleId !== null ? '/' . TextUtils::Sanitize($vehicleId) : ''), isset($amount) ? array('amount' => intval($amount)) : null);
        }

        public function GetFeaturedVehicles($amount, $vehicleType)
        {
            $return =  $this->Request('/vehicle/featured', isset($amount) ? array('amount' => intval($amount), 'vehicleType' => $vehicleType) : null);

            return $return;
        }

        public function GetProducts()
        {
            return $this->Request('/products');
        }

        private function Request($method, $query = null, $requestType = 'GET')
        {
            $requestStart = microtime(true);
            try{
                if($method === null || trim($method) === '')
                {
                    throw new Exception('BT API: No method specified.');
                }

                $data = false;
                $transientName = $method . (isset($query) ? md5(implode('_', $query)) : '');

                if($requestType === 'GET')
                {
                    $data = get_transient( $transientName );
                }
                
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
                            break;
                            case 404:
                                throw new Exception(__('Biltorvet API: method not found', 'biltorvet-dealer-tools'));
                            break;
                            default:
                                throw new Exception(sprintf( __('Biltorvet API: unexpected response code (%u)', 'biltorvet-dealer-tools'), intval($httpCode)));
                            break;
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
//                    if($requestType === 'GET')
//                    {
//                        set_transient( $transientName, $data, 3*60 ); // 3 minutes caching
//                    }
                }

                return $data;
            } catch(Exception $e) {
                $requestEnd = microtime(true);
                if(!is_dir(dirname($this->errLogFile)))
                {
                    mkdir(dirname($this->errLogFile), 0777);
                }
                error_log(date('Y-m-d H:i:s') . ' (Request took '. ($requestEnd - $requestStart) . 's)\r\n' . $e->getMessage() . "\r\nURL: " . $this->endpoint . $method . "\r\n\r\n", 3, $this->errLogFile);
            
                throw $e;
            }
        }
    }