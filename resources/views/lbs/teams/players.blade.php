@extends('layouts.app')
@section('title', $team->name . ' – Spēlētāji')

@section('subnav')
<x-teamnav :team="$team" />
@endsection

@section('content')
  <div class="max-w-6xl mx-auto px-4 space-y-12">

    <section class="flex flex-col items-center space-y-4">
      @if($team->logo)
        <img
          src="{{ asset('storage/' . $team->logo) }}"
          alt="{{ $team->name }}"
          class="h-28 w-28 object-contain rounded-xl shadow bg-[#111827] p-3 ring-2 ring-[#84CC16]/40"
        >
      @endif
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white drop-shadow">
        {{ $team->name }}
      </h1>
    </section>

    <section>
      <h2 class="text-2xl font-bold text-white mb-6">Spēlētāji</h2>

      @if($team->players->isEmpty())
        <p class="text-center text-[#F3F4F6]/70 italic">
          Šai komandai nav pievienotu spēlētāju.
        </p>
      @else
        <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          @foreach($team->players as $player)
            @php
              $hasLocalPhoto = $player->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($player->photo);
            @endphp
            <li>
              <a href="{{ route('lbs.player.show', $player->id) }}"
                 class="group p-6 bg-[#1f2937] rounded-2xl border border-[#374151] shadow transition
                        hover:border-[#84CC16] hover:-translate-y-1 hover:shadow-xl flex flex-col items-center text-center">

                @if($hasLocalPhoto)
                  <img
                    src="{{ asset('storage/' . $player->photo) }}"
                    alt="{{ $player->name }}"
                    class="h-24 w-24 object-cover rounded-full mb-3 border-2 border-[#84CC16]/50 group-hover:scale-105 transition"
                  >
                @elseif(!empty($player->photo_url))
                  <img
                    src="{{ $player->photo_url }}"
                    alt="{{ $player->name }}"
                    class="h-24 w-24 object-cover rounded-full mb-3 border-2 border-[#84CC16]/50 group-hover:scale-105 transition"
                  >
                @else
                  <div class="h-24 w-24 bg-gray-700 rounded-full mb-3 flex items-center justify-center text-gray-400">
                    No Photo
                  </div>
                @endif

                <p class="font-semibold text-lg text-white group-hover:text-[#84CC16] transition">
                  {{ $player->name }}
                </p>

                @if($player->jersey_number)
                  <p class="text-[#84CC16] font-bold">#{{ $player->jersey_number }}</p>
                @endif

                @if($player->height)
                  <p class="text-sm text-[#F3F4F6]/70">{{ $player->height }} cm</p>
                @endif
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </section>

  </div>
@endsection
