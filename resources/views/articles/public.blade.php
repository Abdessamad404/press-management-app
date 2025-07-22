<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Articles Publics</title>
    <script>
        window.setAppearance = function(appearance) {
            let setDark = () => document.documentElement.classList.add('dark')
            let setLight = () => document.documentElement.classList.remove('dark')
            if (appearance === 'system') {
                let media = window.matchMedia('(prefers-color-scheme: dark)')
                window.localStorage.removeItem('appearance')
                media.matches ? setDark() : setLight()
            } else if (appearance === 'dark') {
                window.localStorage.setItem('appearance', 'dark')
                setDark()
            } else if (appearance === 'light') {
                window.localStorage.setItem('appearance', 'light')
                setLight()
            }
        }
        window.setAppearance(window.localStorage.getItem('appearance') || 'system')
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased">
    <div class="min-h-screen flex flex-col" x-data="publicApp()">
        <!-- Header Public -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</h1>
                        <span class="ml-3 px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm font-medium">
                            Articles Publics
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="/login" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm">
                            Connexion
                        </a>
                        
                        <!-- Dark Mode Toggle -->
                        <button 
                            onclick="toggleDarkMode()"
                            class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            aria-label="Toggle dark mode"
                        >
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 bg-gray-100 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <!-- Header avec statistiques -->
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Articles de Presse
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $articles->total() }} articles publiés
                        </p>
                    </div>

                    <!-- Filtres et Tri -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                            <!-- Filtres -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Catégorie
                                    </label>
                                    <select 
                                        x-model="category"
                                        @change="applyFilters()"
                                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="">Toutes les catégories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <!-- Tri -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Trier par
                                </label>
                                <button 
                                    @click="toggleSort('date')"
                                    :class="sortBy === 'date' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                                    class="px-3 py-2 rounded-md text-sm font-medium border border-gray-300 dark:border-gray-600 hover:bg-blue-50 dark:hover:bg-gray-600 transition-colors flex items-center"
                                >
                                    Date
                                    <span x-show="sortBy === 'date'" class="ml-1">
                                        <svg x-show="direction === 'desc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="direction === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Grille d'articles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($articles as $article)
                            <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                                <!-- Article Image -->
                                <div class="h-48 rounded-t-lg relative overflow-hidden">
                                    @if($article->image)
                                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white/90 text-gray-800">
                                            {{ $article->category }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Contenu -->
                                <div class="p-6">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $article->created_at->format('d M Y') }}
                                        
                                        <span class="mx-2">•</span>
                                        
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $article->author->name }}
                                    </div>

                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2">
                                        {{ $article->title }}
                                    </h3>

                                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                        {{ Str::limit($article->content, 120) }}
                                    </p>

                                    <button 
                                        @click="openModal({{ $article->id }})"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                                    >
                                        Lire la suite
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun article</h3>
                                <p class="text-gray-500 dark:text-gray-400">Aucun article publié pour le moment.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="flex justify-center">
                            {{ $articles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- Modal pour afficher l'article complet -->
        <div x-show="showModal" 
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeModal()"
             @keydown.escape.window="closeModal()"
             style="display: none;">
             
            <div @click.stop 
                 class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto shadow-xl border border-gray-200 dark:border-gray-700"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                 
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h2 x-text="currentArticle?.title" class="text-2xl font-bold text-gray-900 dark:text-white mb-2"></h2>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="currentArticle?.category" class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs font-medium mr-3"></span>
                                <span x-text="currentArticle?.formatted_date"></span>
                                <span class="mx-2">•</span>
                                <span x-text="currentArticle?.author?.name"></span>
                            </div>
                        </div>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Article Image -->
                    <div x-show="currentArticle?.image" class="mb-6">
                        <img :src="currentArticle?.image" :alt="currentArticle?.title" 
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                    
                    <div x-html="currentArticle?.formatted_content" class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js data and functionality
        document.addEventListener('alpine:init', () => {
            Alpine.data('publicApp', () => ({
                // Filter data
                category: "{{ $filters['category'] ?? '' }}",
                sortBy: '{{ $sort }}',
                direction: '{{ $direction }}',
                
                // Modal data
                showModal: false,
                currentArticle: null,
                
                // Articles data
                articles: @json($articles->items()),
                
                // Filter methods
                applyFilters() {
                    const params = new URLSearchParams();
                    
                    if (this.category) params.append('category', this.category);
                    if (this.sortBy) params.append('sort', this.sortBy);
                    if (this.direction) params.append('direction', this.direction);
                    
                    window.location.href = `{{ route('articles.public') }}?${params.toString()}`;
                },
                
                // Toggle sort method (only for date)
                toggleSort(newSortBy) {
                    if (newSortBy === 'date') {
                        this.sortBy = 'date';
                        this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                        this.applyFilters();
                    }
                },
                
                // Modal methods
                openModal(articleId) {
                    const article = this.articles.find(a => a.id === articleId);
                    if (!article) return;

                    // Format the article data
                    this.currentArticle = {
                        ...article,
                        formatted_date: new Date(article.created_at).toLocaleDateString('fr-FR'),
                        formatted_content: article.content.replace(/\n/g, '<br>'),
                        image: article.image ? `{{ asset('storage/') }}/${article.image}` : null
                    };
                    
                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },
                
                closeModal() {
                    this.showModal = false;
                    this.currentArticle = null;
                    document.body.style.overflow = 'auto';
                }
            }));
        });

        // Dark mode toggle using the starter kit system
        function toggleDarkMode() {
            const currentTheme = localStorage.getItem('appearance') || 'system';
            
            if (currentTheme === 'dark') {
                window.setAppearance('light');
            } else {
                window.setAppearance('dark');
            }
        }
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .prose {
            line-height: 1.75;
        }

        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            font-weight: 600;
        }

        .prose p {
            margin-bottom: 1.25em;
        }

        .prose ul, .prose ol {
            margin-top: 1.25em;
            margin-bottom: 1.25em;
        }
    </style>
</body>
</html>