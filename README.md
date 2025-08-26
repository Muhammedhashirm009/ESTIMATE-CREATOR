# Overview

This is a mobile-friendly PHP estimate generator for computer accessories with advanced logo and settings management. The application supports dynamic customization of shop details, professional PDF creation with FPDF, and responsive design optimized for small businesses. It features a complete PHP backend with session management and professional letterhead generation for Computer Soft business.

## Recent Changes (August 21, 2025)
- Updated PDF generation to use Computer Soft letterhead information
- Modified default business details throughout the application
- Enhanced header design in PDF estimates with complete contact information
- Updated form placeholders to show Computer Soft business information
- Added dual PDF generation modes: detailed (with prices) and simple (quantities only)
- Implemented "Hide Item Prices" toggle switch for simple estimates
- Created generate_pdf_simple.php for price-hidden PDF generation
- Enhanced JavaScript with dynamic form action switching

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Backend Architecture
- **Technology Stack**: PHP 8.2 server with session-based data management
- **PDF Generation**: FPDF library for professional estimate document creation
- **File Handling**: PHP file upload system for logo management with validation
- **Session Management**: PHP sessions for persistent user settings and shop configuration
- **Error Logging**: Comprehensive error tracking with timestamped logs

## Frontend Architecture
- **Technology Stack**: Bootstrap 5, vanilla JavaScript, and responsive HTML/CSS
- **Design System**: Bootstrap-based responsive design with custom CSS variables
- **Mobile-First Approach**: Responsive grid system optimized for mobile devices
- **UI Components**: Tab-based navigation with dynamic form interactions
- **Asset Management**: Organized CSS and JavaScript with Feather icons

## PHP Components
- **generate_pdf.php**: Professional PDF creation with Computer Soft letterhead
- **save_settings.php**: Shop settings management with JSON responses
- **upload_logo.php**: Logo file handling with security validation
- **index.php**: Main application interface with dual-tab layout

## Data Management
- **Session Storage**: PHP sessions for shop settings and user preferences
- **File System**: Secure logo storage in uploads directory
- **Form Processing**: Server-side validation and data handling

# External Dependencies

## Frontend Libraries
- **Bootstrap**: CSS framework for responsive layout and components
- **Feather Icons**: Icon library for consistent UI iconography
- **No Backend Dependencies**: Purely client-side application

## Browser APIs
- **localStorage**: For persistent settings storage
- **DOM API**: For dynamic content manipulation
- **Event API**: For user interaction handling

## Third-Party Services
- **None**: The application appears to be completely self-contained with no external API integrations or third-party services required for core functionality
