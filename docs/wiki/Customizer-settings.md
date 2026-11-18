<!-- cspell:ignore Customizer -->

Written against v1.0.1.

The theme adds three settings to the Customizer (**Appearance**, **Customize**). Each is stored as a theme modification under the id below, so it belongs to this theme and is kept if another theme is activated and this one comes back. The code is in [`lib/customize.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/lib/customize.php) and the CSS it produces in [`lib/output.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/lib/output.php).

| Label | Section | Id | Type | Default | Validation |
| --- | --- | --- | --- | --- | --- |
| Text Color | Colors | `brief_text_color` | color | `#333` | a hex color (`sanitize_hex_color`) |
| Link Color | Colors | `brief_link_color` | color | `#fff385` | a hex color (`sanitize_hex_color`) |
| Logo Width | Site Identity | `brief_logo_width` | number, pixels | `350` | a whole number, `100` or more |

## Text Color

Body text, headings, links, menu links, the site title, form fields and the menu buttons, and the background of buttons (including WPForms and Ninja Forms submit buttons) and of a header menu item with the `menu-highlight` class. It is also the editor palette's **Text color** (`theme-primary`).

## Link Color

The underline highlight on links in the content and widgets, the post meta, captions, the footer, and the hovered or current menu item. It is also the editor palette's **Link color** (`theme-secondary`).

## Logo Width

The largest width the logo is shown at. Only takes effect when a logo is set under **Site Identity**. An empty value or one that is not a number is refused with *You must supply a valid number.*; a value under 100 with *The logo width cannot be less than 100.* See [Changing the colors and the logo](Changing-the-colors-and-the-logo.md#what-the-logo-width-changes) for the layout changes at larger widths.

## Example

A site with dark blue text and a pale blue highlight, a logo up to 400 pixels wide:

| Setting | Value |
| --- | --- |
| Text Color | `#1a2b4c` |
| Link Color | `#cfe3ff` |
| Logo Width | `400` |
