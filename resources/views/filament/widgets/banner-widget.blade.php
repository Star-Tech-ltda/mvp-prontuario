<x-filament-widgets::widget>

            @if ($banner)
                <div class="flex justify-center">
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner" class="rounded-md shadow-lg max-w-full h-auto" />
                </div>
                <p class="text-center text-gray-500">Publicidade</p>

            @else
            @endif

</x-filament-widgets::widget>
