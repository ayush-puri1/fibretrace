@extends('layouts.dashboard')

@section('title', 'Create Listing - FibreTrace')

@section('page-title', 'Create Listing')

@section('dashboard-content')
    <div class="max-w-[800px] mx-auto">
        <!-- Header -->
        <div class="mb-lg">
            <h2 class="font-headline-md text-primary font-bold">List Waste Material</h2>
            <p class="font-body-sm text-on-surface-variant">Fill in the specifications to digitize your scrap for the market floor.</p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-error/10 border border-error/30 text-error font-label-sm px-4 py-3 rounded-xl mb-lg">
                <div class="font-bold mb-1">Please fix the following errors:</div>
                <ul class="list-disc list-inside text-[12px] space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Wrapper -->
        <form method="POST" action="{{ route('seller.lots.store') }}" enctype="multipart/form-data"
              class="glass-panel p-xl rounded-[2rem] bg-white/70 border-white/90 shadow-[0_12px_40px_rgba(0,53,39,0.05)] relative overflow-hidden">
            @csrf
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-secondary to-primary-fixed"></div>

            <!-- Step 1: Basics -->
            <div class="mb-xl pb-lg border-b border-outline-variant/30">
                <h3 class="font-label-lg text-primary font-bold mb-md flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-[14px]">1</span>
                    Basic Details
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-on-surface-variant font-semibold ml-1">Category *</label>
                        <select name="category" required class="glass-input w-full px-4 py-3.5 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface bg-white">
                            <option value="">Select category...</option>
                            <option value="cutting_scraps"   {{ old('category') === 'cutting_scraps'   ? 'selected' : '' }}>Cutting Scraps</option>
                            <option value="yarn_ends"        {{ old('category') === 'yarn_ends'        ? 'selected' : '' }}>Yarn Ends</option>
                            <option value="rejected_batches" {{ old('category') === 'rejected_batches' ? 'selected' : '' }}>Rejected Batches</option>
                            <option value="selvedge"         {{ old('category') === 'selvedge'         ? 'selected' : '' }}>Selvedge</option>
                        </select>
                        @error('category')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-on-surface-variant font-semibold ml-1">Fiber Type *</label>
                        <input type="text" name="fiber_type" value="{{ old('fiber_type') }}" placeholder="e.g. 100% Cotton, Poly-Blend 65/35"
                               class="glass-input w-full px-4 py-3.5 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" required>
                        @error('fiber_type')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-on-surface-variant font-semibold ml-1">Est. Weight (kg) *</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline-variant">scale</span>
                            <input type="number" name="weight_kg" value="{{ old('weight_kg') }}" min="100" max="25000" placeholder="Min. 100 kg"
                                   class="glass-input w-full px-4 py-3.5 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline/70" required>
                        </div>
                        @error('weight_kg')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-on-surface-variant font-semibold ml-1">Color Description *</label>
                        <input type="text" name="color_description" value="{{ old('color_description') }}" placeholder="e.g. White/Raw, Mixed Colors"
                               class="glass-input w-full px-4 py-3.5 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" required>
                        @error('color_description')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- Step 2: Composition -->
            <div class="mb-xl pb-lg border-b border-outline-variant/30">
                <h3 class="font-label-lg text-primary font-bold mb-md flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-[14px]">2</span>
                    Fiber Composition
                </h3>
                
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-md mb-md">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-label-sm text-on-surface font-semibold">Primary Fiber Purity (%)</span>
                        <span id="purity-display" class="font-bold text-primary">{{ old('fiber_purity_pct', 65) }}%</span>
                    </div>
                    <input type="range" name="fiber_purity_pct" id="purity-slider" min="0" max="100" value="{{ old('fiber_purity_pct', 65) }}"
                           class="w-full accent-secondary cursor-pointer h-2 bg-outline-variant/30 rounded-lg appearance-none"
                           oninput="document.getElementById('purity-display').textContent = this.value + '%'">
                    <div class="flex justify-between text-[11px] text-outline mt-1 font-semibold">
                        <span>0%</span><span>100%</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-3 cursor-pointer group flex-1 bg-surface-container-low p-3 rounded-xl border border-transparent hover:border-primary/30 transition-colors">
                        <input type="radio" name="color_sorted" value="1" {{ old('color_sorted', '1') === '1' ? 'checked' : '' }}
                               class="w-4 h-4 rounded-full text-secondary focus:ring-secondary/50 border-outline-variant">
                        <span class="font-body-sm text-on-surface-variant font-medium">Color Sorted</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group flex-1 bg-surface-container-low p-3 rounded-xl border border-transparent hover:border-primary/30 transition-colors">
                        <input type="radio" name="color_sorted" value="0" {{ old('color_sorted') === '0' ? 'checked' : '' }}
                               class="w-4 h-4 rounded-full text-secondary focus:ring-secondary/50 border-outline-variant">
                        <span class="font-body-sm text-on-surface-variant font-medium">Mixed Colors</span>
                    </label>
                </div>
            </div>

            <!-- Step 3: Pricing & Duration -->
            <div class="mb-xl pb-lg border-b border-outline-variant/30">
                <h3 class="font-label-lg text-primary font-bold mb-md flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-[14px]">3</span>
                    Pricing & Duration
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center ml-1 mb-1">
                            <label class="font-label-sm text-on-surface-variant font-semibold">Reserve Base Price (₹/kg) *</label>
                            <button type="button" id="btn-use-suggested" class="text-xs text-secondary hover:text-secondary/80 font-bold flex items-center gap-1 hidden transition-colors" onclick="useSuggestedPrice()">
                                <span class="material-symbols-outlined text-[14px]">price_change</span> Use Suggestion
                            </button>
                        </div>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant font-bold text-sm">₹</span>
                            <input type="number" name="base_price" id="base_price_input" value="{{ old('base_price') }}" min="1" step="0.01" placeholder="Starting bid per kg"
                                   class="glass-input w-full pl-8 pr-4 py-3.5 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" required>
                        </div>
                        @error('base_price')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-on-surface-variant font-semibold ml-1 mb-1">Auction End Date & Time *</label>
                        <div class="relative group">
                            <input type="datetime-local" name="auction_ends_at" id="auction_ends_at_input" value="{{ old('auction_ends_at', now()->addDays(3)->format('Y-m-d\TH:i')) }}"
                                   class="glass-input w-full px-4 py-3.5 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface bg-white" required>
                        </div>
                        @error('auction_ends_at')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- Step 4: Photos -->
            <div class="mb-xl">
                <h3 class="font-label-lg text-primary font-bold mb-md flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-[14px]">4</span>
                    Verification Photos
                </h3>
                
                <label for="images" class="border-2 border-dashed border-outline-variant/50 rounded-2xl p-xl flex flex-col items-center justify-center bg-surface-container-lowest/50 hover:bg-surface-container-lowest hover:border-primary transition-all cursor-pointer group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-secondary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-16 h-16 rounded-full bg-secondary-container/50 text-secondary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">add_a_photo</span>
                    </div>
                    <div class="font-label-md text-primary font-bold mb-1">Click to upload or drag & drop</div>
                    <div class="font-body-sm text-outline-variant text-[12px]">Minimum 2 photos recommended · JPG, PNG, WebP (Max 5MB each)</div>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                </label>
                @error('images')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror
                @error('images.*')<span class="text-error text-[11px] ml-1">{{ $message }}</span>@enderror

                <!-- Live Upload Image Preview Grid -->
                <div id="image-preview-container" class="grid grid-cols-2 sm:grid-cols-5 gap-md mt-md hidden">
                    <!-- Dynamic previews will be injected here -->
                </div>
            </div>

            <div class="flex items-center justify-between pt-lg border-t border-outline-variant/30">
                <div class="flex items-center gap-2">
                    <span class="font-label-sm text-on-surface-variant">Est. Market Value:</span>
                    <span id="price-suggestion" class="font-headline-sm text-secondary font-bold bg-secondary/10 px-3 py-1 rounded-lg">~ Enter details</span>
                </div>
                <button type="submit" class="btn-magnetic bg-primary text-white font-label-lg px-8 py-3.5 rounded-xl hover:bg-secondary transition-all shadow-md flex items-center gap-2 font-bold">
                    List on Market <span class="material-symbols-outlined text-[18px]">publish</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        let suggestedPriceVal = null;

        const updateSuggestionDisplay = (text) => {
            document.getElementById('price-suggestion').textContent = text;
        };

        // AJAX price suggestion on fiber/purity/sort change
        const fetchPrice = () => {
            const fiberType   = document.querySelector('[name="fiber_type"]').value.trim();
            const purityPct   = document.getElementById('purity-slider').value;
            const colorSorted = document.querySelector('[name="color_sorted"]:checked')?.value ?? '0';
            
            if (!fiberType) {
                updateSuggestionDisplay('~ Enter fiber type');
                document.getElementById('btn-use-suggested').classList.add('hidden');
                suggestedPriceVal = null;
                return;
            }
            
            updateSuggestionDisplay('~ Calculating...');
            
            fetch(`{{ route('seller.price.suggest') }}?fiber_type=${encodeURIComponent(fiberType)}&purity=${purityPct}&color_sorted=${colorSorted}`)
                .then(r => {
                    if (!r.ok) throw new Error('Response error');
                    return r.json();
                })
                .then(d => { 
                    suggestedPriceVal = d.suggested_price;
                    updateSuggestionDisplay(`~ ₹${d.suggested_price}/kg`); 
                    document.getElementById('btn-use-suggested').classList.remove('hidden');
                })
                .catch(() => {
                    updateSuggestionDisplay('~ Price unavailable');
                });
        };

        const useSuggestedPrice = () => {
            if (suggestedPriceVal) {
                document.getElementById('base_price_input').value = suggestedPriceVal;
            }
        };

        document.querySelector('[name="fiber_type"]').addEventListener('input', fetchPrice);
        document.getElementById('purity-slider').addEventListener('input', fetchPrice);
        document.querySelectorAll('[name="color_sorted"]').forEach(el => el.addEventListener('change', fetchPrice));

        // Call immediately to initialize display based on any initial or old inputs
        fetchPrice();

        // Live Upload Previews
        const imagesInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');

        imagesInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const files = Array.from(this.files);
            
            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
                files.forEach((file) => {
                    if (!file.type.startsWith('image/')) return;
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'relative aspect-square rounded-xl overflow-hidden border border-outline-variant/50 group shadow-sm bg-surface-container-low';
                        
                        wrapper.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                                <span class="text-[10px] text-white font-semibold truncate w-full text-center">${file.name}</span>
                            </div>
                        `;
                        previewContainer.appendChild(wrapper);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    </script>
@endsection
