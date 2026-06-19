<?php

return [
    'api_key' => env('CLAUDE_API_KEY'),
    'model' => env('CLAUDE_MODEL', 'claude-opus-4-8'),
    'max_tokens' => (int) env('CLAUDE_MAX_TOKENS', 4096),
    'timeout' => (int) env('CLAUDE_TIMEOUT', 30),
    'base_url' => 'https://api.anthropic.com/v1',
    'api_version' => '2023-06-01',

    'cache' => [
        'ttl' => 86400, // 24 hours
        'prefix' => 'claude_analysis_',
    ],

    'rate_limit' => [
        'per_minute' => (int) env('AI_RATE_LIMIT_PER_MINUTE', 10),
    ],

    'retry' => [
        'times' => 3,
        'sleep' => 1000,
    ],
];
