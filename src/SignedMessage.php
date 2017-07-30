<?php
namespace Plexo\Sdk;

class SignedMessage {

    protected $fingerprint;
    protected $object;
    protected $utcUnixTimeExpiration;
    protected $signature;

    /**
     * 
     * @param (Message|string) $message
     */
    public function __construct($message = null, $fingerprint = null, $utcUnixTimeExpiration = null, $signature = null)
    {
        if (is_string($message)) {
            $this->createFromString($message);
        }
        $this->object = $message;
        $this->fingerprint = $fingerprint;
        $this->utcUnixTimeExpiration = $utcUnixTimeExpiration;
        $this->signature = $signature;
    }

    public function __set($name, $value)
    {
        $method_name = 'set' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            call_user_func([$this, $method_name], $value);
        }
    }

    public function setExpirationTime($expirationTime)
    {
        $this->utcUnixTimeExpiration = $expirationTime;
    }

    /**
     * 
     * @param string $json
     * @return \self
     */
    public static function fromJson($json)
    {
        $signed_json = json_decode($json, true);
        $signature = $signed_json['Signature'];
        $fingerprint = $signed_json['Object']['Fingerprint'];
        $utcUnixTimeExpiration = $signed_json['Object']['UTCUnixTimeExpiration'];
        $object = array_key_exists('Object', $signed_json['Object']) ? $signed_json['Object']['Object'] : null;
var_dump($object, $fingerprint, $utcUnixTimeExpiration, $signature);
        return new self($object, $fingerprint, $utcUnixTimeExpiration, $signature);
    }
    
    public function toArray()
    {
        $arr = [
            'Object' => [
                'Fingerprint' => strtoupper($this->fingerprint),
                'UTCUnixTimeExpiration' => $this->utcUnixTimeExpiration,//(time() + 600),
            ],
            'Signature' => $this->signature,
        ];
        if ($this->object) {
            if ($this->object instanceof Exception\PlexoException) {
                $arr['Object']['Object'] = [
                    'ResultCode' => $this->object->getCode(),
                    'ErrorMessage' => $this->object->getMessage(),
                ];
            } else {
                $arr['Object']['Object'] = is_array($this->object) ? $this->object : $this->object->toArray();
            }
        }
        return $arr;
    }

    public function getSignatureBaseString()
    {
        $message = $this->toArray();
        $message['Object'] = Utilities\functions\array_filter_recursive($message['Object'], function ($v) {
            return !is_null($v);
        });
        Utilities\functions\ksortRecursive($message['Object']);
        return str_replace('\/', '/', json_encode($message['Object']));
    }

//    public function sign($expirationTime = null, $cert_filename = null, $cert_passphrase = null)
    public function sign($cert, $expirationTime = null)
    {
        $this->fingerprint = $cert->fingerprint;
        if ($expirationTime || is_null($this->utcUnixTimeExpiration)) {
            $this->setExpirationTime(($expirationTime ? $expirationTime : (time() + 600)));
        }
        $base_string = $this->getSignatureBaseString();
        openssl_sign($base_string, $signature, $cert->pkey, OPENSSL_ALGO_SHA512);
        $this->signature = base64_encode($signature);
    }
    
    public function verify($cert)
    {
        $base_string = $this->getSignatureBaseString();
        return openssl_verify($base_string, base64_decode($this->signature), $cert->cert, OPENSSL_ALGO_SHA512);
    }

    public function __toString()
    {
        return str_replace('\/', '/', json_encode($this->toArray()));
    }
}