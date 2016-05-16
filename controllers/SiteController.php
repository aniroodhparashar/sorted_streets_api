<?php

namespace app\controllers;

use app\models\User;
use app\models\Event;
use app\models\Places;
use app\models\UserProfile;
use Yii;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\base\Security;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
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

    public function actionIndex()
    {
      //  echo "<pre>";print_r(Yii::$app->user->getId());die;
        //echo "<pre>";print_r(Yii::$app->db);die;
        return $this->render('index');
    }

    public function actionSocialLogin()
    {
        $getData=Yii::$app->request->get();
       // echo"<pre>";print_r($getData);die;
        $access_token="EAACV1WLx2jgBAHzSUzYZBFgbFQSALhGnfNzMiJbI1xfpHjZCDf8lUhnt0gHkQQumBCDuZAAKeNCst9JaC3CPxauEhhPgjyrNw0zFZBuYCzAFzZAZCtthCzbDn14lCYlrUqGiON1DAQ97kqsmVvAKY7apjbDKaFD9J4TJX1BGLjvHGq1oKqPhvGJBZBuE7oPagRtGihoWw3XsVQvXbJiZBGbg";





        if(!empty($getData['access_token']) && !empty($getData['type']) && $getData['type'] ==="facebook" ){
            $url="https://graph.facebook.com/me/?fields=picture.width(800).height(800),id,name,email,gender&access_token=".$access_token;
            $response = file_get_contents($url);
            $response = json_decode($response);
            echo"<pre>";print_r($response);die;
           // $getInfo=User::findByUsername($postData['email_id']);
            if(empty($getInfo)){
                $userProfile = new User();
                $userProfile->first_name=$response->name;
                $userProfile->city_name=$response->name;
                $userProfile->email_id=$response->email;
                $userProfile->phone_number=$response->email;
                $userProfile->username=$response->email;
                $userProfile->password=md5($postData['password']);
                $userProfile->save();

                //echo"<pre>";print_r($userProfile);die;
                if($userProfile->save()){
                    $getInfo=User::findByUsername($postData['email_id']);
                    // echo"<pre>";print_r($getInfo);die;

                    $response=[];
                    $result=[];
                    $result=[
                        'id'=>$getInfo->id,
                        'first_name'=>$getInfo->first_name,
                        'city_name'=>$getInfo->city_name,
                        'email_id'=>$getInfo->email_id,
                        'phone_number'=>$getInfo->phone_number,
                        'username'=>$getInfo->username,
                        //'access_token'=>$getInfo->access_token
                    ];
                    $response=['status'=>'success',
                        'error'=>'null',
                        'result'=>['user_details'=>$result]];
                    echo json_encode($response);
                }else{
                    $result=['name'=>'Bad Request Exception',
                        'message'=>'Invalid Request',
                        'code'=>401];
                    $response=['status'=>'failure',
                        'error'=>'Unable To Register',
                        'result'=>$result];
                    echo json_encode($response);
                }
            }else{
                $result=['name'=>'Bad Request Exception',
                    'message'=>'User Already Exists',
                    'code'=>400];
                $response=['status'=>'failure',
                    'error'=>'User Already Exists',
                    'result'=>$result];
                echo json_encode($response);
            }

        }else{
            $result=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>401];
            $response=['status'=>'failure',
                'error'=>'Unable To Register',
                'result'=>$result];
            echo json_encode($response);
        }
        //echo "<pre>";print_r(Yii::$app->db);die;

    }

    public function actionLogin()
    {
        $data = json_decode(file_get_contents('php://input'), true);
//print_r($data);die;
     //  print_r($_REQUEST);die;
        if (!Yii::$app->user->isGuest) {
            //return $this->goHome();
        }

        $model = new LoginForm();
        $user = new User();
        $userProfile = new UserProfile();
        if(isset($data) && !empty($data)){
$postData=$data;
        }else{
            $postData=Yii::$app->request->post();
        }
        //echo"<pre>";print_r($postData);die;
        //echo"<pre>";print_r($postData);die;
       /* echo"<pre>";print_r($postData);die;
        echo"<pre>";print_r($postData);die;*/
        if((isset($postData['username']) && isset($postData['password']))  && !empty($postData['username']) && !empty($postData['password'])){


        $getInfo=User::findByUsername($postData['username']);
           // echo"<pre>";print_r($getInfo);die;
            if(!empty($getInfo)){
       // echo"<pre>";print_r($getInfo);die;
                if($getInfo->password ===md5($postData['password'])){
        //$getInfo=User::findIdentity($getInfo->id);
        $userInfo = UserProfile::findOne($getInfo->id);
        //$userInfo=$user->getInfo()->all();


        //echo"<pre>";print_r($getInfo);die;
        $user = User::findOne($getInfo->id);
        //$user->id=Yii::$app->user->getId();
        $user->user_ip=Yii::$app->getRequest()->getUserIP();
        $user->access_token=md5($getInfo->username."123");
        //echo  $user->access_token;die;
        $user->update();
        // var_dump($user->update());die;

        // echo"<pre>";print_r($user);die;
        $response=[];
        $result=[];
        $result=[
            'id'=>$getInfo->id,
            'first_name'=>$getInfo->first_name,
            'last_name'=>$getInfo->last_name,
            'email_id'=>$getInfo->email_id,
            'phone_number'=>$getInfo->phone_number,
            'access_token'=>$getInfo->access_token
        ];
        $response=['status'=>'success',
            'error'=>'null',
            'result'=>$result];
        echo json_encode($response);
                }else{
                    $result=['name'=>'Authentication Exception',
                        'message'=>'Invalid Password',
                        'code'=>401];
                    $response=['status'=>'failure',
                        'error'=>'Unable To Login',
                        'result'=>$result];
                    echo json_encode($response);
                }
            }else{
                $result=['name'=>'Authentication Exception',
                'message'=>'Invalid Credentials',
                'code'=>401];
                $response=['status'=>'failure',
                    'error'=>'Unable To Login',
                    'result'=>$result];
                echo json_encode($response);
            }
        }else{
       // var_dump($model->login());die;
 if ($model->load(Yii::$app->request->post()) && $model->login()) {
//echo"<pre>";print_r(json_encode(Yii::$app->request->post()));die;
            $getInfo=User::findIdentity(Yii::$app->user->getId());
            $userInfo = UserProfile::findOne(Yii::$app->user->getId());
            //$userInfo=$user->getInfo()->all();


            //echo"<pre>";print_r($accessToken);die;
            $user = User::findOne(Yii::$app->user->getId());
            //$user->id=Yii::$app->user->getId();
            $user->user_ip=Yii::$app->getRequest()->getUserIP();
            $user->access_token=md5($getInfo->username."123");
            //echo  $user->access_token;die;
            $user->update();
           // var_dump($user->update());die;

          // echo"<pre>";print_r($user);die;
            $response=[];
            $result=[];
            $result=[
                'id'=>$getInfo->id,
                'first_name'=>$getInfo->first_name,
            'last_name'=>$getInfo->last_name,
            'email_id'=>$getInfo->email_id,
            'phone_number'=>$getInfo->phone_number,
            'access_token'=>$getInfo->access_token
            ];
            $response=['status'=>'success',
            'error'=>'null',
            'result'=>$result];
            echo json_encode($response);die;
            //return $this->goBack();
        }else{
            //echo"asd";die;
          return $this->render('login', [
                'model' => $model,
            ]);
         }
            }
       /* return $this->render('login', [
            'model' => $model,
        ]);*/
    }

    public function actionSignUp()
    {
         $data = json_decode(file_get_contents('php://input'),true);
         $postData=Yii::$app->request->post();

        $userProfile = new User();
        if(isset($data) && !empty($data)){
$postData=$data;
        }else{
            $postData=Yii::$app->request->post();
        }

        if(!empty($postData['first_name']) && !empty($postData['city_name']) && !empty($postData['password']) && !empty($postData['username']) && !empty($postData['email_id']) && !empty($postData['phone_number'])){
            $getInfo=User::findByUsername($postData['email_id']);
            if(empty($getInfo)){
            $userProfile = new User();
            $userProfile->first_name=$postData['first_name'];
            $userProfile->city_name=$postData['city_name'];
            $userProfile->email_id=$postData['email_id'];
            $userProfile->phone_number=$postData['phone_number'];
            $userProfile->username=$postData['username'];
            $userProfile->password=md5($postData['password']);
            $userProfile->save();

             //echo"<pre>";print_r($userProfile);die;
                if($userProfile->save()){
                    $getInfo=User::findByUsername($postData['email_id']);
                    // echo"<pre>";print_r($getInfo);die;

                    $response=[];
                    $result=[];
                    $result=[
                        'id'=>$getInfo->id,
                        'first_name'=>$getInfo->first_name,
                        'city_name'=>$getInfo->city_name,
                        'email_id'=>$getInfo->email_id,
                        'phone_number'=>$getInfo->phone_number,
                        'username'=>$getInfo->username,
                        //'access_token'=>$getInfo->access_token
                    ];
                    $response=['status'=>'success',
                        'error'=>'null',
                        'result'=>['user_details'=>$result]];
                    echo json_encode($response);
                }else{
                    $result=['name'=>'Bad Request Exception',
                        'message'=>'Invalid Request',
                        'code'=>401];
                    $response=['status'=>'failure',
                        'error'=>'Unable To Register',
                        'result'=>$result];
                    echo json_encode($response);
                }
            }else{
                $result=['name'=>'Bad Request Exception',
                    'message'=>'User Already Exists',
                    'code'=>400];
                $response=['status'=>'failure',
                    'error'=>'User Already Exists',
                    'result'=>$result];
                echo json_encode($response);
            }

            }else{
            $result=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>401];
            $response=['status'=>'failure',
                'error'=>'Unable To Register',
                'result'=>$result];
            echo json_encode($response);
            }

    }
    public function actionEventListing()
    {
        //  $data = json_decode(file_get_contents('php://input'),true);
        $getData=Yii::$app->request->get();
//echo"<pre>";print_r($getData);die;
        $eventList = new Event;
        /*if(isset($data) && !empty($data)){
            $getData=$data;
        }else{
            $getData=Yii::$app->request->post();
        }*/

        if(!empty($getData['event']) && $getData['event'] ==="list" && !empty($getData['auth_token'])){
            $eventInfo=$eventList->getEventDetails();
            $eventListImagePath=Yii::$app->params;
           // echo"<pre>";print_r(Yii::$app->params);die;
           // $getInfo=User::findByUsername($postData['email_id']);
            if(!empty($eventInfo)){

                    // echo"<pre>";print_r($getInfo);die;

                    $response=[];
                    $result=[];
                    $result=[
                        'event_listing'=>$eventInfo,
                        'image_path'=>urlencode($eventListImagePath['eventImagePath']),

                        //'access_token'=>$getInfo->access_token
                    ];

                    $response=['status'=>'success',
                        'error'=>null,
                        'result'=>['event_list'=>$result]];

                    echo json_encode($response);

                }else{
                    $result=null;
                $error=['name'=>'Bad Request Exception',
                    'message'=>'Invalid Request',
                    'code'=>401];
                    $response=['status'=>'failure',
                        'error'=>$error,
                        'result'=>$result];
                    echo json_encode($response);
                }


        }else{
            $result=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>400];
            $error=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>400];
            $response=['status'=>'failure',
                'error'=>$error,
                'result'=>$result];
            echo json_encode($response);
        }

    }

    public function actionEventDetail()
    {
        //  $data = json_decode(file_get_contents('php://input'),true);
        $getData=Yii::$app->request->get();
//echo"<pre>";print_r($getData);die;
        $eventList = new Event;
        /*if(isset($data) && !empty($data)){
            $getData=$data;
        }else{
            $getData=Yii::$app->request->post();
        }*/
        if(!empty($getData['event_id']) && $getData['event'] ==="details" && !empty($getData['auth_token'])){
$eventDetails=$eventList->eventDetails($getData['event_id']);
            $eventListImagePath=Yii::$app->params;
        $imageArr=[];
        foreach($eventDetails as $eventData){
            $imageArr[]['image'] =$eventData['gallery_images'];
            //echo"<pre>";print_r($imageArr);die;
        }
            if(!empty($eventDetails)){

                // echo"<pre>";print_r($getInfo);die;

                $response=[];
                $result=[];
               /* $result=[
                    'event_listing'=>$eventInfo,
                    'image_path'=>urlencode($eventListImagePath['eventImagePath']),

                    //'access_token'=>$getInfo->access_token
                ];*/
               // echo"<pre>";print_r($imageArr);die;
                $result=['event_id'=>$eventDetails[0]['event_id'],
                    'event_name'=>$eventDetails[0]['event_name'],
                    'event_location'=>$eventDetails[0]['event_location'],
                    'event_date'=>$eventDetails[0]['event_date'],
                    'event_description'=>$eventDetails[0]['event_description'],
                    'event_action'=>$eventDetails[0]['event_action'],
                    'gallery_id'=>$eventDetails[0]['gallery_id'],
                    'gallery_event_id'=>$eventDetails[0]['gallery_event_id'],
                    'image_path'=>$imageArr,

                ];
                $response=['status'=>'success',
                    'error'=>null,
                    'result'=>['event_detail'=>$result]];

                echo json_encode($response);

            }else{
                $result=null;
                $error=['name'=>'Bad Request Exception',
                    'message'=>'Invalid Request',
                    'code'=>401];
                $response=['status'=>'failure',
                    'error'=>$error,
                    'result'=>$result];
                echo json_encode($response);
            }


        }




            // echo"<pre>";print_r(Yii::$app->params);die;
            // $getInfo=User::findByUsername($postData['email_id']);
            else{
            $result=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>400];
            $error=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>400];
            $response=['status'=>'failure',
                'error'=>$error,
                'result'=>$result];
            echo json_encode($response);
        }

    }

    public function actionPlaceListing()
    {
        //  $data = json_decode(file_get_contents('php://input'),true);
        $getData=Yii::$app->request->get();
//echo"<pre>";print_r($getData);die;
        $placesList = new Places;
        /*if(isset($data) && !empty($data)){
            $getData=$data;
        }else{
            $getData=Yii::$app->request->post();
        }*/

        if(!empty($getData['places']) && $getData['places'] ==="list" && !empty($getData['auth_token'])){
            $placeInfo=$placesList->getPlaces();
            $placesImagePath=Yii::$app->params;
            // echo"<pre>";print_r(Yii::$app->params);die;
            // $getInfo=User::findByUsername($postData['email_id']);
            if(!empty($placeInfo)){

                // echo"<pre>";print_r($getInfo);die;

                $response=[];
                $result=[];
                $result=[
                    'place_listing'=>$placeInfo,
                    'image_path'=>urlencode($placesImagePath['placesImagePath']),

                    //'access_token'=>$getInfo->access_token
                ];

                $response=['status'=>'success',
                    'error'=>null,
                    'result'=>['event_list'=>$result]];

                echo json_encode($response);

            }else{
                $result=null;
                $error=['name'=>'Bad Request Exception',
                    'message'=>'Invalid Request',
                    'code'=>401];
                $response=['status'=>'failure',
                    'error'=>$error,
                    'result'=>$result];
                echo json_encode($response);
            }


        }else{
            $result=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>400];
            $error=['name'=>'Authentication Exception',
                'message'=>'Invalid Request',
                'code'=>400];
            $response=['status'=>'failure',
                'error'=>$error,
                'result'=>$result];
            echo json_encode($response);
        }

    }
    public function actionLogout()
    {
       // echo"<pre>";print_r(Yii::$app->user->generateAuthKey());die;
        /*Yii::$app->user->logout();

        return $this->goHome();*/
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
