<?php
/**
 * admin-user config file
 * @package admin-user
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'admin-user',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/admin-user',
    '__files' => [
        'modules/admin-user' => [ 'install', 'remove', 'update' ],
        'theme/admin/user'   => [ 'install', 'remove', 'update' ],
        'theme/admin/static/js/admin-user.js' => [ 'install', 'remove', 'update' ],
        'theme/admin/static/css/admin-user.css' => [ 'install', 'remove', 'update' ]
    ],
    '__dependencies' => [
        'admin'
    ],
    '_services' => [],
    '_autoload' => [
        'classes' => [
            'AdminUser\\Controller\\UserController' => 'modules/admin-user/controller/UserController.php',
            'AdminUser\\Controller\\EmailController' => 'modules/admin-user/controller/EmailController.php',
            'AdminUser\\Controller\\PhoneController' => 'modules/admin-user/controller/PhoneController.php',
            'AdminUser\\Controller\\SocialController' => 'modules/admin-user/controller/SocialController.php',
            'AdminUser\\Controller\\PermsController' => 'modules/admin-user/controller/PermsController.php',
            'AdminUserController' => 'modules/admin-user/controller/AdminUserController.php'
        ],
        'files' => []
    ],
    
    '_routes' => [
        'admin' => [
            'adminUser' => [
                'rule' => '/user',
                'handler' => 'AdminUser\\Controller\\User::index'
            ],
            'adminUserPassword' => [
                'rule' => '/user/:id/password',
                'handler' => 'AdminUser\\Controller\\User::password'
            ],
            'adminUserRemove' => [
                'rule' => '/user/:id/remove',
                'handler' => 'AdminUser\\Controller\\User::remove'
            ],
            'adminUserSingle' => [
                'rule' => '/user/:id',
                'handler' => 'AdminUser\\Controller\\User::profile'
            ],
            
            'adminUserPhone' => [
                'rule' => '/user/:id/phone',
                'handler' => 'AdminUser\\Controller\\Phone::index'
            ],
            'adminUserPhonePrimary' => [
                'rule' => '/user/:id/phone/:phone/primary',
                'handler' => 'AdminUser\\Controller\\Phone::primary'
            ],
            'adminUserPhoneRemove' => [
                'rule' => '/user/:id/phone/:phone/remove',
                'handler' => 'AdminUser\\Controller\\Phone::remove'
            ],
            
            'adminUserEmail' => [
                'rule' => '/user/:id/email',
                'handler' => 'AdminUser\\Controller\\Email::index'
            ],
            'adminUserEmailPrimary' => [
                'rule' => '/user/:id/email/:email/primary',
                'handler' => 'AdminUser\\Controller\\Email::primary'
            ],
            'adminUserEmailRemove' => [
                'rule' => '/user/:id/email/:email/remove',
                'handler' => 'AdminUser\\Controller\\Email::remove'
            ],
            'adminUserEmailVerify' => [
                'rule' => '/user/:id/email/:email/verify',
                'handler' => 'AdminUser\\Controller\\Email::verify'
            ],
            
            'adminUserPerms' => [
                'rule' => '/user/:id/perms',
                'handler' => 'AdminUser\\Controller\\Perms::index'
            ],
            
            'adminUserSocial' => [
                'rule' => '/user/:id/social',
                'handler' => 'AdminUser\\Controller\\Social::index'
            ],
            'adminUserSocialEdit' => [
                'rule' => '/user/:id/social/:social',
                'handler' => 'AdminUser\\Controller\\Social::edit'
            ],
            'adminUserSocialRemove' => [
                'rule' => '/user/:id/social/:social/remove',
                'handler' => 'AdminUser\\Controller\\Social::remove'
            ]
        ]
    ],
    
    'admin' => [
        'menu' => [
            'user'  => [
                'label'     => 'User',
                'icon'      => 'users',
                'order'     => 100,
                'submenu'   => [
                    'all-users' => [
                        'label'     => 'All Users',
                        'target'    => 'adminUser',
                        'perms'     => 'read_user'
                    ]
                ]
            ]
        ]
    ],
    
    'form' => [
        'admin-user-email' => [
            'email' => [
                'type'  => 'email',
                'label' => 'Email',
                'rules' => [
                    'required'  => true,
                    'email'     => true,
                    'unique'    => [
                        'model' => 'UserEmail\\Model\\UserEmail',
                        'field' => 'address'
                    ]
                ]
            ]
        ],
        
        'admin-user-phone' => [
            'phone' => [
                'type'  => 'phone',
                'label' => 'Phone',
                'rules' => [
                    'required'  => true,
                    'regex'     => '!^\+([0-9- ]+)[0-9]$!',
                    'unique'    => [
                        'model' => 'UserPhone\\Model\\UserPhone',
                        'field' => 'number'
                    ]
                ]
            ]
        ],
        
        'admin-user-index' => [
            'q' => [
                'type'      => 'search',
                'label'     => 'Find user',
                'nolabel'   => true,
                'rules'     => []
            ],
            'status' => [
                'type'      => 'select',
                'label'     => 'Status',
                'nolabel'   => true,
                'options'   => [
                    0   => 'Active',
                    1   => 'Unverified',
                    2   => 'Verified',
                    3   => 'Official'
                ],
                'rules'     => []
            ]
        ],
        
        'admin-user-password' => [
            'new_password' => [
                'type'  => 'password',
                'label' => 'New Password',
                'attrs' => [
                    'data-meter' => 'yup'
                ],
                'rules' => [
                    'required'  => true,
                    'length'    => [
                        'min' => 6
                    ]
                ]
            ],
            'retype_password' => [
                'type'  => 'password',
                'label' => 'Retype Password',
                'rules' => [
                    'required' => true
                ]
            ],
            'truncate_session' => [
                'type'  => 'checkbox',
                'label' => 'Log out the user from everywhere',
                'rules' => []
            ]
        ],
        
        'admin-user-profile' => [
            'name' => [
                'type'  => 'text',
                'label' => 'Username',
                'rules' => [
                    'required' => true,
                    'alnumdash' => true,
                    'unique' => [
                        'model' => 'User\\Model\\User',
                        'field' => 'name',
                        'self'  => [
                            'uri' => 'id',
                            'field'   => 'id'
                        ]
                    ]
                ],
                'form-position' => 'left'
            ],
            'fullname' => [
                'type'  => 'text',
                'label' => 'Fullname',
                'rules' => [
                    'required' => true
                ],
                'form-position' => 'left'
            ],
            'status' => [
                'type'      => 'select',
                'label'     => 'Status',
                'nolabel'   => true,
                'options'   => [
                    1   => 'Unverified',
                    2   => 'Verified',
                    3   => 'Official'
                ],
                'rules'     => [],
                'form-position' => 'left'
            ]
        ],
        
        'admin-user-social' => [
            'page'  => [
                'type'    => 'url',
                'label'   => 'Social Account Page',
                'rules'   => [
                    'url'   => true
                ]
            ],
            'vendor' => [
                'type'  => 'select',
                'label' => 'Vendor',
                'rules' => [],
                'options' => [
                    'facebook'      => 'Facebook',
                    'instagram'     => 'Instagram',
                    'linkedin'      => 'LinkedIn',
                    'myspace'       => 'MySpace',
                    'pinterest'     => 'Pinterest',
                    'plus.google'   => 'Google Plus',
                    'soundcloud'    => 'SoundCloud',
                    'tumblr'        => 'Tumblr',
                    'twitter'       => 'Twitter',
                    'wordpress'     => 'Wordpress',
                    'youtube'       => 'Youtube'
                ]
            ]
        ]
    ]
];