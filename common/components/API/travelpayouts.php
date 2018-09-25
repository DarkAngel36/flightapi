<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 25.09.2018
 * Time: 13:08
 */
namespace common\components\API;

class travelpayouts
{
    private $token = '90df0aa3a382975fbb45702ec301115e';
    const SITE_URL = 'http://api.travelpayouts.com/';

    public function __construct()
    {

    }

    private function getData($url, $data = [])
    {
        $data['token'] = $this->token;
        $urlSend = self::SITE_URL . $url . '?'. http_build_query($data);
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
        $url = 'v1/prices/cheap';

        return $this->getData($url, $data);
    }

    public function getDirect($data)
    {
        $url = 'v1/prices/direct';

        return $this->getData($url, $data);
    }

    public function getCalendar($data)
    {
        $url = 'v1/prices/calendar';

        return $this->getData($url, $data);
    }
}