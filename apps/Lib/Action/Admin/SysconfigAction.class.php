<?php

class SysconfigAction extends PublicAction
{

    public function index()
    {
        $model = M('Sysconfig');
        $data = $model->select();
        $sysconf = array();
        foreach($data as $key=>$r) {
            $sysconf[$r['varname']] = $r['value'];
        }

        $this->assign('sysconf',$sysconf);

        //获取robots.txt
        $robots = file_get_contents('robots.txt');
        $this->assign('robots', $robots);
        $robots_txt1 = <<<yuzihao
User-agent:*
Disallow:/
yuzihao;
        $robots_txt2 = <<<yuzihao
User-agent: *
Disallow: /public/
Disallow: /core/
Disallow: /admin/
Disallow: /apps/
Disallow: /*.js$
Disallow: /*.css$
yuzihao;

        $this->assign('robots_txt1', $robots_txt1);
        $this->assign('robots_txt2', $robots_txt2);

        //记录当前位置
        cookie('__forward__', $_SERVER['REQUEST_URI']);
        
        $this->display();
    }

    public function advanced()
    {
        $model = M('Sysconfig');
        if (IS_POST) {
            if(C('TOKEN_ON') && !$model->autoCheckToken($_POST))
                $this->error(L('_TOKEN_ERROR_'));

            if(isset($_POST['site_url'])){
                $_POST['site_url'] = str_replace('http://', '', $_POST['site_url']);
                $_POST['site_url'] = rtrim($_POST['site_url'],'/');
            }

            $where ="";

            if(isset($_POST['lang'])){
                $where.= " AND lang={$_POST['lang']}";
            }

            foreach($_POST as $key=>$value){
                $data['value'] = $value;
                $f = $model->where("varname='".$key."'".$where)->save($data);
            }
            savecache('Sysconfig');

            if($f){
                $this->success('提交成功!');
            }else{
                $this->error('提交失败!');
            }
        } else {
            $sysconfig = getCache("Sysconfig");
            $Urlrule=array();
            foreach((array)$this->Urlrule as $key => $r){
                $urls=$r['showurlrule'].':::'.$r['listurlrule'];
                if(empty($r['ishtml']))
                    $Urlrule[$urls] = "内容页:".$r['showexample'].", 列表页:".$r['listexample'];
            }
            $this->assign('Urlrule',$Urlrule);

            $this->assign('yesorno',array(0 => L('no'),1  => L('yes')));
            $this->assign('openarr',array(0 => L('close_select'),1  => L('open_select')));
            $this->assign('enablearr',array(0 => L('disable'),1  => L('enable')));

            $urlmodelArr = array(
                                0 => '普通模式(m=module&a=action&id=1)',
                                1 => 'PATHINFO模式(index.php/Index_index_id_1)',
                                2 => 'REWRITE模式(Index_index_id_1)',
                                3 => '兼容模式'
                            );
            $this->assign('urlmodelarr', $urlmodelArr);
            $this->assign('readtypearr', array(0=>'readfile',1=> 'redirect'));
            $this->assign('excefilearr', array('./public/exception.html'=>'默认页面','./public/404.html'=>'404页面'));
            $this->assign($sysconfig);

            $this->display();
        }
    }

    function robots()
    {
        $content = $_POST['robots'];
        $r = file_put_contents('robots.txt', $content);

        if ($r) {
            $this->success('保存成功!');
        } else {
            $this->success('保存失败!');
        }
    }

    //邮箱设置
    public function mail()
    {
        $sysconfig_db = M('Sysconfig');
        $data = $sysconfig_db->select();
        $sysconf = array();
        foreach($data as $key=>$r) {
            $sysconf[$r['varname']] = $r['value'];
        }

        $this->assign('sysconf',$sysconf);
        $this->display();
    }

    public function save()
    {
        if(C('TOKEN_ON') && !$this->db->autoCheckToken($_POST))
            $this->error(L('_TOKEN_ERROR_'));

        if(isset($_POST['SITE_DOMAIN'])){
            $_POST['SITE_DOMAIN'] = str_replace('http://', '', $_POST['SITE_DOMAIN']);
            $_POST['SITE_DOMAIN'] = rtrim($_POST['SITE_DOMAIN'],'/');

            if(!empty($_POST['SITE_DOMAINS'])) {
                $siteDomainArr = explode("\n", $_POST['SITE_DOMAINS']);

                if (in_array($_POST['SITE_DOMAIN'], $siteDomainArr)) {
                    $this->error('主域名不能和其他域名重复!');
                }
            }
        }

        //首页开关
        $pos_sts =$_POST['CHANGE_INDEX'];
        $wheres['varname']="CHANGE_INDEX";
        $sts = M('Sysconfig')->where($wheres)->find();
        $db_sts = $sts['value'];
        if($_POST['CHANGE_INDEX'] != null){
            if($sts['value'] != $_POST['CHANGE_INDEX'] && $_SESSION['admin']['id']==1){
                $sts2 = M('Sysconfig')->where($wheres)->find();
                if($sts2['value'] == 0 ){
                    $file = glob('./themes/Home/*/Index_index.html', GLOB_BRACE); //匹配所有文件
                    foreach ($file as $key => $def) {
                        
                        $strlen = strlen($def);
                        $str=strrchr($def, '/');
                        $str2=strripos($def, '/');
                        $str3 = substr($def,-$strlen,$str2); 
                        rename( $def, $str3.'/Index_index.shtml' );

                    }

                    $file2 = glob('./themes/Home/*/Index_index.xhtml', GLOB_BRACE); //匹配所有文件
                    foreach ($file2 as $key => $def2) {
                        
                        $strlen = strlen($def2);
                        $str=strrchr($def2, '/');
                        $str2=strripos($def2, '/');
                        $str3 = substr($def2,-$strlen,$str2); 
                        rename( $def2, $str3.'/Index_index.html' );

                    }
                }else{
                    $file = glob('./themes/Home/*/Index_index.html', GLOB_BRACE); //匹配所有文件
                    foreach ($file as $key => $def) {
                        
                        $strlen = strlen($def);
                        $str=strrchr($def, '/');
                        $str2=strripos($def, '/');
                        $str3 = substr($def,-$strlen,$str2); 
                        rename( $def, $str3.'/Index_index.xhtml' );

                    }
                    $file2 = glob('./themes/Home/*/Index_index.shtml', GLOB_BRACE); //匹配所有文件
                    foreach ($file2 as $key => $def2) {
                        
                        $strlen = strlen($def2);
                        $str=strrchr($def2, '/');
                        $str2=strripos($def2, '/');
                        $str3 = substr($def2,-$strlen,$str2); 
                        rename( $def2, $str3.'/Index_index.html' );

                    }
                }
            }
        }

        $sta = false;

        $wheres[varname]="CHANGE_INDEX";
        $sts = M('Sysconfig')->where($wheres)->find();
        if($sts[value] != $_POST[CHANGE_INDEX]){
            $c_index=1;
        }
        foreach($_POST as $key=>$value){
            $data['value'] = $value;
            $f = M('Sysconfig')->where("varname='".$key."'")->save($data);
            if ($f) {
                $sta = true;
            }
        }
        savecache('Sysconfig');

       
        
        if($sta){
            $this->success('保存成功!');
        }else{
            $this->error('没有发生更改!');
        }
    }

    public function testmail()
    {
        $mailto = I('get.mail_to');
        $message = '这是一封测试邮件';
        $r = sendmail($mailto,$this->Config['site_name'],$message);
        if($r === true){
            $this->success('邮件发送成功！');
        }else{
            $this->error('邮件发送失败！'.$r);
        }
    }
}