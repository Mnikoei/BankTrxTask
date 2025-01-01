<?php

return [
    'notification' => [
        'sms' => [
            'debit_message' => ':user withdraw -:amount',
            'credit_message' => ':user recharge +:amount',
        ]
    ]
];
