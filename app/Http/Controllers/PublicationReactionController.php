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
            // Manually fetch publication by ID
            $publication = Publication::find($id);
            
            if (!$publication) {
                return response()->json(['error' => 'Publication not found'], 404);
            }

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

            $likesCount = PublicationReaction::where('publication_id', $publication->id)
                ->where('type', 'like')->count();
                
            $dislikesCount = PublicationReaction::where('publication_id', $publication->id)
                ->where('type', 'dislike')->count();

            $userReaction = PublicationReaction::where('user_id', $userId)
                ->where('publication_id', $publication->id)
                ->value('type');
            
            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'user_reaction' => $userReaction ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Like error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'publication_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function dislike($id)
    {
        try {
            $publication = Publication::find($id);
            
            if (!$publication) {
                return response()->json(['error' => 'Publication not found'], 404);
            }

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

            $likesCount = PublicationReaction::where('publication_id', $publication->id)
                ->where('type', 'like')->count();
                
            $dislikesCount = PublicationReaction::where('publication_id', $publication->id)
                ->where('type', 'dislike')->count();

            $userReaction = PublicationReaction::where('user_id', $userId)
                ->where('publication_id', $publication->id)
                ->value('type');
            
            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'user_reaction' => $userReaction ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dislike error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'publication_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}