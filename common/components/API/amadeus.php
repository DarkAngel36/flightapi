<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 25.09.2018
 * Time: 15:12
 */

namespace common\components\API;


class amadeus
{
    private $apiKey = 'Qqp9cVlvG0ywG2JncjcpcK1XIiMrGpdA';
    const API_URL = 'https://api.sandbox.amadeus.com/v1.2/flights/';

    public function __construct()
    {
    }

    private function getData($url, $data)
    {
//        $data['apikey'] = $this->apiKey;
        $urlSend = self::API_URL . $url . '?' . 'apikey='.$this->apiKey .'&'. http_build_query($data);
//        die($urlSend);
        $retJson = file_get_contents($urlSend);

        try{
            $returnData = json_decode($retJson, true);
            $status = 'success';
            $message = 'ok';
        } catch (Exception $e) {
            $status = 'error';
            $message = $e->getMessage();
            $returnData = [];
        }

        return ['status' => $status, 'message' => $message, 'data' => $returnData];
    }

    public function getCheap($data)
    {
        $url = 'inspiration-search';
        return $this->getData($url, $data);
    }

    public function getDirect($data)
    {
        $url = 'low-fare-search';

        return $this->getData($url, $data);
    }
}