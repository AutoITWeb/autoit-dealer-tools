<?php

    /*
     * Json to object paste.
     * All classes here represents the new lead importers lead object.
     */

    class NewLeadInputObject {
        public $description; //String
        public $subject; //String If null it will default to "Der er et ny lead fra CarLite"
        public $logoLink; // String
        public $pNumber; // String
        public $externalCreateDate; // datetime
        public $dueDate; // datetime
        public $externalId; // String
        public $externalSource; // String
        public $externalSourceType; //int
        public $prefix; // String
        public $internalInfoRequest; //InternalInfoRequest
        public $personRequest; //PersonRequest
        public $businessRequest; //BusinessRequest
        public $leadStatusRequest; //LeadStatusRequest
        public $contactInformationRequest; //ContactInformationRequest

        public function CreateLead(NewLeadInputObject $lead, $message, $email, $name, $phoneNumber, $address, $postalcode, $city, $externalId, $query_source): NewLeadInputObject
        {
            $this->description = $message;
            $lead->internalInfoRequest = new InternalInfoRequest();

            /*
             * ADT wants both a first- and lastname and we usually only operate with one name field.
             * If the name variable contains whitespace we split it into an array with two stings.
             * If not we add a dot as last to avoid errors when creating the lead.
             * I guess most people use their full name if there's only one name field in a contact form?
             */
            if(strpos($name, ' ') !== false) {

                $firstAndLastNameArray = $this->HandleName($name);

                $lead->personRequest->firstName = $firstAndLastNameArray[0];
                $lead->personRequest->lastName = $firstAndLastNameArray[1];

            } else {

                $lead->personRequest->firstName = $name;
                $lead->personRequest->lastName = ".";
            }

            if($externalId != null)
            {
                $lead->externalId = $externalId;
            }

            if($query_source != null)
            {
                $lead->externalSource = $query_source;
            }

            $lead->businessRequest = new BusinessRequest();
            $lead->leadStatusRequest = new LeadStatusRequest();
            $lead->contactInformationRequest->emailRequest->emailAddress = $email;
            $lead->contactInformationRequest->phoneRequest->number = $phoneNumber; // Is required if not set the API will handle it

            $lead->contactInformationRequest->addressRequest->street = $address !== '' ? $address : null;
            $lead->contactInformationRequest->addressRequest->city = $city !== '' ? $city : null;
            $lead->contactInformationRequest->addressRequest->zipCode = $postalcode !== '' ? $postalcode : null;

            return $lead;
        }

        public function HandleName(string $name): array
        {
            $nameArray = explode(' ', $name, 2);

            return $nameArray;
        }
    }

    class InternalInfoRequest {
        public $employeeId; //array( undefined )
        public $employeeEmail; //array( undefined )
        public $employeePhone; //array( undefined )
        public $dispatchCompanyId; //array( undefined )

    }

    class PersonRequest {
        public $firstName; //String
        public $lastName; //String

    }

    class BusinessRequest {
        public $accountName; //array( undefined )
        public $contactName; //array( undefined )
        public $cvrNo; //array( undefined )

    }

    class LeadStatusRequest {
        public $modified; //array( undefined )
        public $status; //String

    }

    class EmailRequest {
        public $emailAddress; //array( undefined )

    }

    class PhoneRequest {
        public $number; //String
        public $countryCode; //String

    }

    class AddressRequest {
        public $street; //array( undefined )
        public $number; //array( undefined )
        public $side; //array( undefined )
        public $place; //array( undefined )
        public $city; //array( undefined )
        public $zipCode; //array( undefined )
        public $country; //array( undefined )

    }

    class ContactInformationRequest {
        public $emailRequest; //EmailRequest
        public $phoneRequest; //PhoneRequest
        public $addressRequest; //AddressRequest

    }
