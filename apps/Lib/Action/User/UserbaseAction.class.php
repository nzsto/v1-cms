<?php

class UserbaseAction extends PublicAction
{

    function _initialize() {
        parent::_initialize();
        //检测用户是否登陆
        $this->checkLogin();
    }

}