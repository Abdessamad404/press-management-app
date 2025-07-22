<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    private $categories = [
        'Économie',
        'Industrie',
        'Innovation',
        'Politique',
        'Technology'
    ];

    public function index(Request $request)
    {
        $query = Article::with('author');

        // Filtres
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
            });
        }

        // Autorisation selon le rôle
        if (Auth::user()->hasRole('writer')) {
            $query->where('author_id', Auth::id());
        }

        // Tri
        $sortBy = $request->get('sort', 'date');
        $direction = $request->get('direction', $sortBy === 'category' ? 'asc' : 'desc');
        
        if ($sortBy === 'category') {
            $query->orderBy('category', $direction);
            // Add secondary sort by date for consistent results
            if ($direction === 'asc') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('created_at', 'asc');
            }
        } else {
            // Sort by date
            $query->orderBy('created_at', $direction);
        }

        $articles = $query->paginate(10)->appends($request->query());

        return view('articles.index', [
            'articles' => $articles,
            'categories' => $this->categories,
            'filters' => $request->only(['category', 'status', 'author']),
            'sort' => $sortBy,
            'direction' => $direction
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category' => ['required', Rule::in($this->categories)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'image' => $imagePath,
            'author_id' => Auth::id(),
            'status' => 'draft'
        ]);

        return redirect()->route('articles.index')
            ->with('success', 'Article créé avec succès!');
    }

    public function update(Request $request, Article $article)
    {
        // Vérifier l'autorisation: (writer AND owns article AND status is draft) OR (editor)
        $user = Auth::user();
        $isWriter = $user->hasRole('writer');
        $isEditor = $user->hasRole('editor');
        $ownsArticle = $article->author_id === $user->id;

        if (!$isEditor && (!$isWriter || !$ownsArticle || $article->getRawOriginal('status') !== 'draft')) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category' => ['required', Rule::in($this->categories)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Article mis à jour!');
    }

    public function destroy(Article $article)
    {
        // Vérifier l'autorisation: (writer AND owns article AND status is draft) OR (editor)
        $user = Auth::user();
        $isWriter = $user->hasRole('writer');
        $isEditor = $user->hasRole('editor');
        $ownsArticle = $article->author_id === $user->id;

        if (!$isEditor && (!$isWriter || !$ownsArticle || $article->getRawOriginal('status') !== 'draft')) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        // Delete image if it exists
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé!');
    }

    public function submit(Article $article)
    {
        $user = Auth::user();
        $isWriter = $user->hasRole('writer');
        $isEditor = $user->hasRole('editor');
        $ownsArticle = $article->author_id === $user->id;

        // Allow writers and editors to submit their own articles
        if (!($isWriter || $isEditor) || !$ownsArticle) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        if ($article->getRawOriginal('status') !== 'draft') {
            return redirect()->route('articles.index')
                ->with('error', 'Seuls les articles en brouillon peuvent être soumis.');
        }

        $article->update(['status' => 'pending']);
        return redirect()->route('articles.index')->with('success', 'Article soumis pour validation!');
    }

    public function approve(Article $article)
    {
        if (!Auth::user()->hasRole('editor')) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        if ($article->getRawOriginal('status') !== 'pending') {
            return redirect()->route('articles.index')
                ->with('error', 'Seuls les articles en attente peuvent être approuvés.');
        }

        $article->update(['status' => 'approved']);
        return redirect()->route('articles.index')->with('success', 'Article approuvé!');
    }

    public function reject(Article $article)
    {
        if (!Auth::user()->hasRole('editor')) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        if ($article->getRawOriginal('status') !== 'pending') {
            return redirect()->route('articles.index')
                ->with('error', 'Seuls les articles en attente peuvent être rejetés.');
        }

        $article->update(['status' => 'rejected']);
        return redirect()->route('articles.index')->with('success', 'Article rejeté.');
    }

    // Page publique
    public function publicIndex(Request $request)
    {
        $query = Article::with('author');

        // Only show approved articles on public page
        $query->where('status', 'approved');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $sortBy = $request->get('sort', 'date');
        $direction = $request->get('direction', 'desc'); // Default to newest first for date
        
        if ($sortBy === 'category') {
            $query->orderBy('category', $direction);
            // Add secondary sort by date for consistent results
            $query->orderBy('created_at', 'desc');
        } else {
            // Sort by date only
            $query->orderBy('created_at', $direction);
        }

        $articles = $query->paginate(12)->appends($request->query());

        return view('articles.public', [
            'articles' => $articles,
            'categories' => $this->categories,
            'filters' => $request->only(['category']),
            'sort' => $sortBy,
            'direction' => $direction
        ]);
    }
}