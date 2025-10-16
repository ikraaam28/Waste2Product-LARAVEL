<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Tuto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\QuizAttempt;
class QuizController extends Controller
{
    /**
     * Frontend: List all published quizzes
     */
    public function index(Request $request)
    {
        $query = Quiz::with(['tuto'])
            ->where('is_published', true);

        if ($request->has('tuto_id') && $request->tuto_id !== '') {
            $query->where('tuto_id', $request->tuto_id);
        }

        $quizzes = $query->latest()->get();
        $tutos = Tuto::where('is_published', true)->get();

        return view('quizzes.index', compact('quizzes', 'tutos'));
    }

    /**
     * Frontend: Show a quiz for users to take
     */
 public function show(Quiz $quiz)
    {
        if (!$quiz->is_published && !Auth::check()) {
            abort(403, 'This quiz is not published.');
        }

        $quiz->load(['tuto', 'questions']);
        $attempt = Auth::check() ? QuizAttempt::where('user_id', Auth::id())->where('quiz_id', $quiz->id)->first() : null;

        if ($attempt) {
            // User has already taken the quiz; show results directly
            return view('quizzes.show', [
                'quiz' => $quiz,
                'score' => $attempt->score,
                'totalQuestions' => $quiz->questions()->count(),
                'percentage' => $attempt->percentage,
                'status' => $attempt->status,
                'correctAnswers' => $this->generateCorrectAnswers($quiz, $attempt->score, $attempt->percentage, $attempt->status),
            ]);
        }

        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Admin: List all quizzes
     */
    public function adminIndex()
    {
        $quizzes = Quiz::with(['tuto'])->latest()->get();
        return view('admin.quiz.index', compact('quizzes'));
    }

    /**
     * Admin: Show a quiz (for admin viewing)
     */
    public function adminShow(Quiz $quiz)
    {
        $this->authorize('view', $quiz);
        $quiz->load(['tuto', 'questions']);
        return view('admin.quiz.show', compact('quiz'));
    }

    /**
     * Admin: Create a quiz
     */
    public function create()
    {
        $tutos = Tuto::where('is_published', true)->get();
        return view('admin.quiz.create', compact('tutos'));
    }

    /**
     * Admin: Store a quiz with questions and answers
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tuto_id' => 'required|exists:tutos,id',
            'is_published' => 'required|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.correct_answer' => 'required|string|in:Yes,No',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                $quiz = Quiz::create([
                    'title' => $validated['title'],
                    'tuto_id' => $validated['tuto_id'],
                    'is_published' => $validated['is_published'],
                    'user_id' => Auth::id(),
                ]);

                foreach ($validated['questions'] as $questionData) {
                    QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionData['question_text'],
                        'options' => ['Yes', 'No'],
                        'correct_answer' => $questionData['correct_answer'],
                    ]);
                }
            });

            return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully!');
        } catch (\Exception $e) {
            \Log::error("Error creating quiz: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to create quiz. Please try again.');
        }
    }

    /**
     * Admin: Edit a quiz
     */
    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        $tutos = Tuto::where('is_published', true)->get();
        $quiz->load('questions');
        return view('admin.quiz.edit', compact('quiz', 'tutos'));
    }

    /**
     * Admin: Update a quiz
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tuto_id' => 'required|exists:tutos,id',
            'is_published' => 'required|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.correct_answer' => 'required|string|in:Yes,No',
        ]);

        try {
            DB::transaction(function () use ($validated, $quiz) {
                $quiz->update([
                    'title' => $validated['title'],
                    'tuto_id' => $validated['tuto_id'],
                    'is_published' => $validated['is_published'],
                ]);

                $quiz->questions()->delete();
                foreach ($validated['questions'] as $questionData) {
                    QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionData['question_text'],
                        'options' => ['Yes', 'No'],
                        'correct_answer' => $questionData['correct_answer'],
                    ]);
                }
            });

            return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated successfully!');
        } catch (\Exception $e) {
            \Log::error("Error updating quiz: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to update quiz. Please try again.');
        }
    }

    /**
     * Admin: Delete a quiz
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);

        try {
            DB::transaction(function () use ($quiz) {
                $quiz->questions()->delete();
                $quiz->delete();
            });

            return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
        } catch (\Exception $e) {
            \Log::error("Error deleting quiz: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to delete quiz. Please try again.');
        }
    }

    /**
     * Frontend: Submit quiz answers and display results
     */
public function submit(Request $request, Quiz $quiz)
    {
        if (!Auth::check()) {
            abort(403, 'You must be logged in to submit a quiz.');
        }

        // Check if the user has already attempted the quiz
        $existingAttempt = QuizAttempt::where('user_id', Auth::id())->where('quiz_id', $quiz->id)->first();
        if ($existingAttempt) {
            return redirect()->back()->with('error', 'You have already taken this quiz. Results are final.');
        }

        try {
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'required|string|in:Yes,No',
            ]);

            $score = 0;
            $totalQuestions = $quiz->questions()->count();
            $correctAnswers = [];

            foreach ($quiz->questions as $question) {
                $userAnswer = $validated['answers'][$question->id] ?? null;
                if ($userAnswer === null) {
                    \Log::warning("Missing answer for question ID {$question->id} in quiz {$quiz->id}");
                    $correctAnswers[$question->id] = [
                        'user_answer' => 'Not answered',
                        'correct_answer' => $question->correct_answer,
                        'is_correct' => false,
                        'feedback' => "No answer provided. The correct answer is '{$question->correct_answer}'. Please review the tutorial.",
                        'question_text' => $question->question_text,
                    ];
                    continue;
                }

                $isCorrect = $userAnswer === $question->correct_answer;
                if ($isCorrect) {
                    $score++;
                }
                $feedback = $isCorrect
                    ? "Correct! Your answer '$userAnswer' aligns with the tutorial's key points."
                    : "Incorrect. You answered '$userAnswer', but the correct answer is '{$question->correct_answer}'. Review the related tutorial section for more details.";

                $correctAnswers[$question->id] = [
                    'user_answer' => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'is_correct' => $isCorrect,
                    'feedback' => $feedback,
                    'question_text' => $question->question_text,
                ];
            }

            $percentage = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;
            $status = $percentage >= 70 ? 'Validated' : 'Failed';

            // Save the attempt
            QuizAttempt::create([
                'user_id' => Auth::id(),
                'quiz_id' => $quiz->id,
                'score' => $score,
                'percentage' => $percentage,
                'status' => $status,
            ]);

            // Reload questions to ensure data is fresh
            $quiz->load('questions');

            return view('quizzes.show', compact('quiz', 'score', 'totalQuestions', 'percentage', 'status', 'correctAnswers'));
        } catch (\Exception $e) {
            \Log::error("Error in quiz submission: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while processing your quiz. Please try again.');
        }
    }



    /**
     * Generate correct answers array based on existing attempt data
     */
    private function generateCorrectAnswers($quiz, $score, $percentage, $status)
    {
        $correctAnswers = [];
        $totalQuestions = $quiz->questions()->count();
        $correctCount = round(($score / $totalQuestions) * $totalQuestions);

        foreach ($quiz->questions as $index => $question) {
            $isCorrect = $index < $correctCount; // Simplified assumption for existing attempt
            $userAnswer = $isCorrect ? $question->correct_answer : ($isCorrect ? 'No' : 'Yes'); // Mock data
            $correctAnswers[$question->id] = [
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'feedback' => $isCorrect
                    ? "Correct! Your answer '$userAnswer' aligns with the tutorial's key points."
                    : "Incorrect. You answered '$userAnswer', but the correct answer is '{$question->correct_answer}'. Review the related tutorial section for more details.",
                'question_text' => $question->question_text,
            ];
        }

        return $correctAnswers;
    }
}