<?php


namespace Biltorvet\Factory;

use Biltorvet\Helper\DataHelper;
use Biltorvet\Helper\WordpressHelper;
use Biltorvet\Model\Property;
use Biltorvet\Model\Vehicle;
use Biltorvet\Model\VehicleLead;

class VehicleLeadFactory
{
    /**
     * @TODO: Refactor
     *
     * @param  Vehicle $vehicle
     * @param  array   $args
     * @param  array   $queryParams
     * @return VehicleLead
     */
    public static function create(Vehicle $vehicle, array $args, array $queryParams)
    {

        /**
*
         *
 * @var Property[] $properties
*/
        $properties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
        $lead = new VehicleLead();

        $lead
            ->setVin($properties['VIN']->getValue())
            ->setModel($vehicle->getModel())
            ->setFirstRegitrationDate(
                $properties['FirstRegYear']->getValue() ? date(
                    'Y-m-d H:i:s',
                    strtotime($properties['FirstRegYear']->getValue())
                ) : null
            )
            ->setRequestedTestdriveDateTime(null)
            ->setNumberPlate($properties['RegistrationNumber']->getValue())
            ->setMessage(filter_var($args['message'], FILTER_SANITIZE_STRING))
            ->setType($vehicle->getType())
            ->setEmail(WordpressHelper::getReplyTo($args))
            ->setActivityType(filter_var($queryParams['bdt_actiontype'], FILTER_SANITIZE_STRING))
            ->setCompanyId($vehicle->getCompanyId());

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'bdtname') !== false) {
                $lead->setName($value);
            }
            if (strpos($key, 'bdtpostalcode') !== false) {
                $lead->setPostalCode($value);
            }
            if (strpos($key, 'bdtcity') !== false) {
                $lead->setCity($value);
            }
            if (strpos($key, 'bdtphone') !== false) {
                $lead->setCellPhoneNumber($value);
            }
            if (strpos($key, 'bdtrequestedtestdrivedatetime') !== false) {
                $lead->setRequestedTestdriveDateTime(date('Y-m-d H:i:s', strtotime($value)));
            }

            if (strpos($key, 'bdtrequestedday') !== false) {
                $day = intval($value);
            }
            if (strpos($key, 'bdtrequestedtime') !== false) {
                $time = $value;
            }
        }

        if (isset($day)) {
            $lead->setRequestedTestdriveDateTime(date('Y-m-d H:i:s', strtotime('+' . $day . ' day')));
        }

        if (isset($day) && isset($time)) {
            if (strlen($time) === 4) {
                $time = '0' . $time;
            }
            $lead->setRequestedTestdriveDateTime(
                date(
                    'Y-m-d',
                    strtotime($lead->getRequestedTestdriveDateTime())
                ) . ' ' . $time . ':00'
            );
        }

        return $lead;
    }
}
