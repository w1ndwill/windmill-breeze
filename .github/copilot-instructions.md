# Windmill Breeze Theme - AI Coding Instructions

## 1. Project Overview
**Windmill Breeze** is a WordPress portfolio theme featuring a dynamic weather system and CSS-animated windmill.
- **Type**: WordPress Theme (PHP, CSS, JS).
- **Core Concept**: "Weather-aware" UI (Sunny/Cloudy/Rainy) affecting animations and visuals.
- **Design**: Clean, card-based, dark-mode compatible, mobile-responsive.

## 2. Architecture & File Structure
- **Root**: `windmill-breeze/` contains all theme files.
- **Templates**:
  - `front-page.php`: Custom homepage. Logic prioritizes logged-in user info, falls back to Admin (ID 1).
  - `archive-portfolio.php` / `single-portfolio.php`: Portfolio Custom Post Type templates.
  - `header.php` / `footer.php`: Global partials. Footer likely contains the Windmill HTML/CSS structure.
- **Assets**:
  - `style.css`: Main stylesheet & Theme Metadata. **Source of truth for CSS Variables**.
  - `assets/js/script.js`: Handles weather logic, windmill animation speed, and AJAX auth.
  - `assets/css/`: Specialized styles (`mobile.css`, `comments.css`).
- **Logic**:
  - `functions.php`: Theme setup, enqueuing, AJAX handlers (`windmill_ajax_login`, `windmill_ajax_register`), and Customizer settings.

## 3. Key Development Patterns

### Styling & Theming
- **CSS Variables**: ALWAYS use variables defined in `style.css` (`:root`) for colors.
  - Example: `var(--primary-color)`, `var(--bg-color)`.
- **Dark Mode**: Implemented via `[data-theme="dark"]` selector overriding variables.
  - Do not hardcode dark colors; update the variable overrides in `style.css` instead.
- **Responsive**: Mobile styles are separated in `assets/css/mobile.css` but check `style.css` for base responsive rules.

### JavaScript & Interactivity
- **AJAX**: Use the global `windmill_vars` object.
  - URL: `windmill_vars.ajax_url`
  - Nonce: `windmill_vars.nonce`
  - Auth Check: `windmill_vars.is_logged_in`
- **Weather System**:
  - Logic resides in `assets/js/script.js`.
  - UI changes likely triggered by body classes or data attributes (check implementation).
  - Windmill speed is dynamic based on "wind speed" data.

### PHP & WordPress
- **User Data**: In `front-page.php`, use `$display_user_id` logic to toggle between "Owner Mode" (Admin) and "Preview Mode" (Logged-in User).
- **Asset Versioning**: `functions.php` uses `filemtime()` for cache busting. Maintain this pattern when adding new assets.
- **Social Links**: Retrieved via `get_theme_mod('social_key')`.

## 4. Critical Workflows
- **No Build Step**: This is a raw PHP/CSS/JS theme. Changes apply immediately on refresh.
- **Debugging**:
  - PHP: Use `error_log()` or `WP_DEBUG`.
  - AJAX: Check Network tab for `admin-ajax.php` responses.
- **Design Reference**: `design-draft.html` in the parent directory serves as the static prototype.

## 5. Common Tasks
- **Adding a Social Icon**:
  1. Register setting in `functions.php` (Customizer API).
  2. Output in `front-page.php` loop.
- **Modifying Weather Effects**:
  1. Update CSS animations in `style.css`.
  2. Adjust trigger logic in `assets/js/script.js`.
