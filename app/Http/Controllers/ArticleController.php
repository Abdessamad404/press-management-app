<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $articles = $query->latest()->paginate(10)->appends($request->query());

        return view('articles.index', [
            'articles' => $articles,
            'categories' => $this->categories,
            'filters' => $request->only(['category', 'status', 'author'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category' => ['required', Rule::in($this->categories)],
        ]);

        Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'author_id' => Auth::id(),
            'status' => 'draft'
        ]);

        return redirect()->route('articles.index')
            ->with('success', 'Article créé avec succès!');
    }

    public function update(Request $request, Article $article)
    {
        // Vérifier l'autorisation
        if (Auth::user()->hasRole('writer') && $article->author_id !== Auth::id()) {
            return redirect()->route('articles.index')
                ->with('error', 'Non autorisé.');
        }

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category' => ['required', Rule::in($this->categories)],
        ]);

        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Article mis à jour!');
    }

    public function destroy(Article $article)
    {
        if (Auth::user()->hasRole('writer') && $article->author_id !== Auth::id()) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        if ($article->status !== 'draft') {
            return redirect()->route('articles.index')
                ->with('error', 'Seuls les articles en brouillon peuvent être supprimés.');
        }

        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé!');
    }

    public function submit(Article $article)
    {
        if (Auth::user()->hasRole('writer') && $article->author_id !== Auth::id()) {
            return redirect()->route('articles.index')->with('error', 'Non autorisé.');
        }

        if ($article->status !== 'draft') {
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

        if ($article->status !== 'pending') {
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

        if ($article->status !== 'pending') {
            return redirect()->route('articles.index')
                ->with('error', 'Seuls les articles en attente peuvent être rejetés.');
        }

        $article->update(['status' => 'rejected']);
        return redirect()->route('articles.index')->with('success', 'Article rejeté.');
    }

    // Page publique
    public function publicIndex(Request $request)
    {
        $query = Article::with('author')->where('status', 'approved');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $sortBy = $request->get('sort', 'date');
        if ($sortBy === 'category') {
            $query->orderBy('category')->orderBy('created_at', 'desc');
        } else {
            $query->latest();
        }

        $articles = $query->paginate(12)->appends($request->query());

        return view('articles.public', [
            'articles' => $articles,
            'categories' => $this->categories,
            'filters' => $request->only(['category']),
            'sort' => $sortBy
        ]);
    }
}