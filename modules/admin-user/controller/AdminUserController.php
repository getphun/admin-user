<?php
/**
 * Admin user controller main
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

class AdminUserController extends \AdminController
{
    
    public function next($next='adminUser', $pars=[]){
        $ref = $this->req->getQuery('ref');
        if(!$ref)
            $ref = $this->router->to($next, $pars);
        $this->redirect($ref);
    }
    
    public function respond($view, $params=[], $cache=null){
        $sidemenu = [
            [ 'adminUserSingle',    'Profile',          'user',         'update_user' ],
            [ 'adminUserPassword',  'Password',         'user',         'update_user_password' ],
            [ 'adminUserEmail',     'Email',            'user-email',   'update_user' ],
            [ 'adminUserPhone',     'Phone',            'user-phone',   'update_user' ],
            [ 'adminUserSocial',    'Social Account',   'user-social',  'update_user' ],
            [ 'adminUserPerms',     'Permissions',      'user',         'update_user_perms' ]
        ];
        
        $params['sidemenu'] = [];
        
        foreach($sidemenu as $menu){
            if(!module_exists($menu[2]) || !$this->can_i->{$menu[3]})
                continue;
            
            $smenu = (object)[
                'label' => $menu[1],
                'target'=> $menu[0],
                'active'=> false
            ];
            
            if($this->router->route['name'] == $menu[0])
                $smenu->active = true;
            
            $params['sidemenu'][] = $smenu;
        }
        
        parent::respond($view, $params, $cache);
    }
}