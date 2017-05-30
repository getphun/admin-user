DELETE FROM `user_perms_chain` WHERE `user_perms` IN (
    SELECT `id` FROM `user_perms` WHERE `name` IN (
        'create_user',
        'remove_user',
        'update_user',
        'update_user_password',
        'update_user_perms',
        'update_user_session'
    )
);

DELETE FROM `user_perms` WHERE `name` IN (
    'create_user',
    'remove_user',
    'update_user',
    'update_user_password',
    'update_user_perms',
    'update_user_session'
);