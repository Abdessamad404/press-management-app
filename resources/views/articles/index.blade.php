<x-layouts.app>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="space-y-6" x-data="articleForm()">
        <!-- Messages Flash -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header avec bouton Nouvel Article -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des Articles</h1>
            <button @click="openNewArticle()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvel Article
            </button>
        </div>

        <!-- Modal pour créer/éditer -->
        <div x-show="showModal" x-cloak style="display: none !important;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click="closeModal()">

            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">

                <!-- Header du modal -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white"
                        x-text="editing ? 'Modifier l\'article' : 'Nouvel article'"></h2>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <div class="p-6">
                    <form :action="editing ? `/articles/${editingId}` : '/articles'" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <template x-if="editing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Titre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titre</label>
                            <input type="text" name="title" x-model="form.title"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Entrez le titre de l'article" required>
                            @error('title')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                            <select name="category" x-model="form.category"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image (optionnelle)</label>
                            
                            <!-- Current image preview when editing -->
                            <template x-if="editing && form.currentImage">
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Image actuelle :</p>
                                    <img :src="form.currentImage" alt="Image actuelle" 
                                         class="w-32 h-32 object-cover rounded-md border border-gray-300 dark:border-gray-600">
                                </div>
                            </template>
                            
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formats supportés: JPG, PNG, GIF. Taille max: 2MB</p>
                            @error('image')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contenu -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contenu</label>
                            <textarea name="content" x-model="form.content" rows="8"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Rédigez le contenu de votre article..." required></textarea>
                            @error('content')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Boutons du modal -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="closeModal()"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Annuler
                            </button>

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                <span x-text="editing ? 'Mettre à jour' : 'Créer l\'article'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour lire l'article -->
        <div x-show="showReadModal" x-cloak style="display: none !important;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click="closeReadModal()"
            @keydown.escape.window="closeReadModal()">

            <div @click.stop x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-screen overflow-y-auto border border-gray-200 dark:border-gray-700">

                <!-- Header du modal -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h2 x-text="readArticle.title" class="text-xl font-semibold text-gray-900 dark:text-white mb-2"></h2>
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': readArticle.status === 'Approuvé',
                                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': readArticle.status === 'En attente',
                                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': readArticle.status === 'Rejeté',
                                      'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': readArticle.status === 'Brouillon'
                                  }"
                                  x-text="readArticle.status"></span>
                            <span x-text="readArticle.category" class="font-medium"></span>
                            <span x-text="readArticle.author"></span>
                            <span x-text="readArticle.date"></span>
                        </div>
                    </div>
                    <button @click="closeReadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <div class="p-6">
                    <!-- Article Image -->
                    <div x-show="readArticle.image" class="mb-6">
                        <img :src="readArticle.image" :alt="readArticle.title" 
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                    
                    <div class="prose dark:prose-invert max-w-none">
                        <div x-html="readArticle.content" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres avec Alpine.js -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow" x-data="articleFilters()">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filtres</h3>
            </div>

            <div class="p-6">
                <div class="flex flex-col lg:flex-row gap-6 items-start justify-between">
                    <!-- Filtres -->
                    <div class="flex flex-col sm:flex-row gap-4 flex-1">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                            <select x-model="filters.category" @change="applyFilters()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Toutes les catégories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                            <select x-model="filters.status" @change="applyFilters()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Tous les statuts</option>
                                <option value="draft">Brouillon</option>
                                <option value="pending">En attente</option>
                                <option value="approved">Approuvé</option>
                                <option value="rejected">Rejeté</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Auteur</label>
                            <input type="text" x-model="filters.author" @input.debounce.300ms="applyFilters()"
                                placeholder="Nom de l'auteur"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
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
        </div>

        <!-- Liste des articles -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Articles ({{ $articles->total() }} total)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Titre</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Image</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Catégorie</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Auteur</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($articles as $article)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="viewArticle({{ $article->id }}, '{{ addslashes($article->title) }}', '{{ addslashes($article->content) }}', '{{ $article->category }}', '{{ $article->author->name }}', '{{ $article->created_at->format('d/m/Y') }}', '{{ ucfirst($article->status) }}', '{{ $article->image ? addslashes(asset('storage/' . $article->image)) : '' }}')">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $article->title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($article->content, 60) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($article->image)
                                        <img src="{{ asset('storage/' . $article->image) }}" alt="Image de l'article" 
                                             class="w-12 h-12 object-cover rounded-md">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-md flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $article->category }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $article->author->name }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if ($article->getRawOriginal('status') === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($article->getRawOriginal('status') === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($article->getRawOriginal('status') === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                        {{ $article->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $article->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        @php
                                            $user = auth()->user();
                                            $isEditor = $user->hasRole('editor');
                                            $isWriter = $user->hasRole('writer');
                                            $ownsArticle = $article->author_id === $user->id;
                                            $canEdit = $isEditor || ($isWriter && $ownsArticle && $article->getRawOriginal('status') === 'draft');
                                            $canDelete = $isEditor || ($isWriter && $ownsArticle && $article->getRawOriginal('status') === 'draft');
                                        @endphp

                                        <!-- Edit/Delete Actions -->
                                        @if ($canEdit)
                                            <button
                                                @click.stop="editArticle({{ $article->id }}, '{{ addslashes($article->title) }}', '{{ addslashes($article->content) }}', '{{ $article->category }}', '{{ $article->image ? addslashes(asset('storage/' . $article->image)) : '' }}')"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                Modifier
                                            </button>
                                        @endif

                                        @if ($canDelete)
                                            <form action="{{ route('articles.destroy', $article) }}"
                                                method="POST" class="inline" @click.stop
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Writer and Editor actions for own draft articles -->
                                        @if (($isWriter || $isEditor) && $ownsArticle && $article->getRawOriginal('status') === 'draft')
                                            <form action="{{ route('articles.submit', $article) }}" method="POST" class="inline" @click.stop>
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 font-medium">
                                                    Soumettre
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Editor actions for pending articles -->
                                        @if ($isEditor && $article->getRawOriginal('status') === 'pending')
                                            <form action="{{ route('articles.approve', $article) }}"
                                                method="POST" class="inline" @click.stop>
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                                    Approuver
                                                </button>
                                            </form>

                                            <form action="{{ route('articles.reject', $article) }}"
                                                method="POST" class="inline" @click.stop>
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    Rejeter
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p>Aucun article trouvé</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($articles->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function articleForm() {
            return {
                showModal: {{ $errors->any() ? 'true' : 'false' }},
                editing: false,
                editingId: null,
                form: {
                    title: '{{ old('title', '') }}',
                    content: '{{ old('content', '') }}',
                    category: '{{ old('category', '') }}',
                    currentImage: null
                },

                // Read modal data
                showReadModal: false,
                readArticle: {
                    id: null,
                    title: '',
                    content: '',
                    category: '',
                    author: '',
                    date: '',
                    status: '',
                    image: ''
                },

                openNewArticle() {
                    // Ne reset QUE si pas d'erreurs
                    @if (!$errors->any())
                        this.resetForm();
                    @endif
                    this.showModal = true;
                },

                editArticle(id, title, content, category, image) {
                    this.editing = true;
                    this.editingId = id;
                    this.form.title = title;
                    this.form.content = content;
                    this.form.category = category;
                    this.form.currentImage = image || null;
                    this.showModal = true;
                },

                resetForm() {
                    this.editing = false;
                    this.editingId = null;
                    this.form = {
                        title: '',
                        content: '',
                        category: '',
                        currentImage: null
                    };
                },

                closeModal() {
                    this.showModal = false;
                    this.resetForm(); // Reset seulement à la fermeture
                },

                // Read modal methods
                viewArticle(id, title, content, category, author, date, status, image) {
                    this.readArticle = {
                        id: id,
                        title: title,
                        content: content.replace(/\n/g, '<br>'),
                        category: category,
                        author: author,
                        date: date,
                        status: status,
                        image: image
                    };
                    this.showReadModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeReadModal() {
                    this.showReadModal = false;
                    document.body.style.overflow = 'auto';
                }
            }
        }

        function articleFilters() {
            return {
                filters: {
                    category: '{{ $filters['category'] ?? '' }}',
                    status: '{{ $filters['status'] ?? '' }}',
                    author: '{{ $filters['author'] ?? '' }}'
                },
                
                // Sorting data
                sortBy: '{{ $sort }}',
                direction: '{{ $direction }}',

                applyFilters() {
                    const params = new URLSearchParams();

                    if (this.filters.category) params.append('category', this.filters.category);
                    if (this.filters.status) params.append('status', this.filters.status);
                    if (this.filters.author) params.append('author', this.filters.author);
                    if (this.sortBy) params.append('sort', this.sortBy);
                    if (this.direction) params.append('direction', this.direction);

                    window.location.href = `{{ route('articles.index') }}?${params.toString()}`;
                },
                
                // Toggle sort method (only for date)
                toggleSort(newSortBy) {
                    if (newSortBy === 'date') {
                        this.sortBy = 'date';
                        this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                        this.applyFilters();
                    }
                }
            }
        }
    </script>
</x-layouts.app>
