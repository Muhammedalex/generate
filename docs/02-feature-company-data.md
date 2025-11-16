# Feature: Company Data Management

## Overview
This feature allows admins to manage company information and generate QR codes that link to a public company profile page.

## Requirements

### Data Fields

#### Company Information
- **Name** (required, string, max 255)
- **Description** (optional, text)
- **Email** (optional, email)
- **Phone** (optional, string)
- **Website** (optional, URL)
- **Address** (optional, text)

#### Brand Identity
- **Primary Color** (required, hex color code, e.g., #FF5733)
- **Secondary Color** (optional, hex color code)
- **Accent Color** (optional, hex color code)
- **Logo** (optional, image file - jpg, png, svg)
  - Max file size: 5MB
  - Recommended dimensions: 512x512px or higher
  - Stored in: `storage/app/public/logos/`

#### Social Media Links
- **Facebook** (optional, URL)
- **Twitter/X** (optional, URL)
- **Instagram** (optional, URL)
- **LinkedIn** (optional, URL)
- **YouTube** (optional, URL)
- **TikTok** (optional, URL)
- **Other** (optional, JSON field for additional platforms)

### QR Code Generation
- Generate QR code that links to: `/company/{slug}` or `/company/{id}`
- QR code should be downloadable (PNG, SVG formats)
- QR code should be displayable in admin panel
- QR code size options: Small, Medium, Large
- Error correction level: Medium (M) or High (H)

### Public Company Profile Page
- Route: `/company/{slug}` or `/company/{id}`
- Display all company information
- Show logo prominently
- Display social media links as icons
- Use company colors for branding
- Responsive design (mobile-friendly)
- SEO-friendly meta tags

## User Stories

### Admin
1. As an admin, I want to create/edit company information
2. As an admin, I want to upload a company logo
3. As an admin, I want to set brand colors
4. As an admin, I want to add social media links
5. As an admin, I want to generate a QR code for the company profile
6. As an admin, I want to download the QR code in different formats
7. As an admin, I want to preview the public company profile page

### Public User
1. As a visitor, I want to view company information by scanning a QR code
2. As a visitor, I want to see company logo and branding
3. As a visitor, I want to access company social media links
4. As a visitor, I want to contact the company via email/phone

## API Endpoints (Future)

### Admin Routes
- `GET /admin/company` - View company data
- `GET /admin/company/edit` - Edit form
- `POST /admin/company` - Create/Update company data
- `GET /admin/company/qrcode` - Generate QR code
- `GET /admin/company/qrcode/download` - Download QR code

### Public Routes
- `GET /company/{slug}` - Public company profile page

## Database Schema

See [04-database-erd.md](./04-database-erd.md) for detailed schema.

### Main Table: `companies`
- id (primary key)
- name
- slug (unique, for SEO-friendly URLs)
- description
- email
- phone
- website
- address
- primary_color
- secondary_color
- accent_color
- logo_path
- social_links (JSON)
- is_active (boolean, default true)
- created_at
- updated_at

## Implementation Notes

### QR Code Library
Recommended: `simplesoftwareio/simple-qrcode`
```bash
composer require simplesoftwareio/simple-qrcode
```

### File Storage
- Use Laravel's storage system
- Store logos in `storage/app/public/logos/`
- Create symbolic link: `php artisan storage:link`

### Slug Generation
- Auto-generate slug from company name
- Ensure uniqueness
- Use Laravel's `Str::slug()` helper

### Color Validation
- Validate hex color format (#RRGGBB or #RGB)
- Store as string in database

### Image Upload
- Validate file type and size
- Resize/optimize images if needed
- Generate thumbnails for different use cases

## UI/UX Considerations

### Admin Panel
- Clean, modern form layout
- Color picker for brand colors
- Image upload with preview
- Real-time QR code preview
- Responsive design

### Public Profile Page
- Hero section with logo
- Company information cards
- Social media icons grid
- Contact information section
- Mobile-optimized layout

## Future Enhancements
- Multiple company support (multi-tenant)
- Custom domain support
- Analytics for QR code scans
- Custom QR code styling
- Multiple language support
- Company branches/locations

