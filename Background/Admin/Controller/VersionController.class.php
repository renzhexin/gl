<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 版本管理
 */
class VersionController extends BaseController
{
    /**
     * 版本列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key === ""){
            $model = D("Version");  
        }else{
            $where['title'] = array('like',"%$key%");
            $where['body'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = D("Version")->where($where);
        }
        
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15, array('key'=>$key));// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $member = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id DESC')->select();
        $this->assign('version', $member);
        $this->assign('page',$show);
        $this->assign('key', $key);
        $this->display();     
    }
    
	/**
     * 添加版本
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Version");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->success("用户添加成功", U('version/index'));
                } else {
                    $this->error("用户添加失败");
                }
            }
        }
    }
    /**
     * 更新管理员信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function update()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = D('Version')->find(I('id',"addslashes"));
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("Version");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                //验证密码是否为空   
                $data = I();
               /*  unset($data['password']);
                if(I('password') != ""){
                    $data['password'] = md5(I('password'));
                }
                //强制更改超级管理员用户类型
                if(C('SUPER_ADMIN_ID') == I('id')){
                    $data['type'] = 2;
                } */
                //更新
                if ($model->save($data)) {
                    $this->success("用户信息更新成功", U('version/index'));
                } else {
                    $this->error("未做任何修改,用户信息更新失败");
                }        
            }
        }
    }
    /**
     * 删除管理员
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
    	$id = intval($id);
    	if(C('SUPER_ADMIN_ID') == $id) $this->error("超级管理员不可禁用!");
        $model = M('Version');
        //查询status字段值
        $result = $model->find($id);
        //更新字段
        $data['id']=$id;
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($model->save($data)){
            $this->success("状态更新成功", U('version/index'));
        }else{
            $this->error("状态更新失败");
        }
    }
}
