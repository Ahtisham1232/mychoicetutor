<?php

namespace App\Helpers;

class CommonHelper
{
    /**
     * Ensure a stored mobile value is in E.164 for SMS/WhatsApp APIs.
     * If already E.164, return as-is; otherwise convert using default country.
     *
     * @param string      $mobile             Stored value (local or E.164)
     * @param string|null $defaultCountryCode Used when value is local format
     * @return string E.164 number
     */

    public static function ensureE164($mobile, $countryCode)
    {
        if (empty($mobile) || empty($countryCode)) {
            return null;
        }

        // remove all non-digits
        $mobile = preg_replace('/\D/', '', $mobile);

        // remove leading zeros
        $mobile = ltrim($mobile, '0');

        // remove non-digits from country code
        $countryCode = preg_replace('/\D/', '', $countryCode);

        // return in proper E.164 format
        return '+' . $countryCode . $mobile;
    }
}
