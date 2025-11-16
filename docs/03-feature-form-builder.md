# Feature: Form Builder

## Overview
A comprehensive form builder that allows admins to create custom forms and surveys similar to Google Forms, with full customization options and response management.

## Requirements

### Form Management

#### Form Properties
- **Title** (required, string, max 255)
- **Description** (optional, text)
- **Slug** (auto-generated, unique)
- **Status** (draft, published, closed)
- **Settings**:
  - Allow multiple submissions per user (boolean)
  - Require authentication (boolean)
  - Collect email addresses (boolean)
  - Show progress bar (boolean)
  - Randomize question order (boolean)
  - Set expiration date (optional, datetime)
  - Set start date (optional, datetime)
  - Custom thank you message (optional, text)
  - Redirect URL after submission (optional, URL)

#### Form Appearance
- **Theme/Color Scheme** (custom colors)
- **Logo** (optional, custom upload)
- **Background Color** (optional)
- **Text Color** (optional)
- **Font Family** (optional)

### Question Types

1. **Short Text** (single line)
   - Placeholder text
   - Max length
   - Required/optional

2. **Long Text** (textarea)
   - Placeholder text
   - Max length
   - Min length
   - Required/optional

3. **Multiple Choice** (radio buttons)
   - Options (array)
   - Allow "Other" option
   - Required/optional

4. **Checkboxes**
   - Options (array)
   - Allow "Other" option
   - Min selections
   - Max selections
   - Required/optional

5. **Dropdown**
   - Options (array)
   - Required/optional

6. **Linear Scale** (rating)
   - Min value (default: 1)
   - Max value (default: 5)
   - Min label (e.g., "Poor")
   - Max label (e.g., "Excellent")
   - Required/optional

7. **Date**
   - Date format
   - Min date
   - Max date
   - Required/optional

8. **Time**
   - Time format (12h/24h)
   - Required/optional

9. **Date and Time**
   - Date and time format
   - Required/optional

10. **File Upload**
    - Allowed file types
    - Max file size
    - Max number of files
    - Required/optional

11. **Email**
    - Validation
    - Required/optional

12. **Number**
    - Min value
    - Max value
    - Decimal places
    - Required/optional

13. **Phone Number**
    - Format validation
    - Required/optional

14. **URL**
    - Validation
    - Required/optional

15. **Yes/No** (boolean)
    - Required/optional

16. **Section Break** (visual separator)
    - Title
    - Description

17. **Page Break** (multi-page forms)
    - Title
    - Description

### Question Properties
- **Question Text** (required)
- **Help Text** (optional, shown below question)
- **Question Order** (integer, for sorting)
- **Required** (boolean)
- **Conditional Logic** (optional)
  - Show question only if previous question meets condition
  - Conditions: equals, not equals, contains, greater than, less than, etc.

### Form Sections
- Forms can have multiple sections
- Each section can have a title and description
- Questions belong to sections
- Sections can be collapsed/expanded

### Response Management

#### Response Data
- **Form ID** (foreign key)
- **Submitted At** (timestamp)
- **IP Address** (optional, for analytics)
- **User Agent** (optional)
- **User ID** (optional, if authenticated)
- **Email** (optional, if collected)
- **Response Data** (JSON, stores all answers)
- **Status** (completed, partial, abandoned)

#### Response Analytics
- Total submissions
- Completion rate
- Average time to complete
- Response breakdown by question
- Export responses (CSV, Excel, JSON)
- Charts and graphs for visual data

### Form Sharing
- **Public Link** (unique URL)
- **Embed Code** (iframe)
- **QR Code** (for form access)
- **Email Invitations** (future feature)
- **Social Media Sharing** (future feature)

## User Stories

### Admin
1. As an admin, I want to create a new form
2. As an admin, I want to add different types of questions
3. As an admin, I want to reorder questions
4. As an admin, I want to set question properties (required, validation, etc.)
5. As an admin, I want to customize form appearance
6. As an admin, I want to set form settings (expiration, authentication, etc.)
7. As an admin, I want to preview the form before publishing
8. As an admin, I want to publish/unpublish forms
9. As an admin, I want to view form responses
10. As an admin, I want to export responses
11. As an admin, I want to see response analytics
12. As an admin, I want to duplicate a form
13. As an admin, I want to delete a form
14. As an admin, I want to add conditional logic to questions

### Form Respondent
1. As a user, I want to access a form via link or QR code
2. As a user, I want to see a progress indicator
3. As a user, I want to fill out the form easily
4. As a user, I want to see validation errors
5. As a user, I want to save progress (if allowed)
6. As a user, I want to submit the form
7. As a user, I want to see a thank you message after submission

## API Endpoints (Future)

### Admin Routes
- `GET /admin/forms` - List all forms
- `GET /admin/forms/create` - Create form page
- `POST /admin/forms` - Store new form
- `GET /admin/forms/{id}` - View form
- `GET /admin/forms/{id}/edit` - Edit form page
- `PUT /admin/forms/{id}` - Update form
- `DELETE /admin/forms/{id}` - Delete form
- `POST /admin/forms/{id}/duplicate` - Duplicate form
- `GET /admin/forms/{id}/responses` - View responses
- `GET /admin/forms/{id}/responses/export` - Export responses
- `GET /admin/forms/{id}/analytics` - View analytics
- `GET /admin/forms/{id}/qrcode` - Generate QR code

### Public Routes
- `GET /forms/{slug}` - Public form page
- `POST /forms/{slug}/submit` - Submit form response
- `GET /forms/{slug}/preview` - Preview form (if authenticated)

## Database Schema

See [04-database-erd.md](./04-database-erd.md) for detailed schema.

### Main Tables
- `forms` - Form metadata
- `form_sections` - Form sections
- `form_questions` - Questions
- `question_options` - Options for multiple choice, checkbox, dropdown
- `form_responses` - Form submissions
- `response_answers` - Individual answers

## Implementation Notes

### Form Builder UI
- Drag-and-drop interface for question ordering
- Inline editing for questions
- Real-time preview
- Use Vue.js or Alpine.js for interactivity

### Response Storage
- Store responses as JSON for flexibility
- Also store normalized data for easy querying
- Consider using MongoDB for complex nested responses (optional)

### File Uploads
- Store uploaded files in `storage/app/public/form-uploads/`
- Validate file types and sizes
- Generate unique filenames
- Clean up old files periodically

### Conditional Logic
- Store logic rules as JSON
- Evaluate on frontend for real-time display
- Validate on backend before saving

### Analytics
- Use Laravel's query builder for aggregations
- Consider caching for performance
- Use Chart.js or similar for visualizations

## UI/UX Considerations

### Form Builder Interface
- Sidebar with question types
- Main canvas for form design
- Properties panel for selected question
- Preview panel
- Responsive design

### Form Display
- Clean, modern design
- Mobile-optimized
- Accessible (WCAG compliance)
- Fast loading
- Progress indicator
- Auto-save (optional)

### Response Management
- Table view with sorting/filtering
- Individual response view
- Bulk actions
- Export options
- Analytics dashboard

## Future Enhancements
- Form templates
- Collaboration (multiple admins)
- Form versioning
- A/B testing
- Payment integration
- Email notifications
- Webhooks
- API for programmatic form creation
- Multi-language support
- Advanced analytics (funnel analysis, etc.)
- Integration with third-party services (CRM, email marketing, etc.)

