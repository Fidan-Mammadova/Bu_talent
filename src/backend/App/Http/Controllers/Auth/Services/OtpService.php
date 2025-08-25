<?php

namespace App\Http\Controllers\Auth\Services;

use App\Http\Controllers\Auth\Models\Otp;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Auth\Mail\OtpMail;

class OtpService
{
    /**
     * Generate a new OTP for the given email
     *
     * @param string $email
     * @return Otp
     */
    public function generateOtp(string $email): Otp
    {
        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Set expiration time to 5 minutes from now
        $expiresAt = now()->addMinutes(5);

        // Create new OTP record
        $otpRecord = Otp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'is_used' => false
        ]);

        // For testing purposes, we'll skip sending the email
        // and just return the OTP in the response
        return $otpRecord;
    }

    /**
     * Validate the OTP for the given email
     *
     * @param string $email
     * @param string $otp
     * @return bool
     */
    public function validateOtp(string $email, string $otp): bool
    {
        $otpRecord = Otp::where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return false;
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        return true;
    }

    /**
     * Send OTP via email
     *
     * @param string $email
     * @param string $otp
     * @return void
     */
    private function sendOtpEmail(string $email, string $otp): void
    {
        Mail::to($email)->send(new OtpMail($otp));
    }
} 