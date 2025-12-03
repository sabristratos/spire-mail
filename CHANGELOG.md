# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.1] - 2025-12-03

### Fixed
- Updated repository URLs from sabrenski to sabristratos
- Fixed tag controller logging method

### Changed
- Improved editor sidebar and block properties

## [0.1.0] - 2025-11-29

### Added
- Initial release
- Visual drag-and-drop email template editor
- 12 block types:
  - Text - Rich text content with formatting
  - Heading - H1-H6 headings with styling
  - Button - Call-to-action buttons with customization
  - Image - Responsive images with alt text
  - Divider - Horizontal rule separators
  - Spacer - Vertical spacing control
  - Social Icons - Social media icon links
  - Video - Video thumbnail with play button
  - Row - Multi-column layouts (1-4 columns)
  - Column - Container within rows
  - HTML - Custom HTML blocks
  - Code - Syntax-highlighted code blocks
- Tag system with merge tag support (`{{variable}}`)
- Built-in formatters:
  - `date` - Date formatting
  - `currency` - Currency formatting
  - `uppercase` - Convert to uppercase
  - `lowercase` - Convert to lowercase
  - `capitalize` - Capitalize words
  - `truncate` - Truncate with ellipsis
  - `count` - Count array/string length
  - `number` - Number formatting
  - `default` - Fallback value
- Conditional blocks (`{{#if}}`, `{{#unless}}`, `{{else}}`)
- MJML-powered responsive email rendering
- Inertia.js Vue 3 frontend components
- Template management API with CRUD operations
- `SpireTemplateMailable` for sending template-based emails
- `UsesSpireTemplate` trait for existing Mailables
- `SpireMail` facade for programmatic access
- Custom layout support via Inertia persistent layouts
- Authorization via Laravel Gates
- Configurable route prefix and middleware
