# admin-user

Management user dari panel admin. Module ini mengenal satu konfigurasi yang bertugas
menambahkan dropdown menu pada list user. Konfigurasi ini bisa digunakan untuk menambah
menu untuk masing-masing user. Contoh konfigurasi tersebut adalah sebagai berikut:

```php
return [
    'name' => 'Phun',
    ...,
    'admin-user' => [
        'inject-menu' => [
            'User posts' => [
                'icon'  => 'paper-plane-o',
                'perms' => 'read_post',
                'router' => 'adminPost',
                'params' => [],
                'query' => [
                    'user' => 'id'
                ]
            ]
        ]
    ]
];
```

Property  `icon` adalah icon menu tersebut. Property `perms` digunakan untuk mengecek
jika menu akan dimunculkan berdasarkan permissions, `router` adalah nama router untuk
membentuk link, `params` dan `query` akan diteruskan ke fungsi `router->to`
dimana array key akan digunakan untuk key parameter, dan array value digunakan
untuk menentukan nilai parameter dimana nilai tersebut diambil dari property user.