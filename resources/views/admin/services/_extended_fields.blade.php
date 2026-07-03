<div class="mb-4 border-t pt-4">
    <h4 class="font-bold text-gray-800 mb-3">Refund & Enrollment Details</h4>
    <div class="mb-3">
        <label for="refund_policy" class="block text-sm font-bold text-gray-700 mb-1">Refund Policy</label>
        <textarea id="refund_policy" name="refund_policy" rows="3" class="w-full border rounded px-3 py-2">{{ old('refund_policy', $refundPolicy ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="what_to_bring" class="block text-sm font-bold text-gray-700 mb-1">What to Bring</label>
        <textarea id="what_to_bring" name="what_to_bring" rows="3" class="w-full border rounded px-3 py-2">{{ old('what_to_bring', $whatToBring ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="prerequisites" class="block text-sm font-bold text-gray-700 mb-1">Prerequisites</label>
        <textarea id="prerequisites" name="prerequisites" rows="3" class="w-full border rounded px-3 py-2">{{ old('prerequisites', $prerequisites ?? '') }}</textarea>
    </div>
</div>

<div class="mb-4 border-t pt-4">
    <h4 class="font-bold text-gray-800 mb-3">Travel-Based Class Settings</h4>
    <label class="flex items-center mb-3">
        <input type="checkbox" name="is_travel_based" value="1" {{ old('is_travel_based', $isTravelBased ?? false) ? 'checked' : '' }} class="mr-2">
        <span class="text-sm text-gray-700">This is a travel-based class</span>
    </label>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
        <div><label class="block text-sm text-gray-700 mb-1">Distance Fee ($)</label><input type="number" step="0.01" name="travel_distance_fee" value="{{ old('travel_distance_fee', $travelDistanceFee ?? '') }}" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm text-gray-700 mb-1">Lodging Fee ($)</label><input type="number" step="0.01" name="travel_lodging_fee" value="{{ old('travel_lodging_fee', $travelLodgingFee ?? '') }}" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm text-gray-700 mb-1">Travel Time Fee ($)</label><input type="number" step="0.01" name="travel_time_fee" value="{{ old('travel_time_fee', $travelTimeFee ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    </div>
    <div class="mb-3"><label class="block text-sm text-gray-700 mb-1">Minimum Students (travel)</label><input type="number" name="travel_minimum_students" value="{{ old('travel_minimum_students', $travelMinimumStudents ?? '') }}" min="1" class="w-full border rounded px-3 py-2 max-w-xs"></div>
    <div class="mb-3"><label class="block text-sm text-gray-700 mb-1">Travel Notes</label><textarea name="travel_notes" rows="2" class="w-full border rounded px-3 py-2">{{ old('travel_notes', $travelNotes ?? '') }}</textarea></div>
    <div><label class="block text-sm text-gray-700 mb-1">Lodging Instructions</label><textarea name="lodging_instructions" rows="2" class="w-full border rounded px-3 py-2">{{ old('lodging_instructions', $lodgingInstructions ?? '') }}</textarea></div>
</div>
