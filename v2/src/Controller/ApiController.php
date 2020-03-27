<?php

namespace Biltorvet\Controller;

use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Helper\WordpressHelper;
use Biltorvet\Model\ApiResponse;
use Biltorvet\Model\SearchFilter;
use Biltorvet\Model\Vehicle;
use Biltorvet\Model\VehicleLead;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Handles requests to Biltorvet REST api
 *
 * Class ApiController
 *
 * @package Biltorvet\Controller
 */
class ApiController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->apiKey = WordpressHelper::getApiKey(1);
    }

    public function getVehicles($filter)
    {
        try {
            return $this->requestApi('vehicle', ['filter' => json_encode($filter)]);
        } catch (Exception $e) {
        }
    }

    /**
     * @param string $vehicleId
     * @return array|Vehicle
     */
    public function getVehicleDetails(string $vehicleId)
    {
        try {
            return $this->requestApi(
                'vehicle/detail/' . filter_var($vehicleId, FILTER_SANITIZE_STRING),
                [],
                'vehicleDetail'
            );
        } catch (Exception $e) {
        }
    }

    public function getOrderByValues()
    {
        try {
            return $this->requestApi('/vehicle/orderbyvalues', [], 'orderbyValues');
        } catch (Exception $e) {
        }
    }

    /**
     * @param  VehicleLead $lead
     * @return array|Vehicle
     * @throws Exception
     */
    public function sendLead(VehicleLead $lead)
    {
        $respone = $this->requestApi(
            'autodesktop/sendlead',
            [
            'leadInput' => DataHelper::createLeadObject($lead),
            'emailReciept' => WordpressHelper::getOption(1,'adt_email_receipt') === 'ja'
            ],
            'sendLead',
            'POST'
        );

        return $respone;
    }

    /**
     * @return array|Vehicle
     * @throws Exception
     */
    public function getCompanyProducts() {
        return $this->requestApi('/products', [], 'products');
    }


    /**
     * @param  string $resource
     * @param  array  $params
     * @param  string $response_type
     * @param  string $request_method
     * @return array|Vehicle
     * @throws Exception
     */
    public function requestApi(
        string $resource,
        array $params = [],
        $response_type = 'vehicles',
        string $request_method = "GET"
    ) {
        try {
            /** @var TYPE_NAME $response_type */
            return $this->handleResponse(
                $this->client->request(
                    $request_method,
                    $_ENV['API_URL'] . '/' . $resource,
                    ['query' => array_merge($params, ['a' => $this->apiKey])]
                ),
                $response_type
            );
        } catch (GuzzleException $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * @param  Response $response
     * @return mixed
     * @throws Exception
     */
    private function handleResponse(Response $response, string $response_type)
    {

        if ($response->getStatusCode() === 200) {
            /**
*
             *
 * @var ApiResponse $apiResponse
*/
            try {
                $apiResponse = $this->serializer->deserialize(
                    $response->getBody()->getContents(),
                    ApiResponse::class,
                    'json'
                );
            } catch (Exception $e) {
                try {
                    $apiResponse = json_decode($response->getBody()->getContents());
                    $response_type = 'default';
                } catch (Exception $e) {
                    throw $e;
                }

                return $e->getMessage();
            }

            switch ($response_type) {
                case 'vehicles':
                    return DataHelper::getVehiclesFromApiResponse($apiResponse);
                    break;
                case 'vehicleDetail':
                    return VehicleFactory::create($apiResponse->getResult());
                    break;
                case 'sendLead':
                    return $apiResponse->getStatus();
                    break;
                case 'orderbyValues':
                    return $apiResponse->getResult();
                    break;
                default:
                    return $apiResponse;
            }
        } else {
            throw(new Exception('Invalid status code: ' . $response->getStatusCode()));
        }
    }
}
