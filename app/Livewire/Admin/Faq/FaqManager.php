<?php

namespace App\Livewire\Admin\Faq;

use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FaqManager extends Component
{
    // Category Modal State
    public $showCategoryModal = false;
    public $categoryId = null;
    public $categoryName = '';
    public $categoryDescription = '';
    public $categoryIcon = '';
    public $categorySortOrder = 0;

    // Question Modal State
    public $showQuestionModal = false;
    public $questionId = null;
    public $questionCategoryId = null;
    public $questionTitle = '';
    public $questionSummary = '';
    public $questionContent = '';
    public $questionSortOrder = 0;

    protected $listeners = ['contentUpdated' => 'updateQuestionContent'];

    public function updateQuestionContent($content)
    {
        $this->questionContent = $content;
    }

    // --- CATEGORIES ---

    public function createCategory()
    {
        $this->resetCategoryForm();
        $this->categorySortOrder = FaqCategory::max('sort_order') + 1;
        $this->showCategoryModal = true;
    }

    public function editCategory($id)
    {
        $this->resetCategoryForm();
        $category = FaqCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryDescription = $category->description;
        $this->categoryIcon = $category->icon;
        $this->categorySortOrder = $category->sort_order;
        
        $this->showCategoryModal = true;
    }

    public function saveCategory()
    {
        $data = $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryDescription' => 'nullable|string|max:1000',
            'categoryIcon' => 'nullable|string',
            'categorySortOrder' => 'required|integer',
        ], [
            'categoryName.required' => 'Le nom de la catégorie est requis.',
        ]);

        if ($this->categoryId) {
            FaqCategory::findOrFail($this->categoryId)->update([
                'name' => $data['categoryName'],
                'description' => $data['categoryDescription'],
                'icon' => $data['categoryIcon'],
                'sort_order' => $data['categorySortOrder'],
            ]);
            $this->dispatch('notify', 'Catégorie mise à jour avec succès.');
        } else {
            FaqCategory::create([
                'name' => $data['categoryName'],
                'description' => $data['categoryDescription'],
                'icon' => $data['categoryIcon'],
                'sort_order' => $data['categorySortOrder'],
            ]);
            $this->dispatch('notify', 'Catégorie créée avec succès.');
        }

        $this->showCategoryModal = false;
    }

    public function deleteCategory($id)
    {
        FaqCategory::findOrFail($id)->delete();
        $this->dispatch('notify', 'Catégorie et ses questions supprimées.');
    }

    private function resetCategoryForm()
    {
        $this->categoryId = null;
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->categoryIcon = '';
        $this->categorySortOrder = 0;
        $this->resetValidation();
    }

    // --- QUESTIONS ---

    public function createQuestion($categoryId = null)
    {
        $this->resetQuestionForm();
        $this->questionCategoryId = $categoryId ?? FaqCategory::first()?->id;
        $this->questionSortOrder = FaqQuestion::where('faq_category_id', $this->questionCategoryId)->max('sort_order') + 1;
        $this->showQuestionModal = true;
        
        // Dispatch event to clear CKEditor
        $this->dispatch('initEditor', '');
    }

    public function editQuestion($id)
    {
        $this->resetQuestionForm();
        $question = FaqQuestion::findOrFail($id);
        $this->questionId = $question->id;
        $this->questionCategoryId = $question->faq_category_id;
        $this->questionTitle = $question->title;
        $this->questionSummary = $question->summary;
        $this->questionContent = $question->content;
        $this->questionSortOrder = $question->sort_order;
        
        $this->showQuestionModal = true;
        
        // Dispatch event to load data into CKEditor
        $this->dispatch('initEditor', $this->questionContent);
    }

    public function saveQuestion()
    {
        $data = $this->validate([
            'questionCategoryId' => 'required|exists:faq_categories,id',
            'questionTitle' => 'required|string|max:255',
            'questionSummary' => 'nullable|string|max:1000',
            'questionContent' => 'required|string',
            'questionSortOrder' => 'required|integer',
        ], [
            'questionCategoryId.required' => 'Veuillez sélectionner une catégorie.',
            'questionTitle.required' => 'Le titre de la question est requis.',
            'questionContent.required' => 'Le contenu détaillé est requis.',
        ]);

        if ($this->questionId) {
            FaqQuestion::findOrFail($this->questionId)->update([
                'faq_category_id' => $data['questionCategoryId'],
                'title' => $data['questionTitle'],
                'summary' => $data['questionSummary'],
                'content' => $data['questionContent'],
                'sort_order' => $data['questionSortOrder'],
            ]);
            $this->dispatch('notify', 'Question mise à jour avec succès.');
        } else {
            FaqQuestion::create([
                'faq_category_id' => $data['questionCategoryId'],
                'title' => $data['questionTitle'],
                'summary' => $data['questionSummary'],
                'content' => $data['questionContent'],
                'sort_order' => $data['questionSortOrder'],
            ]);
            $this->dispatch('notify', 'Question créée avec succès.');
        }

        $this->showQuestionModal = false;
    }

    public function deleteQuestion($id)
    {
        FaqQuestion::findOrFail($id)->delete();
        $this->dispatch('notify', 'Question supprimée.');
    }

    private function resetQuestionForm()
    {
        $this->questionId = null;
        $this->questionCategoryId = null;
        $this->questionTitle = '';
        $this->questionSummary = '';
        $this->questionContent = '';
        $this->questionSortOrder = 0;
        $this->resetValidation();
    }

    // --- RENDER ---

    public function render()
    {
        $categories = FaqCategory::with('questions')->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        // Also get categories for the dropdown
        $allCategories = FaqCategory::orderBy('sort_order')->get();

        return view('livewire.admin.faq.faq-manager', [
            'categories' => $categories,
            'allCategories' => $allCategories,
        ]);
    }
}
