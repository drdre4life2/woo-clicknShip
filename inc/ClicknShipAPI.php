<?php

namespace Inc;

use Exception;

class ClicknShipAPI
{

    // generate token to call all endpoints
    public static function getToken($username, $password)
    {
        $grant_type = "password";

        $params   = array(
            "userName" => $username,
            "password" => $password,
            "grant_type" => $grant_type
        );

        $payload = json_encode($params);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/Token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "username=$username&password=$password&grant_type=password",
            CURLOPT_HTTPHEADER => array(


                "content-type: application/x-www-form-urlencoded"

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);
            return  $responseObject;
            if ($responseObject->access_token) {
                $token = $responseObject->data->access_token;
                $error = false;
                $error_detail = "";
            } else {
                $error = true;
                $error_detail = 'Register token error: ' . $response;
            };

            $clicknship_args = array(
                'cp_error' => $error,
                'cp_errorDetail' => $error_detail,
                'cp_redirectUrl' => $token,
            );

            $clicknship_args = apply_filters('woocommerce_clicknship_args',  $clicknship_args);

            return  $clicknship_args;
        }
    }

    public static function getOperationStates($authorization)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/operations/cities",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $authorization"

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);
            return $responseObject;
        }
    }

    public static function pickupRequest($authorization, $params)
    {

        $payload = json_encode($params);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/Operations/PickupRequest",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $authorization",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);

            return $responseObject;
        }
    }

    public static function getDeliveryTown($authorization, $cityCode)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/Operations/DeliveryTowns?CityCode=$cityCode",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $authorization"

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);
            return $responseObject;
        }
    }

    public static function getSpecificCity($authorization, $cityCode)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/Operations/DeliveryTowns?CityCode=$cityCode",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $authorization"

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);
            return $responseObject;
        }
    }

    public static function getCityCode($authorization, $cityName)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/operations/cities",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $authorization"

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);
        }

        $code = [];
        $cityName = strtoupper($cityName);
        foreach ($responseObject as $city) {

            if ($cityName == $city->CityName) {
                $code[] = $city->CityCode;
            }
        }
        return $code;
    }


    public static function calculateDeliveryFee($shipping)
    {

       
        $params   = array(
            "origin" => $shipping['origin'],
            "destination" => $shipping['destination'],
            "weight" => $shipping['weight']
        );
        $token = $shipping['token'];
        $payload = json_encode($params);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/Operations/DeliveryFee",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $token",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);

            return $responseObject;
        }
    }



    public static function setTownID($token)
    {

        global $woocommerce;
        $shipping_city = $woocommerce->customer->get_shipping_city();
        $shipping_state = $woocommerce->customer->get_shipping_state();
        $shipping_address = $woocommerce->customer->get_shipping_address();
        $store_city  = get_option('woocommerce_store_city');
        if ($shipping_city == '' || $store_city == '')
            return null;
        $store_raw_country = get_option('woocommerce_default_country');
        $split_country = explode(":", $store_raw_country);



        $store_state   = $split_country[1];
        //$token = new ClicknShip_Shipping_Method();

        //$token = $token->getToken();

        //  $token = $token->access_token;
        $params   = array(
            "origin" => $store_city,
            "destination" => $shipping_city,
            "shipping_state" => $shipping_state,
            "store_state" => $store_state,
            "token" => $token
        );
      //  $cleanCity = ClicknShipApi::filterLagos($params);

        //filter Lagos source and destination

        $recipient_city = $params['destination'];
        $source = $params['origin'];

        //get opertions city name and city code
        $operationCities = ClicknShipApi::getOperationCities($token);

        $shipping_destination = trim($recipient_city);
        $shipping_dest = strtoupper($shipping_destination);

        // match destination city with system city,
        $ship_dest = '';
        foreach ($operationCities as $city) {

            if ($shipping_dest == $city->CityName) {
                $ship_dest = $city->CityCode;
            }
        }

        // Tricky Part
        $operationCities = ClicknShipApi::getDeliveryTown($token, $ship_dest);
        if (empty($operationCities) || !isset($operationCities)) {
            return null;
        }
        $entered_city = $recipient_city;
        $entered_city = strtoupper($entered_city);


        $shipping_locationID = $operationCities;
        $customer_address = ClicknShipAPI::splitAddress($shipping_address);

        foreach ($operationCities as $op) {

            //Check if entered city is in the town list

            if (trim($op->TownName) == $entered_city) {
                $townId = $op->TownID;
            }
            // Check if town is in street address line one

            elseif (in_array($op->TownName, $customer_address)) {
                $townId = $op->TownID;
            }
        }

        //  return print_r($shipping_locationID[1]['TownID']);
        try {
            if (!isset($townId) && !empty($shipping_locationID[1])) {
                // print_r($test);
                $townId = ($shipping_locationID[1]->TownID);
            }
        } catch (Exception $e) {
            return null;
        }
        // if (!isset($townId)) {
            
        // }
        //print_r($townId);
        //  if (session_status() == PHP_SESSION_NONE) {
        //     session_start();
        //  }
       // $_SESSION['storeCity'] = $params['origin'];
        $_SESSION['destination'] = $params['destination'];
        $_SESSION['locationID'] = $townId;
        // Country and state separated:


        if (isset($townId)) {
            return $townId;
        } else {
            return null;
        }
    }
    public static function splitAddress($recipient_address)
    {

        $street =  preg_replace("/(\,|\.)/", "", "$recipient_address");
        $street1 = strtoupper($street);
        $street1 = (explode(" ", $street1));
        return $street1;
    }

    public static function getOperationCities($authorization)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.clicknship.com.ng/clicknship/operations/cities",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $authorization"

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response);
            return $responseObject;
        }
    }

    //set shipping city for Lagos
    public static function filterLagos($shipping)
    {
        if ($shipping['store_state'] == 'LA') {

            if (
                $shipping['origin'] == 'Victoira Island'  || $shipping['origin'] == 'Lagos Island'

                || $shipping['origin'] == 'Lekki' || $shipping['origin'] == 'Ajah'
            ) {

                $origin = "Lagos Island";
            } elseif (
                $shipping['origin'] != 'Victoira Island' || $shipping['origin'] != 'Lagos Island'

                || $shipping['origin'] != 'Lekki'  || $shipping['origin'] != 'Ajah'
            ) {

                $origin = "Mainland";
            }
        } else {
            $origin = $shipping['origin'];
        }


        if ($shipping['shipping_state'] == 'LA') {


            // check it destination is mailand
            if (
                $shipping['destination'] != 'Victoira Island' || $shipping['destination'] != 'Lagos Island'

                || $shipping['destination'] != 'Lekki'  || $shipping['destination'] != 'Ajah'
            ) {

                $destination = "Mainland";
            }  // do nothing if its not lagos

            if (
                $shipping['destination'] == 'Victoira Island' || $shipping['destination'] == 'Lagos Island'

                || $shipping['destination'] == 'Lekki'  || $shipping['destination'] == 'Ajah'
            ) {

                $destination = "Lagos Island";
            }
        } else {
            $destination = $shipping['destination'];
        }



        //handle Osogbo
        if ($destination == 'Osogbo')
            $destination = "Oshogbo";
        if ($origin == "Osogbo")
            $origin = "Oshogbo";

        return array("origin" => $origin, "destination" => $destination);
    }
}
