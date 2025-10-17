<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'huggingface' => [
        'token' => env('HUGGINGFACE_TOKEN'),
        'model' => env('HUGGINGFACE_MODEL', 'google/vit-base-patch16-224'),
        'caption_model' => env('HUGGINGFACE_CAPTION_MODEL', 'Salesforce/blip-image-captioning-base'),
        'secondary_model' => env('HUGGINGFACE_SECONDARY_MODEL', 'microsoft/resnet-50'),
        'zeroshot_model' => env('HUGGINGFACE_ZEROSHOT_MODEL', 'openai/clip-vit-base-patch32'),
    ],

    // Optional local Python classifier (no API key). When enabled, PHP will run
    // python/local_classifier.py to classify and caption images locally.
    'local_classifier' => [
        'enabled' => env('LOCAL_CLASSIFIER_ENABLED', false),
        'python_bin' => env('LOCAL_PYTHON_BIN', 'python'),
        'script_path' => env('LOCAL_CLASSIFIER_SCRIPT', base_path('python/local_classifier.py')),
        'top_k' => env('LOCAL_CLASSIFIER_TOPK', 5),
        'timeout' => env('LOCAL_CLASSIFIER_TIMEOUT', 60),
    ],
];
