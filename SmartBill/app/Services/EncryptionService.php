<?php

namespace App\Services;

class EncryptionService
{
    protected string $ciphering = "AES-128-CTR";
    protected int $options = 0;
    protected string $encryption_iv = '1234567890111213';
    protected string $encryption_key = "vcard_system";

    public function encrypt($data): string
    {
        return openssl_encrypt($data, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
    }

    public function decrypt($data): string
    {
        return openssl_decrypt($data, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
    }
}
