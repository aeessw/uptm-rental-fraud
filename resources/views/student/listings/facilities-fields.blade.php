<fieldset class="space-y-3">
    <legend class="text-xs font-semibold text-slate-700">Facilities <span class="text-rose-500">*</span></legend>
    <p class="text-xs text-slate-500">Select at least one facility.</p>
    @error('facilities')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach(\App\Models\Listing::FACILITIES as $value => $label)
            <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                <input type="checkbox" name="facilities[]" value="{{ $value }}" @checked(in_array($value, (array) old('facilities', session()->hasOldInput() ? [] : ($listing->facilities ?? [])), true)) class="h-4 w-4 rounded border-slate-300">
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
