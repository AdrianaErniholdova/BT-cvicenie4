<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*$notes = DB::table('notes')
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc')
            ->get();*/
        $notes = Note::query()
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['notes' => $notes], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*DB::table('notes')->insert([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
            'created_at' => now(),
            'updated_at' => now(),
        ]);*/
        $note = Note::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json([
            'message' => 'Poznámka bola úspešne vytvorená.',
            'note' => $note
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /*$note = DB::table('notes')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();*/
        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Poznámka nenájdená.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['note' => $note], Response::HTTP_OK);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //$note = DB::table('notes')->where('id', $id)->first();
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }
/*
        DB::table('notes')->where('id', $id)->update([
            'title' => $request->title,
            'body' => $request->body,
            'updated_at' => now(),
        ]);*/

        $note->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json(['note' => $note], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id) // toto je soft delete
    {
        /*$note = DB::table('notes')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();*/

        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Poznámka nenájdená.'], Response::HTTP_NOT_FOUND);
        }

        /*DB::table('notes')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);*/

//        DB::table('notes')->where('id', $id)->delete();

        $note->delete(); // soft delete

        return response()->json(['message' => 'Poznámka bola úspešne odstránená.'], Response::HTTP_OK);
    }

    public function statsByStatus()
    {
        /*$stats = DB::table('notes')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();*/
        $stats = Note::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'stats' => $stats
        ]);
    }

    public function archiveOldDrafts()
    {
        //$affected = DB::table('notes')
        $affected = Note::query()
            ->where('status', 'draft')
            ->where('updated_at', '<', now()->subDays(30))
            ->update([
                'status' => 'archived',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Staré koncepty boli archivované.',
            'affected_rows' => $affected
        ]);
    }

    public function userNotesWithCategories(string $userId)
    {
        //$notes = DB::table('notes')
        $notes = Note::query()
            ->join('note_category', 'notes.id', '=', 'note_category.note_id')
            ->join('categories', 'note_category.category_id', '=', 'categories.id')
            ->where('notes.user_id', $userId)
            ->orderByDesc('notes.updated_at')
            ->select('notes.id', 'notes.title', 'categories.name as category')
            ->get();

        return response()->json([
            'notes' => $notes
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        /*$notes = DB::table('notes')
            ->whereNull('deleted_at')
            ->where('status', 'published')
            ->where(function ($x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            })
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();*/
        $notes = Note::searchPublished($q);

        return response()->json([
            'query' => $q,
            'notes' => $notes,
        ], Response::HTTP_OK);
    }

    public function userPinnedNotes(string $userId) {

        //$notes = DB::table('notes')
        $notes = Note::query()
            ->where('user_id', $userId)
            ->where('is_pinned', 1)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'user_id' => $userId,
            'pinned_notes' => $notes
        ], Response::HTTP_OK);
    }

    public function pin(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $note->pin();

        return response()->json([
            'message' => 'Poznámka bola pripnutá.'
        ]);
    }

    public function unpin(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $note->unpin();

        return response()->json([
            'message' => 'Poznámka bola odopnutá.'
        ]);
    }

    public function publish(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $note->publish();

        return response()->json([
            'message' => 'Poznámka bola publikovaná.'
        ]);
    }

    public function archive(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $note->archive();

        return response()->json([
            'message' => 'Poznámka bola archivovaná.'
        ]);
    }
}
