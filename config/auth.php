<?php

return [
    'users' => json_decode(env('ACCESS_CONTROL', '[]')),
];
