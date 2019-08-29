<?php

namespace app\api\controller;

use app\BaseController;
use think\facade\Config;
use EasyWeChat\Factory;
use app\model\WxPublicAccount;
use app\model\SystemConfig;
use app\UploadFiles as AppUploadFiles;
use think\facade\Cache;

class WxOpen extends BaseController
{
    public function init()
    {
        $wx_open_app_config = Config::get('wx_open_app');
        $openPlatform = Factory::openPlatform($wx_open_app_config);

        $server = $openPlatform->server;

        return $server->serve();
    }

    public function appAuthCallback()
    {
        $auth_code = $this->request->param('auth_code');

        if (empty($auth_code)) {
            return $this->error('缺少参数','admin/System/others');
        }
        
        $wx_open_app_config = Config::get('wx_open_app');
        $openPlatform = Factory::openPlatform($wx_open_app_config);
        
        $auth_info = $openPlatform->handleAuthorize($auth_code);

        $model_auth_account = WxPublicAccount::where('authorizer_appid',$auth_info['authorization_info']['authorizer_appid'])->find();

        if(!empty($model_auth_account)){
            if($model_auth_account->getData('deauth_time') === 0){
                return $this->error('不能重复授权','admin/System/others');
            }else{
                $model_auth_account->deauth_time = 0;
            }
        }else{
            $model_auth_account = new WxPublicAccount();
            $model_auth_account->authorizer_appid = $auth_info['authorization_info']['authorizer_appid'];
            // $model_auth_account->authorizer_access_token = $auth_info['authorization_info']['authorizer_access_token'];
            $model_auth_account->authorizer_refresh_token = $auth_info['authorization_info']['authorizer_refresh_token'];
            $model_auth_account->create_time = time();
        }

        $wx_public_account_info = $openPlatform->getAuthorizer($model_auth_account->authorizer_appid);


        $model_auth_account->nick_name = $wx_public_account_info['authorizer_info']['nick_name'];
        
        
        if(!empty($model_auth_account->getData('head_img'))){
            
            AppUploadFiles::delete($model_auth_account->getData('head_img'));
        }
        
        $model_auth_account->head_img = \save_url_file($wx_public_account_info['authorizer_info']['head_img'],3);
        
        AppUploadFiles::use($model_auth_account->getData('head_img'));
        $model_auth_account->service_type_info = $wx_public_account_info['authorizer_info']['service_type_info']['id'];
        $model_auth_account->verify_type_info = $wx_public_account_info['authorizer_info']['verify_type_info']['id'];
        $model_auth_account->user_name = $wx_public_account_info['authorizer_info']['user_name'];
        $model_auth_account->alias = $wx_public_account_info['authorizer_info']['alias'];

        if(!empty($model_auth_account->getData('qrcode_url'))){
            AppUploadFiles::delete($model_auth_account->getData('qrcode_url'));
        }
        $model_auth_account->qrcode_url = save_url_file($wx_public_account_info['authorizer_info']['qrcode_url'],2);
        
        AppUploadFiles::use($model_auth_account->getData('qrcode_url'));
        $model_auth_account->business_info = json_encode($wx_public_account_info['authorizer_info']['business_info']);
        $model_auth_account->principal_name = $wx_public_account_info['authorizer_info']['principal_name'];
        $model_auth_account->signature = $wx_public_account_info['authorizer_info']['signature'];

        $func_info = '';

        foreach ($wx_public_account_info['authorization_info']['func_info'] as $key => $value) {
            $func_info .= $value['funcscope_category']['id'];
        }

        $model_auth_account->func_info = $func_info;

        $model_auth_account->save();
        $auth_type = $this->request->param('auth_type');

        switch ($auth_type) {
            case 'system':
                $default_wx_public_account_id = get_system_config('default_wx_public_account_id');

                if(empty($default_wx_public_account_id)){
                    SystemConfig::create(['name'=>'default_wx_public_account_id','value'=>$model_auth_account->id]);
                }else{
                    SystemConfig::update(['value'=>$model_auth_account->id],['name'=>get_system_config('default_wx_public_account_id')]);
                }
        
                $list = SystemConfig::column('value','name');
                Cache::set('system_config',$list);
        
                if($model_auth_account->getData('verify_type_info') !== 0){
                    return $this->error('授权成功，但不能使用，公众号未认证','admin/System/others');
                }
        
                return $this->success('授权成功','admin/System/others');
                break;
            
            default:
                # code...
                break;
        }
        
    }

    public function test()
    {
        $wx_open_app_config = Config::get('wx_open_app');
        $openPlatform = Factory::openPlatform($wx_open_app_config);
        $info = $openPlatform->getAuthorizer('wx3280c83a307cbe7c');

        dump($info);
    }
}
