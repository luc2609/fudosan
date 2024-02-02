<?php
// rule password
define(
    'PASSWORD_RULE',
    [
        'required',
        'min:8', 'max:20',
        'regex:/^[A-Za-z0-9]*([0-9][A-Za-z0-9]*[a-zA-Z]|[a-zA-Z][A-Za-z0-9]*[0-9])[A-Za-z0-9]*$/'
    ]
);
