<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'header_logo',
        'footer_logo',
        'favicon',
        'phone',
        'email',
        'address',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        // QuickBooks API Configuration
        'quickbooks_client_id',
        'quickbooks_client_secret',
        'quickbooks_company_id',
        'quickbooks_environment',
        'quickbooks_access_token',
        'quickbooks_refresh_token',
        'quickbooks_enabled',
        // Bank API Configuration
        'bank_api_provider',
        'bank_api_key',
        'bank_api_secret',
        'bank_account_id',
        'bank_sync_enabled',
        // Square Payment
        'square_application_id',
        'square_access_token',
        'square_location_id',
        'square_environment',
        'square_enabled',
        // Instructor Bios
        'jayson_bio',
        'kenny_bio',
        // SMS / Twilio
        'sms_enabled',
        'twilio_sid',
        'twilio_token',
        'twilio_from_number',
    ];

    protected $casts = [
        'quickbooks_enabled' => 'boolean',
        'bank_sync_enabled' => 'boolean',
        'square_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];
}
