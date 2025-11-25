# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MyDefenseLaw WordPress is a Dockerized WordPress development environment for a law firm website. It includes a custom theme with ACF Pro integration, custom post types, and a contact form system.

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
├── themes/mydefenselaw/     # Custom theme
│   ├── acf-json/            # ACF field group JSON (version controlled)
│   ├── assets/css/          # Stylesheets
│   ├── assets/js/           # JavaScript
│   ├── inc/                 # PHP includes (modular functionality)
│   └── template-parts/      # Reusable template partials
│       ├── sections/        # Homepage sections
│       └── global/          # Site-wide components
└── plugins/
    └── advanced-custom-fields-pro/  # ACF Pro (version controlled)
```

### Theme Architecture (mydefenselaw)

**Functions.php Structure** - Uses modular includes pattern. Main functions.php contains:
- Theme setup and configuration
- Navigation menu registration
- Widget area registration
- Script/style enqueueing
- ACF Options Pages registration

**Modular PHP Includes** (`inc/` directory) - All loaded via `require_once` in functions.php:
- `custom-post-types.php` - CPT registration for practice_area, attorney, testimonial, case_result
- `enqueue.php` - Additional asset enqueueing logic
- `customizer.php` - WordPress Customizer settings
- `template-tags.php` - Helper functions for templates
- `contact-form-handler.php` - AJAX contact form processing (wp_ajax action handlers)
- `acf-sync-helper.php` - ACF field synchronization utilities

**Custom Post Types** (defined in `inc/custom-post-types.php`):
- `practice_area` - Legal practice areas (public, has archive at /practice-areas/)
- `attorney` - Attorney profiles (public, has archive at /attorneys/)
- `testimonial` - Client testimonials (admin-only, no public pages)
- `case_result` - Case results/outcomes (admin-only, no public pages)

Note: Attorney CPT has Gutenberg editor disabled (`'show_in_rest' => false`, `'supports' => ['title']`)

**ACF Field Groups** (in `acf-json/`) - Auto-synced from database to version control:
- `group_homepage_hero.json` - Homepage hero section fields
- `group_homepage_practice_areas.json` - Homepage practice areas section
- `group_homepage_attorneys.json` - Homepage attorneys section
- `group_homepage_results.json` - Homepage results display
- `group_homepage_sections_control.json` - Homepage section visibility toggles
- `group_attorney_cpt.json` - Attorney post type fields (photo, bio, credentials, etc.)
- `group_practice_area_cpt.json` - Practice area post type fields
- `group_theme_options.json` - Global theme settings (Options Page)

**Theme Settings** (ACF Options Pages - registered in functions.php):
- Theme Settings (main menu) - menu_slug: 'theme-settings'
- Contact Info (submenu) - parent_slug: 'theme-settings'
- Social Media (submenu) - parent_slug: 'theme-settings'

**Template Parts** (`template-parts/`):
- `sections/` - Homepage section templates:
  - `hero.php` - Homepage hero banner
  - `practice-areas.php` - Practice areas grid
  - `attorneys.php` - Meet the attorneys section
  - `why-choose-us.php` - Why choose section with attorney sidebar
  - `contact.php` - Contact form section
- `global/` - Site-wide components:
  - `top-bar.php` - Top bar with contact info
  - `mobile-sticky-bar.php` - Mobile call-to-action bar
  - `consultation-modal.php` - Consultation request modal

**Homepage Template** (`front-page.php`) - ACF-driven modular sections with visibility controls:
- Each section can be toggled on/off via ACF field (`enable_hero`, `enable_practice_areas`, etc.)
- Sections loaded via `get_template_part()` from `template-parts/sections/`
- Homepage uses Classic Editor (Gutenberg disabled for front page only)

**AJAX Implementation**:
- Action: `mydefenselaw_contact_form` (both logged-in and logged-out via wp_ajax and wp_ajax_nopriv)
- Nonce: `mydefenselaw_contact` (created in wp_localize_script)
- Handler: `inc/contact-form-handler.php`
- Includes honeypot spam protection (website_url field)
- Sends email via wp_mail (captured by Mailhog in dev)

### Key Design Decisions

1. **Gutenberg disabled for Attorney CPT** - Uses Classic Editor mode with ACF fields only
2. **ACF JSON sync** - Field groups stored in theme for version control (acf-json/)
3. **Docker-based development** - Uses docker-compose with MariaDB 10.11, not MySQL
4. **Volume mounting** - Only `wp-content/` is mounted; WordPress core lives in Docker volume at wordpress_data
5. **Mailhog integration** - All emails captured locally at http://localhost:8025 for testing
6. **Modular architecture** - Theme uses include files and template parts for maintainability
7. **Section visibility controls** - Homepage sections individually toggleable via ACF

### Navigation Menus
- `primary` - Main navigation
- `footer-legal` - Footer legal services links
- `footer-about` - Footer about section links
- `footer-contact` - Footer contact links

### Widget Areas
- `sidebar-practice-areas` - Practice areas page sidebar
- `footer-widgets` - Footer widget area

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
- Theme version constant: `MYDEFENSELAW_VERSION` defined in functions.php
- Theme uses jQuery (enqueued as dependency)
- Font Awesome 6.4.0 loaded from CDN
- Google Fonts: Merriweather + Open Sans
- AJAX endpoints use nonce `mydefenselaw_contact`
- Debug log location: `wp-content/debug.log` (when WORDPRESS_DEBUG=1)
- WP-CLI runs in separate container with profile 'cli' (user 33:33)
- Never save working files to root folder; use appropriate subdirectories

## Docker Services

- **wordpress** - WordPress container (port 8088), custom Dockerfile in docker/wordpress/
- **db** - MariaDB 10.11 (port 3307)
- **phpmyadmin** - Database management UI (port 8089)
- **mailhog** - Email testing (SMTP 1025, Web UI 8025)
- **wp-cli** - WP-CLI utilities (profile: cli, runs on-demand only)
