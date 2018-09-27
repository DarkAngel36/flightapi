<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 25.09.2018
 * Time: 14:24
 */

namespace console\controllers;

use common\models\Airports;
use common\models\Aviacompanies;
use yii\console\Controller;

class ImportController extends Controller
{
    public function actionIndex()
    {
        $this->actionAirports();
        $this->actionAviacompanies();
    }

    public function actionAirports()
    {
        Airports::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/airports.json');
        $airports = json_decode($json, true);
        foreach ($airports as $item) {
            $item['name_translations'] = json_encode($item['name_translations']);
            $item['coordinates'] = json_encode($item['coordinates']);
            $model = new Airports($item);
            $model->save();
        }
    }

    public function actionAviacompanies()
    {
        Aviacompanies::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/airlines.json');
        $airports = json_decode($json, true);

        foreach ($airports as $item) {
    	    if(!isset($item['code'])) {print_r($item); die();}
	    if(!isset($item['name_translations']['en'])) $item['name_translations']['en'] = $item['name'];
            $item['name'] = $item['name_translations']['en'];
            $item['name_translations'] = json_encode($item['name_translations']);
//            print_r($item);die();
            $model = new Aviacompanies();
            $model->code = $item['code'];
            $model->name = $item['name'];
            $model->name_translations = $item['name_translations'];

            if(!$model->save()) print_r($model->errors);
        }
    }
}