<?php

$accessControl = env('ACCESS_CONTROL', '');

try{
    $users = json_decode($accessControl, true, 512, JSON_THROW_ON_ERROR);
}catch(Exception $e){
    $users = json_decode(base64_decode($accessControl), true);
}

return [
    'users' => $users
];
