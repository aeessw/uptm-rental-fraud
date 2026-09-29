<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="space-y-1.5">
        <label for="pax" class="block text-xs font-semibold text-slate-700">Pax / People <span class="text-rose-500">*</span></label>
        <input type="number" id="pax" name="pax" required min="1" max="100" step="1" value="{{ old('pax', $listing->pax ?? '') }}" placeholder="Number of people" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
        @error('pax')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="space-y-1.5">
        <label for="available_from" class="block text-xs font-semibold text-slate-700">Available From <span class="text-rose-500">*</span></label>
        <input type="date" id="available_from" name="available_from" required value="{{ old('available_from', isset($listing) ? $listing->available_from?->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
    </div>
    <div class="space-y-1.5">
        <label for="rental_period" class="block text-xs font-semibold text-slate-700">Rental Period <span class="text-rose-500">*</span></label>
        <select id="rental_period" name="rental_period" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
            <option value="" disabled>Select rental period</option>
            @foreach(\App\Models\Listing::RENTAL_PERIODS as $value => $label)
                <option value="{{ $value }}" @selected(old('rental_period', $listing->rental_period ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="space-y-1.5">
        <label for="preferred_tenant" class="block text-xs font-semibold text-slate-700">Preferred Tenant <span class="text-rose-500">*</span></label>
        <select id="preferred_tenant" name="preferred_tenant" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
            <option value="" disabled @selected(old('preferred_tenant', $listing->preferred_tenant ?? '') === '')>Select preferred tenant</option>
            @foreach(\App\Models\Listing::TENANT_PREFERENCES as $value => $label)
                <option value="{{ $value }}" @selected(old('preferred_tenant', $listing->preferred_tenant ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
