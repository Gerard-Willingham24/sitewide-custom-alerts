# Site Alert Banner

A WordPress plugin that displays customizable alert banners across your entire website. Perfect for announcements, maintenance notices, promotions, or important updates that need to be visible on every page.

## Features

- **Sitewide Display**: Alert appears on every page of your website
- **Visual Editor**: Rich text editor with formatting options, links, and media support
- **Multiple Alert Types**: Info (blue), Success (green), Warning (orange), and Error (red) color schemes
- **Flexible Positioning**: Display alerts at the top or bottom of pages
- **Dismissible Option**: Allow visitors to close alerts (remembers their choice)
- **Width Control**: Full-width or contained layout options
- **Easy Management**: Single settings page to control all aspects

## Installation

1. Upload the `sitewide-custom-alerts` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Settings > Site Alert** to configure your alert

## Usage

### Basic Setup

1. Go to **Settings > Site Alert** in your WordPress admin
2. Check "Display alert on site" to enable the alert
3. Choose an alert type (Info, Success, Warning, or Error)
4. Enter your message using the visual editor
5. Select position (top or bottom of page)
6. Configure width and dismissible options
7. Click "Save Settings"

### Configuration Options

#### Alert Type
- **Info (Blue)**: General information or announcements
- **Success (Green)**: Positive messages or confirmations
- **Warning (Orange)**: Important notices or cautions
- **Error (Red)**: Critical alerts or urgent messages

#### Position
- **Top of page**: Alert appears at the very top
- **Bottom of page**: Alert appears at the bottom

#### Width Options
- **Full width**: Alert spans edge-to-edge across the page
- **Container width**: Alert is contained within a 1200px max-width, centered

#### Dismissible Feature
When enabled, visitors can close the alert by clicking the × button. The alert won't show again until:
- They clear their browser data, or
- You update the alert content

## File Structure

```
sitewide-custom-alerts/
├── sitewide-custom-alerts.php    # Main plugin file
├── assets/
│   ├── css/
│   │   ├── admin.css             # Admin styling
│   │   └── frontend.css          # Frontend alert styling
│   └── js/
│       ├── admin.js              # Admin functionality
│       └── frontend.js           # Frontend alert behavior
└── README.md                     # This file
```

## Requirements

- WordPress 4.0 or higher
- PHP 5.6 or higher

## Security Features

- Input sanitization using `wp_kses_post()`
- Capability checks for admin access
- Nonce verification for settings
- Escaped output for all user data

## Customization

The plugin includes CSS and JavaScript files that can be customized:

- **Frontend styling**: `assets/css/frontend.css`
- **Admin styling**: `assets/css/admin.css`
- **Frontend behavior**: `assets/js/frontend.js`
- **Admin functionality**: `assets/js/admin.js`

## License

GPL v2 or later

## Author

Gerard Willingham

## Changelog

### Version 1.0.0
- Initial release
- Sitewide alert banner functionality
- Admin settings page
- Multiple alert types and positioning options
- Dismissible alerts with content hash tracking
- Rich text editor support