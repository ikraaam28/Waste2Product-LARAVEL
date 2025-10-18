<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\PublicationReaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PublicationReactionController extends Controller
{
    public function like($id)
    {
        try {
            $publication = Publication::findOrFail($id);

            if (!Auth::check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $userId = Auth::id();
            
            if ($userId === $publication->user_id) {
                return response()->json(['error' => 'Cannot react to your own publication'], 403);
            }

            $existingReaction = PublicationReaction::where('user_id', $userId)
                ->where('publication_id', $publication->id)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->type === 'like') {
                    $existingReaction->delete();
                    $action = 'unliked';
                } else {
                    $existingReaction->update(['type' => 'like']);
                    $action = 'liked';
                }
            } else {
                PublicationReaction::create([
                    'user_id' => $userId,
                    'publication_id' => $publication->id,
                    'type' => 'like'
                ]);
                $action = 'liked';
            }

            $this->updateCounts($publication->id, $userId);

            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => PublicationReaction::where('publication_id', $publication->id)
                    ->where('type', 'like')->count(),
                'dislikes_count' => PublicationReaction::where('publication_id', $publication->id)
                    ->where('type', 'dislike')->count(),
                'user_reaction' => $this->getUserReaction($publication->id, $userId)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Like error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'publication_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function dislike($id)
    {
        try {
            $publication = Publication::findOrFail($id);

            if (!Auth::check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $userId = Auth::id();
            
            if ($userId === $publication->user_id) {
                return response()->json(['error' => 'Cannot react to your own publication'], 403);
            }

            $existingReaction = PublicationReaction::where('user_id', $userId)
                ->where('publication_id', $publication->id)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->type === 'dislike') {
                    $existingReaction->delete();
                    $action = 'undisliked';
                } else {
                    $existingReaction->update(['type' => 'dislike']);
                    $action = 'disliked';
                }
            } else {
                PublicationReaction::create([
                    'user_id' => $userId,
                    'publication_id' => $publication->id,
                    'type' => 'dislike'
                ]);
                $action = 'disliked';
            }

            $this->updateCounts($publication->id, $userId);

            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => PublicationReaction::where('publication_id', $publication->id)
                    ->where('type', 'like')->count(),
                'dislikes_count' => PublicationReaction::where('publication_id', $publication->id)
                    ->where('type', 'dislike')->count(),
                'user_reaction' => $this->getUserReaction($publication->id, $userId)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dislike error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'publication_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    private function updateCounts($publicationId, $userId)
    {
        // This method can be used for caching or other optimizations if needed
    }

    private function getUserReaction($publicationId, $userId)
    {
        return PublicationReaction::where('user_id', $userId)
            ->where('publication_id', $publicationId)
            ->value('type');
    }
}