<?php

namespace Betnex\Utils;

class CallbackResponse
{
    public static function create(array $options = []): array
    {
        return [
            'success' => $options['success'] ?? true,
            'msg' => $options['msg']
                ?? 'Callback processed successfully',
            'handle' => $options['handle'] ?? true,
            'money' => (float)($options['money'] ?? 0)
        ];
    }
}