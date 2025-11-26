# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Responsible AI Institute WordPress is a Dockerized WordPress development environment for the Responsible AI Institute website. It includes a custom theme with ACF Pro integration, custom post types for events and resources, and a contact form system. The site showcases AI ethics education, resources, and community events.

## Development Commands

### Docker Environment
```bash
make up              # Start all containers (WordPress, MariaDB, phpMyAdmin, Mailhog)
make down            # Stop all containers
make restart         # Restart all containers
make status          # Show container status
make logs            # Follow logs from all containers
make logs-wp         # Follow WordPress container logs only
make logs-db         # Follow database logs only
make build           # Rebuild containers (no cache)
```

### WordPress CLI
```bash
make wp cmd="<command>"        # Run WP-CLI command (e.g., make wp cmd="plugin list")
make install                   # Install WordPress via WP-CLI
make update                    # Update WordPress core, plugins, and themes
```

### Database Operations
```bash
make backup                    # Create database backup in ./backups/
make restore file=<path>       # Restore from backup file
make db-shell                  # Open MySQL shell
make shell                     # Open bash shell in WordPress container
```

### Destructive Operations
```bash
make fresh           # Delete all data and start clean (prompts for confirmation)
make clean           # Remove all containers, volumes, and data
```

### Local Development URLs
- WordPress: http://localhost:8088
- phpMyAdmin: http://localhost:8089 (DB_USER and DB_PASSWORD from .env)
- Mailhog (email testing): http://localhost:8025

## Architecture

### Directory Structure
```
wp-content/
├── themes/
│   ├── mydefenselaw/        # Legacy law firm theme (for reference only)
│   └── responsible-ai/      # Active Responsible AI Institute theme
│       ├── acf-json/        # ACF field group JSON (version controlled)
│       ├── assets/
│       │   ├── css/         # Stylesheets
│       │   ├── js/          # JavaScript
│       │   └── images/      # Theme images and icons
│       ├── inc/             # PHP includes (modular functionality)
│       └── template-parts/  # Reusable template partials
│           ├── sections/    # Page section templates
│           ├── cards/       # Reusable card components
│           └── global/      # Site-wide components
└── plugins/
    └── advanced-custom-fields-pro/  # ACF Pro (version controlled)
```

### Theme Architecture (responsible-ai)

**Functions.php Structure** - Uses modular includes pattern. Main functions.php contains:
- Theme setup and configuration
- Navigation menu registration
- Widget area registration
- Script/style enqueueing
- ACF Options Pages registration
- Custom image size registration

**Modular PHP Includes** (`inc/` directory) - All loaded via `require_once` in functions.php:
- `custom-post-types.php` - CPT and taxonomy registration for rai_event, rai_resource, case_study
- `enqueue.php` - Additional asset enqueueing logic
- `template-tags.php` - Helper functions for templates
- `contact-form-handler.php` - AJAX contact form processing (wp_ajax action handlers)
- `acf-sync-helper.php` - ACF field synchronization utilities
- `admin-customization.php` - WordPress admin UI customizations
- `disable-comments.php` - Disable WordPress comments site-wide
- `email-templates.php` - Email template functions
- `seo-schema.php` - Schema.org structured data markup

**Custom Post Types** (defined in `inc/custom-post-types.php`):
- `rai_event` - Events (public, has archive at /events/)
  - Taxonomies: `event_type` (hierarchical)
  - Uses Classic Editor with ACF fields
  - Custom admin columns: event_date, event_format
- `rai_resource` - Educational resources (public, has archive at /resources/)
  - Taxonomies: `resource_type` (hierarchical), `resource_topic` (tags)
  - Uses Classic Editor with ACF fields
  - Custom admin columns: resource_format, featured
- `case_study` - Case studies (public, has archive at /case-studies/)
  - Taxonomies: `industry` (hierarchical)
  - Uses Classic Editor with ACF fields

Note: All CPTs have Gutenberg disabled (`'show_in_rest' => false`) to use Classic Editor with ACF

**ACF Field Groups** (in `acf-json/`) - Auto-synced from database to version control:
- `group_rai_homepage.json` - Homepage sections and content fields
- `group_rai_event.json` - Event post type fields (date, time, location, format, registration)
- `group_rai_resource.json` - Resource post type fields (format, file upload, external link, featured)
- `group_rai_who_we_are.json` - Who We Are page fields (team, mission, partners)
- `group_theme_options.json` - Global theme settings (Options Page)

**Theme Settings** (ACF Options Pages - registered in functions.php):
- Theme Settings (main menu) - menu_slug: 'theme-settings'
- Contact Info (submenu) - parent_slug: 'theme-settings'
- Social Media (submenu) - parent_slug: 'theme-settings'

**Template Parts** (`template-parts/`):
- `sections/` - Reusable section templates:
  - `hero.php` - Hero banner section
  - `about-intro.php` - About intro section
  - `mission.php` - Mission statement section
  - `team.php` - Team members display
  - `partners.php` - Partner logos display
  - `events-grid.php` - Events listing grid
  - `resources-grid.php` - Resources listing grid
  - `contact-form.php` - Contact form section
  - `newsletter.php` - Newsletter signup section
  - Various homepage sections
- `cards/` - Reusable card components:
  - `event-card.php` - Event card template
  - `resource-card.php` - Resource card template
- `global/` - Site-wide components:
  - `breadcrumbs.php` - Breadcrumb navigation
  - `social-share.php` - Social sharing buttons

**Page Templates**:
- `front-page.php` - Homepage template
- `page-who-we-are.php` - Who We Are page template
- `page-raise-pathways.php` - RAISE Pathways program page
- `page-contact.php` - Contact page template
- `archive-rai_event.php` - Events archive template
- `archive-rai_resource.php` - Resources archive template
- `single-rai_event.php` - Single event template

**AJAX Implementation**:
- Action: `responsibleai_contact_form` (both logged-in and logged-out via wp_ajax and wp_ajax_nopriv)
- Nonce: `responsibleai_contact_nonce` (created in wp_localize_script)
- Handler: `inc/contact-form-handler.php`
- Includes honeypot spam protection
- Sends email via wp_mail (captured by Mailhog in dev)

### Key Design Decisions

1. **Gutenberg disabled for all CPTs** - Uses Classic Editor mode with ACF fields only for events, resources, and case studies
2. **ACF JSON sync** - Field groups stored in theme for version control (acf-json/)
3. **Docker-based development** - Uses docker-compose with MariaDB 10.11, not MySQL
4. **Volume mounting** - Only `wp-content/` is mounted; WordPress core lives in Docker volume at wordpress_data
5. **Mailhog integration** - All emails captured locally at http://localhost:8025 for testing
6. **Modular architecture** - Theme uses include files and template parts for maintainability
7. **Taxonomies for content organization** - Event types, resource types, topics, and industries
8. **Custom admin columns** - Enhanced admin list views for CPTs with ACF field data
9. **Legacy theme retained** - mydefenselaw theme kept in repo for reference but not active

### Navigation Menus
- `primary` - Main navigation
- `footer-about` - Footer about section links
- `footer-resources` - Footer resources section links
- `footer-community` - Footer community section links
- `footer-legal` - Footer legal/privacy links

### Widget Areas
- `footer-widgets` - Footer widget area

### Custom Image Sizes
- `hero-slide` - 1920x1080 (cropped) - Hero banner images
- `team-photo` - 400x500 (cropped) - Team member photos
- `partner-logo` - 200x100 (not cropped) - Partner logo images
- `resource-thumb` - 400x300 (cropped) - Resource thumbnails
- `blog-card` - 800x450 (cropped) - Blog/news card images

## Environment Configuration

Database and site config in `.env` file. Key variables:
- `DB_HOST=db` - Docker service name (not localhost)
- `DB_NAME`, `DB_USER`, `DB_PASSWORD` - Database connection credentials
- `DB_ROOT_PASSWORD` - MariaDB root password
- `SITE_URL=http://localhost:8088` - WordPress site URL (port 8088 not 8080)
- `WORDPRESS_DEBUG=1` - Debug mode toggle
- `ADMIN_USER`, `ADMIN_PASSWORD`, `ADMIN_EMAIL` - For WP-CLI installation

## Important Notes

- WordPress runs on port **8088** (not 8080)
- Database is **MariaDB 10.11** (not MySQL) - exposed on port 3307
- wp-content is volume-mounted; core WordPress files are in Docker volume `wordpress_data`
- Theme version constant: `RESPONSIBLEAI_VERSION` defined in functions.php
- Theme uses jQuery (enqueued as dependency)
- Font Awesome 6.4.0 loaded from CDN
- Google Fonts: Roboto (multiple weights)
- AJAX endpoints use nonce `responsibleai_contact_nonce`
- Debug log location: `wp-content/debug.log` (when WORDPRESS_DEBUG=1)
- WP-CLI runs in separate container with profile 'cli' (user 33:33)
- Never save working files to root folder; use appropriate subdirectories
- Active theme: `responsible-ai` (mydefenselaw is legacy/reference only)
- Text domain: `responsible-ai` for translations
- Comments are disabled site-wide via `inc/disable-comments.php`

## Docker Services

- **wordpress** - WordPress container (port 8088), custom Dockerfile in docker/wordpress/
- **db** - MariaDB 10.11 (port 3307)
- **phpmyadmin** - Database management UI (port 8089)
- **mailhog** - Email testing (SMTP 1025, Web UI 8025)
- **wp-cli** - WP-CLI utilities (profile: cli, runs on-demand only)

## Content Types Overview

### Events (`rai_event`)
Events are used for workshops, webinars, conferences, and community gatherings related to AI ethics and responsible AI practices. Each event has:
- Event date and time
- Location (physical or virtual)
- Format (In-Person, Virtual, Hybrid)
- Registration details
- Event type taxonomy for categorization

### Resources (`rai_resource`)
Resources include educational materials such as:
- Articles and blog posts
- Research papers and whitepapers
- Videos and webinars
- Toolkits and frameworks
- Reports and publications
Organized by resource type and topic taxonomies, with featured resource capability.

### Case Studies (`case_study`)
Real-world examples of responsible AI implementation across different industries. Includes industry taxonomy for filtering and organization.

## Theme Functionality

### Contact Form
- AJAX-powered contact form submission
- Server-side validation
- Honeypot spam protection
- Email notifications via wp_mail
- Custom email templates in `inc/email-templates.php`

### SEO & Schema
- Schema.org structured data for events, articles, and organization
- SEO meta fields via ACF
- Breadcrumb navigation
- Social sharing integration

### Admin Customizations
- Custom dashboard widgets
- Enhanced admin list views with ACF field columns
- Sortable custom columns for events
- Font Awesome icons in admin for visual feedback
