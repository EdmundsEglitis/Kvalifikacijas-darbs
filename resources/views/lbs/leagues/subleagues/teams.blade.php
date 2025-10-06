@extends('layouts.app')
@section('title', $subLeague->name . ' – Komandas')

@section('subnav')
  <x-lbs-subnav :subLeague="$subLeague" />
@endsection

@section('content')
  <div class="max-w-6xl mx-auto px-4 space-y-8">
    <h1 class="text-3xl font-bold text-white">{{ $subLeague->name }} – Komandas</h1>

    @if($teams->isEmpty())
      <p class="mt-4 text-[#F3F4F6]/70">Šai līgai vēl nav komandu.</p>
    @else
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($teams as $team)
          <div class="bg-[#1f2937] shadow-md rounded-lg p-4 flex flex-col justify-between border border-[#374151] hover:border-[#84CC16] hover:shadow-xl transition">
            <h2 class="text-lg font-semibold text-white">{{ $team->name }}</h2>

            @if($team->logo)
              <img
                src="{{ asset('storage/' . $team->logo) }}"
                alt="{{ $team->name }}"
                class="mt-2 h-24 w-auto object-contain"
                loading="lazy"
              >
            @endif

            <a href="{{ route('lbs.team.show', $team->id) }}"
               class="mt-4 text-center bg-[#84CC16] text-[#111827] py-2 rounded font-semibold hover:bg-[#a6e23a] transition">
              Skatīt komandu
            </a>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endsection
