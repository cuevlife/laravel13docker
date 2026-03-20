<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Users Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Users</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['users_count'] }}</div>
                </div>

                <!-- Merchants Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Merchants</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['merchants_count'] }}</div>
                </div>

                <!-- Slips Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Processed Slips</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['slips_count'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.slip-reader') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Scan New Slip
                        </a>
                        <a href="{{ route('admin.merchants') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Manage Merchants
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
