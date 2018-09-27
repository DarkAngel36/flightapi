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
use common\models\Cities;
use common\models\Countries;
use common\models\Planes;
use yii\console\Controller;

class ImportController extends Controller
{
    public function actionIndex()
    {
//        $this->actionAirports();
//        $this->actionAviacompanies();
        $this->actionCountries();
        $this->actionCities();
        $this->actionPlanes();
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

    public function actionCountries()
    {
        Countries::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/countries.json');
        $airports = json_decode($json, true);
        foreach ($airports as $item) {
            $item['name_translations'] = json_encode($item['name_translations']);

            $model = new Countries($item);
            $model->save();
        }
    }

    public function actionCities()
    {
        Cities::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/cities.json');
        $airports = json_decode($json, true);
        foreach ($airports as $item) {
            $item['name_translations'] = json_encode($item['name_translations']);
            $item['coordinates'] = json_encode($item['coordinates']);
            $model = new Cities($item);
            $model->save();
        }
    }

    public function actionPlanes()
    {
        Planes::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/planes.json');
        $airports = json_decode($json, true);

        foreach ($airports as $item) {
            $model = new Planes($item);
            $model->save();
        }
    }
}