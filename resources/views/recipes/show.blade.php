<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $recipe->name }} — {{ config('app.name') }}</title>
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
        <div class="absolute -top-40 -left-20 h-[32rem] w-[52rem] rounded-full bg-[radial-gradient(circle,rgba(124,58,237,0.10),transparent_65%)] blur-3xl dark:bg-[radial-gradient(circle,rgba(139,92,246,0.07),transparent_65%)]"></div>
        <div class="absolute top-1/2 -right-20 h-[24rem] w-[36rem] rounded-full bg-[radial-gradient(circle,rgba(245,158,11,0.07),transparent_65%)] blur-3xl dark:bg-[radial-gradient(circle,rgba(251,191,36,0.04),transparent_65%)]"></div>
    </div>

    {{-- ── Navbar ── --}}
    <header class="sticky top-0 z-20 border-b border-zinc-200/60 bg-white/85 shadow-sm shadow-zinc-900/5 backdrop-blur-xl dark:border-zinc-800/60 dark:bg-zinc-950/85 dark:shadow-black/20">
        <div class="mx-auto flex w-full max-w-5xl items-center justify-between gap-4 px-5 py-3.5 sm:px-8">

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
                        {{ __('Login') }}
                    </a>
                @endguest

                @auth
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-violet-500/25 transition hover:from-violet-500 hover:to-indigo-500">
                        {{ __('Backend') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main
        class="mx-auto w-full max-w-5xl px-5 pb-32 sm:px-8"
        x-data="recipeServings({ initial: {{ $recipe->servings ?? 'null' }} })"
    >

        {{-- ── Breadcrumb / back + edit ── --}}
        <div class="flex items-center justify-between gap-3 py-6">
            <a href="{{ route('recipes.index') }}" class="group inline-flex items-center gap-1.5 text-sm font-semibold text-zinc-500 transition hover:text-violet-700 dark:text-zinc-400 dark:hover:text-violet-400">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 transition group-hover:-translate-x-0.5" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/>
                </svg>
                {{ __('Back to recipes') }}
            </a>

            @if (user() && (user()->admin || user()->author_id === $recipe->author_id))
                <a
                    href="{{ \App\Filament\Resources\Recipes\RecipeResource::getUrl('edit', ['record' => $recipe]) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-violet-600 dark:hover:bg-violet-950/50 dark:hover:text-violet-400"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 opacity-70" aria-hidden="true"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z"/><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z"/></svg>
                    {{ __('Edit') }}
                </a>
            @endif
        </div>

        {{-- ── Article ── --}}
        <article class="rounded-2xl border border-zinc-200/80 bg-white shadow-lg shadow-zinc-900/5 dark:border-zinc-800/80 dark:bg-zinc-900 dark:shadow-black/20">

            {{-- Header section --}}
            <div class="p-6 md:p-8">
                {{-- Category + Cookbook --}}
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    @if ($recipe->category)
                        <a
                            href="{{ route('recipes.index', ['category' => $recipe->category->id]) }}"
                            class="inline-flex items-center rounded-full border border-violet-200 bg-violet-50 px-3 py-0.5 text-[0.68rem] font-bold uppercase tracking-widest text-violet-700 transition hover:bg-violet-100 dark:border-violet-800 dark:bg-violet-950/50 dark:text-violet-400 dark:hover:bg-violet-950"
                        >{{ $recipe->category->name }}</a>
                    @endif
                    @if ($recipe->cookbook)
                        <span class="inline-flex items-center gap-1 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-0.5 text-[0.68rem] font-bold uppercase tracking-widest text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-400">
                            <svg viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3" aria-hidden="true"><path d="M3 2.5h10a.5.5 0 01.5.5v9a.5.5 0 01-.5.5H3a.5.5 0 01-.5-.5V3a.5.5 0 01.5-.5z"/></svg>
                            {{ $recipe->cookbook->name }}
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-3xl font-black leading-tight tracking-[-0.03em] text-zinc-900 sm:text-4xl lg:text-5xl dark:text-zinc-50">
                    {{ $recipe->name }}
                </h1>

                {{-- Author + date --}}
                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $recipe->author?->name }}</span>
                    @if ($recipe->created_at)
                        <span class="mx-1.5 text-zinc-300 dark:text-zinc-700">·</span>
                        <time datetime="{{ $recipe->created_at->toDateString() }}">{{ $recipe->created_at->isoFormat('LL') }}</time>
                    @endif
                </p>

                {{-- Meta badges --}}
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if ($recipe->servings)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 py-1.5 pl-3 pr-2 text-xs font-semibold leading-none text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-zinc-400 dark:text-zinc-500" aria-hidden="true"><path d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM1.615 16.428a1.224 1.224 0 01-.569-1.175 6.002 6.002 0 0111.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 017 17a9.953 9.953 0 01-5.385-1.572zM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 00-1.588-3.755 4.502 4.502 0 015.874 2.636.818.818 0 01-.36.98A7.465 7.465 0 0114.5 16z"/></svg>

                            <button
                                type="button"
                                @click="decrease()"
                                :disabled="servings <= 1"
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-200 hover:text-zinc-700 disabled:pointer-events-none disabled:opacity-30 dark:text-zinc-500 dark:hover:bg-zinc-700 dark:hover:text-zinc-200"
                                aria-label="{{ __('Decrease servings') }}"
                            >
                                <svg viewBox="0 0 12 12" fill="none" class="h-2.5 w-2.5" aria-hidden="true"><path d="M2 6h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>

                            <span x-text="formatServings()"></span>{{ $recipe->serving_type ? ' '.$recipe->serving_type : '' }}

                            <button
                                type="button"
                                @click="increase()"
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-200 hover:text-zinc-700 dark:text-zinc-500 dark:hover:bg-zinc-700 dark:hover:text-zinc-200"
                                aria-label="{{ __('Increase servings') }}"
                            >
                                <svg viewBox="0 0 12 12" fill="none" class="h-2.5 w-2.5" aria-hidden="true"><path d="M6 2v8M2 6h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </span>
                    @endif

                    @if ($recipe->complexity)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold leading-none text-sky-700 dark:bg-sky-500/15 dark:text-sky-400">
                            @switch($recipe->complexity)
                                @case(\App\Enums\Complexity::Simple)
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true"><rect x="3" y="14" width="4" height="7" rx="1" fill="currentColor"/><rect x="10" y="9" width="4" height="12" rx="1" stroke="currentColor"/><rect x="17" y="4" width="4" height="17" rx="1" stroke="currentColor"/></svg>
                                    @break
                                @case(\App\Enums\Complexity::Normal)
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true"><rect x="3" y="14" width="4" height="7" rx="1" fill="currentColor"/><rect x="10" y="9" width="4" height="12" rx="1" fill="currentColor"/><rect x="17" y="4" width="4" height="17" rx="1" stroke="currentColor"/></svg>
                                    @break
                                @case(\App\Enums\Complexity::Difficult)
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true"><rect x="3" y="14" width="4" height="7" rx="1" fill="currentColor"/><rect x="10" y="9" width="4" height="12" rx="1" fill="currentColor"/><rect x="17" y="4" width="4" height="17" rx="1" fill="currentColor"/></svg>
                                    @break
                            @endswitch
                            {{ $recipe->complexity->label() }}
                        </span>
                    @endif

                    @if ($recipe->preparation_time)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-semibold leading-none text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-zinc-400 dark:text-zinc-500" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/></svg>
                            {{ $recipe->preparation_time->format('H:i') }}
                        </span>
                    @endif
                </div>

                {{-- Tags --}}
                @if ($recipe->tags->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($recipe->tags as $tag)
                            <a
                                href="{{ route('recipes.index', ['tags' => [$tag->id]]) }}"
                                class="inline-flex items-center rounded-full border border-zinc-200 px-2.5 py-0.5 text-[0.68rem] font-semibold text-zinc-600 transition hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 dark:border-zinc-700 dark:text-zinc-400 dark:hover:border-violet-600 dark:hover:bg-violet-950/40 dark:hover:text-violet-400"
                            >{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Photo(s) --}}
            @php($photoUrls = $recipe->photos->map(fn ($photo) => [
                'url'            => $photo->url().'?v='.$recipe->updated_at->timestamp,
                'source'         => $photo->source(),
                'is_ai_generated'=> $photo->isAiGenerated(),
            ])->values())

            @if ($photoUrls->count() > 1)
                <div
                    x-data="{
                        images: {{ \Illuminate\Support\Js::from($photoUrls) }},
                        current: 0,
                        touchStartX: null,
                        next() { this.current = (this.current + 1) % this.images.length; },
                        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                        onTouchStart(e) { this.touchStartX = e.changedTouches[0].clientX; },
                        onTouchEnd(e) {
                            if (this.touchStartX === null) return;
                            const d = e.changedTouches[0].clientX - this.touchStartX;
                            this.touchStartX = null;
                            if (Math.abs(d) < 40) return;
                            d < 0 ? this.next() : this.prev();
                        },
                    }"
                    class="grid gap-3 px-6 pb-6 md:px-8"
                >
                    <div
                        class="relative aspect-[16/9] overflow-hidden rounded-xl touch-pan-y"
                        @touchstart.passive="onTouchStart($event)"
                        @touchend.passive="onTouchEnd($event)"
                    >
                        <img :src="images[current].url" alt="{{ $recipe->name }}" class="h-full w-full object-cover">
                        <span
                            x-show="images[current].source || images[current].is_ai_generated"
                            x-cloak
                            class="absolute bottom-3 right-3 inline-flex items-center rounded-full border border-white/30 bg-zinc-900/60 px-3 py-1 text-[0.7rem] font-medium text-white backdrop-blur-sm"
                            x-text="images[current].is_ai_generated ? '{{ __('AI generated') }}' + (images[current].source ? ' · ' + images[current].source : '') : images[current].source"
                        ></span>
                        <button type="button" @click="prev()" aria-label="{{ __('Previous image') }}"
                            class="absolute left-3 top-1/2 -translate-y-1/2 inline-flex h-10 w-10 items-center justify-center rounded-full bg-zinc-900/60 text-white backdrop-blur-sm transition hover:bg-zinc-900/80 active:scale-95">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 010 1.06L8.06 10l3.72 3.72a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>
                        </button>
                        <button type="button" @click="next()" aria-label="{{ __('Next image') }}"
                            class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex h-10 w-10 items-center justify-center rounded-full bg-zinc-900/60 text-white backdrop-blur-sm transition hover:bg-zinc-900/80 active:scale-95">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06L9.28 14.78a.75.75 0 01-1.06-1.06L12.94 10 8.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2" role="tablist" aria-label="{{ __('Recipe images') }}">
                        <template x-for="(image, index) in images" :key="`dot-${index}`">
                            <button
                                type="button"
                                @click="current = index"
                                :aria-label="`{{ __('Image') }} ${index + 1}`"
                                class="h-2 rounded-full bg-zinc-300 transition-all duration-200 dark:bg-zinc-700"
                                :class="current === index ? 'w-6 bg-violet-500 dark:bg-violet-500' : 'w-2'"
                            ></button>
                        </template>
                    </div>
                </div>

            @elseif ($photoUrls->isNotEmpty())
                <div class="relative overflow-hidden px-6 pb-6 md:px-8">
                    <img
                        src="{{ $photoUrls->first()['url'] }}"
                        alt="{{ $recipe->name }}"
                        class="max-h-[28rem] w-full rounded-xl object-cover shadow-lg shadow-zinc-900/10 dark:shadow-black/30"
                    >
                    @if ($photoUrls->first()['source'] || $photoUrls->first()['is_ai_generated'])
                        <span class="absolute bottom-9 right-9 inline-flex items-center rounded-full border border-white/30 bg-zinc-900/60 px-3 py-1 text-[0.7rem] font-medium text-white backdrop-blur-sm">
                            @if ($photoUrls->first()['is_ai_generated'])
                                {{ __('AI generated') }}{{ $photoUrls->first()['source'] ? ' · '.$photoUrls->first()['source'] : '' }}
                            @else
                                {{ $photoUrls->first()['source'] }}
                            @endif
                        </span>
                    @endif
                </div>
            @endif

            {{-- ── Ingredients + Instructions ── --}}
            <div class="grid gap-0 divide-y divide-zinc-100 border-t border-zinc-100 md:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)] md:divide-x md:divide-y-0 dark:divide-zinc-800 dark:border-zinc-800">

                {{-- Ingredients --}}
                <section class="p-6 md:p-8">
                    <h2 class="mb-5 flex items-center gap-2.5 text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-violet-700 dark:bg-violet-950/60 dark:text-violet-400">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path fill-rule="evenodd" d="M6 4.75A.75.75 0 016.75 4h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 4.75zM6 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 10zm0 5.25a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75a.75.75 0 01-.75-.75zM1.99 4.75a1 1 0 011-1H3a1 1 0 110 2h-.01a1 1 0 01-1-1zM1.99 15.25a1 1 0 011-1H3a1 1 0 110 2h-.01a1 1 0 01-1-1zM1.99 10a1 1 0 011-1H3a1 1 0 110 2h-.01a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                        </span>
                        {{ __('Ingredients') }}
                    </h2>

                    <p
                        x-show="changed"
                        x-transition.opacity
                        x-cloak
                        class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs font-medium leading-relaxed text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400"
                    >
                        {{ __('Servings amounts are recalculated automatically and may not be perfectly accurate.') }}
                    </p>

                    <div class="space-y-6">
                        @php($ungroupedIngredients = $recipe->ingredients->whereNull('ingredient_group_id'))

                        @if ($ungroupedIngredients->isNotEmpty())
                            <ul class="grid gap-1.5 text-sm text-zinc-700 dark:text-zinc-300">
                                @foreach ($ungroupedIngredients->filter(fn ($ingredient) => !$ingredient->ingredient_id) as $ingredient)
                                    @include('recipes.partials.ingredient', ['ingredient' => $ingredient])
                                @endforeach
                            </ul>
                        @endif

                        @foreach ($recipe->ingredientGroups as $group)
                            @php($groupIngredients = $recipe->ingredients->where('ingredient_group_id', $group->id))
                            <div>
                                <h3 class="mb-2.5 text-sm font-bold tracking-tight text-zinc-700 dark:text-zinc-300">
                                    {{ $group->name }}
                                </h3>
                                <ul class="grid gap-1.5 text-sm text-zinc-700 dark:text-zinc-300">
                                    @foreach ($groupIngredients->filter(fn ($ingredient) => !$ingredient->ingredient_id) as $ingredient)
                                        @include('recipes.partials.ingredient', ['ingredient' => $ingredient])
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Instructions --}}
                <section class="p-6 md:p-8">
                    <h2 class="mb-5 flex items-center gap-2.5 text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path d="M10.75 16.82A7.462 7.462 0 0115 15.5c.71 0 1.396.098 2.046.282A.75.75 0 0018 15.06v-11a.75.75 0 00-.546-.721A9.006 9.006 0 0015 3a8.963 8.963 0 00-4.25 1.065V16.82zM9.25 4.065A8.963 8.963 0 005 3c-.85 0-1.673.118-2.454.339A.75.75 0 002 4.06v11a.75.75 0 00.954.721A7.506 7.506 0 015 15.5c1.579 0 3.042.487 4.25 1.32V4.065z"/></svg>
                        </span>
                        {{ __('Preparation') }}
                    </h2>

                    <div class="prose-recipe text-sm leading-7 text-zinc-700 dark:text-zinc-300
                        [&_h1]:mt-5 [&_h1]:mb-2 [&_h1]:text-base [&_h1]:font-bold [&_h1]:text-zinc-900 dark:[&_h1]:text-zinc-100
                        [&_h2]:mt-5 [&_h2]:mb-2 [&_h2]:text-base [&_h2]:font-bold [&_h2]:text-zinc-900 dark:[&_h2]:text-zinc-100
                        [&_h3]:mt-4 [&_h3]:mb-1 [&_h3]:text-sm [&_h3]:font-semibold [&_h3]:text-zinc-800 dark:[&_h3]:text-zinc-200
                        [&_p]:my-3 [&_ul]:my-3 [&_ol]:my-3
                        [&_ul]:ml-5 [&_ul]:list-disc [&_ul]:space-y-1
                        [&_ol]:ml-5 [&_ol]:list-decimal [&_ol]:space-y-2
                        [&_li>p]:my-0
                        [&_blockquote]:border-l-4 [&_blockquote]:border-violet-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-zinc-500 dark:[&_blockquote]:border-violet-700 dark:[&_blockquote]:text-zinc-400
                        *:first:mt-0 *:last:mb-0">
                        {!! $recipe->instructions !!}
                    </div>

                    @if ($recipe->source)
                        <div class="mt-5 flex items-center gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                            <svg viewBox="0 0 16 16" fill="currentColor" class="h-3.5 w-3.5 flex-shrink-0 text-zinc-400 dark:text-zinc-500" aria-hidden="true"><path d="M8.75 2.75a.75.75 0 00-1.5 0v5.69L5.03 6.22a.75.75 0 00-1.06 1.06l3.5 3.5a.75.75 0 001.06 0l3.5-3.5a.75.75 0 00-1.06-1.06L8.75 8.44V2.75z"/><path d="M3.5 9.75a.75.75 0 00-1.5 0v1.5A2.75 2.75 0 004.75 14h6.5A2.75 2.75 0 0014 11.25v-1.5a.75.75 0 00-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5z"/></svg>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                <span class="font-semibold text-zinc-600 dark:text-zinc-300">{{ __('Source') }}:</span>
                                {{ $recipe->source }}
                            </p>
                        </div>
                    @endif
                </section>
            </div>
        </article>
    </main>
</body>
</html>
