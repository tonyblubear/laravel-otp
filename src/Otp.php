<?php

namespace Blubear\LaravelOtp;

use DateTime;

class Otp
{
    /**
     * Constant representing a successfully processed otp.
     *
     * @var string
     */
    public const OTP_EMPTY  = 'otp.empty';
    /**
     * Constant representing a successfully processed otp.
     *
     * @var string
     */
    public const OTP_INVALID  = 'otp.invalid';
    /**
     * Constant representing a successfully processed otp.
     *
     * @var string
     */
    public const OTP_EXPIRED  = 'otp.expired';
    /**
     * Constant representing a successfully processed otp.
     *
     * @var string
     */
    public const OTP_VALID  = 'otp.valid';
    /**
     * Length of the generated OTP
     *
     * @var int
     */
    protected ?int $length;
    /**
     * Format of the generated OTP
     *
     * @var string|null
     */
    protected ?string $format;
    /**
     * Expiration time of the generated OTP
     *
     * @var integer|DateTime
     */
    protected int|DateTime $expires = 5;
    /**
     * Prefix of the generated OTP
     *
     * @var string
     */
    protected $prefix = 'OTP_';
    /**
     * Store to save the OTP
     *
     * @param OptSetting $settings
     * @param OtpStore $store
     */
    public function __construct(
        protected OtpStore $store
    ) {
        $this->length = config('laravel-otp.length');
        $this->expires = config('laravel-otp.expires');
    }
    /**
     * Generate a new OTP
     *
     * @param string $identifier
     * @return array
     */
    public function generate(string $identifier): array
    {
        $this->store->setIdentifier($identifier);
        $token = $this->createCode();
        $expires = now()
            ->addMinutes($this->expires);
        $data = ['token' => $token, 'expires' => $expires];
        $this->store->put($data, now()->addMinutes($this->expires + 2));
        return $data;
    }
    /**
     * Create a random code
     *
     * @return string
     */
    private function createCode(): string
    {
        $characters = '0123456789';
        $length = strlen($characters);
        $pin = '';
        for ($i = 0; $i < $this->length; $i++) {
            $pin .= $characters[rand(0, $length - 1)];
        }
        return $pin;
    }

    /**
     * Set length of the generated OTP
     *
     * @param  int  $length  Length of the generated OTP
     *
     * @return  self
     */
    public function setLength(int $length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Set the value of expires
     *
     * @return  self
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }
    /**
     * Set the value of prefix
     *
     * @param string|null $identifier
     * @return self
     */
    public function identifier(?string $identifier): self
    {
        $this->store->setIdentifier($identifier);
        return $this;
    }
    /**
     * validate if the token exists
     *
     * @param string $identifier
     * @return boolean
     */
    public function has(string $identifier): bool
    {
        $this->store->setIdentifier($identifier);
        return $this->store->has();
    }
    /**
     * Validate if the token has expired
     *
     * @param string $identifier
     * @return boolean
     */
    public function expired(string $identifier): bool
    {
        $this->store->setIdentifier($identifier);
        return $this->isExpired();
    }
    /**
     * Validate if the token has expired
     *
     * @return boolean
     */
    public function isExpired(): bool
    {
        $token =  $this->store->get();
        return isset($token['expires']) ? now()->isAfter($token['expires']) : false;
    }
    /**
     * Validate the OTP
     *
     * @param string $identifier
     * @param string|int $code
     * @return object|array
     */
    public function validate(string $identifier, string|int $code): object|array
    {
        $this->store->setIdentifier($identifier);
        $token = $this->store->get();

        if (!$this->store->has()) {
            return (object) [
                'status' => false,
                'message' => static::OTP_EMPTY,
            ];
        }
        if ($this->isExpired()) {
            return (object) [
                'status' => false,
                'message' => static::OTP_EXPIRED,
            ];
        }
        if ($token['token'] != $code) {
            return (object) [
                'status' => false,
                'message' => static::OTP_INVALID,
            ];
        }
        $this->store->clear();
        return (object) [
            'status' => true,
            'message' => static::OTP_VALID,
        ];
    }

    /**
     * Get the value of expires
     *
     * @return  integer|DateTime
     */
    public function getExpires(string $identifier)
    {
        $this->store->setIdentifier($identifier);
        $expires = $this->store->get();
        return $expires['expires'];
    }
}
