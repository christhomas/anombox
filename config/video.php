<?php

return [
    'minimumFileSize'   => env('FILE_MINIMUM_SIZE', 0),
    'maximumFileSize'   => env('FILE_MAXIMUM_SIZE', 0),
    'regexPattern'      => base64_decode(env('FILE_REGEX_PATTERN')),
];
