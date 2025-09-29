<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ✍️ Blog Generator
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <form method="POST" action="{{ route('generate.text') }}">
                    @csrf
                    <label class="block mb-2 font-semibold">Enter your topic:</label>
                    <input type="text" name="prompt"
                           class="w-full p-2 mb-4 border rounded"
                           placeholder="Write your blog topic..." required>

                    <button type="submit"
                            class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                        Generate
                    </button>
                </form>

                {{-- رسائل الخطأ --}}
                @if (session('error'))
                    <p class="mt-4 text-red-600">{{ session('error') }}</p>
                @endif

                {{-- عرض النص الناتج --}}
                @if (session('success'))
                    <div class="p-4 mt-6 border border-green-200 rounded bg-green-50">
                        <h3 class="mb-2 font-semibold">Generated Blog:</h3>
                        <p class="whitespace-pre-line">{{ session('success') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
