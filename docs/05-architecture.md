# Technical Architecture

## Overview
This document outlines the technical architecture, workflow, and implementation details for the Generate application.

## Application Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── CompanyController.php
│   │   │   ├── FormController.php
│   │   │   └── DashboardController.php
│   │   ├── Public/
│   │   │   ├── CompanyProfileController.php
│   │   │   └── FormSubmissionController.php
│   │   └── Auth/
│   │       └── LoginController.php
│   ├── Requests/
│   │   ├── CompanyRequest.php
│   │   └── FormRequest.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── Company.php
│   ├── Form.php
│   ├── FormSection.php
│   ├── FormQuestion.php
│   ├── QuestionOption.php
│   ├── FormResponse.php
│   └── ResponseAnswer.php
├── Services/
│   ├── QRCodeService.php
│   ├── FormBuilderService.php
│   └── FileUploadService.php
└── Helpers/
    └── SlugHelper.php
```

## Workflow

### Company Data Management Flow

```
1. Admin Login
   ↓
2. Navigate to Company Management
   ↓
3. Create/Edit Company Information
   ├── Enter company details
   ├── Upload logo
   ├── Set brand colors
   └── Add social media links
   ↓
4. Save Company Data
   ├── Validate input
   ├── Generate slug
   ├── Store logo file
   └── Save to database
   ↓
5. Generate QR Code
   ├── Create QR code with company profile URL
   ├── Display QR code preview
   └── Allow download (PNG/SVG)
   ↓
6. Public Access
   ├── User scans QR code
   ├── Redirects to /company/{slug}
   └── Display company profile page
```

### Form Builder Flow

```
1. Admin Login
   ↓
2. Navigate to Form Builder
   ↓
3. Create New Form
   ├── Enter form title and description
   ├── Configure form settings
   └── Set appearance options
   ↓
4. Add Questions
   ├── Select question type
   ├── Enter question text
   ├── Configure question settings
   ├── Add options (if applicable)
   └── Set conditional logic (optional)
   ↓
5. Organize Form
   ├── Add sections (optional)
   ├── Reorder questions
   └── Preview form
   ↓
6. Publish Form
   ├── Generate form slug
   ├── Generate QR code (optional)
   └── Get shareable link
   ↓
7. Form Submission
   ├── User accesses form via link/QR
   ├── Fill out form
   ├── Validate responses
   └── Submit form
   ↓
8. Response Management
   ├── View responses in admin panel
   ├── Export responses
   └── View analytics
```

## Key Components

### Controllers

#### CompanyController
- `index()` - List all companies
- `create()` - Show create form
- `store()` - Save new company
- `edit()` - Show edit form
- `update()` - Update company
- `destroy()` - Delete company
- `generateQRCode()` - Generate QR code
- `downloadQRCode()` - Download QR code

#### FormController
- `index()` - List all forms
- `create()` - Show create form
- `store()` - Save new form
- `edit()` - Show edit form
- `update()` - Update form
- `destroy()` - Delete form
- `duplicate()` - Duplicate form
- `preview()` - Preview form
- `responses()` - View responses
- `export()` - Export responses
- `analytics()` - View analytics

### Services

#### QRCodeService
```php
class QRCodeService
{
    public function generate(string $url, int $size = 300): string
    public function download(string $url, string $format = 'png'): Response
    public function getQRCodePath(string $url): string
}
```

#### FormBuilderService
```php
class FormBuilderService
{
    public function createForm(array $data): Form
    public function addQuestion(Form $form, array $questionData): FormQuestion
    public function reorderQuestions(Form $form, array $order): void
    public function duplicateForm(Form $form): Form
    public function validateResponse(Form $form, array $answers): array
}
```

#### FileUploadService
```php
class FileUploadService
{
    public function uploadLogo(UploadedFile $file): string
    public function uploadFormFile(UploadedFile $file, Form $form): string
    public function deleteFile(string $path): bool
}
```

### Models

#### Company Model
```php
class Company extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'email', 'phone',
        'website', 'address', 'primary_color', 'secondary_color',
        'accent_color', 'logo_path', 'social_links', 'is_active'
    ];
    
    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function user()
    public function getQRCodeUrlAttribute()
}
```

#### Form Model
```php
class Form extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'status', 'settings',
        'appearance', 'allow_multiple', 'require_auth', 'collect_email',
        'show_progress', 'randomize_questions', 'expires_at', 'starts_at',
        'thank_you_message', 'redirect_url'
    ];
    
    protected $casts = [
        'settings' => 'array',
        'appearance' => 'array',
        'allow_multiple' => 'boolean',
        'require_auth' => 'boolean',
        'collect_email' => 'boolean',
        'show_progress' => 'boolean',
        'randomize_questions' => 'boolean',
        'expires_at' => 'datetime',
        'starts_at' => 'datetime',
    ];
    
    public function user()
    public function sections()
    public function questions()
    public function responses()
    public function isPublished()
    public function isExpired()
}
```

## Routes

### Admin Routes
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Company routes
    Route::resource('companies', CompanyController::class);
    Route::get('companies/{company}/qrcode', [CompanyController::class, 'generateQRCode']);
    Route::get('companies/{company}/qrcode/download', [CompanyController::class, 'downloadQRCode']);
    
    // Form routes
    Route::resource('forms', FormController::class);
    Route::post('forms/{form}/duplicate', [FormController::class, 'duplicate']);
    Route::get('forms/{form}/preview', [FormController::class, 'preview']);
    Route::get('forms/{form}/responses', [FormController::class, 'responses']);
    Route::get('forms/{form}/responses/export', [FormController::class, 'export']);
    Route::get('forms/{form}/analytics', [FormController::class, 'analytics']);
    Route::get('forms/{form}/qrcode', [FormController::class, 'generateQRCode']);
});
```

### Public Routes
```php
// Company profile
Route::get('company/{slug}', [CompanyProfileController::class, 'show'])->name('company.show');

// Form submission
Route::get('forms/{slug}', [FormSubmissionController::class, 'show'])->name('forms.show');
Route::post('forms/{slug}/submit', [FormSubmissionController::class, 'submit'])->name('forms.submit');
```

## Frontend Architecture

### Blade Components
- `components.company-form.blade.php` - Company form
- `components.form-builder.blade.php` - Form builder interface
- `components.question-editor.blade.php` - Question editor
- `components.qr-code-display.blade.php` - QR code display
- `components.response-table.blade.php` - Response table

### JavaScript
- Use Alpine.js for interactivity
- Form builder drag-and-drop (Sortable.js or similar)
- Real-time preview
- Form validation

### Styling
- Tailwind CSS 4.0 for styling
- Custom components for form builder
- Responsive design
- Dark mode support (optional)

## Security Considerations

1. **Authentication & Authorization**
   - Laravel Sanctum or default auth
   - Admin middleware for protected routes
   - Role-based access control

2. **Input Validation**
   - Form requests for validation
   - Sanitize user input
   - File upload validation

3. **CSRF Protection**
   - Laravel's built-in CSRF protection
   - Verify tokens on all POST requests

4. **File Upload Security**
   - Validate file types
   - Limit file sizes
   - Store files outside public directory
   - Scan for malware (optional)

5. **SQL Injection**
   - Use Eloquent ORM (parameterized queries)
   - Avoid raw queries

6. **XSS Protection**
   - Blade's automatic escaping
   - Sanitize user-generated content

## Performance Optimization

1. **Database**
   - Proper indexing
   - Eager loading relationships
   - Query optimization
   - Database caching

2. **File Storage**
   - Use CDN for static assets (optional)
   - Image optimization
   - Lazy loading

3. **Caching**
   - Cache company profiles
   - Cache form structures
   - Cache analytics data

4. **Frontend**
   - Minify CSS/JS
   - Lazy load images
   - Code splitting

## Testing Strategy

1. **Unit Tests**
   - Model tests
   - Service tests
   - Helper function tests

2. **Feature Tests**
   - Controller tests
   - Form submission tests
   - QR code generation tests

3. **Browser Tests**
   - Form builder interface
   - Form submission flow
   - Company profile display

## Deployment

1. **Environment Setup**
   - Production database (MySQL/PostgreSQL)
   - File storage (S3 or local)
   - Queue workers
   - Cron jobs

2. **Configuration**
   - Environment variables
   - Cache configuration
   - Session configuration
   - Mail configuration

3. **Monitoring**
   - Error logging
   - Performance monitoring
   - User analytics

## Future Enhancements

1. **API Development**
   - RESTful API
   - GraphQL API
   - API documentation (Swagger)

2. **Real-time Features**
   - WebSocket support
   - Real-time form preview
   - Live response updates

3. **Advanced Features**
   - Multi-language support
   - Payment integration
   - Email notifications
   - Webhooks
   - Third-party integrations

