#使用

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use kongClient\Client;
$config = [
    'gate_way_url' => '',
    'api_key' => '',
    'api_secret' => '',
    'client_name' => '',
    'link_tag' => '&',
];

$client = new Client($config);

$path = '';
$res = $client->request($path);
var_export($res);
```

