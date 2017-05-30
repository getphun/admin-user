<?php
/**
 * Admin user mangement
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminUser\Controller;
use User\Model\User;
use UserSocial\Model\UserSocial as USocial;

class SocialController extends \AdminUserController
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
        if(!$this->can_i->update_user || !module_exists('user-social'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['title']  = 'User Social Accounts';
        $params['user']   = $user;
        $params['socials'] = USocial::get(['user'=>$id], true, false, 'page');
        
        $this->respond('user/social', $params);
    }
    
    public function editAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-social'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $social_id = $this->param->social;
        if($social_id){
            $social = USocial::get($social_id, false);
            if(!$social || $social->user != $user->id)
                return $this->show404();
        }else{
            $social = new \stdClass();
        }
        
        $params = $this->_defaultParams();
        $params['title']  = 'Create User Social Account';
        $params['user']   = $user;
        $params['social'] = $social;
        $params['jses'] = ['js/admin-user.js'];
        
        if(false === ($form = $this->form->validate('admin-user-social', $social)))
            return $this->respond('user/social-edit', $params);
        
        if($social_id){
            USocial::set($form, $social_id);
        }else{
            $form->user = $user->id;
            $form->id = USocial::create($form);
        }
        
        $this->next('adminUserSocial', ['id'=>$user->id]);
    }
    
    public function removeAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->update_user || !module_exists('user-social'))
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $social_id = $this->param->social;
        $social = USocial::get($social_id, false);
        if(!$social || $social->user != $user->id)
            return $this->show404();
        
        USocial::remove($social_id);
        
        $this->next('adminUserSocial', ['id'=>$user->id]);
    }
}