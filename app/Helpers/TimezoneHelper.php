<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    public const DEFAULT_TIMEZONE = 'Asia/Karachi';

    /**
     * Get the timezone for display/conversion for the given user.
     * Uses the user's stored timezone if set; otherwise falls back to Asia/Karachi.
     * Does not modify or write to the database.
     *
     * @param object|null $user User model (studentregistration or tutorregistration) with optional timezone attribute.
     *                          If null, uses session('userid').
     * @return string Valid timezone string (e.g. 'Asia/Karachi', 'America/New_York').
     */
    public static function userTimezone($user = null): string
    {
        $user = $user ?? session('userid');
        if ($user && !empty(trim((string) ($user->timezone ?? '')))) {
            $tz = trim((string) $user->timezone);
            try {
                new \DateTimeZone($tz);
                return $tz;
            } catch (\Exception $e) {
                // Invalid timezone in DB; fallback to default
            }
        }
        return self::DEFAULT_TIMEZONE;
    }

    /**
     * Parse a datetime (or date-only / time-only) from a given timezone and return a Carbon instance in the user's timezone.
     *
     * @param string|\DateTimeInterface|null $datetime Date/time string or object (e.g. UTC date + time from DB).
     * @param string $fromTz Timezone of the given datetime (e.g. 'UTC').
     * @param object|null $user User for target timezone; null = current session user.
     * @return Carbon Carbon instance in the user's timezone.
     */
    public static function toUserTz($datetime, string $fromTz = 'UTC', $user = null): Carbon
    {
        $tz = self::userTimezone($user);
        if ($datetime === null || $datetime === '') {
            return Carbon::now($tz);
        }
        $carbon = $datetime instanceof Carbon
            ? $datetime->copy()
            : Carbon::parse($datetime, $fromTz);
        return $carbon->setTimezone($tz);
    }

    /**
     * Format a datetime in the user's timezone.
     *
     * @param string|\DateTimeInterface|null $datetime Date/time from DB (e.g. UTC).
     * @param string $format Carbon format string (e.g. 'd/m/Y', 'h:i A').
     * @param string $fromTz Source timezone (e.g. 'UTC').
     * @param object|null $user User for target timezone; null = current session user.
     * @return string Formatted date/time string.
     */
    public static function formatInUserTz($datetime, string $format = 'd M Y h:i A', string $fromTz = 'UTC', $user = null): string
    {
        if ($datetime === null || $datetime === '') {
            return '';
        }
        return self::toUserTz($datetime, $fromTz, $user)->format($format);
    }

    /**
     * Convert a UTC date string to the user's timezone and return as Y-m-d.
     *
     * @param string|null $dateString Date string (e.g. from slot_bookings.date).
     * @param object|null $user User for target timezone.
     * @return string Date in user TZ as Y-m-d.
     */
    public static function dateToUserTz(?string $dateString, $user = null): string
    {
        if ($dateString === null || $dateString === '') {
            return '';
        }
        return self::toUserTz($dateString, 'UTC', $user)->toDateString();
    }

    /**
     * Convert a UTC time string to the user's timezone and return as H:i or H:i:s.
     *
     * @param string|null $timeString Time string (e.g. from slot_bookings.slot).
     * @param object|null $user User for target timezone.
     * @param bool $withSeconds Whether to include seconds.
     * @return string Time in user TZ.
     */
    public static function timeToUserTz(?string $timeString, $user = null, bool $withSeconds = false): string
    {
        if ($timeString === null || $timeString === '') {
            return '';
        }
        $carbon = self::toUserTz($timeString, 'UTC', $user);
        return $withSeconds ? $carbon->format('H:i:s') : $carbon->format('H:i');
    }
    public static function slotToUserTz($date, $time, $user = null): Carbon
    {
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time, 'UTC');
        return self::toUserTz($datetime, 'UTC', $user);
    }

    public static function formatDate($date, $format = 'd F, Y')
    {
        if (empty($date)) {
            return '';
        }

        return Carbon::parse($date)->format($format);
    }
}
