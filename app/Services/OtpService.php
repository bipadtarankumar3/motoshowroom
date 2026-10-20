<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Default OTP during development / testing.
     */
    public const DEFAULT_OTP = '123456';

    /**
     * Generate an OTP for a user.
     * Currently defaults to '123456', ready for dynamic generator/SMS/Email gateway implementation.
     */
    public function generateOtp(?User $user = null): string
    {
        // Default OTP for now as requested
        $otp = self::DEFAULT_OTP;

        // Future dynamic generation helper example:
        // $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        Log::info('OTP generated for login', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'otp' => $otp,
        ]);

        return $otp;
    }

    /**
     * Verify whether the entered OTP matches the expected OTP.
     */
    public function verifyOtp(string $enteredOtp, ?string $expectedOtp): bool
    {
        if (empty($enteredOtp) || empty($expectedOtp)) {
            return false;
        }

        return hash_equals((string) $expectedOtp, trim((string) $enteredOtp));
    }
}
