# Database ERD and Schema Design

## Entity Relationship Diagram

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ role            │
│ created_at      │
│ updated_at      │
└─────┬───────┬───┘
      │       │
      │ 1:N   │ 1:N
      │       │
┌─────▼───────┴───────────────────┐
│      companies                  │
├──────────────────────────────────┤
│ id (PK)                         │
│ user_id (FK)                    │
│ name                            │
│ slug (UNIQUE)                   │
│ description                     │
│ email                           │
│ phone                           │
│ website                         │
│ address                         │
│ primary_color                   │
│ secondary_color                 │
│ accent_color                    │
│ logo_path                       │
│ social_links (JSON)             │
│ is_active                       │
│ created_at                      │
│ updated_at                      │
└──────────────────────────────────┘

┌──────────────────────────┐
│        forms             │
├──────────────────────────┤
│ id (PK)                  │
│ user_id (FK)             │
│ title                    │
│ slug (UNIQUE)            │
│ description              │
│ status                   │
│ settings (JSON)          │
│ appearance (JSON)        │
│ allow_multiple           │
│ require_auth             │
│ collect_email            │
│ show_progress            │
│ randomize_questions      │
│ expires_at (NULL)        │
│ starts_at (NULL)         │
│ thank_you_message        │
│ redirect_url (NULL)      │
│ created_at               │
│ updated_at               │
└────────┬─────────────────┘
         │
         │ 1:N
         │
┌────────▼─────────────────┐
│    form_sections         │
├──────────────────────────┤
│ id (PK)                  │
│ form_id (FK)             │
│ title                    │
│ description              │
│ order                    │
│ created_at               │
│ updated_at               │
└────────┬─────────────────┘
         │
         │ 1:N
         │
┌────────▼─────────────────┐
│   form_questions         │
├──────────────────────────┤
│ id (PK)                  │
│ form_id (FK)             │
│ section_id (FK, NULL)    │
│ type                     │
│ question_text            │
│ help_text (NULL)         │
│ order                    │
│ is_required              │
│ settings (JSON)          │
│ conditional_logic (JSON) │
│ created_at               │
│ updated_at               │
└────────┬─────────────────┘
         │
         │ 1:N
         │
┌────────▼─────────────────┐
│   question_options       │
├──────────────────────────┤
│ id (PK)                  │
│ question_id (FK)         │
│ option_text              │
│ order                    │
│ created_at               │
│ updated_at               │
└──────────────────────────┘

┌──────────────────────────┐
│    form_responses        │
├──────────────────────────┤
│ id (PK)                  │
│ form_id (FK)             │
│ user_id (FK, NULL)       │
│ email (NULL)             │
│ ip_address (NULL)        │
│ user_agent (NULL)        │
│ status                   │
│ submitted_at             │
│ created_at               │
│ updated_at               │
└────────┬─────────────────┘
         │
         │ 1:N
         │
┌────────▼─────────────────┐
│   response_answers       │
├──────────────────────────┤
│ id (PK)                  │
│ response_id (FK)         │
│ question_id (FK)         │
│ answer_text (NULL)       │
│ answer_number (NULL)     │
│ answer_boolean (NULL)    │
│ answer_date (NULL)       │
│ answer_json (NULL)       │
│ file_path (NULL)         │
│ created_at               │
│ updated_at               │
└──────────────────────────┘
```

## Database Tables

### users
Standard Laravel users table (already exists)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user', -- 'admin', 'user'
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### companies
Stores company information and branding

```sql
CREATE TABLE companies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    website VARCHAR(255) NULL,
    address TEXT NULL,
    primary_color VARCHAR(7) NOT NULL DEFAULT '#000000', -- Hex color
    secondary_color VARCHAR(7) NULL,
    accent_color VARCHAR(7) NULL,
    logo_path VARCHAR(255) NULL,
    social_links JSON NULL, -- {"facebook": "...", "twitter": "...", etc.}
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_user_id (user_id)
);
```

### forms
Stores form metadata and settings

```sql
CREATE TABLE forms (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    status ENUM('draft', 'published', 'closed') DEFAULT 'draft',
    settings JSON NULL, -- Additional settings
    appearance JSON NULL, -- Theme, colors, logo
    allow_multiple BOOLEAN DEFAULT FALSE,
    require_auth BOOLEAN DEFAULT FALSE,
    collect_email BOOLEAN DEFAULT FALSE,
    show_progress BOOLEAN DEFAULT TRUE,
    randomize_questions BOOLEAN DEFAULT FALSE,
    expires_at TIMESTAMP NULL,
    starts_at TIMESTAMP NULL,
    thank_you_message TEXT NULL,
    redirect_url VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);
```

### form_sections
Organizes questions into sections

```sql
CREATE TABLE form_sections (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    form_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NULL,
    description TEXT NULL,
    order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    INDEX idx_form_id (form_id),
    INDEX idx_order (order)
);
```

### form_questions
Stores form questions

```sql
CREATE TABLE form_questions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    form_id BIGINT UNSIGNED NOT NULL,
    section_id BIGINT UNSIGNED NULL,
    type VARCHAR(50) NOT NULL, -- 'short_text', 'long_text', 'multiple_choice', etc.
    question_text TEXT NOT NULL,
    help_text TEXT NULL,
    order INT UNSIGNED DEFAULT 0,
    is_required BOOLEAN DEFAULT FALSE,
    settings JSON NULL, -- Type-specific settings (max_length, min_value, etc.)
    conditional_logic JSON NULL, -- Conditional display rules
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES form_sections(id) ON DELETE SET NULL,
    INDEX idx_form_id (form_id),
    INDEX idx_section_id (section_id),
    INDEX idx_order (order)
);
```

### question_options
Stores options for multiple choice, checkbox, and dropdown questions

```sql
CREATE TABLE question_options (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (question_id) REFERENCES form_questions(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id),
    INDEX idx_order (order)
);
```

### form_responses
Stores form submissions

```sql
CREATE TABLE form_responses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    form_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    email VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    status ENUM('completed', 'partial', 'abandoned') DEFAULT 'completed',
    submitted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_form_id (form_id),
    INDEX idx_user_id (user_id),
    INDEX idx_submitted_at (submitted_at)
);
```

### response_answers
Stores individual answers to questions

```sql
CREATE TABLE response_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    response_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer_text TEXT NULL,
    answer_number DECIMAL(15, 4) NULL,
    answer_boolean BOOLEAN NULL,
    answer_date DATE NULL,
    answer_json JSON NULL, -- For complex answers (arrays, objects)
    file_path VARCHAR(255) NULL, -- For file uploads
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (response_id) REFERENCES form_responses(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES form_questions(id) ON DELETE CASCADE,
    INDEX idx_response_id (response_id),
    INDEX idx_question_id (question_id)
);
```

## Relationships Summary

1. **User → Companies**: One-to-Many (one user can have multiple companies)
2. **User → Forms**: One-to-Many (one user can create multiple forms)
3. **Form → Form Sections**: One-to-Many (one form can have multiple sections)
4. **Form → Form Questions**: One-to-Many (one form can have multiple questions)
5. **Form Section → Form Questions**: One-to-Many (one section can have multiple questions)
6. **Form Question → Question Options**: One-to-Many (one question can have multiple options)
7. **Form → Form Responses**: One-to-Many (one form can have multiple responses)
8. **User → Form Responses**: One-to-Many (one user can submit multiple responses)
9. **Form Response → Response Answers**: One-to-Many (one response can have multiple answers)
10. **Form Question → Response Answers**: One-to-Many (one question can have multiple answers)

**Note**: Forms are independent of companies. Companies are only used for the QR code feature to display company profiles.

## Indexes

- All foreign keys are indexed
- `slug` fields are indexed for fast lookups
- `status` fields are indexed for filtering
- `order` fields are indexed for sorting
- `submitted_at` is indexed for date-based queries

## JSON Fields

### companies.social_links
```json
{
  "facebook": "https://facebook.com/company",
  "twitter": "https://twitter.com/company",
  "instagram": "https://instagram.com/company",
  "linkedin": "https://linkedin.com/company/company",
  "youtube": "https://youtube.com/@company",
  "tiktok": "https://tiktok.com/@company"
}
```

### forms.settings
```json
{
  "custom_field_1": "value",
  "custom_field_2": "value"
}
```

### forms.appearance
```json
{
  "theme": "light",
  "primary_color": "#FF5733",
  "background_color": "#FFFFFF",
  "text_color": "#000000",
  "font_family": "Arial",
  "logo_path": "/path/to/logo.png"
}
```

### form_questions.settings
```json
{
  "max_length": 255,
  "min_length": 10,
  "placeholder": "Enter your answer",
  "min_value": 1,
  "max_value": 5,
  "min_label": "Poor",
  "max_label": "Excellent",
  "allowed_file_types": ["jpg", "png", "pdf"],
  "max_file_size": 5242880
}
```

### form_questions.conditional_logic
```json
{
  "enabled": true,
  "condition": {
    "question_id": 5,
    "operator": "equals",
    "value": "Yes"
  }
}
```

### response_answers.answer_json
For multiple selections, arrays, etc.
```json
{
  "selected_options": [1, 3, 5],
  "other_text": "Custom answer"
}
```

## Migration Strategy

1. Create `companies` table
2. Create `forms` table
3. Create `form_sections` table
4. Create `form_questions` table
5. Create `question_options` table
6. Create `form_responses` table
7. Create `response_answers` table

## Notes

- All timestamps use Laravel's standard `created_at` and `updated_at`
- Soft deletes can be added later if needed
- JSON fields provide flexibility for future enhancements
- Indexes are optimized for common query patterns
- Foreign keys ensure data integrity

