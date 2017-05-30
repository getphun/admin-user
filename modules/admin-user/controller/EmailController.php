<?php
/**
 * Admin user mangement
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminUser\Controller;
use User\Model\User;
use UserEmail\Model\UserEmail as UEmail;

class EmailController extends \AdminUserController
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
        if(!$this->can_i->update_user || !module_exists('user-email'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['title']  = 'User Emails';
        $params['user']   = $user;
        $params['emails'] = UEmail::get(['user'=>$id], true, false, 'address');
        $params['success'] = false;
        
        if(false === ($form = $this->form->validate('admin-user-email')))
            return $this->respond('user/email', $params);
        
        $new = [ 'user' => $id, 'address' => $form->email ];
        UEmail::create($new);
        $params['emails'] = UEmail::get(['user'=>$id], true, false, 'address');
        
        $params['success'] = true;
        
        $this->respond('user/email', $params);
    }
    
    public function primaryAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-email'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $email_id = $this->param->email;
        $email = UEmail::get($email_id, false);
        if(!$email || $email->user != $id)
            return $this->show404();
        
        if($email->status == 2){
            UEmail::set(['status'=>2], [
                'status = 3 AND user = :id',
                'bind' => [ 'id' => $id ]
            ]);
            UEmail::set(['status'=>3], $email_id);
        }
        
        return $this->next('adminUserEmail', ['id'=>$id]);
    }
    
    public function removeAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-email'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $email_id = $this->param->email;
        $email = UEmail::get($email_id, false);
        if(!$email || $email->user != $id)
            return $this->show404();
        
        if($email->status != 3)
            UEmail::remove($email_id);
        
        return $this->next('adminUserEmail', ['id'=>$id]);
    }
    
    public function verifyAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-email'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $email_id = $this->param->email;
        $email = UEmail::get($email_id, false);
        if(!$email || $email->user != $id)
            return $this->show404();
        
        if($email->status == 1)
            UEmail::set(['status'=>2], $email_id);
        
        return $this->next('adminUserEmail', ['id'=>$id]);
    }
}