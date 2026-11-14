<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — {{ __('Recipes') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <script>
        (function () {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        }());
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-zinc-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">

    {{-- Background decoration --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 -left-20 h-[32rem] w-[52rem] rounded-full bg-[radial-gradient(circle,rgba(124,58,237,0.12),transparent_65%)] blur-3xl dark:bg-[radial-gradient(circle,rgba(139,92,246,0.08),transparent_65%)]"></div>
        <div class="absolute top-1/3 -right-20 h-[28rem] w-[40rem] rounded-full bg-[radial-gradient(circle,rgba(99,102,241,0.10),transparent_65%)] blur-3xl dark:bg-[radial-gradient(circle,rgba(99,102,241,0.07),transparent_65%)]"></div>
        <div class="absolute -bottom-20 left-1/3 h-[24rem] w-[36rem] rounded-full bg-[radial-gradient(circle,rgba(245,158,11,0.08),transparent_65%)] blur-3xl dark:bg-[radial-gradient(circle,rgba(251,191,36,0.05),transparent_65%)]"></div>
    </div>

    {{-- ── Navbar ── --}}
    <header class="sticky top-0 z-20 border-b border-zinc-200/60 bg-white/85 shadow-sm shadow-zinc-900/5 backdrop-blur-xl dark:border-zinc-800/60 dark:bg-zinc-950/85 dark:shadow-black/20">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-5 py-3.5 sm:px-8">

            {{-- Brand --}}
            <a href="{{ route('recipes.index') }}" class="group inline-flex items-center gap-2.5 font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-md shadow-violet-500/30 ring-1 ring-white/20 transition group-hover:shadow-lg group-hover:shadow-violet-500/40">
                    <img src="{{ asset('favicon.ico') }}" alt="" class="h-5 w-5 opacity-90">
                </span>
                <span class="hidden sm:inline">{{ config('app.name') }}</span>
            </a>

            {{-- Nav actions --}}
            <div class="flex items-center gap-2" x-data="themeToggle">
                @include('partials.locale-switcher')

                {{-- Dark mode toggle --}}
                <button
                    type="button"
                    @click="toggle()"
                    :aria-label="dark ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-zinc-200 bg-white/80 text-zinc-500 shadow-sm transition hover:border-violet-300 hover:bg-violet-50 hover:text-violet-600 dark:border-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-400 dark:hover:border-violet-600 dark:hover:bg-violet-950/50 dark:hover:text-violet-400"
                >
                    <svg x-show="!dark" viewBox="0 0 24 24" fill="none" aria-hidden="true" class="h-4 w-4">
                        <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 2v2M12 20v2M4 12H2M22 12h-2M19.07 4.93l-1.41 1.41M6.34 17.66l-1.41 1.41M19.07 19.07l-1.41-1.41M6.34 6.34 4.93 4.93" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <svg x-show="dark" x-cloak viewBox="0 0 24 24" fill="none" aria-hidden="true" class="h-4 w-4">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                @guest
                    <a href="{{ url('/admin/login') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white/80 px-3.5 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 dark:border-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-300 dark:hover:border-violet-600 dark:hover:bg-violet-950/50 dark:hover:text-violet-400">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 opacity-70" aria-hidden="true"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M6 10a.75.75 0 01.75-.75h9.546l-1.048-.943a.75.75 0 111.004-1.114l2.5 2.25a.75.75 0 010 1.114l-2.5 2.25a.75.75 0 11-1.004-1.114l1.048-.943H6.75A.75.75 0 016 10z" clip-rule="evenodd"/></svg>
                        {{ __('Login') }}
                    </a>
                @endguest

                @auth
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-violet-500/25 transition hover:from-violet-500 hover:to-indigo-500 hover:shadow-lg hover:shadow-violet-500/35">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 opacity-85" aria-hidden="true"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" clip-rule="evenodd"/></svg>
                        {{ __('Backend') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-5 pb-24 sm:px-8">

        {{-- ── Hero heading ── --}}
        <div class="py-10 lg:py-14">
            <h1 class="text-4xl font-black tracking-[-0.04em] text-zinc-900 sm:text-5xl lg:text-6xl dark:text-zinc-50">
                {{ __('Recipes') }}
            </h1>
            <p class="mt-2 text-base text-zinc-500 dark:text-zinc-400">
                {{ __('Discover, cook and share your favourite dishes.') }}
            </p>
        </div>

        {{-- ── Filter panel ── --}}
        <section class="mb-8 overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-lg shadow-zinc-900/5 dark:border-zinc-800/80 dark:bg-zinc-900 dark:shadow-black/20">
            <form method="GET" action="{{ route('recipes.index') }}" class="p-4 sm:p-5" data-auto-submit-filters>

                {{-- Quick + Difficulty row --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    {{-- Quick filter --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Quick') }}</span>
                        <label @class([
                            'inline-flex min-h-7 cursor-pointer select-none items-center rounded-full border px-3 py-1 text-[0.65rem] font-bold uppercase tracking-[0.08em] leading-none transition',
                            'border-violet-500 bg-violet-50 text-violet-700 shadow-sm dark:border-violet-500 dark:bg-violet-500/15 dark:text-violet-400' => $quick,
                            'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-200' => !$quick,
                        ])>
                            <input type="checkbox" name="quick" value="1" class="sr-only" @checked($quick)>
                            {{ __('Max. 30 min') }}
                        </label>
                    </div>

                    {{-- Difficulty filter --}}
                    <div class="flex flex-wrap items-center gap-2 border-t border-dashed border-zinc-200 pt-3 sm:border-l sm:border-t-0 sm:pt-0 sm:pl-4 dark:border-zinc-700">
                        <span class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Difficulty') }}</span>
                        @php($complexityOptions = ['' => __('All'), 'simple' => __('Simple'), 'normal' => __('Normal'), 'difficult' => __('Difficult')])
                        @foreach ($complexityOptions as $value => $label)
                            <label @class([
                                'inline-flex min-h-7 cursor-pointer select-none items-center rounded-full border px-3 py-1 text-[0.65rem] font-bold uppercase tracking-[0.08em] leading-none transition',
                                'border-violet-500 bg-violet-50 text-violet-700 shadow-sm dark:border-violet-500 dark:bg-violet-500/15 dark:text-violet-400' => $complexity === $value,
                                'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-200' => $complexity !== $value,
                            ])>
                                <input type="radio" name="complexity" value="{{ $value }}" class="sr-only" @checked($complexity === $value)>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-12 gap-3">
                    {{-- Search --}}
                    <div class="col-span-12 grid gap-1">
                        <label for="search" class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Search') }}</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400 dark:text-zinc-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                            </svg>
                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="{{ __('Search for recipes…') }}"
                                class="h-10 w-full rounded-xl border border-zinc-200 bg-white pl-9 pr-3 text-sm text-zinc-800 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-violet-500 dark:focus:ring-violet-500/20"
                            >
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-span-12 grid gap-1 sm:col-span-4">
                        <label for="category" class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Category') }}</label>
                        <select id="category" name="category" class="h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-800 shadow-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:focus:border-violet-500 dark:focus:ring-violet-500/20">
                            <option value="">{{ __('All categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($selectedCategory === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Cookbook --}}
                    <div class="col-span-12 grid gap-1 sm:col-span-4">
                        <label for="cookbook" class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Cookbook') }}</label>
                        <select id="cookbook" name="cookbook" class="h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-800 shadow-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:focus:border-violet-500 dark:focus:ring-violet-500/20">
                            <option value="">{{ __('All cookbooks') }}</option>
                            @foreach ($cookbooks as $cookbook)
                                <option value="{{ $cookbook->id }}" @selected($selectedCookbook === $cookbook->id)>{{ $cookbook->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="col-span-12 grid gap-1 sm:col-span-4">
                        <label for="sort" class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Sort') }}</label>
                        <select id="sort" name="sort" class="h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-800 shadow-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:focus:border-violet-500 dark:focus:ring-violet-500/20">
                            <option value="created_at_desc" @selected($selectedSort === 'created_at_desc')>{{ __('Newest first') }}</option>
                            <option value="created_at_asc" @selected($selectedSort === 'created_at_asc')>{{ __('Oldest first') }}</option>
                            <option value="name_asc" @selected($selectedSort === 'name_asc')>{{ __('Name A-Z') }}</option>
                            <option value="name_desc" @selected($selectedSort === 'name_desc')>{{ __('Name Z-A') }}</option>
                            <option value="complexity_asc" @selected($selectedSort === 'complexity_asc')>{{ __('Difficulty low-high') }}</option>
                            <option value="complexity_desc" @selected($selectedSort === 'complexity_desc')>{{ __('Difficulty high-low') }}</option>
                        </select>
                    </div>

                    {{-- Tags --}}
                    @if ($tags->isNotEmpty())
                        <div class="col-span-12 grid gap-1.5">
                            <p class="text-[0.6rem] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Tags') }}</p>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach ($tags as $tag)
                                    @php($isSelected = in_array($tag->id, $selectedTags, true))
                                    <label @class([
                                        'inline-flex min-h-7 cursor-pointer select-none items-center rounded-full border px-3 py-1 text-[0.65rem] font-bold uppercase tracking-[0.08em] leading-none transition',
                                        'border-violet-500 bg-violet-50 text-violet-700 shadow-sm dark:border-violet-500 dark:bg-violet-500/15 dark:text-violet-400' => $isSelected,
                                        'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-200' => !$isSelected,
                                    ])>
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only" @checked($isSelected)>
                                        {{ $tag->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="col-span-12 flex items-center gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                        <a href="{{ route('recipes.index') }}" class="inline-flex h-8 items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 text-xs font-semibold text-zinc-600 shadow-sm transition hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-200">
                            <svg viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3 opacity-70" aria-hidden="true"><path d="M2.22 2.22a.75.75 0 011.06 0L8 6.94l4.72-4.72a.75.75 0 111.06 1.06L9.06 8l4.72 4.72a.75.75 0 11-1.06 1.06L8 9.06l-4.72 4.72a.75.75 0 01-1.06-1.06L6.94 8 2.22 3.28a.75.75 0 010-1.06z"/></svg>
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </section>

        {{-- ── Recipe grid ── --}}
        <section class="grid gap-4 sm:grid-cols-2 cards:grid-cols-3 md:gap-5 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($recipes as $recipe)
                <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-md shadow-zinc-900/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/10 dark:border-zinc-800/80 dark:bg-zinc-900 dark:hover:shadow-violet-500/10">
                    <a href="{{ route('recipes.show', $recipe) }}" class="flex flex-col">

                        {{-- Thumbnail --}}
                        @if ($recipe->photos->isNotEmpty())
                            <div class="relative aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                <img
                                    src="{{ $recipe->photos->first()->url() }}?v={{ $recipe->updated_at->timestamp }}"
                                    alt="{{ $recipe->name }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]"
                                    loading="lazy"
                                >
                                {{-- Category pill overlay --}}
                                @if ($recipe->category)
                                    <span class="absolute bottom-2 left-2 inline-flex items-center rounded-full bg-black/50 px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-white backdrop-blur-sm">
                                        {{ $recipe->category->name }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="relative hidden aspect-[4/3] items-center justify-center overflow-hidden bg-gradient-to-br from-violet-50 to-indigo-50 sm:flex dark:from-violet-950/30 dark:to-indigo-950/30">
                                <svg viewBox="0 0 48 48" fill="none" class="h-12 w-12 text-violet-200 dark:text-violet-900" aria-hidden="true">
                                    <path d="M24 4C13 4 4 13 4 24s9 20 20 20 20-9 20-20S35 4 24 4z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M16 20c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8-8 8-8-3.6-8-8z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8.9 36.6C10.8 32 17 28 24 28s13.2 4 15.1 8.6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                @if ($recipe->category)
                                    <span class="absolute bottom-2 left-2 inline-flex items-center rounded-full bg-violet-100/80 px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-violet-700 dark:bg-violet-900/50 dark:text-violet-400">
                                        {{ $recipe->category->name }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Content --}}
                        <div class="flex flex-1 flex-col gap-2 p-4">
                            {{-- Cookbook badge --}}
                            @if ($recipe->cookbook)
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[0.6rem] font-bold uppercase tracking-wide text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400">
                                        <svg viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3" aria-hidden="true"><path d="M3 2.5h10a.5.5 0 01.5.5v9a.5.5 0 01-.5.5H3a.5.5 0 01-.5-.5V3a.5.5 0 01.5-.5z"/></svg>
                                        {{ $recipe->cookbook->name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Title --}}
                            <h2 class="line-clamp-2 text-[1rem] font-bold leading-snug tracking-tight text-zinc-900 transition group-hover:text-violet-700 dark:text-zinc-100 dark:group-hover:text-violet-400">{{ $recipe->name }}</h2>

                            {{-- Description --}}
                            <p class="line-clamp-3 text-[0.82rem] leading-relaxed text-zinc-500 dark:text-zinc-400">{{ \Illuminate\Support\Str::limit(strip_tags((string) $recipe->instructions), 130) }}</p>

                            {{-- Metadata --}}
                            <div class="mt-auto flex flex-wrap items-center gap-1.5 pt-2">
                                @if ($recipe->complexity)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-sky-700 dark:bg-sky-500/15 dark:text-sky-400">
                                        {{ $recipe->complexity->label() }}
                                    </span>
                                @endif
                                @if ($recipe->preparation_time)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2 py-0.5 text-[0.65rem] font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        <svg viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3 opacity-70" aria-hidden="true"><path fill-rule="evenodd" d="M1 8a7 7 0 1114 0A7 7 0 011 8zm7-4.75a.75.75 0 00-.75.75v3.5c0 .414.336.75.75.75h2.5a.75.75 0 000-1.5H8.75V4a.75.75 0 00-.75-.75z" clip-rule="evenodd"/></svg>
                                        {{ $recipe->preparation_time->format('H:i') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Author + Date --}}
                            <div class="flex items-center justify-between gap-2 border-t border-zinc-100 pt-2.5 text-[0.72rem] text-zinc-400 dark:border-zinc-800 dark:text-zinc-500">
                                <span class="font-medium">{{ $recipe->author?->name }}</span>
                                <span>{{ $recipe->created_at?->isoFormat('ll') }}</span>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="col-span-full flex flex-col items-center gap-4 rounded-2xl border border-zinc-200/80 bg-white p-12 text-center dark:border-zinc-800/80 dark:bg-zinc-900">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-950/50">
                        <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 text-violet-400" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('No recipes found.') }}</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-500">{{ __('Try adjusting your filters.') }}</p>
                    </div>
                    <a href="{{ route('recipes.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-violet-500">
                        {{ __('Reset filters') }}
                    </a>
                </div>
            @endforelse
        </section>

        {{-- ── Pagination ── --}}
        @if ($recipes->hasPages())
            <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="{{ __('Pagination') }}">
                {{-- Previous --}}
                <a
                    href="{{ $recipes->onFirstPage() ? '#' : $recipes->previousPageUrl() }}"
                    @class([
                        'inline-flex h-10 items-center gap-1.5 rounded-xl border px-4 text-sm font-semibold transition',
                        'border-zinc-200 bg-white text-zinc-700 shadow-sm hover:border-violet-300 hover:text-violet-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-violet-600 dark:hover:text-violet-400' => !$recipes->onFirstPage(),
                        'pointer-events-none border-zinc-100 bg-zinc-50 text-zinc-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-600' => $recipes->onFirstPage(),
                    ])
                >
                    <svg viewBox="0 0 16 16" fill="currentColor" class="h-3.5 w-3.5" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 3.22a.75.75 0 010 1.06L8.06 8l3.72 3.72a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>
                    {{ __('Previous') }}
                </a>

                {{-- Page numbers --}}
                @foreach ($paginationPages as $page)
                    @if (is_null($page))
                        <span class="px-1 text-sm font-bold text-zinc-400 dark:text-zinc-600">…</span>
                    @else
                        <a
                            href="{{ $recipes->url($page) }}"
                            @class([
                                'inline-flex h-10 min-w-10 items-center justify-center rounded-xl border text-sm font-bold transition',
                                'border-zinc-200 bg-white text-zinc-700 shadow-sm hover:border-violet-300 hover:text-violet-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-violet-600 dark:hover:text-violet-400' => $page !== $currentPage,
                                'border-violet-500 bg-violet-600 text-white shadow-md shadow-violet-500/20' => $page === $currentPage,
                            ])
                        >{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                <a
                    href="{{ $recipes->hasMorePages() ? $recipes->nextPageUrl() : '#' }}"
                    @class([
                        'inline-flex h-10 items-center gap-1.5 rounded-xl border px-4 text-sm font-semibold transition',
                        'border-zinc-200 bg-white text-zinc-700 shadow-sm hover:border-violet-300 hover:text-violet-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-violet-600 dark:hover:text-violet-400' => $recipes->hasMorePages(),
                        'pointer-events-none border-zinc-100 bg-zinc-50 text-zinc-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-600' => !$recipes->hasMorePages(),
                    ])
                >
                    {{ __('Next') }}
                    <svg viewBox="0 0 16 16" fill="currentColor" class="h-3.5 w-3.5" aria-hidden="true"><path fill-rule="evenodd" d="M4.22 3.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06L5.28 12.78a.75.75 0 01-1.06-1.06L8.94 8 4.22 4.28a.75.75 0 010-1.06z" clip-rule="evenodd"/></svg>
                </a>
            </nav>
        @endif

    </main>

    <script>
        (function () {
            const form = document.querySelector('[data-auto-submit-filters]');
            if (!form) return;

            let searchTimer;
            const submitForm = () => form.requestSubmit();

            form.querySelectorAll('select, input[type="checkbox"], input[type="radio"]').forEach((field) => {
                field.addEventListener('change', submitForm);
            });

            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimer);
                    searchTimer = window.setTimeout(submitForm, 600);
                });
            }
        }());
    </script>
</body>
</html>
