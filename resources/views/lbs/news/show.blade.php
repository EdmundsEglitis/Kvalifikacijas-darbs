@extends('layouts.app')

@section('title', $news->title)

@section('content')
  <article class="max-w-4xl mx-auto px-4 pt-6 pb-16 space-y-8">
    <header class="space-y-2">
      <h1 class="text-4xl font-extrabold text-white">
        {{ $news->title }}
      </h1>
      <div class="flex items-center gap-4 text-sm text-[#F3F4F6]/70">
        <time datetime="{{ $news->created_at->toIso8601String() }}">
          Publicēts: {{ $news->created_at->format('Y-m-d H:i') }}
        </time>
      </div>
    </header>

    {{-- Article body --}}
    <div class="prose prose-invert max-w-none">
      {!! $news->clean_content !!}
    </div>

    <footer>
      <button
        onclick="handleBack()"
        class="inline-block mt-4 px-6 py-3 rounded-full bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a6e23a] transition"
      >
        ⬅ Atpakaļ
      </button>
    </footer>
  </article>
@endsection

@push('scripts')
<script>
  function handleBack() {
    if (document.referrer && document.referrer !== window.location.href) {
      window.history.back();
    } else {
      window.location.href = "{{ route('lbs.home') }}";
    }
  }
</script>
@endpush
