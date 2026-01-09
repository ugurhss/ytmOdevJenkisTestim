<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Paneli') }}
             <a href="/groups/create" style="color: red"> grub oluştur</a>

        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                   Admin olarak giriş yaptınız.
                </div>

            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium">Aktif Öğrenciler (Sistem)</h3>
                    <p class="mt-2 text-3xl font-semibold">{{ $stats['active_students'] }}</p>
                </div>
                 <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium">Onay Bekleyenler</h3>
                    <p class="mt-2 text-3xl font-semibold">{{ $stats['pending_approvals'] }}</p>
                </div>
                 <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium">Son Aktiviteler</h3>
                    <p class="mt-2 text-3xl font-semibold">{{ $stats['recent_activity'] }}</p>
                </div>
            </div>

             </div>
    </div>
</x-app-layout>
