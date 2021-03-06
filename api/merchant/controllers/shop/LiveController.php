<?php

namespace app\controllers\shop;


use app\models\merchant\partnerUser\PartnerUserModel;
use app\models\system\PluginModel;
use app\models\system\SystemAppAccessVersionModel;
use app\models\system\SystemPluginAccessModle;
use app\models\system\SystemPluginModel;
use Yii;
use app\models\admin\app\AppAccessModel;
use yii\web\ShopController;

class LiveController extends ShopController
{

    public $enableCsrfValidation = false; //禁用CSRF令牌验证，可以在基类中设置

    public function behaviors()
    {
        return [
            'token' => [
                'class' => 'yii\filters\ShopFilter', //调用过滤器
//                'only' => ['single'],//指定控制器应用到哪些动作
                'except' => ['list', 'search-partner'],//指定控制器不应用到哪些动作
            ]
        ];
    }

    public function init()
    {
        $res = $this->db();
        if($res['status']==204){
            echo json_encode($res);
            die();
        }else{
            parent::init(); // TODO: Change the autogenerated stub
        }

    }
    private  function db(){
        isset($_SESSION) or session_start();
        if(!isset($_SESSION['authcode']) || $_SESSION['authcode'] != 'cadef7d447'.'9_200313181511971_14d56eed_2413be81da11deedfaccded290966607'){
            $hosts = $_SERVER['HTTP_HOST'].'|'.$_SERVER['SERVER_NAME'];
            $ckret = xzphp_curl_get('http://shouquanjs.juanpao.com/check.php?a=index&appsign=9_200313181511971_14d56eed_2413be81da11deedfaccded290966607&h='.urlencode($hosts).'&t='.$_SERVER['REQUEST_TIME'].'&token='.md5($_SERVER['REQUEST_TIME'].'|'.$hosts.'|xzphp|cadef7d447'));

            if($ckret){
                $ckret = json_decode($ckret, true);
                if($ckret['status'] != 1){
                    return result(500,$ckret['msg']);
                }else{
                    $_SESSION['authcode'] = 'cadef7d447'.'9_200313181511971_14d56eed_2413be81da11deedfaccded290966607';
                    unset($hosts,$ckret);
                    return result(200,'请求成功');
                }
            }else{
                return result(204,'授权检测失败，请联系授权提供商。');
            }
        }
    }

    public function actionList()
    {
        if (yii::$app->request->isGet) {
            $request = yii::$app->request; //获取 request 对象
            $params = $request->get(); //获取地址栏参数
            $data = getConfig('live');

            if (isset($params['status'])) {
                if ($params['status'] == 1) {
                    $res = array();
                    $bool = false;
                    for ($i = 0; $i < count($data['data']['room_info']); $i++) {
                        if ($data['data']['room_info'][$i]['status'] == 1) {
                            $bool = true;
                            $res['status'] = 200;
                            $res['message'] = "请求成功";
                            $res['data'] = $data['data']['room_info'][$i];
                        }
                    }
                    if($bool){
                        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return $res;
                    }

                }
                return result(204,"请求成功");
            } else {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $data;
            }
        } else {
            return result(500, "请求方式错误");
        }
    }


}
