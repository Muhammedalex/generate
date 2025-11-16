@extends('layouts.app')

@section('title', __('forms.form_builder') . ' - ' . $form->getTranslatedTitle())

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" x-data="formBuilder({{ json_encode($form) }})">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $form->getTranslatedTitle() }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('forms.form_builder') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('forms.show', $form) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        {{ __('forms.view') }}
                    </a>
                    <a href="{{ route('forms.index') }}" 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ __('forms.cancel') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar - Question Types -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('forms.question_types') }}</h2>
                    <div class="space-y-2 max-h-[60vh] overflow-y-auto">
                        @foreach(\App\Models\FormQuestion::getTypes() as $type => $label)
                        <button @click="addQuestion('{{ $type }}')" 
                                class="w-full text-right px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    <button @click="addSection()" 
                            class="w-full mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        {{ __('forms.add_section') }}
                    </button>
                </div>
            </div>

            <!-- Main Builder Area -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div id="form-builder" class="space-y-4">
                        <!-- Display existing sections and questions -->
                        <template x-if="formData.sections && formData.sections.length > 0">
                            <div>
                                <template x-for="(section, sectionIndex) in formData.sections" :key="section.id">
                                    <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="getSectionTitle(section) || 'Section'"></h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getSectionDescription(section) || ''"></p>
                                            </div>
                                            <div class="flex gap-2">
                                                <button @click="editSection(section)" class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                                                <button @click="deleteSection(section.id)" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                                            </div>
                                        </div>
                                        <div class="space-y-3">
                                            <template x-if="section.questions && section.questions.length > 0">
                                                <template x-for="(question, qIndex) in section.questions" :key="question.id">
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded p-3 flex items-center justify-between">
                                                        <div class="flex-1">
                                                            <p class="font-medium text-gray-900 dark:text-white" x-text="getQuestionText(question) || 'Question'"></p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="getQuestionTypeLabel(question.type)"></p>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button @click="editQuestion(question)" class="px-2 py-1 text-xs bg-blue-500 text-white rounded">Edit</button>
                                                            <button @click="duplicateQuestion(question.id)" class="px-2 py-1 text-xs bg-green-500 text-white rounded">Duplicate</button>
                                                            <button @click="deleteQuestion(question.id)" class="px-2 py-1 text-xs bg-red-500 text-white rounded">Delete</button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2" x-show="!section.questions || section.questions.length === 0">
                                                No questions in this section
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Display questions without sections -->
                        <template x-if="formData.questions && formData.questions.length > 0">
                            <div>
                                <template x-for="(question, qIndex) in formData.questions.filter(q => !q.section_id)" :key="question.id">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-3 flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="getQuestionText(question) || 'Question'"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="getQuestionTypeLabel(question.type)"></p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="editQuestion(question)" class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                                            <button @click="duplicateQuestion(question.id)" class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">Duplicate</button>
                                            <button @click="deleteQuestion(question.id)" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <div x-show="(!formData.sections || formData.sections.length === 0) && (!formData.questions || formData.questions.filter(q => !q.section_id).length === 0)" 
                             class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                {{ __('forms.add_question') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Modal -->
    <div x-show="showQuestionModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.away="closeQuestionModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4" x-text="editingQuestion ? 'Edit Question' : 'Add Question'"></h3>
            
            <form @submit.prevent="saveQuestion()">
                <div class="space-y-4">
                    <!-- Question Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.question_type') }}
                        </label>
                        <select x-model="questionForm.type" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                :disabled="editingQuestion">
                            @foreach(\App\Models\FormQuestion::getTypes() as $type => $label)
                            <option value="{{ $type }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Question Text Translations -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.question_text') }} <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- English Question Text -->
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.english') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="questionForm.question_text_translations.en"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="{{ __('forms.question_text') }}">
                        </div>
                        
                        <!-- Arabic Question Text -->
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.arabic') }}
                            </label>
                            <input type="text" 
                                   x-model="questionForm.question_text_translations.ar"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="{{ __('forms.question_text') }}">
                        </div>
                    </div>

                    <!-- Help Text Translations -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.help_text') }}
                        </label>
                        
                        <!-- English Help Text -->
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.english') }}
                            </label>
                            <textarea x-model="questionForm.help_text_translations.en"
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                      placeholder="{{ __('forms.help_text') }}"></textarea>
                        </div>
                        
                        <!-- Arabic Help Text -->
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.arabic') }}
                            </label>
                            <textarea x-model="questionForm.help_text_translations.ar"
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                      placeholder="{{ __('forms.help_text') }}"></textarea>
                        </div>
                    </div>

                    <!-- Is Required -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               x-model="questionForm.is_required"
                               id="is_required"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_required" class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('forms.is_required') }}
                        </label>
                    </div>

                    <!-- Options for multiple choice, checkbox, dropdown -->
                    <div x-show="['multiple_choice', 'checkbox', 'dropdown'].includes(questionForm.type)">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.options') }}
                        </label>
                        <div class="space-y-4">
                            <template x-for="(option, index) in questionForm.options" :key="index">
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Option <span x-text="index + 1"></span></span>
                                        <button type="button" 
                                                @click="removeOption(index)"
                                                class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                            ×
                                        </button>
                                    </div>
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                {{ __('common.english') }} <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   x-model="option.option_text_translations.en"
                                                   required
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                                   :placeholder="'Option ' + (index + 1)">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                {{ __('common.arabic') }}
                                            </label>
                                            <input type="text" 
                                                   x-model="option.option_text_translations.ar"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                                   :placeholder="'Option ' + (index + 1)">
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <button type="button" 
                                    @click="addOption()"
                                    class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                + {{ __('forms.add_option') }}
                            </button>
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('forms.settings') }}</h4>
                        
                        <!-- Placeholder (for text inputs) -->
                        <div x-show="['short_text', 'long_text', 'email', 'url', 'phone'].includes(questionForm.type)" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('forms.placeholder') }}
                            </label>
                            <input type="text" 
                                   x-model="questionForm.settings.placeholder"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="{{ __('forms.placeholder') }}">
                        </div>

                        <!-- Max Length (for text inputs) -->
                        <div x-show="['short_text', 'long_text'].includes(questionForm.type)" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('forms.max_length') }}
                            </label>
                            <input type="number" 
                                   x-model.number="questionForm.settings.max_length"
                                   min="1"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="e.g. 100">
                        </div>

                        <!-- Min Length (for long text) -->
                        <div x-show="questionForm.type === 'long_text'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('forms.min_length') }}
                            </label>
                            <input type="number" 
                                   x-model.number="questionForm.settings.min_length"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                   placeholder="e.g. 10">
                        </div>

                        <!-- Min/Max Value (for number) -->
                        <div x-show="questionForm.type === 'number'" class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.min_value') }}
                                </label>
                                <input type="number" 
                                       x-model.number="questionForm.settings.min_value"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                       placeholder="Min">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.max_value') }}
                                </label>
                                <input type="number" 
                                       x-model.number="questionForm.settings.max_value"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                       placeholder="Max">
                            </div>
                        </div>

                        <!-- Min/Max Date (for date) -->
                        <div x-show="questionForm.type === 'date'" class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.min_date') }}
                                </label>
                                <input type="date" 
                                       x-model="questionForm.settings.min_date"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.max_date') }}
                                </label>
                                <input type="date" 
                                       x-model="questionForm.settings.max_date"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- Linear Scale Settings -->
                        <div x-show="questionForm.type === 'linear_scale'" class="space-y-4 mb-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('forms.min_value') }}
                                    </label>
                                    <input type="number" 
                                           x-model.number="questionForm.settings.min_value"
                                           min="1"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('forms.max_value') }}
                                    </label>
                                    <input type="number" 
                                           x-model.number="questionForm.settings.max_value"
                                           min="2"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="5">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('forms.min_label') }}
                                    </label>
                                    <input type="text" 
                                           x-model="questionForm.settings.min_label"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="Poor">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('forms.max_label') }}
                                    </label>
                                    <input type="text" 
                                           x-model="questionForm.settings.max_label"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="Excellent">
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Settings -->
                        <div x-show="questionForm.type === 'checkbox'" class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.min_selections') }}
                                </label>
                                <input type="number" 
                                       x-model.number="questionForm.settings.min_selections"
                                       min="0"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                       placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.max_selections') }}
                                </label>
                                <input type="number" 
                                       x-model.number="questionForm.settings.max_selections"
                                       min="1"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                       placeholder="Unlimited">
                            </div>
                        </div>

                        <!-- File Upload Settings -->
                        <div x-show="questionForm.type === 'file_upload'" class="space-y-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('forms.allowed_file_types') }}
                                </label>
                                <input type="text" 
                                       x-model="questionForm.settings.allowed_file_types"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                       placeholder="pdf,doc,docx,jpg,png (comma separated)">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('forms.allowed_file_types_hint') }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('forms.max_file_size') }} (MB)
                                    </label>
                                    <input type="number" 
                                           x-model.number="questionForm.settings.max_file_size"
                                           min="1"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="5">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('forms.max_files') }}
                                    </label>
                                    <input type="number" 
                                           x-model.number="questionForm.settings.max_files"
                                           min="1"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                           placeholder="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.section') }} ({{ __('common.optional') }})
                        </label>
                        <select x-model="questionForm.section_id" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="">-- {{ __('forms.no_section') }} --</option>
                            <template x-for="section in formData.sections" :key="section.id">
                                <option :value="section.id" x-text="getSectionTitle(section) || 'Section'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button"
                            @click="closeQuestionModal()" 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ __('forms.cancel') }}
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        {{ __('forms.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section Modal -->
    <div x-show="showSectionModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.away="closeSectionModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-2xl w-full mx-4">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4" x-text="editingSection ? 'Edit Section' : 'Add Section'"></h3>
            
            <form @submit.prevent="saveSection()">
                <div class="space-y-4">
                    <!-- Title Translations -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.title') }}
                        </label>
                        
                        <!-- English Title -->
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.english') }}
                            </label>
                            <input type="text" 
                                   x-model="sectionForm.title_translations.en"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <!-- Arabic Title -->
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.arabic') }}
                            </label>
                            <input type="text" 
                                   x-model="sectionForm.title_translations.ar"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    
                    <!-- Description Translations -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('forms.description') }}
                        </label>
                        
                        <!-- English Description -->
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.english') }}
                            </label>
                            <textarea x-model="sectionForm.description_translations.en"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        
                        <!-- Arabic Description -->
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('common.arabic') }}
                            </label>
                            <textarea x-model="sectionForm.description_translations.ar"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button"
                            @click="closeSectionModal()" 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">
                        {{ __('forms.cancel') }}
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                        {{ __('forms.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function formBuilder(formData) {
    const questionTypes = @json(\App\Models\FormQuestion::getTypes());
    
    return {
        formData: formData || { sections: [], questions: [] },
        showQuestionModal: false,
        showSectionModal: false,
        editingQuestion: null,
        editingSection: null,
        questionForm: {
            type: 'short_text',
            question_text_translations: { en: '', ar: '' },
            help_text_translations: { en: '', ar: '' },
            is_required: false,
            section_id: null,
            options: [],
            settings: {},
            conditional_logic: null
        },
        sectionForm: {
            title_translations: { en: '', ar: '' },
            description_translations: { en: '', ar: '' }
        },
        
        init() {
            this.loadFormData();
        },
        
        loadFormData() {
            fetch(`/forms/${this.formData.id}/builder`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .catch(() => {
                // If JSON fails, reload page
                window.location.reload();
            });
        },
        
        getQuestionTypeLabel(type) {
            return questionTypes[type] || type;
        },
        
        getQuestionText(question) {
            if (question.translations) {
                const currentLocale = '{{ app()->getLocale() }}';
                const translation = question.translations.find(t => t.field_name === 'question_text' && t.locale === currentLocale);
                if (translation) return translation.value;
                const enTranslation = question.translations.find(t => t.field_name === 'question_text' && t.locale === 'en');
                if (enTranslation) return enTranslation.value;
            }
            return question.question_text || '';
        },
        
        getSectionTitle(section) {
            if (section.translations) {
                const currentLocale = '{{ app()->getLocale() }}';
                const translation = section.translations.find(t => t.field_name === 'title' && t.locale === currentLocale);
                if (translation) return translation.value;
                const enTranslation = section.translations.find(t => t.field_name === 'title' && t.locale === 'en');
                if (enTranslation) return enTranslation.value;
            }
            return section.title || '';
        },
        
        getSectionDescription(section) {
            if (section.translations) {
                const currentLocale = '{{ app()->getLocale() }}';
                const translation = section.translations.find(t => t.field_name === 'description' && t.locale === currentLocale);
                if (translation) return translation.value;
                const enTranslation = section.translations.find(t => t.field_name === 'description' && t.locale === 'en');
                if (enTranslation) return enTranslation.value;
            }
            return section.description || '';
        },
        
        addQuestion(type) {
            this.editingQuestion = null;
            this.questionForm = {
                type: type,
                question_text_translations: { en: '', ar: '' },
                help_text_translations: { en: '', ar: '' },
                is_required: false,
                section_id: null,
                options: [],
                settings: {},
                conditional_logic: null
            };
            
            // Add default options for choice types
            if (['multiple_choice', 'checkbox', 'dropdown'].includes(type)) {
                this.questionForm.options = [
                    { option_text_translations: { en: '', ar: '' } },
                    { option_text_translations: { en: '', ar: '' } }
                ];
            }
            
            // Set default settings based on type
            if (type === 'linear_scale') {
                this.questionForm.settings = { min_value: 1, max_value: 5, min_label: '', max_label: '' };
            } else if (type === 'checkbox') {
                this.questionForm.settings = { min_selections: 0, max_selections: null };
            } else if (type === 'file_upload') {
                this.questionForm.settings = { allowed_file_types: 'pdf,doc,docx', max_file_size: 5, max_files: 1 };
            }
            
            this.showQuestionModal = true;
        },
        
        editQuestion(question) {
            this.editingQuestion = question;
            
            // Extract translations
            const questionTextEn = question.translations?.find(t => t.field_name === 'question_text' && t.locale === 'en')?.value || '';
            const questionTextAr = question.translations?.find(t => t.field_name === 'question_text' && t.locale === 'ar')?.value || '';
            const helpTextEn = question.translations?.find(t => t.field_name === 'help_text' && t.locale === 'en')?.value || '';
            const helpTextAr = question.translations?.find(t => t.field_name === 'help_text' && t.locale === 'ar')?.value || '';
            
            this.questionForm = {
                type: question.type,
                question_text_translations: { en: questionTextEn, ar: questionTextAr },
                help_text_translations: { en: helpTextEn, ar: helpTextAr },
                is_required: question.is_required || false,
                section_id: question.section_id || null,
                options: question.options ? question.options.map(opt => {
                    const optTextEn = opt.translations?.find(t => t.field_name === 'option_text' && t.locale === 'en')?.value || '';
                    const optTextAr = opt.translations?.find(t => t.field_name === 'option_text' && t.locale === 'ar')?.value || '';
                    return {
                        id: opt.id,
                        option_text_translations: { en: optTextEn, ar: optTextAr }
                    };
                }) : [],
                settings: question.settings || {},
                conditional_logic: question.conditional_logic || null
            };
            
            // Handle file types as string if array
            if (this.questionForm.settings.allowed_file_types && Array.isArray(this.questionForm.settings.allowed_file_types)) {
                this.questionForm.settings.allowed_file_types = this.questionForm.settings.allowed_file_types.join(',');
            }
            
            if (['multiple_choice', 'checkbox', 'dropdown'].includes(question.type) && this.questionForm.options.length === 0) {
                this.questionForm.options = [
                    { option_text_translations: { en: '', ar: '' } },
                    { option_text_translations: { en: '', ar: '' } }
                ];
            }
            
            this.showQuestionModal = true;
        },
        
        saveQuestion() {
            const url = this.editingQuestion 
                ? `/forms/${this.formData.id}/questions/${this.editingQuestion.id}`
                : `/forms/${this.formData.id}/questions`;
            const method = this.editingQuestion ? 'PUT' : 'POST';
            
            // Clean settings object - remove empty values
            const cleanedSettings = {};
            if (this.questionForm.settings) {
                Object.keys(this.questionForm.settings).forEach(key => {
                    const value = this.questionForm.settings[key];
                    if (value !== null && value !== undefined && value !== '') {
                        // Handle file types - convert string to array
                        if (key === 'allowed_file_types' && typeof value === 'string') {
                            cleanedSettings[key] = value.split(',').map(t => t.trim()).filter(t => t);
                        } else if (key === 'max_file_size' && typeof value === 'number') {
                            // Convert MB to bytes
                            cleanedSettings[key] = value * 1024 * 1024;
                        } else {
                            cleanedSettings[key] = value;
                        }
                    }
                });
            }
            
            const data = {
                form_id: this.formData.id,
                type: this.questionForm.type,
                question_text_translations: this.questionForm.question_text_translations,
                help_text_translations: this.questionForm.help_text_translations,
                is_required: this.questionForm.is_required,
                section_id: this.questionForm.section_id || null,
                settings: Object.keys(cleanedSettings).length > 0 ? cleanedSettings : null,
                conditional_logic: this.questionForm.conditional_logic || null
            };
            
            if (['multiple_choice', 'checkbox', 'dropdown'].includes(this.questionForm.type)) {
                data.options = this.questionForm.options
                    .filter(opt => opt.option_text_translations?.en && opt.option_text_translations.en.trim())
                    .map((opt, index) => ({
                        id: opt.id,
                        option_text_translations: opt.option_text_translations || { en: '', ar: '' },
                        order: index + 1
                    }));
            }
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving question. Please try again.');
            });
        },
        
        deleteQuestion(questionId) {
            if (!confirm('Are you sure you want to delete this question?')) return;
            
            fetch(`/forms/${this.formData.id}/questions/${questionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting question. Please try again.');
            });
        },
        
        duplicateQuestion(questionId) {
            fetch(`/forms/${this.formData.id}/questions/${questionId}/duplicate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error duplicating question. Please try again.');
            });
        },
        
        addOption() {
            this.questionForm.options.push({ option_text_translations: { en: '', ar: '' } });
        },
        
        removeOption(index) {
            this.questionForm.options.splice(index, 1);
        },
        
        closeQuestionModal() {
            this.showQuestionModal = false;
            this.editingQuestion = null;
            this.questionForm = {
                type: 'short_text',
                question_text_translations: { en: '', ar: '' },
                help_text_translations: { en: '', ar: '' },
                is_required: false,
                section_id: null,
                options: [],
                settings: {},
                conditional_logic: null
            };
        },
        
        addSection() {
            this.editingSection = null;
            this.sectionForm = { 
                title_translations: { en: '', ar: '' }, 
                description_translations: { en: '', ar: '' } 
            };
            this.showSectionModal = true;
        },
        
        editSection(section) {
            this.editingSection = section;
            
            // Extract translations
            const titleEn = section.translations?.find(t => t.field_name === 'title' && t.locale === 'en')?.value || '';
            const titleAr = section.translations?.find(t => t.field_name === 'title' && t.locale === 'ar')?.value || '';
            const descEn = section.translations?.find(t => t.field_name === 'description' && t.locale === 'en')?.value || '';
            const descAr = section.translations?.find(t => t.field_name === 'description' && t.locale === 'ar')?.value || '';
            
            this.sectionForm = {
                title_translations: { en: titleEn, ar: titleAr },
                description_translations: { en: descEn, ar: descAr }
            };
            this.showSectionModal = true;
        },
        
        saveSection() {
            const url = this.editingSection
                ? `/forms/${this.formData.id}/sections/${this.editingSection.id}`
                : `/forms/${this.formData.id}/sections`;
            const method = this.editingSection ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.sectionForm)
            })
            .then(response => response.json())
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving section. Please try again.');
            });
        },
        
        deleteSection(sectionId) {
            if (!confirm('Are you sure you want to delete this section? All questions in it will be moved to no section.')) return;
            
            fetch(`/forms/${this.formData.id}/sections/${sectionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting section. Please try again.');
            });
        },
        
        closeSectionModal() {
            this.showSectionModal = false;
            this.editingSection = null;
        }
    }
}
</script>
@endsection
