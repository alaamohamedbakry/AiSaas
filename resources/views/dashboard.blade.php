<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid gap-6">

            {{-- Blog Generator Card --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-lg text-center">
                <h2 class="text-xl font-bold mb-4">✍️ Blog Generator</h2>
                <a href="{{ route('blog.page') }}"
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                   Open
                </a>
            </div>

            {{-- Image Generator Card --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-lg text-center">
                <h2 class="text-xl font-bold mb-4">🎨 Image Generator</h2>
                <a href="{{ route('image.page') }}"
                   class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                   Open
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
