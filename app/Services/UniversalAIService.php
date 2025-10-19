<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UniversalAIService
{
    protected $providers = ['openai', 'fallback'];
    protected $currentProvider = 0;

    public function generateAnswer($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        Log::info('🎯 Universal AI Service called', [
            'question' => $question,
            'tutorial' => $tutorialTitle,
            'steps_count' => count($tutorialSteps)
        ]);

        $cacheKey = 'ai_response_' . md5($question . $tutorialTitle);
        
        // Retourner la réponse en cache si elle existe
        if (Cache::has($cacheKey)) {
            Log::info('📦 Serving cached AI response');
            return Cache::get($cacheKey);
        }

        // Essayer chaque provider dans l'ordre
        foreach ($this->providers as $provider) {
            try {
                Log::info("🔄 Trying AI provider: {$provider}");
                
                $response = null;
                
                switch ($provider) {
                    case 'openai':
                        $response = $this->callOpenAI($question, $tutorialTitle, $tutorialDescription, $tutorialSteps);
                        break;
                        
                    case 'fallback':
                        $response = $this->generateSmartResponse($question, $tutorialTitle, $tutorialDescription, $tutorialSteps);
                        break;
                }

                if ($response && !empty(trim($response))) {
                    Log::info("✅ {$provider} response successful");
                    Cache::put($cacheKey, $response, now()->addHours(2));
                    return $response;
                }
                
            } catch (\Exception $e) {
                Log::warning("❌ {$provider} failed", ['error' => $e->getMessage()]);
                continue;
            }
        }

        // Dernière solution de secours
        return $this->getBasicFallbackResponse($tutorialTitle, $tutorialDescription, count($tutorialSteps));
    }

    /**
     * OpenAI API Call
     */
    private function callOpenAI($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        $apiKey = config('services.openai.api_key');
        
        if (empty($apiKey) || $apiKey === 'your-openai-api-key-here') {
            return null; // Passer au provider suivant
        }

        $prompt = $this->buildOpenAIPrompt($question, $tutorialTitle, $tutorialDescription, $tutorialSteps);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(20)
          ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are RecycleVerse AI, a friendly recycling expert. Provide helpful, concise answers in English. Be encouraging and practical.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
            'max_tokens' => 350,
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return trim($data['choices'][0]['message']['content']);
        }

        return null;
    }

    /**
     * Google Gemini API Call
     */
    private function callGemini($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        $apiKey = config('services.gemini.api_key');
        
        if (empty($apiKey)) {
            return null;
        }

        $prompt = $this->buildPrompt($question, $tutorialTitle, $tutorialDescription, $tutorialSteps);

        $response = Http::timeout(20)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 400,
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return trim($data['candidates'][0]['content']['parts'][0]['text']);
        }

        return null;
    }

    /**
     * Hugging Face API Call
     */
    private function callHuggingFace($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        $apiKey = config('services.huggingface.api_key');
        
        if (empty($apiKey)) {
            return null;
        }

        $prompt = $this->buildPrompt($question, $tutorialTitle, $tutorialDescription, $tutorialSteps);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->timeout(25)
          ->post('https://api-inference.huggingface.co/models/microsoft/DialoGPT-large', [
            'inputs' => $prompt,
            'parameters' => [
                'max_length' => 250,
                'temperature' => 0.7,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $this->extractHuggingFaceResponse($data, $prompt);
        }

        return null;
    }

    /**
     * Réponse intelligente locale (fallback)
     */
    private function generateSmartResponse($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        $questionLower = strtolower($question);
        $stepCount = count($tutorialSteps);
        
        // Détection intelligente du type de question
        if ($this->containsAny($questionLower, ['start', 'begin', 'how start', 'how begin', 'first'])) {
            return $this->generateStartResponse($tutorialTitle, $tutorialSteps);
        }
        
        if ($this->containsAny($questionLower, ['material', 'need', 'require', 'what need', 'supply', 'tool'])) {
            return $this->generateMaterialsResponse($tutorialTitle, $tutorialDescription);
        }
        
        if ($this->containsAny($questionLower, ['difficult', 'hard', 'easy', 'beginner', 'level'])) {
            return $this->generateDifficultyResponse($tutorialTitle, $stepCount);
        }
        
        if ($this->containsAny($questionLower, ['time', 'long', 'take', 'duration', 'hour', 'minute'])) {
            return $this->generateTimeResponse($tutorialTitle, $stepCount);
        }
        
        if ($this->containsAny($questionLower, ['step', 'part', 'phase']) || preg_match('/step\s*\d+/', $questionLower)) {
            return $this->generateStepsResponse($questionLower, $tutorialSteps);
        }
        
        if ($this->containsAny($questionLower, ['why', 'purpose', 'reason', 'benefit'])) {
            return $this->generateWhyResponse($tutorialTitle, $tutorialDescription);
        }
        
        return $this->generateGeneralResponse($tutorialTitle, $tutorialDescription, $stepCount, $question);
    }

    /**
     * Méthodes de construction de prompt
     */
    private function buildOpenAIPrompt($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        $stepsText = implode("\n", array_map(function($step, $index) {
            return ($index + 1) . ". " . $step;
        }, $tutorialSteps, array_keys($tutorialSteps)));

        return "Tutorial: {$tutorialTitle}
Description: {$tutorialDescription}

Steps:
{$stepsText}

Question: {$question}

Provide a helpful, concise answer as a recycling expert:";
    }

    private function buildPrompt($question, $tutorialTitle, $tutorialDescription, $tutorialSteps)
    {
        $stepsText = implode(". ", array_slice($tutorialSteps, 0, 5));
        
        return "As a recycling expert assistant, help with this tutorial.

Tutorial: {$tutorialTitle}
Description: {$tutorialDescription}
Main steps: {$stepsText}

User question: {$question}

Provide a helpful response:";
    }

    /**
     * Méthodes de génération de réponses locales
     */
    private function generateStartResponse($title, $steps)
    {
        $firstSteps = array_slice($steps, 0, min(2, count($steps)));
        $stepsText = implode(" → ", $firstSteps);
        $remaining = count($steps) - 2;
        
        return "🚀 **Perfect! Let's begin \"{$title}\"**\n\nStart with: {$stepsText}" . 
               ($remaining > 0 ? "\n\nThen {$remaining} more steps to complete your project." : "") .
               "\n\n💡 **Pro Tip**: Read all steps first and prepare your materials!\n\nYou've got this! 🌟";
    }

    private function generateMaterialsResponse($title, $description)
    {
        return "🛠️ **Materials for \"{$title}\"**\n\nYou'll typically need:\n" .
               "• Basic recycling materials\n• Standard crafting tools\n• Safety equipment\n" .
               "• Items mentioned in the tutorial\n\n📋 **Preparation**: Clean workspace + all materials ready = success!";
    }

    private function generateDifficultyResponse($title, $stepCount)
    {
        $level = $stepCount <= 3 ? 'beginner-friendly' : ($stepCount <= 6 ? 'intermediate' : 'comprehensive');
        return "📊 **{$title} Difficulty**: {$level}\n\n" .
               "With {$stepCount} clear steps, this project is perfect for {$level} crafters.\n\n" .
               "🌟 **Remember**: Every recycling project makes a positive impact!";
    }

    private function generateTimeResponse($title, $stepCount)
    {
        $totalTime = $stepCount * 15;
        return "⏱️ **Time for \"{$title}\"**: {$totalTime}-" . ($totalTime + 20) . " minutes\n\n" .
               "Breakdown:\n• Preparation: 10-15 min\n• {$stepCount} steps: {$totalTime} min\n• Finishing: 5-10 min\n\n" .
               "🎯 **Quality recycling is worth the time!**";
    }

    private function generateStepsResponse($question, $steps)
    {
        preg_match('/step\s*(\d+)/', $question, $matches);
        
        if (isset($matches[1])) {
            $stepNum = (int)$matches[1];
            if ($stepNum >= 1 && $stepNum <= count($steps)) {
                return "📝 **Step {$stepNum} Details**\n\n{$steps[$stepNum-1]}\n\n" .
                       "💡 **Focus on this step** - gather materials and work carefully!";
            }
        }
        
        $allSteps = implode("\n", array_map(function($step, $index) {
            return ($index + 1) . ". {$step}";
        }, $steps, array_keys($steps)));
        
        return "📋 **All Steps**\n\n{$allSteps}\n\n🎯 **Ask me about any specific step!**";
    }

    private function generateWhyResponse($title, $description)
    {
        return "🌟 **Why \"{$title}\" Matters**\n\n" .
               "**Environmental Impact**:\n• Reduces waste\n• Conserves resources\n• Promotes sustainability\n\n" .
               "**Personal Benefits**:\n• Learn new skills\n• Create useful items\n• Save money\n• Feel accomplished!\n\n" .
               "This project: {$description}";
    }

    private function generateGeneralResponse($title, $description, $stepCount, $question)
    {
        return "🌱 **Thanks for your question!**\n\n" .
               "About **{$title}**: {$description}\n\n" .
               "This {$stepCount}-step tutorial will guide you through creating something amazing while helping our planet. " .
               "I recommend following each step carefully.\n\n" .
               "🔧 **Need help with**:\n• Specific steps\n• Materials\n• Time estimates\n• Any tutorial aspect\n\n" .
               "Let's make recycling fun! ♻️";
    }

    /**
     * Méthodes utilitaires
     */
    private function containsAny($text, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function extractHuggingFaceResponse($data, $prompt)
    {
        if (isset($data[0]['generated_text'])) {
            $response = str_replace($prompt, '', $data[0]['generated_text']);
            return trim($response) ?: null;
        }
        return null;
    }

    private function getBasicFallbackResponse($title, $description, $stepCount)
    {
        return "Hello! I'm here to help with **{$title}**. " .
               "This tutorial covers {$description} through {$stepCount} steps. " .
               "Feel free to ask any questions about the process! 🌍";
    }
}