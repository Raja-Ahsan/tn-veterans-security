@extends('admin.layouts.master')

@section('title', 'Site Settings')
@section('page-title', 'Site Settings')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- Logo Section -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Logos & Favicon</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Header Logo -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Header Logo</label>
                    @if(isset($settings) && $settings && $settings->header_logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $settings->header_logo) }}" alt="Header Logo" class="h-20 w-auto object-contain border rounded p-2">
                        </div>
                    @endif
                    <input type="file" name="header_logo" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('header_logo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer Logo -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Footer Logo</label>
                    @if(isset($settings) && $settings && $settings->footer_logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $settings->footer_logo) }}" alt="Footer Logo" class="h-20 w-auto object-contain border rounded p-2">
                        </div>
                    @endif
                    <input type="file" name="footer_logo" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('footer_logo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Favicon -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Favicon</label>
                    @if(isset($settings) && $settings && $settings->favicon)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="h-16 w-16 object-contain border rounded p-2">
                        </div>
                    @endif
                    <input type="file" name="favicon" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-xs text-gray-500 mt-1">Recommended: 32x32 or 16x16 pixels</p>
                    @error('favicon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Contact Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $settings->phone ?? '') }}"
                           placeholder="e.g., 615-554-1131"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $settings->email ?? '') }}"
                           placeholder="e.g., info@example.com"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="mt-6">
                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                <textarea id="address" 
                          name="address" 
                          rows="3"
                          placeholder="e.g., 123 Security Way, Nashville, TN 37201"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('address', $settings->address ?? '') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Social Media Links Section -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Social Media Links</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facebook -->
                <div>
                    <label for="facebook_url" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fab fa-facebook text-blue-600 mr-2"></i> Facebook URL
                    </label>
                    <input type="url" 
                           id="facebook_url" 
                           name="facebook_url" 
                           value="{{ old('facebook_url', $settings->facebook_url ?? '') }}"
                           placeholder="https://facebook.com/yourpage"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('facebook_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Twitter -->
                <div>
                    <label for="twitter_url" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fab fa-twitter text-blue-400 mr-2"></i> Twitter URL
                    </label>
                    <input type="url" 
                           id="twitter_url" 
                           name="twitter_url" 
                           value="{{ old('twitter_url', $settings->twitter_url ?? '') }}"
                           placeholder="https://twitter.com/yourhandle"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('twitter_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instagram -->
                <div>
                    <label for="instagram_url" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fab fa-instagram text-pink-600 mr-2"></i> Instagram URL
                    </label>
                    <input type="url" 
                           id="instagram_url" 
                           name="instagram_url" 
                           value="{{ old('instagram_url', $settings->instagram_url ?? '') }}"
                           placeholder="https://instagram.com/yourhandle"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('instagram_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LinkedIn -->
                <div>
                    <label for="linkedin_url" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fab fa-linkedin text-blue-700 mr-2"></i> LinkedIn URL
                    </label>
                    <input type="url" 
                           id="linkedin_url" 
                           name="linkedin_url" 
                           value="{{ old('linkedin_url', $settings->linkedin_url ?? '') }}"
                           placeholder="https://linkedin.com/company/yourcompany"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('linkedin_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- YouTube -->
                <div>
                    <label for="youtube_url" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fab fa-youtube text-red-600 mr-2"></i> YouTube URL
                    </label>
                    <input type="url" 
                           id="youtube_url" 
                           name="youtube_url" 
                           value="{{ old('youtube_url', $settings->youtube_url ?? '') }}"
                           placeholder="https://youtube.com/yourchannel"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('youtube_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- API Integration Section -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">API Integration</h3>
            
            <!-- QuickBooks Integration -->
            <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">
                        <i class="fab fa-quickbooks text-blue-600 mr-2"></i> QuickBooks Integration
                    </h4>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="quickbooks_enabled" 
                               value="1"
                               {{ old('quickbooks_enabled', $settings->quickbooks_enabled ?? false) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable QuickBooks Sync</span>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="quickbooks_client_id" class="block text-gray-700 text-sm font-bold mb-2">Client ID</label>
                        <input type="text" 
                               id="quickbooks_client_id" 
                               name="quickbooks_client_id" 
                               value="{{ old('quickbooks_client_id', $settings->quickbooks_client_id ?? '') }}"
                               placeholder="Enter QuickBooks Client ID"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('quickbooks_client_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="quickbooks_client_secret" class="block text-gray-700 text-sm font-bold mb-2">Client Secret</label>
                        <input type="password" 
                               id="quickbooks_client_secret" 
                               name="quickbooks_client_secret" 
                               value="{{ old('quickbooks_client_secret', $settings->quickbooks_client_secret ?? '') }}"
                               placeholder="Enter QuickBooks Client Secret"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" onclick="togglePassword('quickbooks_client_secret')" class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-eye" id="eye-quickbooks_client_secret"></i> Show/Hide
                        </button>
                        @error('quickbooks_client_secret')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="quickbooks_company_id" class="block text-gray-700 text-sm font-bold mb-2">Company ID</label>
                        <input type="text" 
                               id="quickbooks_company_id" 
                               name="quickbooks_company_id" 
                               value="{{ old('quickbooks_company_id', $settings->quickbooks_company_id ?? '') }}"
                               placeholder="Enter QuickBooks Company ID"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('quickbooks_company_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="quickbooks_environment" class="block text-gray-700 text-sm font-bold mb-2">Environment</label>
                        <select id="quickbooks_environment" 
                                name="quickbooks_environment" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="sandbox" {{ old('quickbooks_environment', $settings->quickbooks_environment ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="production" {{ old('quickbooks_environment', $settings->quickbooks_environment ?? 'sandbox') === 'production' ? 'selected' : '' }}>Production</option>
                        </select>
                        @error('quickbooks_environment')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="quickbooks_access_token" class="block text-gray-700 text-sm font-bold mb-2">Access Token (Optional - Auto-generated)</label>
                        <input type="text" 
                               id="quickbooks_access_token" 
                               name="quickbooks_access_token" 
                               value="{{ old('quickbooks_access_token', $settings->quickbooks_access_token ?? '') }}"
                               placeholder="Will be auto-generated after OAuth"
                               readonly
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="text-xs text-gray-500 mt-1">This token is automatically generated during OAuth authentication</p>
                    </div>

                    <a href="{{ route('quickbooks.connect') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        <i class="fab fa-quickbooks mr-2"></i> Connect with QuickBooks
                    </a>
                    @php
                        $qbConnected = filled($settings->quickbooks_access_token ?? null) && filled($settings->quickbooks_refresh_token ?? null);
                        $qbReady = false;
                        if ($qbConnected && ($settings->quickbooks_enabled ?? false)) {
                            try {
                                $qbReady = app(\App\Services\QuickBooksService::class)->hasValidConnection();
                            } catch (\Throwable) {
                                $qbReady = false;
                            }
                        }
                    @endphp
                    @if(($settings->quickbooks_enabled ?? false) && ! $qbReady)
                        <div class="md:col-span-2 mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> QuickBooks reconnect required</p>
                            <p class="mt-1">
                                The OAuth token is expired or invalid (<code>invalid_grant</code>). Student card payments will fail until you click
                                <strong>Connect with QuickBooks</strong> again and approve access.
                            </p>
                        </div>
                    @elseif($qbReady)
                        <div class="md:col-span-2 mt-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> QuickBooks is connected and ready for payments.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Bank Integration -->
            <div class="mb-8 p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-university text-green-600 mr-2"></i> Bank Account Integration
                    </h4>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="bank_sync_enabled" 
                               value="1"
                               {{ old('bank_sync_enabled', $settings->bank_sync_enabled ?? false) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable Bank Sync</span>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bank_api_provider" class="block text-gray-700 text-sm font-bold mb-2">API Provider</label>
                        <select id="bank_api_provider" 
                                name="bank_api_provider" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Provider</option>
                            <option value="plaid" {{ old('bank_api_provider', $settings->bank_api_provider ?? '') === 'plaid' ? 'selected' : '' }}>Plaid</option>
                            <option value="yodlee" {{ old('bank_api_provider', $settings->bank_api_provider ?? '') === 'yodlee' ? 'selected' : '' }}>Yodlee</option>
                            <option value="other" {{ old('bank_api_provider', $settings->bank_api_provider ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('bank_api_provider')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bank_account_id" class="block text-gray-700 text-sm font-bold mb-2">Bank Account ID</label>
                        <input type="text" 
                               id="bank_account_id" 
                               name="bank_account_id" 
                               value="{{ old('bank_account_id', $settings->bank_account_id ?? '') }}"
                               placeholder="Enter Bank Account ID"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('bank_account_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bank_api_key" class="block text-gray-700 text-sm font-bold mb-2">API Key</label>
                        <input type="text" 
                               id="bank_api_key" 
                               name="bank_api_key" 
                               value="{{ old('bank_api_key', $settings->bank_api_key ?? '') }}"
                               placeholder="Enter Bank API Key"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('bank_api_key')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bank_api_secret" class="block text-gray-700 text-sm font-bold mb-2">API Secret</label>
                        <input type="password" 
                               id="bank_api_secret" 
                               name="bank_api_secret" 
                               value="{{ old('bank_api_secret', $settings->bank_api_secret ?? '') }}"
                               placeholder="Enter Bank API Secret"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" onclick="togglePassword('bank_api_secret')" class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-eye" id="eye-bank_api_secret"></i> Show/Hide
                        </button>
                        @error('bank_api_secret')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- QuickBooks Payments Info -->
            <div class="mb-8 p-4 bg-green-50 rounded-lg border border-green-200 mt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i> QuickBooks Payments
                </h4>
                <p class="text-sm text-gray-600">The $20 deposit is collected via QuickBooks Payments. Connect QuickBooks above and ensure your app has Payments scope. Reconnect QuickBooks if needed to enable card payments.</p>
            </div>
        </div>

        <!-- SMS / Twilio Section -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">SMS Notifications (Twilio)</h3>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="sms_enabled" value="1" {{ old('sms_enabled', $settings->sms_enabled ?? false) ? 'checked' : '' }} class="mr-2">
                    <span class="text-gray-700 text-sm font-bold">Enable SMS notifications</span>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Twilio Account SID</label>
                    <input type="text" name="twilio_sid" value="{{ old('twilio_sid', $settings->twilio_sid ?? '') }}" class="shadow border rounded w-full py-2 px-3">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Twilio Auth Token</label>
                    <input type="password" name="twilio_token" placeholder="Leave blank to keep current" class="shadow border rounded w-full py-2 px-3">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">From Phone Number</label>
                    <input type="text" name="twilio_from_number" value="{{ old('twilio_from_number', $settings->twilio_from_number ?? '') }}" placeholder="+1234567890" class="shadow border rounded w-full py-2 px-3">
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Used for enrollment confirmations, class change alerts, and blended course completion texts.</p>
        </div>

        <!-- Instructor Bios Section -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Instructor Bios</h3>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Jayson Bio -->
                <div>
                    <label for="jayson_bio" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fas fa-user text-[var(--primary-color)] mr-2"></i> Jayson's Bio
                    </label>
                    <textarea id="jayson_bio" 
                              name="jayson_bio" 
                              rows="6"
                              placeholder="Enter Jayson's bio and contact information for certificates, gear, and job questions..."
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('jayson_bio', $settings->jayson_bio ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">This bio will appear on the main page. Include contact information for certificates, gear, and job questions.</p>
                    @error('jayson_bio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kenny Bio -->
                <div>
                    <label for="kenny_bio" class="block text-gray-700 text-sm font-bold mb-2">
                        <i class="fas fa-user text-[var(--primary-color)] mr-2"></i> Kenny's Bio
                    </label>
                    <textarea id="kenny_bio" 
                              name="kenny_bio" 
                              rows="6"
                              placeholder="Enter Kenny's bio and contact information for certificates, gear, and job questions..."
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('kenny_bio', $settings->kenny_bio ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">This bio will appear on the main page. Include contact information for certificates, gear, and job questions.</p>
                    @error('kenny_bio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                <i class="fas fa-save mr-2"></i> Save Settings
            </button>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-' + fieldId);
    
    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
