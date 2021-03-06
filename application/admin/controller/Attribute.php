<?php
// +----------------------------------------------------------------------
// | HeilPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.heilphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Jason <1878566968@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 属性控制器
 * @author huajie <banhuajie@163.com>
 * @modify Jason <1878566968@qq.com>
 */
class Attribute extends Admin {

    /**
     * 属性列表
     * @author huajie <banhuajie@163.com>
	 * @modify Jason <1878566968@qq.com>
     */
    public function index(){
        $model_id = Input('param.model_id');
        /* 查询条件初始化 */
        $map['model_id']    =   ['model_id','=',$model_id];

        $list = $this->getListing('Attribute', $map);
        int_to_string($list);

        // 记录当前列表页的cookie
        Cookie('__forward__',       $_SERVER['REQUEST_URI']);
        $this->assign('_list',      $list);
        $this->assign('model_id',   $model_id);
        $this->assign('meta_title','属性列表');
		return view();
    }

    /**
     * 新增页面初始化
     * @author huajie <banhuajie@163.com>
	 * @modify Jason <1878566968@qq.com>
     */
    public function add(){
        $model_id   =   Input('param.model_id');
        $model      =   db('Model')->field('title,name,field_group')->find($model_id);
        $this->assign('model',$model);
        $this->assign('info', array('model_id'=>$model_id));
        $this->assign('meta_title','新增属性');
		return view('edit');
    }

    /**
     * 编辑页面初始化
     * @author huajie <banhuajie@163.com>
	 * @modify Jason <1878566968@qq.com>
     */
    public function edit(){
        $id = Input('param.id/d',null);
        if(empty($id)){
            $this->error('参数不能为空！');
        }

        /*获取一条记录的详细数据*/
        $Model = db('Attribute');
        $data = $Model->field(true)->find($id);
        if(!$data){
            $this->error($Model->getError());
        }
        $model  =   db('Model')->field('title,name,field_group')->find($data['model_id']);
        $this->assign('model',$model);
        $this->assign('info', $data);
        $this->assign('meta_title','编辑属性');
		return view();
    }

    /**
     * 更新一条数据
     * @author huajie <banhuajie@163.com>
	 * @modify Jason <1878566968@qq.com>
     */
    public function update(){
        $res = model('Attribute')->edit();
        if(!$res){
            $this->error(model('Attribute')->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }

    /**
     * 删除一条数据
     * @author huajie <banhuajie@163.com>
	 * @modify Jason <1878566968@qq.com>
     */
    public function remove(){
        $id = Input('id');
        empty($id) && $this->error('参数错误！');

        $Model = model('Attribute');

        $info = $Model->getById($id);
        empty($info) && $this->error('该字段不存在！');

        //删除属性数据
        $res = $Model->destroy($id);

        //删除表字段
        $Model->deleteField($info);
        if(!$res){
            $this->error(model('Attribute')->getError());
        }else{
            //记录行为
            action_log('update_attribute', 'attribute', $id, UID);
            $this->success('删除成功', Url('index','model_id='.$info['model_id']));
        }
    }
}
