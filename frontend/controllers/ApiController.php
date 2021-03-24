<?php

namespace frontend\controllers;

use common\components\API\amadeus;
use common\models\Airports;
use common\models\Cities;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\components\API\travelpayouts;
use yii\web\Response;

class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['cheap'],
                'rules' => [
                    [
                        'actions' => ['signup', 'cheap', 'direct', 'airports'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionAirports()
    {
        if(Yii::$app->request->isGet) {
	        Yii::$app->response->format = Response::FORMAT_JSON;
	        return [
		        'data' => [
			        'results' => Cities::getCitiesWithAirports(
				        Yii::$app->request->get('q', '----'),
				        Yii::$app->request->get('value', '')
			        )
		        ]
	        ];
        } else {
	        Yii::$app->response->format = Response::FORMAT_JSONP;
	        return [
		        'callback' => Yii::$app->request->get('callback'),
		        'data' => [
			        'results' => Cities::getCitiesWithAirports(
				        Yii::$app->request->get('q', '----'),
				        Yii::$app->request->get('value', ''))
		        ]
	        ];
        }
    	
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //return $this->render('index');
        die('---');
    }

    public function actionCheap()
    {
//        $api = new travelpayouts();
        $api = new amadeus();
        $data = [
            'currency' => 'usd',
            'origin' => 'MOW',
            'destination' => 'ROV',
            'show_to_affiliates' => 'true',
            'depart_date' => '2018-10-01',
        ];

        $data = [
            'origin' => 'MOW',
            'destination' => 'ROV',
            'departure_date' => '2021-04-05',
            'one-way' => 'false',
            'direct' => 'false',
            'currency' => 'USD',
        ];

        $result = $api->getCheap($data);

        echo '<pre>';
        print_r($result);
        echo '</pre>';
        die();
    }

    public function actionDirect()
    {
//        $api = new travelpayouts();
        $api = new amadeus();
        
/*        $data = [
            'currency' => 'usd',
            'origin' => 'DME',
            'destination' => 'ROV',
            'show_to_affiliates' => 'true',
            'depart_date' => '2018-10-01'
        ];
*/
        $data = [
            'origin' => 'CDG',
            'destination' => 'DME',
            'departureDate' => '2021-04-10',
            'oneWay' => 'true',
            'nonStop' => 'false',
//            'adults' => 1,
//            'children' => 0,
//            'infants' => 0,
//            'currencyCode' => 'USD',
//            'travelClass' => 'ECONOMY'
        ];
        /*$data = [
            'origin' => Yii::$app->request->get('origin', null),
            'destination' => Yii::$app->request->get('destination', null),
            'departure_date' => Yii::$app->request->get('departure_date', null),
            'return_date' => Yii::$app->request->get('return_date', null),
            'one-way' => Yii::$app->request->get('one-way', 'true'),
            'direct' => Yii::$app->request->get('direct', 'false'),
            'currency' => Yii::$app->request->get('currency', 'USD'),
            'adults' => Yii::$app->request->get('adults', 1),
            'children' => Yii::$app->request->get('children', 0),
            'infants' => Yii::$app->request->get('infants', 0),
            'nonstop' => Yii::$app->request->get('nonstop', 'false'),
            'travel_class' => Yii::$app->request->get('travel_class', 'ECONOMY'),
        ];*/

        $result = $api->getDirect($data);
print_r($result); die();
//        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->format = Response::FORMAT_JSONP;
        /*echo '<pre>';
        print_r($result);
        echo '</pre>';
        die();*/
//        return $result;

        return ['callback' => Yii::$app->request->get('callback'), 'data' => $result ];
    }

    public function actionCalendar()
    {
        $api = new travelpayouts();
        $data = [
            'currency' => 'usd',
            'origin' => 'DME',
            'destination' => 'ROV',
            'show_to_affiliates' => 'true',
            'departure_date' => '2021-04-01',
//            'return_date' => '2018-10-11',
            'calendar_type' => 'depart_date'
        ];

//        $result = $api->getCalendar($data);
	    $result = $api->getDirect($data);

        echo '<pre>';
        print_r($result);
        echo '</pre>';
        die();
    }


}
