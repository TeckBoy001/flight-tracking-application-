@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="max-w-sm mx-auto bg-white border rounded-xl shadow-sm p-6">
        <h1 class="text-xl font-bold mb-6 text-center">Create an account</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-md px-3 py-2" required autofocus>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-md px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded-md px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded-md px-3 py-2" required>
            </div>
            <button class="w-full bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Register</button>
        </form>
        <p class="text-sm text-center text-gray-500 mt-4">
            Already have an account? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Log in</a>
        </p>
    </div>
@endsection
