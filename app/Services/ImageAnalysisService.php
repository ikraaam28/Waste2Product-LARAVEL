<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class ImageAnalysisService
{
    public function generateTitleFromImage($imagePath)
    {
        try {
            Log::info('🎯 [TITLE GENERATION] Starting analysis', ['path' => $imagePath]);

            if (!file_exists($imagePath)) {
                Log::error('❌ [TITLE GENERATION] Image file not found');
                throw new Exception("Image file not found");
            }

            // Log des informations sur l'image
            $imageInfo = [
                'file_size' => filesize($imagePath),
                'file_name' => basename($imagePath),
                'is_readable' => is_readable($imagePath)
            ];
            Log::info('📷 [TITLE GENERATION] Image details', $imageInfo);

            // Générer un titre basé sur des templates aléatoires
            $title = $this->generateSmartTitle();
            
            Log::info('✅ [TITLE GENERATION] Title generated successfully', ['title' => $title]);
            
            return $title;

        } catch (Exception $e) {
            Log::error('❌ [TITLE GENERATION] Complete failure', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->generateFallbackTitle();
        }
    }

    private function generateSmartTitle()
    {
        $materials = ['Plastic', 'Wood', 'Paper', 'Glass', 'Metal', 'Textile', 'Composite'];
        $actions = ['Recycling', 'Upcycling', 'Transformation', 'Creation', 'DIY', 'Crafting'];
        $adjectives = ['Easy', 'Creative', 'Sustainable', 'Eco-Friendly', 'Innovative', 'Practical'];
        $levels = ['Beginner', 'Simple', 'Intermediate', 'Advanced', 'Professional'];
        $formats = ['Guide', 'Tutorial', 'Step-by-Step', 'Project', 'Method', 'Technique'];

        $material = $materials[array_rand($materials)];
        $action = $actions[array_rand($actions)];
        $adjective = $adjectives[array_rand($adjectives)];
        $level = $levels[array_rand($levels)];
        $format = $formats[array_rand($formats)];

        $templates = [
            "{$adjective} {$material} {$action} {$format}",
            "{$level} {$material} {$action} - Complete {$format}",
            "How to {$action} {$material} - {$adjective} {$format}",
            "{$material} {$action}: {$level} {$adjective} {$format}",
            "The Art of {$material} {$action} - {$format}",
            "{$adjective} {$action} with {$material} - {$level} {$format}",
            "{$material} Waste {$action} - Sustainable {$format}",
            "DIY {$material} {$action} - {$adjective} {$level} Guide"
        ];

        $selectedTitle = $templates[array_rand($templates)];
        
        Log::info('🔤 [TITLE GENERATION] Template selected', [
            'material' => $material,
            'action' => $action,
            'adjective' => $adjective,
            'level' => $level,
            'format' => $format,
            'final_title' => $selectedTitle
        ]);

        return $selectedTitle;
    }

    private function generateFallbackTitle()
    {
        $fallbackTitles = [
            'Creative Recycling Project Guide',
            'Sustainable DIY Tutorial',
            'Eco-Friendly Upcycling Method',
            'Step-by-Step Recycling Guide',
            'Waste Transformation Project',
            'Green Crafting Tutorial'
        ];
        
        $title = $fallbackTitles[array_rand($fallbackTitles)];
        Log::info('🔄 [TITLE GENERATION] Using fallback title', ['title' => $title]);
        
        return $title;
    }
}