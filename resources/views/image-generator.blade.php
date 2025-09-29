<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            🎨 Image Generator
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <form method="POST" action="{{ route('ImageGenerator') }}">
                    @csrf
                    <label class="block mb-2 font-semibold">Enter image prompt:</label>
                    <input type="text" name="imageprompt" class="w-full p-2 mb-4 border rounded"
                        placeholder="Describe your image..." required>

                    <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                        Generate Image
                    </button>
                </form>

                @if (session('success'))
                    <p class="mt-4 text-green-600">{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <p class="mt-4 text-red-600">{{ session('error') }}</p>
                @endif
                @if (session('imagePath') && session('fileName'))
                    <div>
                        <img src="{{ asset('storage/' . session('imagePath')) }}" alt="Generated Image"
                            style="max-width:300px;">
                        <br>
                        <a href="{{ route('image.download', session('fileName')) }}" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-blue-600"
                            download>تحميل الصورة</a>
                    </div>
                @endif



            </div>
        </div>
    </div>

</x-app-layout>
