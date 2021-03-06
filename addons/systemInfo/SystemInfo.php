<?php
// +----------------------------------------------------------------------
// | HeilPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.heilphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Jason <1878566968@qq.com>
// +----------------------------------------------------------------------

namespace addons\systemInfo;
use app\common\controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

    class SystemInfo extends Addon{

        public $info = array(
            'name'=>'SystemInfo',
            'title'=>'系统环境信息',
            'description'=>'用于显示一些服务器的信息',
            'status'=>1,
            'author'=>'thinkphp',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的AdminIndex钩子方法
        public function adminIndex($param){
            $config = $this->getConfig();
            
            if(extension_loaded('curl')){
                $url = 'http://www.heilphp.com/index.php?m=home&c=check_version';
                $params = array(
                    'version' => HEILPHP_VERSION,
                    'domain'  => $_SERVER['HTTP_HOST'],
                    'auth'    => sha1(config('DATA_AUTH_KEY')),
                );
    
                $vars = http_build_query($params);
                $opts = array(
                    CURLOPT_TIMEOUT        => 5,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL            => $url,
                    CURLOPT_POST           => 1,
                    CURLOPT_POSTFIELDS     => $vars,
                    CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
                );
    
                /* 初始化并执行curl请求 */
                $ch = curl_init();
                curl_setopt_array($ch, $opts);
                $data  = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);
            }

            if(!empty($data) && strlen($data)<400 ){
                $config['new_version'] = $data;
            }

            $this->assign('addons_config', $config);

            if($config['display']){
				$system_info_mysql = Db()->query("select version() as v;");
				$this->assign('system_info_mysql',$system_info_mysql);
                $this->display('widget');
            }
        }
    }
