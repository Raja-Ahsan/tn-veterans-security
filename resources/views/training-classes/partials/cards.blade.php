@if($services->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
        @foreach($services as $index => $service)
            @if($service->title === 'Unarmed  Security')
            <div class="group block cursor-pointer" onclick="document.getElementById('unarmed-modal').classList.remove('hidden')">
            @else
            <a href="{{ $service->title === 'Enhanced Armed Guard Security' ? route('handgun.subcategories') : route('training-classes.show', $service->id) }}" class="group block">
            @endif
                <div class="service-detail-card bg-white rounded-lg overflow-hidden h-full flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <div class="relative h-[280px] overflow-hidden">
                        <img src="{{ $service->display_image_url }}"
                                 alt="{{ $service->title }}"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-[var(--primary-color)] text-white px-4 py-2 rounded-full text-sm font-bold opacity-0 group-hover:opacity-100 transform translate-y-[-10px] group-hover:translate-y-0 transition-all duration-300">
                            Learn More
                        </div>
                    </div>

                    <div class="p-6 lg:p-8 flex-1 flex flex-col">
                        <h3 class="text-[22px] lg:text-[26px] font-bold text-[var(--text-color)] mb-4 uppercase group-hover:text-[var(--primary-color)] transition-colors" style="font-family: var(--font-display);">
                            {{ $service->title }}
                        </h3>

                        @if($service->short_description)
                            <p class="text-[#666] text-[15px] lg:text-[16px] leading-relaxed mb-4 flex-1 line-clamp-3">
                                {{ $service->short_description }}
                            </p>
                        @elseif($service->description)
                            <p class="text-[#666] text-[15px] lg:text-[16px] leading-relaxed mb-4 flex-1 line-clamp-3">
                                {{ \Illuminate\Support\Str::limit(strip_tags($service->description), 150) }}
                            </p>
                        @endif

                        <div class="mb-4 space-y-2">
                            @if($service->is_travel_based)
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold uppercase text-amber-800">Travel class</span>
                            @endif
                            @if($service->location)
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt text-[var(--primary-color)]"></i>
                                    <span>{{ $service->location }}</span>
                                </div>
                            @endif
                            @if($service->requires_dallas_law || $service->requires_active_shooter)
                                <div class="flex flex-wrap gap-2">
                                    @if($service->requires_dallas_law)
                                        <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Dallas Law Required</span>
                                    @endif
                                    @if($service->requires_active_shooter)
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Active Shooter Required</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-[var(--primary-color)] font-semibold mt-auto pt-4 border-t border-gray-100 group-hover:border-[var(--primary-color)]/30 transition-colors">
                            <span class="text-[15px] uppercase tracking-wide">View Details</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </div>
            @if($service->title === 'Unarmed  Security')
            </div>
            @else
            </a>
            @endif
        @endforeach
    </div>
@else
    <div class="text-center py-16">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <i class="fas fa-search text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-[var(--text-color)] uppercase" style="font-family: var(--font-display);">
            No classes found
        </h3>
        <p class="mt-2 text-gray-600">Try a different class name, or clear the search.</p>
    </div>
@endif
