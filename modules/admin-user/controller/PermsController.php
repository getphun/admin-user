<?php
/**
 * Admin user phone mangement
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminUser\Controller;

use User\Model\User;
use Admin\Model\UserPerms as UPerms;
use Admin\Model\UserPermsChain as UPChain;

class PermsController extends \AdminUserController
{

    private function _defaultParams(){
        return [
            'title'             => 'Users',
            'nav_title'         => 'User',
            'active_menu'       => 'User',
            'active_submenu'    => 'All Users',
            'pagination'        => []
        ];
    }
    
    public function indexAction(){
        if(!$this->user->login)
            return $this->show404();
        if(!$this->can_i->update_user_perms)
            return $this->show404();
        
        $id = $this->param->id;
        $user = User::get($id, false);
        if(!$user || $user->id == 1)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['jses']         = [ '/js/admin-user.js' ];
        $params['title']        = 'User Permissions';
        $params['success']      = false;
        $params['user']         = $user;
        
        $params['grouped_perms'] = [];
        $params['all_roles']     = [];
        $params['user_perms']    = [];
        
        $all_perms = null;
        
        // All user perms
        $user_perms = UPChain::get(['user'=>$user->id]);
        if($user_perms)
            $user_perms = array_column($user_perms, 'user_perms');
        else
            $user_perms = [];
        $params['user_perms'] = $user_perms;
        
        // All my perms
        $my_perms = UPChain::get(['user'=>$this->user->id]);
        $my_perms = $my_perms ? array_column($my_perms, 'user_perms') : [];
        
        $processed_perms = [];
        
        // All permission
        if(!$all_perms)
            $all_perms = UPerms::get([], true, false, 'name');
        
        if($all_perms){
            $grouped_perms   = [];
            $all_roles       = [];
            
            foreach($all_perms as $perm){
                if(!in_array($perm->id, $my_perms) && $this->user->id != 1)
                    continue;
                $grouped_perms[$perm->group][] = $perm;
                $processed_perms[] = $perm;
                
                $roles = explode(' ', $perm->role);
                $all_roles = array_merge($all_roles, $roles);
            }
            
            ksort($grouped_perms);
            $params['grouped_perms'] = $grouped_perms;
            
            $all_roles = array_unique($all_roles);
            sort($all_roles);
            $params['all_roles'] = $all_roles;
        }
        
        if($this->req->method == 'POST'){
            $to_insert = [];
            $to_remove = [];
            
            foreach($processed_perms as $perm){
                $exists = $this->req->get($perm->name);
                
                if($exists){
                    if(!in_array($perm->id, $user_perms))
                        $to_insert[] = $perm->id;
                }else{
                    if(in_array($perm->id, $user_perms))
                        $to_remove[] = $perm->id;
                }
            }
            
            if($to_remove){
                UPChain::remove([
                    'user' => $user->id,
                    'user_perms' => $to_remove
                ]);
                
                $params['user_perms'] = array_diff($params['user_perms'], $to_remove);
            }
            
            if($to_insert){
                $rows = [];
                foreach($to_insert as $pid){
                    $rows[] = [
                        'user' => $user->id,
                        'user_perms' => $pid
                    ];
                }
                
                UPChain::createMany($rows);
                
                $params['user_perms'] = array_merge($params['user_perms'], $to_insert);
                $params['user_perms'] = array_unique($params['user_perms']);
            }
            
            $params['success'] = true;
        }
        
        return $this->respond('user/permission', $params);
    }
}