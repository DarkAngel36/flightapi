<?php

namespace frontend\controllers;

use common\components\API\amadeus;
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
                        'actions' => ['signup', 'cheap', 'direct'],
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
            'departure_date' => '2018-10-01',
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
        ];*/

        $data = [
            'origin' => 'CDG',
            'destination' => 'DME',
            'departure_date' => '2018-10-23',
            'one-way' => 'true',
            'direct' => 'false',
            'currency' => 'USD',
            'adults' => 1,
            'children' => 0,
            'infants' => 0,
            'nonstop' => 'false',
            'currency' => 'USD',
            'travel_class' => 'ECONOMY'
        ];

        $result = $api->getDirect($data);

        echo '<pre>';
        print_r($result);
        echo '</pre>';
        die();
    }

    public function actionCalendar()
    {
        $api = new travelpayouts();
        $data = [
            'currency' => 'usd',
            'origin' => 'DME',
            'destination' => 'ROV',
            'show_to_affiliates' => 'true',
            'departure_date' => '2018-10-01',
            'return_date' => '2018-10-11',
            'calendar_type' => 'depart_date'
        ];

        $result = $api->getCalendar($data);

        echo '<pre>';
        print_r($result);
        echo '</pre>';
        die();
    }


}
