@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="max-w-sm mx-auto bg-white border rounded-xl shadow-sm p-6">
        <h1 class="text-xl font-bold mb-6 text-center">Log in</h1>
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-md px-3 py-2" required autofocus>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded-md px-3 py-2" required>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button class="w-full bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Log in</button>
        </form>
        <p class="text-sm text-center text-gray-500 mt-4">
            No account? <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Register</a>
        </p>
    </div>
@endsection
