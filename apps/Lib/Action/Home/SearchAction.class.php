<?php

class SearchAction extends PublicAction
{

    public function index()
    {
        $keyword = $_GET['keyword'] = I('keyword');
        $modelid = $_GET['modelid'] = I('modelid', 3, 'intval');

        $this->assign($_REQUEST);

        $this->assign('seo_title', $this->Config['seo_title']);
        $this->assign('seo_keywords', $this->Config['seo_keywords']);
        $this->assign('seo_description', $this->Config['seo_description']);

        $modelname = $this->Model[$modelid]['tablename'];

        $where = array();
        $where['status'] = 1;
        $where['lang'] = LANG_ID;

        //2018-04-21 修改 全文查询
        $db = M($modelname);
        // $m = new Model();
        //用于生成查询条件
        // $table_columns = $m->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = '".C('DB_PREFIX').$modelname."' AND TABLE_SCHEMA = '".C('DB_NAME')."' AND DATA_TYPE NOT LIKE '%int%'");
        //把查询到的字段名数组维度降为二维
        // $table_columns_arr = array();
        // foreach ($table_columns as $key => $value) {
        //     foreach ($value as $k => $v) {
        //         $table_columns_arr[] = $v;
        //     }
        // }
        //条件生成
        // $array_where = implode('|', $table_columns_arr);
        $where['title'] = array('like',"%$keyword%");

        $count = $db->where($where)->count();
        if($count) {
            import("@.ORG.Page");
            $page = new Page($count, 10);
            $pages = $page->show();
            $page->parameter .= "&keyword=" . urlencode($keyword);
            $field = 'id,url,title,keywords,description,thumb,createtime';

            $list = $db->field($field)->where($where)->order('listorder desc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

            $this->assign('pages', $pages);
            $this->assign('list', $list);
        }
        $this->assign($_GET);
        $this->display();
    }
}