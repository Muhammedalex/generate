<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormQuestion;
use App\Models\QuestionOption;
use Illuminate\Support\Str;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        
        if (!$admin) {
            $this->command->error('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $forms = [
            $this->createCustomerFeedbackForm($admin),
            $this->createJobApplicationForm($admin),
            $this->createEventRegistrationForm($admin),
            $this->createProductSurveyForm($admin),
            $this->createContactForm($admin),
        ];

        $this->command->info('Created ' . count($forms) . ' forms with all question types!');
    }

    private function createCustomerFeedbackForm(User $user): Form
    {
        // Generate slug
        $slug = Str::slug('Customer Feedback Survey');
        $counter = 1;
        while (Form::where('slug', $slug)->exists()) {
            $slug = Str::slug('Customer Feedback Survey') . '-' . $counter;
            $counter++;
        }

        $form = Form::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'status' => 'published',
            'allow_multiple' => false,
            'require_auth' => false,
            'collect_email' => true,
            'show_progress' => true,
            'randomize_questions' => false,
        ]);

        // Set translations
        $form->setTranslations('title', [
            'en' => 'Customer Feedback Survey',
            'ar' => 'استبيان آراء العملاء'
        ]);
        $form->setTranslations('description', [
            'en' => 'Help us improve our services by sharing your feedback',
            'ar' => 'ساعدنا في تحسين خدماتنا من خلال مشاركة آرائك'
        ]);
        $form->setTranslations('thank_you_message', [
            'en' => 'Thank you for your feedback!',
            'ar' => 'شكراً لك على ملاحظاتك!'
        ]);

        // Section 1: Personal Information
        $section1 = FormSection::create([
            'form_id' => $form->id,
            'order' => 1,
        ]);
        $section1->setTranslations('title', ['en' => 'Personal Information', 'ar' => 'المعلومات الشخصية']);
        $section1->setTranslations('description', ['en' => 'Please provide your contact details', 'ar' => 'يرجى تقديم تفاصيل الاتصال الخاصة بك']);

        // Question 1: Short Text - Name
        $q1 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section1->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'order' => 1,
            'is_required' => true,
            'settings' => ['max_length' => 100, 'placeholder' => 'John Doe'],
        ]);
        $q1->setTranslations('question_text', ['en' => 'What is your full name?', 'ar' => 'ما هو اسمك الكامل؟']);
        $q1->setTranslations('help_text', ['en' => 'Enter your first and last name', 'ar' => 'أدخل اسمك الأول والأخير']);

        // Question 2: Email
        $q2 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section1->id,
            'type' => FormQuestion::TYPE_EMAIL,
            'order' => 2,
            'is_required' => true,
            'settings' => ['placeholder' => 'your.email@example.com'],
        ]);
        $q2->setTranslations('question_text', ['en' => 'Email Address', 'ar' => 'عنوان البريد الإلكتروني']);

        // Question 3: Phone
        $q3 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section1->id,
            'type' => FormQuestion::TYPE_PHONE,
            'order' => 3,
            'is_required' => false,
            'settings' => ['placeholder' => '+1234567890'],
        ]);
        $q3->setTranslations('question_text', ['en' => 'Phone Number', 'ar' => 'رقم الهاتف']);

        // Section 2: Feedback
        $section2 = FormSection::create([
            'form_id' => $form->id,
            'order' => 2,
        ]);
        $section2->setTranslations('title', ['en' => 'Your Feedback', 'ar' => 'ملاحظاتك']);

        // Question 4: Multiple Choice
        $q4 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_MULTIPLE_CHOICE,
            'order' => 4,
            'is_required' => true,
        ]);
        $q4->setTranslations('question_text', ['en' => 'How satisfied are you with our service?', 'ar' => 'ما مدى رضاك عن خدمتنا؟']);

        $opt1 = QuestionOption::create(['question_id' => $q4->id, 'order' => 1]);
        $opt1->setTranslations('option_text', ['en' => 'Very Satisfied', 'ar' => 'راضٍ جداً']);
        $opt2 = QuestionOption::create(['question_id' => $q4->id, 'order' => 2]);
        $opt2->setTranslations('option_text', ['en' => 'Satisfied', 'ar' => 'راضٍ']);
        $opt3 = QuestionOption::create(['question_id' => $q4->id, 'order' => 3]);
        $opt3->setTranslations('option_text', ['en' => 'Neutral', 'ar' => 'محايد']);
        $opt4 = QuestionOption::create(['question_id' => $q4->id, 'order' => 4]);
        $opt4->setTranslations('option_text', ['en' => 'Dissatisfied', 'ar' => 'غير راضٍ']);
        $opt5 = QuestionOption::create(['question_id' => $q4->id, 'order' => 5]);
        $opt5->setTranslations('option_text', ['en' => 'Very Dissatisfied', 'ar' => 'غير راضٍ جداً']);

        // Question 5: Linear Scale
        $q5 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_LINEAR_SCALE,
            'order' => 5,
            'is_required' => true,
            'settings' => ['min_value' => 1, 'max_value' => 10, 'min_label' => 'Poor', 'max_label' => 'Excellent'],
        ]);
        $q5->setTranslations('question_text', ['en' => 'Rate your overall experience (1-10)', 'ar' => 'قيم تجربتك الإجمالية (1-10)']);

        // Question 6: Checkbox
        $q6 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_CHECKBOX,
            'order' => 6,
            'is_required' => false,
            'settings' => ['min_selections' => 0, 'max_selections' => 5],
        ]);
        $q6->setTranslations('question_text', ['en' => 'What features would you like to see improved? (Select all that apply)', 'ar' => 'ما الميزات التي ترغب في تحسينها؟ (اختر كل ما ينطبق)']);

        $opt6_1 = QuestionOption::create(['question_id' => $q6->id, 'order' => 1]);
        $opt6_1->setTranslations('option_text', ['en' => 'User Interface', 'ar' => 'واجهة المستخدم']);
        $opt6_2 = QuestionOption::create(['question_id' => $q6->id, 'order' => 2]);
        $opt6_2->setTranslations('option_text', ['en' => 'Performance', 'ar' => 'الأداء']);
        $opt6_3 = QuestionOption::create(['question_id' => $q6->id, 'order' => 3]);
        $opt6_3->setTranslations('option_text', ['en' => 'Customer Support', 'ar' => 'دعم العملاء']);
        $opt6_4 = QuestionOption::create(['question_id' => $q6->id, 'order' => 4]);
        $opt6_4->setTranslations('option_text', ['en' => 'Pricing', 'ar' => 'التسعير']);
        $opt6_5 = QuestionOption::create(['question_id' => $q6->id, 'order' => 5]);
        $opt6_5->setTranslations('option_text', ['en' => 'Documentation', 'ar' => 'التوثيق']);

        // Question 7: Long Text
        $q7 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_LONG_TEXT,
            'order' => 7,
            'is_required' => false,
            'settings' => ['max_length' => 1000, 'min_length' => 10, 'placeholder' => 'Share your thoughts...'],
        ]);
        $q7->setTranslations('question_text', ['en' => 'Additional comments or suggestions', 'ar' => 'تعليقات أو اقتراحات إضافية']);

        // Question 8: Yes/No
        $q8 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_YES_NO,
            'order' => 8,
            'is_required' => true,
        ]);
        $q8->setTranslations('question_text', ['en' => 'Would you recommend us to a friend?', 'ar' => 'هل تنصح بنا لصديق؟']);

        // Question 9: Date
        $q9 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_DATE,
            'order' => 9,
            'is_required' => false,
            'settings' => ['min_date' => '2020-01-01', 'max_date' => date('Y-m-d')],
        ]);
        $q9->setTranslations('question_text', ['en' => 'When did you last use our service?', 'ar' => 'متى استخدمت خدمتنا آخر مرة؟']);

        // Question 10: Number
        $q10 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_NUMBER,
            'order' => 10,
            'is_required' => false,
            'settings' => ['min_value' => 1, 'max_value' => 1000],
        ]);
        $q10->setTranslations('question_text', ['en' => 'How many times have you used our service?', 'ar' => 'كم مرة استخدمت خدمتنا؟']);

        // Question 11: URL
        $q11 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_URL,
            'order' => 11,
            'is_required' => false,
            'settings' => ['placeholder' => 'https://example.com'],
        ]);
        $q11->setTranslations('question_text', ['en' => 'Website (if applicable)', 'ar' => 'الموقع الإلكتروني (إن وجد)']);

        // Question 12: Dropdown
        $q12 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_DROPDOWN,
            'order' => 12,
            'is_required' => false,
        ]);
        $q12->setTranslations('question_text', ['en' => 'How did you hear about us?', 'ar' => 'كيف سمعت عنا؟']);

        $opt12_1 = QuestionOption::create(['question_id' => $q12->id, 'order' => 1]);
        $opt12_1->setTranslations('option_text', ['en' => 'Social Media', 'ar' => 'وسائل التواصل الاجتماعي']);
        $opt12_2 = QuestionOption::create(['question_id' => $q12->id, 'order' => 2]);
        $opt12_2->setTranslations('option_text', ['en' => 'Search Engine', 'ar' => 'محرك البحث']);
        $opt12_3 = QuestionOption::create(['question_id' => $q12->id, 'order' => 3]);
        $opt12_3->setTranslations('option_text', ['en' => 'Friend/Colleague', 'ar' => 'صديق/زميل']);
        $opt12_4 = QuestionOption::create(['question_id' => $q12->id, 'order' => 4]);
        $opt12_4->setTranslations('option_text', ['en' => 'Advertisement', 'ar' => 'إعلان']);
        $opt12_5 = QuestionOption::create(['question_id' => $q12->id, 'order' => 5]);
        $opt12_5->setTranslations('option_text', ['en' => 'Other', 'ar' => 'أخرى']);

        // Question 13: Time
        $q13 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_TIME,
            'order' => 13,
            'is_required' => false,
        ]);
        $q13->setTranslations('question_text', ['en' => 'Preferred contact time', 'ar' => 'وقت الاتصال المفضل']);

        // Question 14: DateTime
        $q14 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_DATETIME,
            'order' => 14,
            'is_required' => false,
        ]);
        $q14->setTranslations('question_text', ['en' => 'Best time to schedule a follow-up call', 'ar' => 'أفضل وقت لجدولة مكالمة متابعة']);

        // Question 15: File Upload
        $q15 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_FILE_UPLOAD,
            'order' => 15,
            'is_required' => false,
            'settings' => ['allowed_file_types' => ['pdf', 'doc', 'docx', 'jpg', 'png'], 'max_file_size' => 5242880, 'max_files' => 3],
        ]);
        $q15->setTranslations('question_text', ['en' => 'Upload any supporting documents (optional)', 'ar' => 'قم بتحميل أي مستندات داعمة (اختياري)']);

        // Question 16: Section Break
        $q16 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_SECTION_BREAK,
            'order' => 16,
            'is_required' => false,
        ]);
        $q16->setTranslations('question_text', ['en' => 'Final Questions', 'ar' => 'الأسئلة النهائية']);

        // Question 17: Page Break
        $q17 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_PAGE_BREAK,
            'order' => 17,
            'is_required' => false,
        ]);
        $q17->setTranslations('question_text', ['en' => 'Thank you for completing the first part!', 'ar' => 'شكراً لك على إكمال الجزء الأول!']);

        return $form;
    }

    private function createJobApplicationForm(User $user): Form
    {
        $slug = Str::slug('Job Application Form');
        $counter = 1;
        while (Form::where('slug', $slug)->exists()) {
            $slug = Str::slug('Job Application Form') . '-' . $counter;
            $counter++;
        }

        $form = Form::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'status' => 'published',
            'allow_multiple' => false,
            'require_auth' => false,
            'collect_email' => true,
            'show_progress' => true,
        ]);

        $form->setTranslations('title', ['en' => 'Job Application Form', 'ar' => 'نموذج طلب التوظيف']);
        $form->setTranslations('description', ['en' => 'Apply for a position at our company', 'ar' => 'التقدم لوظيفة في شركتنا']);

        $this->addJobApplicationQuestions($form);

        return $form;
    }

    private function createEventRegistrationForm(User $user): Form
    {
        $slug = Str::slug('Event Registration');
        $counter = 1;
        while (Form::where('slug', $slug)->exists()) {
            $slug = Str::slug('Event Registration') . '-' . $counter;
            $counter++;
        }

        $form = Form::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'status' => 'published',
            'allow_multiple' => true,
            'require_auth' => false,
            'collect_email' => true,
        ]);

        $form->setTranslations('title', ['en' => 'Event Registration', 'ar' => 'تسجيل الفعالية']);
        $form->setTranslations('description', ['en' => 'Register for our upcoming event', 'ar' => 'سجل في فعاليتنا القادمة']);

        $this->addEventRegistrationQuestions($form);

        return $form;
    }

    private function createProductSurveyForm(User $user): Form
    {
        $slug = Str::slug('Product Survey');
        $counter = 1;
        while (Form::where('slug', $slug)->exists()) {
            $slug = Str::slug('Product Survey') . '-' . $counter;
            $counter++;
        }

        $form = Form::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'status' => 'draft',
            'allow_multiple' => false,
            'require_auth' => false,
            'collect_email' => true,
            'randomize_questions' => true,
        ]);

        $form->setTranslations('title', ['en' => 'Product Survey', 'ar' => 'استبيان المنتج']);
        $form->setTranslations('description', ['en' => 'Help us understand your product preferences', 'ar' => 'ساعدنا في فهم تفضيلات المنتج الخاصة بك']);

        $this->addProductSurveyQuestions($form);

        return $form;
    }

    private function createContactForm(User $user): Form
    {
        $slug = Str::slug('Contact Us');
        $counter = 1;
        while (Form::where('slug', $slug)->exists()) {
            $slug = Str::slug('Contact Us') . '-' . $counter;
            $counter++;
        }

        $form = Form::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'status' => 'published',
            'allow_multiple' => true,
            'require_auth' => false,
            'collect_email' => true,
        ]);

        $form->setTranslations('title', ['en' => 'Contact Us', 'ar' => 'اتصل بنا']);
        $form->setTranslations('description', ['en' => 'Get in touch with us', 'ar' => 'تواصل معنا']);
        $form->setTranslations('thank_you_message', [
            'en' => 'Thank you for contacting us! We will get back to you soon.',
            'ar' => 'شكراً لتواصلك معنا! سنعود إليك قريباً.'
        ]);

        $this->addContactFormQuestions($form);

        return $form;
    }

    private function addJobApplicationQuestions(Form $form): void
    {
        $order = 1;

        // Personal Info Section
        $section = FormSection::create([
            'form_id' => $form->id,
            'order' => 1,
        ]);
        $section->setTranslations('title', ['en' => 'Personal Information', 'ar' => 'المعلومات الشخصية']);

        $q1 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q1->setTranslations('question_text', ['en' => 'Full Name', 'ar' => 'الاسم الكامل']);

        $q2 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section->id,
            'type' => FormQuestion::TYPE_EMAIL,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q2->setTranslations('question_text', ['en' => 'Email', 'ar' => 'البريد الإلكتروني']);

        $q3 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section->id,
            'type' => FormQuestion::TYPE_PHONE,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q3->setTranslations('question_text', ['en' => 'Phone Number', 'ar' => 'رقم الهاتف']);

        // Position Section
        $section2 = FormSection::create([
            'form_id' => $form->id,
            'order' => 2,
        ]);
        $section2->setTranslations('title', ['en' => 'Position Details', 'ar' => 'تفاصيل الوظيفة']);

        $q4 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_DROPDOWN,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q4->setTranslations('question_text', ['en' => 'Position Applied For', 'ar' => 'الوظيفة المتقدم لها']);

        $opt4_1 = QuestionOption::create(['question_id' => $q4->id, 'order' => 1]);
        $opt4_1->setTranslations('option_text', ['en' => 'Software Developer', 'ar' => 'مطور برمجيات']);
        $opt4_2 = QuestionOption::create(['question_id' => $q4->id, 'order' => 2]);
        $opt4_2->setTranslations('option_text', ['en' => 'Designer', 'ar' => 'مصمم']);
        $opt4_3 = QuestionOption::create(['question_id' => $q4->id, 'order' => 3]);
        $opt4_3->setTranslations('option_text', ['en' => 'Marketing Manager', 'ar' => 'مدير التسويق']);

        $q5 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_LONG_TEXT,
            'order' => $order++,
            'is_required' => true,
            'settings' => ['max_length' => 2000],
        ]);
        $q5->setTranslations('question_text', ['en' => 'Cover Letter', 'ar' => 'خطاب التقديم']);

        $q6 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_FILE_UPLOAD,
            'order' => $order++,
            'is_required' => true,
            'settings' => ['allowed_file_types' => ['pdf', 'doc', 'docx'], 'max_file_size' => 5242880],
        ]);
        $q6->setTranslations('question_text', ['en' => 'Upload Resume', 'ar' => 'تحميل السيرة الذاتية']);

        $q7 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_NUMBER,
            'order' => $order++,
            'is_required' => true,
            'settings' => ['min_value' => 0, 'max_value' => 50],
        ]);
        $q7->setTranslations('question_text', ['en' => 'Years of Experience', 'ar' => 'سنوات الخبرة']);

        $q8 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_CHECKBOX,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q8->setTranslations('question_text', ['en' => 'Skills (Select all that apply)', 'ar' => 'المهارات (اختر كل ما ينطبق)']);

        $opt8_1 = QuestionOption::create(['question_id' => $q8->id, 'order' => 1]);
        $opt8_1->setTranslations('option_text', ['en' => 'PHP', 'ar' => 'PHP']);
        $opt8_2 = QuestionOption::create(['question_id' => $q8->id, 'order' => 2]);
        $opt8_2->setTranslations('option_text', ['en' => 'JavaScript', 'ar' => 'JavaScript']);
        $opt8_3 = QuestionOption::create(['question_id' => $q8->id, 'order' => 3]);
        $opt8_3->setTranslations('option_text', ['en' => 'Laravel', 'ar' => 'Laravel']);
        $opt8_4 = QuestionOption::create(['question_id' => $q8->id, 'order' => 4]);
        $opt8_4->setTranslations('option_text', ['en' => 'Vue.js', 'ar' => 'Vue.js']);
        $opt8_5 = QuestionOption::create(['question_id' => $q8->id, 'order' => 5]);
        $opt8_5->setTranslations('option_text', ['en' => 'MySQL', 'ar' => 'MySQL']);

        $q9 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_DATE,
            'order' => $order++,
            'is_required' => false,
            'settings' => ['min_date' => date('Y-m-d')],
        ]);
        $q9->setTranslations('question_text', ['en' => 'Available Start Date', 'ar' => 'تاريخ البدء المتاح']);

        $q10 = FormQuestion::create([
            'form_id' => $form->id,
            'section_id' => $section2->id,
            'type' => FormQuestion::TYPE_YES_NO,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q10->setTranslations('question_text', ['en' => 'Are you willing to relocate?', 'ar' => 'هل أنت مستعد للانتقال؟']);
    }

    private function addEventRegistrationQuestions(Form $form): void
    {
        $order = 1;

        $q1 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q1->setTranslations('question_text', ['en' => 'Full Name', 'ar' => 'الاسم الكامل']);

        $q2 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_EMAIL,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q2->setTranslations('question_text', ['en' => 'Email', 'ar' => 'البريد الإلكتروني']);

        $q3 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_MULTIPLE_CHOICE,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q3->setTranslations('question_text', ['en' => 'Ticket Type', 'ar' => 'نوع التذكرة']);

        $opt3_1 = QuestionOption::create(['question_id' => $q3->id, 'order' => 1]);
        $opt3_1->setTranslations('option_text', ['en' => 'VIP', 'ar' => 'VIP']);
        $opt3_2 = QuestionOption::create(['question_id' => $q3->id, 'order' => 2]);
        $opt3_2->setTranslations('option_text', ['en' => 'Standard', 'ar' => 'عادي']);
        $opt3_3 = QuestionOption::create(['question_id' => $q3->id, 'order' => 3]);
        $opt3_3->setTranslations('option_text', ['en' => 'Student', 'ar' => 'طالب']);

        $q4 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_NUMBER,
            'order' => $order++,
            'is_required' => true,
            'settings' => ['min_value' => 1, 'max_value' => 10],
        ]);
        $q4->setTranslations('question_text', ['en' => 'Number of Tickets', 'ar' => 'عدد التذاكر']);

        $q5 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_DATETIME,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q5->setTranslations('question_text', ['en' => 'Preferred Session Time', 'ar' => 'وقت الجلسة المفضل']);

        $q6 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_CHECKBOX,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q6->setTranslations('question_text', ['en' => 'Dietary Requirements', 'ar' => 'المتطلبات الغذائية']);

        $opt6_1 = QuestionOption::create(['question_id' => $q6->id, 'order' => 1]);
        $opt6_1->setTranslations('option_text', ['en' => 'Vegetarian', 'ar' => 'نباتي']);
        $opt6_2 = QuestionOption::create(['question_id' => $q6->id, 'order' => 2]);
        $opt6_2->setTranslations('option_text', ['en' => 'Vegan', 'ar' => 'نباتي صرف']);
        $opt6_3 = QuestionOption::create(['question_id' => $q6->id, 'order' => 3]);
        $opt6_3->setTranslations('option_text', ['en' => 'Gluten-Free', 'ar' => 'خالي من الجلوتين']);
        $opt6_4 = QuestionOption::create(['question_id' => $q6->id, 'order' => 4]);
        $opt6_4->setTranslations('option_text', ['en' => 'Halal', 'ar' => 'حلال']);

        $q7 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_LONG_TEXT,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q7->setTranslations('question_text', ['en' => 'Special Requests', 'ar' => 'طلبات خاصة']);
    }

    private function addProductSurveyQuestions(Form $form): void
    {
        $order = 1;

        $q1 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_LINEAR_SCALE,
            'order' => $order++,
            'is_required' => true,
            'settings' => ['min_value' => 1, 'max_value' => 5, 'min_label' => 'Very Unlikely', 'max_label' => 'Very Likely'],
        ]);
        $q1->setTranslations('question_text', ['en' => 'How likely are you to purchase this product?', 'ar' => 'ما مدى احتمالية شرائك لهذا المنتج؟']);

        $q2 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_MULTIPLE_CHOICE,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q2->setTranslations('question_text', ['en' => 'Price Range', 'ar' => 'نطاق السعر']);

        $opt2_1 = QuestionOption::create(['question_id' => $q2->id, 'order' => 1]);
        $opt2_1->setTranslations('option_text', ['en' => '$0 - $50', 'ar' => '0$ - 50$']);
        $opt2_2 = QuestionOption::create(['question_id' => $q2->id, 'order' => 2]);
        $opt2_2->setTranslations('option_text', ['en' => '$51 - $100', 'ar' => '51$ - 100$']);
        $opt2_3 = QuestionOption::create(['question_id' => $q2->id, 'order' => 3]);
        $opt2_3->setTranslations('option_text', ['en' => '$101 - $200', 'ar' => '101$ - 200$']);
        $opt2_4 = QuestionOption::create(['question_id' => $q2->id, 'order' => 4]);
        $opt2_4->setTranslations('option_text', ['en' => '$200+', 'ar' => '200$+']);

        $q3 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_URL,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q3->setTranslations('question_text', ['en' => 'Product Website', 'ar' => 'موقع المنتج']);

        $q4 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_TIME,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q4->setTranslations('question_text', ['en' => 'Best time to contact you', 'ar' => 'أفضل وقت للاتصال بك']);
    }

    private function addContactFormQuestions(Form $form): void
    {
        $order = 1;

        $q1 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q1->setTranslations('question_text', ['en' => 'Name', 'ar' => 'الاسم']);

        $q2 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_EMAIL,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q2->setTranslations('question_text', ['en' => 'Email', 'ar' => 'البريد الإلكتروني']);

        $q3 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_PHONE,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q3->setTranslations('question_text', ['en' => 'Phone', 'ar' => 'الهاتف']);

        $q4 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_DROPDOWN,
            'order' => $order++,
            'is_required' => true,
        ]);
        $q4->setTranslations('question_text', ['en' => 'Subject', 'ar' => 'الموضوع']);

        $opt4_1 = QuestionOption::create(['question_id' => $q4->id, 'order' => 1]);
        $opt4_1->setTranslations('option_text', ['en' => 'General Inquiry', 'ar' => 'استفسار عام']);
        $opt4_2 = QuestionOption::create(['question_id' => $q4->id, 'order' => 2]);
        $opt4_2->setTranslations('option_text', ['en' => 'Support', 'ar' => 'الدعم']);
        $opt4_3 = QuestionOption::create(['question_id' => $q4->id, 'order' => 3]);
        $opt4_3->setTranslations('option_text', ['en' => 'Sales', 'ar' => 'المبيعات']);
        $opt4_4 = QuestionOption::create(['question_id' => $q4->id, 'order' => 4]);
        $opt4_4->setTranslations('option_text', ['en' => 'Partnership', 'ar' => 'شراكة']);

        $q5 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_LONG_TEXT,
            'order' => $order++,
            'is_required' => true,
            'settings' => ['max_length' => 2000, 'min_length' => 10],
        ]);
        $q5->setTranslations('question_text', ['en' => 'Message', 'ar' => 'الرسالة']);

        $q6 = FormQuestion::create([
            'form_id' => $form->id,
            'type' => FormQuestion::TYPE_YES_NO,
            'order' => $order++,
            'is_required' => false,
        ]);
        $q6->setTranslations('question_text', ['en' => 'Subscribe to newsletter', 'ar' => 'الاشتراك في النشرة الإخبارية']);
    }
}
