<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::with('user')->latest()->paginate(7);

        return view('announcements.index', [
            'announcements' => $announcements,
            'total'         => Announcement::count(),
            'active'        => Announcement::where(
                fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now())
            )->count(),
            'expired'       => Announcement::where('expires_at', '<', now())->count(),
            'audiences'     => [
                'all'      => 'Tous',
                'teachers' => 'Enseignants',
                'students' => 'Élèves',
                'parents'  => 'Parents',
            ],
        ]);
    }

    public function create(): View
    {
        $announcement = new Announcement();

        return view('announcements.form', [
            'announcement' => $announcement,
            'audiences'    => [
                'all'      => 'Tous',
                'teachers' => 'Enseignants',
                'students' => 'Élèves',
                'parents'  => 'Parents',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:200'],
            'content'    => ['required', 'string'],
            'audience'   => ['required', 'in:all,teachers,students,parents'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_pinned'  => ['boolean'],
        ], [
            'title.required'    => 'Le titre est obligatoire.',
            'content.required'  => 'Le contenu est obligatoire.',
            'audience.required' => 'L\'audience est obligatoire.',
            'expires_at.after'  => 'La date d\'expiration doit être dans le futur.',
        ]);

        $validated['user_id']   = Auth::id();
        $validated['is_pinned'] = $request->boolean('is_pinned');

        $announcement = Announcement::create($validated);

        return to_route('announcements.index')
            ->with('success', "L'annonce « {$announcement->title} » a été publiée avec succès.");
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load('user');

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement): View
    {
        return view('announcements.form', [
            'announcement' => $announcement,
            'audiences'    => [
                'all'      => 'Tous',
                'teachers' => 'Enseignants',
                'students' => 'Élèves',
                'parents'  => 'Parents',
            ],
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:200'],
            'content'    => ['required', 'string'],
            'audience'   => ['required', 'in:all,teachers,students,parents'],
            'expires_at' => ['nullable', 'date'],
            'is_pinned'  => ['boolean'],
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned');

        $announcement->update($validated);

        return to_route('announcements.index')
            ->with('success', "L'annonce « {$announcement->title} » a été mise à jour.");
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $title = $announcement->title;
        $announcement->delete();

        return to_route('announcements.index')
            ->with('success', "L'annonce « {$title} » a été supprimée.");
    }

    /**
     * Renouveler une annonce expirée.
     */
    public function renew(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'expires_at' => ['required', 'date', 'after:now'],
        ], [
            'expires_at.required' => 'La nouvelle date d\'expiration est obligatoire.',
            'expires_at.after'    => 'La date doit être dans le futur.',
        ]);

        $announcement->update(['expires_at' => $validated['expires_at']]);

        return to_route('announcements.index')
            ->with('success', "L'annonce a été renouvelée jusqu'au " .
                \Carbon\Carbon::parse($validated['expires_at'])->format('d/m/Y') . '.');
    }

    /**
     * Réordonner les annonces via SortableJS (AJAX).
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:announcements,id'],
        ]);

        foreach ($request->input('order') as $position => $id) {
            Announcement::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}