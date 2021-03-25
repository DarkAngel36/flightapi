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
            'corsFilter' => [
	            'class' => \yii\filters\Cors::className(),
	            'cors' => [
		            // restrict access to
		            'Origin' => ['*'],
		            // Allow only POST and PUT methods
		            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
		            // Allow only headers 'X-Wsse'
		            'Access-Control-Request-Headers' => ['X-Wsse'],
		            // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
//		            'Access-Control-Allow-Credentials' => true,
		            // Allow OPTIONS caching
		            'Access-Control-Max-Age' => 3600,
		            // Allow the X-Pagination-Current-Page header to be exposed to the browser.
		            'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
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
            'options' => [
	            'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }
	
	public function actionOptions() {
	
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
    	if(Yii::$app->request->isOptions) {
    		die();
	    }
//        $api = new travelpayouts();
        $api = new amadeus();
    	$data = [];
        
	    if(Yii::$app->request->isGet) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
		
		    $data = [
			    'originLocationCode' => Yii::$app->request->get('origin', null),
			    'destinationLocationCode' => Yii::$app->request->get('destination', null),
			    'departureDate' => Yii::$app->request->get('departure_date', null),
			    'returnDate' => Yii::$app->request->get('return_date', null),
			    'currencyCode' => Yii::$app->request->get('currency', 'USD'),
			    'adults' => (int)Yii::$app->request->get('adults', 1),
			    'children' => (int)Yii::$app->request->get('children', 0),
			    'infants' => (int)Yii::$app->request->get('infants', 0),
			    'nonStop' => Yii::$app->request->get('nonstop', 'false'),
			    'travelClass' => Yii::$app->request->get('travel_class', 'ECONOMY'),
		    ];
		    
		    if(strtotime($data['returnDate']) < strtotime($data['departureDate'])) {
			    $data['returnDate'] = null;
		    }
	    }
        
        $result = $api->getDirect($data);

        return ['callback' => Yii::$app->request->get('callback', null), 'data' => $result ];
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
