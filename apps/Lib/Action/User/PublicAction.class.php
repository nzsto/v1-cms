<?php

class PublicAction extends Action
{
    protected $Config;
    protected $MemberConfig;
    protected $SysConfig;
    protected $Categorys;
    protected $Mod;
    protected $Model;
    protected $_groupid;

    public function _initialize()
    {
        $this->SysConfig = F('sys.config');
        $this->Model     = F('Model');
        $this->Mod       = F('Mod');

        if ( ! F($this->MemberConfig)) {
            $list = M('MemberConfig')->select();

            $this->MemberConfig = array();

            foreach ($list as $key=>$r) {
                $this->MemberConfig[$r['varname']]=$r['value'];
            }

            F('MemberConfig',$this->MemberConfig);
        } else {
            $this->MemberConfig = F($this->MemberConfig);
        }

        //用户组
        $this->_groupid = !empty($_SESSION['member']['groupid'])?$_SESSION['member']['groupid']:0;

        $this->lang = F('Lang');
        $this->assign('Lang',$this->lang);
        $default_lang = C('DEFAULT_LANG');

        $l = I('get.l','');
        $lang = isset($this->Lang[$l]) ? $l : $default_lang;

        define('LANG_NAME', $lang);
        define('LANG_ID', $this->lang[$lang]['id']);

        //获取栏目
        $this->Categorys = F('Category_'.$lang);

        //获取网站配置信息
        $this->Config = F('Config_'.$lang);

        C('TMPL_CACHFILE_SUFFIX', $lang.C('TMPL_CACHFILE_SUFFIX'));

        if ($lang == $default_lang) {
            cookie('think_template', $default_lang);
        } else {
            cookie('think_template', $lang);
        }

        $this->assign($this->Config);
        $this->assign('Model',$this->Model);
        $this->assign('Cats',$this->Categorys);
    }

    //验证码
    public function verify()
    {
        header('Content-type: image/jpeg');
        $type = isset($_GET['type'])?$_GET['type']:'jpeg';
        import("@.ORG.Image");
        Image::buildImageVerify(4,1,$type);
    }

    //检测是否登录
    function checkLogin()
    {
        if (empty($_SESSION['member']['username'])) {
            $this->redirect('User/Login/index');
        }
    }


    //检测是否是移动设备
    function checkMobile()
    {
        import('ORG.Util.MobileDetect');
        $detect = new MobileDetect;
        if ($detect->isMobile() || $detect->isTablet()) {
            if ($this->SysConfig['SUB_DOMAIN'] == 1) {
                redirect('http://'.C('SITE_WAP_DOMAIN'));
            }
        }
    }
}