<?php
/**
 * Admin user mangement
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminUser\Controller;
use User\Model\User;
use User\Model\UserSession as USession;
use UserProperty\Model\UserProperty as UProperty;

class UserController extends \AdminUserController
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
        if(!$this->can_i->read_user)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['title'] = 'Users';
        $params['users'] = [];
        
        $cond = ['id' => ['__op', '!=', '1'], 'status' => ['__op', '>', '0']];
        $pcond= [];
        
        $form = $this->form->validate('admin-user-index', null, 'GET');
        
        if($form){
            if(!$form->status)
                unset($form->status);
            elseif($form->status == -1)
                $form->status = 0;
                    
            if(!$form->q)
                unset($form->q);
            foreach($form as $field => $value)
                $cond[$field] = $pcond[$field] = $value;
        }
        
        $rpp = 20;
        $page= $this->req->getQuery('page', 1);
        
        $users = User::get($cond, $rpp, $page, 'fullname');
        if($users)
            $params['users'] = $users;
        
        $params['total'] = $total = User::count($cond);
        if($total > $rpp)
            $params['pagination'] = \calculate_pagination($page, $rpp, $total, 10, $pcond);
        
        $this->respond('user/index', $params);
    }
    
    public function passwordAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user_password)
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['title'] = 'User Password';
        $params['user']  = $user;
        $params['saved'] = false;
        $params['jses'] = ['js/admin-user.js'];
        $params['csses']= ['css/admin-user.css'];
        
        if(false === ($form = $this->form->validate('admin-user-password', $user)))
            return $this->respond('user/password', $params);
        
        // let validate both submited password
        if($form->retype_password != $form->new_password){
            $this->form->setError('retype_password', 'Both password don\'t match');
            return $this->respond('user/password', $params);
        }
        
        $new_password = $this->user->genPassword($form->new_password);
        User::set(['password'=>$new_password], $id);
        
        $params['saved'] = true;
        
        if($form->truncate_session){
            USession::remove([
                'user = :user',
                'bind' => [
                    'user' => $id
                ]
            ]);
        }
        
        return $this->respond('user/password', $params);
    }
    
    public function profileAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user)
            return $this->show404();
        
        $id = $this->param->id;
        if($id){
            $user = User::get($id, false);
            if(!$user || $user->id == 1)
                return $this->show404();
        }else{
            $user = new \stdClass();
            $user->id = 0;
            $user->name = '';
            $user->fullname = '';
            $user->status = 1;
        }
        
        $params = $this->_defaultParams();
        $params['title'] = 'User Profile';
        $params['user'] = $user;
        $params['saved'] = false;
        $params['created'] = false;
        $params['form'] = [];
        
        $form_validation = $this->config->form['admin-user-profile'];
        
        if(module_exists('user-property')){
            $user_properties = $this->config->user_property;
            if($user_properties)
                $form_validation = array_replace($form_validation, $user_properties);
            
            $this->config->set('form', 'admin-user-profile', $form_validation);
            
            if($id){
                $user_prop = UProperty::get(['user'=>$id]);
                $user_prop_current = [];
                if($user_prop){
                    foreach($user_prop as $prop){
                        $user->{$prop->name} = $prop->value;
                        $user_prop_current[$prop->name] = $prop->value;
                    }
                }
            }
        }
        
        $params['form'] = $form_validation;
        
        if(false === ($form = $this->form->validate('admin-user-profile', $user)))
            return $this->respond('user/profile', $params);
        
        // user object
        $udiff = [];
        if(isset($form->name)){
            $form->name = strtolower($form->name);
            if($form->name != $user->name)
                $udiff['name'] = $user->name = $form->name;
        }
        if(isset($form->fullname) && $form->fullname != $user->fullname)
            $udiff['fullname'] = $user->fullname = $form->fullname;
        if(isset($form->status) && $user->status != $form->status)
            $udiff['status'] = $user->status = $form->status;
        
        if($user){
            if(!$id){
                $params['created'] = true;
                $user->id = User::create($udiff);
            }else{
                User::set($udiff, $id);
            }
        }
        
        // user profiles
        if(module_exists('user-property')){
            $user_profile_update = [];
            $user_profile_insert = [];
            
            foreach($user_properties as $field => $args){
                if(!isset($form->$field))
                    continue;
                if(!isset($user_prop_current[$field]))
                    $user_profile_insert[$field] = $form->$field;
                elseif($user_prop_current[$field] != $form->$field)
                    $user_profile_update[$field] = $form->$field;
            }
            
            if($user_profile_insert){
                $insb = [];
                foreach($user_profile_insert as $field => $value)
                    $insb[] = ['user' => $user->id, 'name' => $field, 'value' => $value];
                UProperty::createMany($insb);
            }
            
            if($user_profile_update){
                foreach($user_profile_update as $field => $value)
                    UProperty::set(['value' => $value], ['user'=>$user->id, 'name'=>$field]);
            }
        }
        
        $params['saved'] = true;
        
        $this->respond('user/profile', $params);
    }
    
    public function removeAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->remove_user)
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->next();
        
        if($user->status != 0)
            User::set(['status'=>0], $user->id);
        
        $this->next();
    }
}