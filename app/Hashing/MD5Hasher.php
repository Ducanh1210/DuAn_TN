<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher;

class MD5Hasher implements Hasher
{
    public function info($hashedValue)
    {
        return [
            'algo' => 'md5',
            'algoName' => 'md5',
            'options' => [],
        ];
    }

    public function make($value, array $options = [])
    {
        return md5($value);
    }

    public function check($value, $hashedValue, array $options = [])
    {
        return $this->make($value) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }
}
