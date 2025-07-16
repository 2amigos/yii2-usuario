<?php

namespace Da\User\Helper;

use CBOR\Decoder;
use CBOR\Stream;
use CBOR\StringStream;

class UserEntityHelper
{
    public function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function utf8ize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        return $data;
    }
    //this function is used to extract the attestation type from the passkeys. the attestation type is the type of device used as the passkey provider
    public function extractAttestationFormat(string $attestationBase64Url): ?string
    {
        $binary = $this->base64UrlDecode($attestationBase64Url);
        $stream = new \CBOR\StringStream($binary);
        $decoder = \CBOR\Decoder::create();
        $object = $decoder->decode($stream);

        if (!($object instanceof \CBOR\MapObject)) {
            return null;
        }

        $reflection = new \ReflectionClass($object);
        $prop = $reflection->getProperty('data');
        $prop->setAccessible(true);
        $items = $prop->getValue($object);

        foreach ($items as $item) {
            $keyProp = (new \ReflectionClass($item))->getProperty('key');
            $keyProp->setAccessible(true);
            $key = $keyProp->getValue($item);

            $valueProp = (new \ReflectionClass($item))->getProperty('value');
            $valueProp->setAccessible(true);
            $value = $valueProp->getValue($item);

            $keyDataProp = (new \ReflectionClass($key))->getProperty('data');
            $keyDataProp->setAccessible(true);
            $keyString = $keyDataProp->getValue($key);

            if ($keyString === 'fmt') {
                $valueDataProp = (new \ReflectionClass($value))->getProperty('data');
                $valueDataProp->setAccessible(true);
                $valueString = $valueDataProp->getValue($value);

                return $valueString;
            }
        }
        return null;
    }
}
