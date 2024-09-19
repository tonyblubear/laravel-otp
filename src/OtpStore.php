<?php

namespace Blubear\LaravelOtp;

use DateTime;
use Illuminate\Support\Facades\Cache;

class OtpStore
{
    const STORE_KEY = 'otp';
    protected string $identifier;
    /**
     * Save OTP in Cache
     *
     * @param array|integer $otp
     * @param integer $expired_in
     * @return void
     */
    public function put(array $otp, int|DateTime $expired_in = 5)
    {
        Cache::put(
            $this->getIdentifier(),
            $otp,
            $expired_in
        );
    }
    /**
     * Get Otp in cache
     *
     * @return array|null
     */
    public function get(): array|null
    {
        return Cache::get($this->getIdentifier()) ?: null;
    }
    /**
     * Validate active OTP
     *
     * @return boolean
     */
    public function has(): bool
    {
        return Cache::has($this->getIdentifier()) ?: false;
    }

    /**
     * Remove Otp from cache
     *
     * @return void
     */
    public function clear()
    {
        Cache::forget($this->getIdentifier());
    }
    /**
     * Get key identifier
     *
     * @return string
     */
    function getKey(): string
    {
        return $this->identifier;
    }

    /**
     * Set the value of identifier
     *
     * @return  self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Get the value of identifier
     */
    public function getIdentifier()
    {
        if (!isset($this->identifier)) {
            throw new \Exception("No OTP identifier set!");
        }
        return static::STORE_KEY . '_' . $this->identifier;
    }
}
