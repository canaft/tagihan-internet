<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

    <div class="bg-white shadow-2xl rounded-2xl max-w-md w-full p-8 animate-fadeIn">
<div class="flex justify-center mb-6">
    <div class="p-3 bg-white rounded-xl border border-gray-200 shadow-md">
        <img 
            src="{{ asset('storage/images/logo-dhs.png') }}" 
            alt="Logo DHS" 
            class="w-28 h-28 object-contain rounded-lg"
        >
    </div>
</div>


        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Welcome Back!</h2>

        @if(session('status'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-center">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-center">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition duration-200">
                Log in
            </button>
        </form>

        <p class="mt-6 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} DHS. All rights reserved.
        </p>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 1s ease-out;
        }
    </style>
</body>
</html>
