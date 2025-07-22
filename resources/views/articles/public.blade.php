<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles - Press Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Header Public -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Press Management</h1>
                        <span class="ml-3 px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm">
                            Articles Publics
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="/login" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            Connexion
                        </a>
                        
                        <!-- Dark Mode Toggle -->
                        <button 
                            onclick="toggleDarkMode()"
                            class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
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

        <!-- Contenu Principal -->
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" x-data="publicFilters()">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <!-- Filtres -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Catégorie
                                </label>
                                <select 
                                    x-model="filters.category"
                                    @change="applyFilters()"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
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
                            <select 
                                x-model="sort"
                                @change="applyFilters()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="date">Date (plus récent)</option>
                                <option value="category">Catégorie</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Grille d'articles -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($articles as $article)
                        <article class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <!-- Image placeholder -->
                            <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-lg relative">
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
                                    onclick="openModal({{ $article->id }})"
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
        </main>

        <!-- Modal pour afficher l'article complet -->
        <div id="articleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="closeModal()">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto" onclick="event.stopPropagation()">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 dark:text-white mb-2"></h2>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <span id="modalCategory" class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs font-medium mr-3"></span>
                                    <span id="modalDate"></span>
                                    <span class="mx-2">•</span>
                                    <span id="modalAuthor"></span>
                                </div>
                            </div>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div id="modalContent" class="prose dark:prose-invert max-w-none">
                            <!-- Le contenu sera injecté ici -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Articles data pour JavaScript -->
    <script>
        const articlesData = @json($articles->items());

        function publicFilters() {
            return {
                filters: {
                    category: "{{ $filters['category'] ?? '' }}""
                },
                sort: '{{ $sort }}',
                
                applyFilters() {
                    const params = new URLSearchParams();
                    
                    if (this.filters.category) params.append('category', this.filters.category);
                    if (this.sort) params.append('sort', this.sort);
                    
                    window.location.href = `{{ route('articles.public') }}?${params.toString()}`;
                }
            }
        }

        function openModal(articleId) {
            const article = articlesData.find(a => a.id === articleId);
            if (!article) return;

            document.getElementById('modalTitle').textContent = article.title;
            document.getElementById('modalCategory').textContent = article.category;
            document.getElementById('modalDate').textContent = new Date(article.created_at).toLocaleDateString('fr-FR');
            document.getElementById('modalAuthor').textContent = article.author.name;
            document.getElementById('modalContent').innerHTML = article.content.replace(/\n/g, '<br>');
            
            document.getElementById('articleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('articleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.contains('dark');
            
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Initialiser le thème au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        });

        // Fermer modal avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
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
    </style>
</body>
</html>