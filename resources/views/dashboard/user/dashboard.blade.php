<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gösterge Paneli') }}
             <a href="/groups/create" style="color: red"> grub oluştur</a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Giriş yaptınız! (Öğrenci Paneli)") }}
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Grup Bildirimlerim</h3>

                @if($user->groups->isEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                           Henüz herhangi bir gruba dahil değilsiniz.
                        </div>
                    </div>
                @else
                    @php
                        // Tüm gruplardaki tüm duyuruları toplayıp tarihe göre sıralayalım
                        $allAnnouncements = $user->groups
                            ->flatMap(function ($group) {
                                return $group->announcements;
                            })
                            ->sortByDesc('created_at');
                    @endphp

                    @forelse($allAnnouncements as $announcement)
                        <div class="mb-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-xl font-semibold">{{ $announcement->title }}</h4>
                                    <span class="text-sm text-gray-500">{{ $announcement->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">
                                    <strong>Grup:</strong> {{ $announcement->group->groups_name }} |
                                    <strong>Gönderen:</strong> {{ $announcement->user->name }}
                                </p>
                                <p class="text-gray-800">
                                    {!! nl2br(e($announcement->content)) !!}
                                </p>
                            </div>
                        </div>
                    @empty
                         <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                Dahil olduğunuz gruplarda henüz bir bildirim yok.
                            </div>
                        </div>
                    @endforelse
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
