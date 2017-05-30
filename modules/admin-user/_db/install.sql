INSERT IGNORE INTO `user_perms` ( `name`, `group`, `role`, `about` ) VALUES
    ( 'create_user',            'User', 'Management',       'Allow user to create new user' ),
    ( 'remove_user',            'User', 'Management',       'Allow user to remove exists user' ),
    ( 'update_user',            'User', 'Management',       'Allow user to update exists user' ),
    ( 'update_user_password',   'User', 'Management',       'Allow user to update exists user password' ),
    ( 'update_user_perms',      'User', 'Management',       'Allow user to update exists user permissions' ),
    ( 'update_user_session',    'User', 'Administrator',    'Allow user to login as other user' );