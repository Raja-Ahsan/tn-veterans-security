@php
    $isTravelBased = (bool) old('is_travel_based', $isTravelBased ?? false);
@endphp

{{-- Refund & enrollment --}}
<div class="mb-6 border-t border-gray-100 pt-5 mt-2">
    <div class="mb-4">
        <h4 class="font-bold text-gray-900">Refund &amp; Enrollment Details</h4>
        <p class="mt-0.5 text-xs text-gray-500">Shown on the class page so students know policies and what to bring.</p>
    </div>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-3">
            <label for="refund_policy" class="mb-1.5 flex items-center gap-1.5 text-sm font-semibold text-gray-800">
                <i class="fas fa-undo text-gray-400 text-xs"></i> Refund Policy
            </label>
            <textarea id="refund_policy" name="refund_policy" rows="4"
                      placeholder="e.g. Full refund if cancelled 7+ days before class…"
                      class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('refund_policy', $refundPolicy ?? '') }}</textarea>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-3">
            <label for="what_to_bring" class="mb-1.5 flex items-center gap-1.5 text-sm font-semibold text-gray-800">
                <i class="fas fa-suitcase text-gray-400 text-xs"></i> What to Bring
            </label>
            <textarea id="what_to_bring" name="what_to_bring" rows="4"
                      placeholder="e.g. ID, notebook, closed-toe shoes…"
                      class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('what_to_bring', $whatToBring ?? '') }}</textarea>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-3">
            <label for="prerequisites" class="mb-1.5 flex items-center gap-1.5 text-sm font-semibold text-gray-800">
                <i class="fas fa-list-check text-gray-400 text-xs"></i> Prerequisites
            </label>
            <textarea id="prerequisites" name="prerequisites" rows="4"
                      placeholder="e.g. Must complete Unarmed first…"
                      class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('prerequisites', $prerequisites ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Travel-based --}}
<div class="mb-2 border-t border-gray-100 pt-5 mt-2 space-y-3">
    <div>
        <h4 class="font-bold text-gray-900">Travel-Based Class Settings</h4>
        <p class="mt-0.5 text-xs text-gray-500">Only for classes taught off-site / on the road. Leave off for normal in-house classes.</p>
    </div>

    <label for="is_travel_based" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-amber-300 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
        <input type="checkbox"
               id="is_travel_based"
               name="is_travel_based"
               value="1"
               {{ $isTravelBased ? 'checked' : '' }}
               class="mt-1 rounded border-gray-400 text-amber-600 focus:ring-amber-500"
               data-travel-toggle>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-gray-900">This is a travel-based class</span>
                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Off-site</span>
            </div>
            <p class="mt-1 text-xs text-gray-500">Enable to set travel fees, minimum enrollment, and lodging notes for students.</p>
        </div>
        <i class="fas fa-plane-departure text-amber-500 mt-1"></i>
    </label>

    <div id="travel-settings-panel"
         class="rounded-lg border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-4 {{ $isTravelBased ? '' : 'hidden' }}"
         @if(!$isTravelBased) hidden @endif>
        <div class="mb-4 flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white">
                <i class="fas fa-road"></i>
            </div>
            <div>
                <h5 class="text-sm font-bold text-gray-900">Travel fees &amp; requirements</h5>
                <p class="mt-0.5 text-xs text-gray-600">These amounts and notes apply when the class is taught away from your main location.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="travel_distance_fee" class="mb-1 block text-xs font-semibold text-gray-700">Distance Fee ($)</label>
                <input type="number" step="0.01" min="0" id="travel_distance_fee" name="travel_distance_fee"
                       value="{{ old('travel_distance_fee', $travelDistanceFee ?? '') }}"
                       placeholder="0.00"
                       class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="travel_lodging_fee" class="mb-1 block text-xs font-semibold text-gray-700">Lodging Fee ($)</label>
                <input type="number" step="0.01" min="0" id="travel_lodging_fee" name="travel_lodging_fee"
                       value="{{ old('travel_lodging_fee', $travelLodgingFee ?? '') }}"
                       placeholder="0.00"
                       class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="travel_time_fee" class="mb-1 block text-xs font-semibold text-gray-700">Travel Time Fee ($)</label>
                <input type="number" step="0.01" min="0" id="travel_time_fee" name="travel_time_fee"
                       value="{{ old('travel_time_fee', $travelTimeFee ?? '') }}"
                       placeholder="0.00"
                       class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="travel_minimum_students" class="mb-1 block text-xs font-semibold text-gray-700">Min students (travel)</label>
                <input type="number" min="1" id="travel_minimum_students" name="travel_minimum_students"
                       value="{{ old('travel_minimum_students', $travelMinimumStudents ?? '') }}"
                       placeholder="e.g. 6"
                       class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <p class="mt-1 text-[11px] text-gray-500">Class may be cancelled below this.</p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="travel_notes" class="mb-1 block text-xs font-semibold text-gray-700">Travel Notes</label>
                <textarea id="travel_notes" name="travel_notes" rows="3"
                          placeholder="Extra travel info for students or staff…"
                          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('travel_notes', $travelNotes ?? '') }}</textarea>
            </div>
            <div>
                <label for="lodging_instructions" class="mb-1 block text-xs font-semibold text-gray-700">Lodging Instructions</label>
                <textarea id="lodging_instructions" name="lodging_instructions" rows="3"
                          placeholder="Hotel recommendations, check-in tips…"
                          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('lodging_instructions', $lodgingInstructions ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var toggle = document.querySelector('[data-travel-toggle]');
    var panel = document.getElementById('travel-settings-panel');
    if (!toggle || !panel) return;

    function syncPanel() {
        if (toggle.checked) {
            panel.classList.remove('hidden');
            panel.removeAttribute('hidden');
        } else {
            panel.classList.add('hidden');
            panel.setAttribute('hidden', 'hidden');
        }
    }

    toggle.addEventListener('change', syncPanel);
    syncPanel();
})();
</script>
