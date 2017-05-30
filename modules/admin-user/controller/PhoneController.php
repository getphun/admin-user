<?php
/**
 * Admin user mangement
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminUser\Controller;
use User\Model\User;
use UserPhone\Model\UserPhone as UPhone;

class PhoneController extends \AdminUserController
{

    private function _defaultParams(){
        return [
            'title'             => 'Users',
            'nav_title'         => 'User',
            'active_menu'       => 'user',
            'active_submenu'    => 'all-users',
            'pagination'        => []
        ];
    }
    
    public function indexAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-phone'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['title']  = 'User Phones';
        $params['user']   = $user;
        $params['phones'] = UPhone::get(['user'=>$id], true, false, 'number');
        $params['success'] = false;
        
        if(false === ($form = $this->form->validate('admin-user-phone')))
            return $this->respond('user/phone', $params);
        
        $new = [ 'user' => $id, 'number' => $form->phone ];
        UPhone::create($new);
        $params['phones'] = UPhone::get(['user'=>$id], true, false, 'number');
        
        $params['success'] = true;
        
        $this->respond('user/phone', $params);
    }
    
    public function primaryAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-phone'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $phone_id = $this->param->phone;
        $phone = UPhone::get($phone_id, false);
        if(!$phone || $phone->user != $id)
            return $this->show404();
        
        if($phone->status == 1){
            UPhone::set(['status'=>1], [
                'status = 3 AND user = :id',
                'bind' => [ 'id' => $id ]
            ]);
            UPhone::set(['status'=>3], $phone_id);
        }
        
        return $this->next('adminUserPhone', ['id'=>$id]);
    }
    
    public function removeAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-phone'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $phone_id = $this->param->phone;
        $phone = UPhone::get($phone_id, false);
        if(!$phone || $phone->user != $id)
            return $this->show404();
        
        if($phone->status != 3)
            UPhone::remove($phone_id);
        
        return $this->next('adminUserPhone', ['id'=>$id]);
    }
}