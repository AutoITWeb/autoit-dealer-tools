<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class BiltorvetAPI {
    private string $endpoint = 'https://vehicle-api.autoitweb.dk'; // Prod
    // private string $endpoint = 'https://vehicle-api-dev.autoitweb.dk'; // Dev
    // private string $endpoint = 'http://localhost:5085'; // Local

    private ?string $apiKey;
    private int $vehicleResultsPageLimit = 24;
    private string $errLogFile;
    private $_makes;

    public function __construct(?string $apiKey = null)
    {
        $this->errLogFile = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'errors.log';
        $this->apiKey = $apiKey;
    }

    public function GetKeyEndpointsForCachePreload()
    {
        return [
            $this->endpoint . '/v2/vehicle' . '?a=' . $this->apiKey,
            $this->endpoint . '/v1/vehicle/filteroptions' . '?a=' . $this->apiKey,
            $this->endpoint . '/v1/vehicle/orderbyvalues' . '?a=' . $this->apiKey,
            $this->endpoint . '/v1/vehicle/count' . '?a=' . $this->apiKey,
            $this->endpoint . '/v1/companies' . '?a=' . $this->apiKey,
            $this->endpoint . '/v1/products' . '?a=' . $this->apiKey,
        ];
    }

    public function GetFilterOptions($filter = null)
    {
        return $this->Request('/v1/vehicle/filteroptions', ($filter !== null ? ['filter' => json_encode($filter)] : null));
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
        return $this->Request('/v1/vehicle/make' . ($make !== null && trim((string)$make) !== '' ? '/' . TextUtils::Sanitize($make) : ''));
    }

    public function GetPropellantTypes()
    {
        $propellantsRaw = $this->Request('/v1/vehicle/propellant');
        $propellants = [];
        foreach ($propellantsRaw as $propellant) {
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
        return $this->Request('/v1/vehicle/count', $filter !== null ? ['filter' => json_encode($filter)] : null);
    }

    public function GetVehicles($filter)
    {
        if ($filter->Limit == null) {
            $filter->Limit = $this->vehicleResultsPageLimit;
        }

        return $this->Request('/v2/vehicle', ['filter' => json_encode($filter)]);
    }

    public function GetVehiclesQuickSearch($filter)
    {
        return $this->Request('/v1/vehicle/quicksearch', ['filter' => json_encode($filter)]);
    }

    public function AutodesktopSendLead($lead, $emailReciept = false)
    {
        if (!isset($lead)) {
            throw new Exception("BT API: No lead specified");
        }

        return $this->Request(
            '/v1/autodesktop/sendlead',
            ['leadInput' => json_encode($lead), 'emailReciept' => $emailReciept === true ? 'true' : 'false'],
            'POST'
        );
    }

    // New simple lead creator!
    public function CreateSimpleLead($lead, $companyId)
    {
        $endpoint = $this->endpoint . '/v1/leadimporter/createsimplelead/' . $companyId . '?a=' . $this->apiKey;
        $body = wp_json_encode($lead);

        $options = [
            'method'      => 'POST',
            'body'        => $body,
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
            'timeout'     => 10,
            'data_format' => 'body',
        ];

        return wp_remote_post($endpoint, $options);
    }

    public function CreateLead($lead, $companyId)
    {
        if (!isset($lead)) {
            throw new Exception("BT API: No lead specified");
        }

        return $this->Request('/v1/leadimporter/createlead/' . $companyId, ['leadObject' => json_encode($lead)]);
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
        if ($vehicle !== null) {
            // Direct property
            if (property_exists($vehicle, $propertyName)) {
                $value = $vehicle->{lcfirst($propertyName)};
                if (trim((string)$value) === '') {
                    return null;
                }
                return $value;
            }

            // Properties array
            if (isset($vehicle->properties) && is_iterable($vehicle->properties)) {
                foreach ($vehicle->properties as $property) {
                    if (strtolower($property->id) == strtolower($propertyName)) {
                        return $raw === false && isset($property->valueFormatted) && trim((string)$property->valueFormatted) !== ''
                            ? $property->valueFormatted
                            : $property->value;
                    }
                }
            }
        }
        return null;
    }

    public function GetMakeFromSlug($slug)
    {
        foreach ($this->GetMakes() as $make) {
            if (strtolower(TextUtils::Sanitize($make->name)) == strtolower(TextUtils::Sanitize($slug))) {
                return $make->id;
            }
        }
        return null;
    }

    public function GetBodyTypeFromSlug($slug)
    {
        foreach ($this->GetBodyTypes() as $bodyType) {
            if (strtolower(TextUtils::Sanitize($bodyType->name)) == strtolower(TextUtils::Sanitize($slug))) {
                return $bodyType->id;
            }
        }
        return null;
    }

    public function GetRecommendedVehicles($vehicleId, $amount, $hideInternalVehiclesBilinfo = false, $hideOnlyWholesaleVehicles = false, $showOnlyWholesaleVehicles = false)
    {
        return $this->Request(
            '/v2/vehicle/recommended' . ($vehicleId !== null ? '/' . TextUtils::Sanitize($vehicleId) : ''),
            isset($amount) ? [
                'amount' => intval($amount),
                'hideInternalVehiclesBilinfo' => $hideInternalVehiclesBilinfo,
                'hideOnlyWholesaleVehicles'   => $hideOnlyWholesaleVehicles,
                'showOnlyWholesaleVehicles'   => $showOnlyWholesaleVehicles
            ] : null
        );
    }

    public function GetFeaturedVehicles($amount, $vehicleType, $hideInternalVehiclesBilinfo = false, $hideOnlyWholesaleVehicles = false)
    {
        return $this->Request(
            '/v2/vehicle/featured',
            isset($amount) ? [
                'amount' => intval($amount),
                'vehicleType' => $vehicleType,
                'hideInternalVehiclesBilinfo' => $hideInternalVehiclesBilinfo,
                'hideOnlyWholesaleVehicles'   => $hideOnlyWholesaleVehicles
            ] : null
        );
    }

    public function GetProducts()
    {
        return $this->Request('/v1/products');
    }

    private function Request($method, $query = null, $requestType = 'GET')
    {
        try {
            if ($method === null || trim((string)$method) === '') {
                throw new Exception('BT API: No method specified.');
            }

            $data = false;
            $ch = curl_init($this->endpoint . $method . '?' . ($requestType === 'GET' && isset($query) ? http_build_query($query) . '&' : '') . 'a=' . $this->apiKey);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            if ($requestType === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                if (isset($query)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
                }
            }

            $body = curl_exec($ch);
            $curl_errno = curl_errno($ch);

            if ($curl_errno > 0) {
                throw new Exception(sprintf(__('Biltorvet API: can not connect to the server (%u)', 'biltorvet-dealer-tools'), intval($curl_errno)));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                switch ($httpCode) {
                    case 401:
                        throw new Exception(__('Biltorvet API: not authorized', 'biltorvet-dealer-tools'));
                    case 404:
                        throw new Exception(__('Biltorvet API: method not found', 'biltorvet-dealer-tools'));
                    default:
                        throw new Exception(sprintf(__('Biltorvet API: unexpected response code (%u)', 'biltorvet-dealer-tools'), intval($httpCode)));
                }
            }

            $response = json_decode($body);

            if (!is_object($response) || !property_exists($response, 'status')) {
                throw new Exception(__('Malformed API response', 'biltorvet-dealer-tools'));
            }

            if (intval($response->status) !== 1) {
                if (property_exists($response, 'errors')) {
                    throw new Exception(sprintf(__('Biltorvet API returned following error(s): %s', 'biltorvet-dealer-tools'), implode(', ', $response->errors)));
                }
                throw new Exception(__('Biltorvet API: Unexpected API error', 'biltorvet-dealer-tools'));
            }

            if (!property_exists($response, 'result')) {
                throw new Exception(__('Biltorvet API: Response missing', 'biltorvet-dealer-tools'));
            }

            $data = $response->result;

            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
