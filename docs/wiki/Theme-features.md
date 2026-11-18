<!-- cspell:ignore Customizer Dashicons -->

Written against v1.0.1.

What the theme registers, and what it changes in Genesis, the block editor, WooCommerce and the WordPress admin. Most of it is set in [`functions.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/functions.php) and the files under [`config/`](https://github.com/UAMS-Web/brief-arprevention/tree/main/config).

## Requirements

| | |
| --- | --- |
| Parent theme | Genesis (`Template: genesis`), 3.0 or later for the responsive menus |
| PHP | 8.2 or later |
| Text domain | `brief` |

## Menus

| Location | Label | Shown | Depth |
| --- | --- | --- | --- |
| `primary` | Header Menu | in the site header | any |
| `secondary` | Footer Menu | in the site footer | one level |

Below 960 pixels the header menu collapses behind a menu button (Genesis responsive menus).

## Widget areas

- **Footer** widget area: one.
- **After entry** widget area, shown after single posts.
- **Primary sidebar**, for the layouts that have one.
- Removed: **Header Right** and the **Secondary sidebar**.

## Layouts

Full width content (the default), content and sidebar, and sidebar and content. The three-column layouts are removed.

## Genesis settings on activation

Genesis applies these when the theme is activated, from [`config/child-theme-settings.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/config/child-theme-settings.php). They can be changed afterward under **Genesis**, **Theme Settings**.

| Setting | Value |
| --- | --- |
| Site layout | full width content |
| Posts per page | 4 |
| Content archives | full content, limited to 220 characters |
| Featured image in archives | off (size medium, aligned left when on) |
| Entry meta before content | `[post_date]` |
| Entry meta after content | `[post_categories before=""] [post_tags before=""]` |
| Post navigation | numeric |
| Breadcrumbs | off everywhere |
| Posts in the blog page category list | 4 |

## Entries

- The post info (date) is shown above the title, and the post meta (categories and tags) just below it.
- The edit link is removed from entries.
- A content limit ends with *... Continue Reading*.
- Gravatars are 90 pixels in the author box and 60 pixels in comments.

## Image sizes

| Name | Size | Cropped |
| --- | --- | --- |
| `sidebar-featured` | 75 by 75 | yes |
| `genesis-singular-images` | 702 by 526 | yes |

## Block editor

- Wide and full alignments, responsive embeds and editor styles are on.
- Content width is 1062 pixels; the `brief_content_width` filter changes it.
- Color palette: **Text color** (`theme-primary`) and **Link color** (`theme-secondary`), following the [Customizer settings](Customizer-settings.md).
- Font sizes: Small 14.4, Normal 18, Large 20, Larger 24 (pixels).
- Body classes on single entries: `has-no-blocks` when the content has no blocks; `first-block-<block name>` and `first-block-align-<alignment>` from the first block, for styling such as removing the space above a Cover block.

## Fonts and styles

- Lora and Open Sans are loaded from Google Fonts.
- Dashicons are loaded on the front end.
- An AMP stylesheet is added on AMP pages.

## Forms

- **Gravity Forms:** the submit button is removed from the forms with ids `1` and `2`. See [Troubleshooting](Troubleshooting.md#a-gravity-form-has-no-submit-button).
- WPForms and Ninja Forms buttons take the theme's text color.

## Editor access to menus

Users with the Editor role are given the `edit_theme_options` capability the first time they open an admin page, so they can open **Appearance**, **Menus**. The capability also opens every other screen that requires it, such as **Genesis**, **Theme Settings**; only the Themes, Widgets and Customize pages are hidden from them. Hiding a page does not block it: an editor who goes to its address can still open it.

**Note:** the capability is added to the Editor role itself and stays on the role if the theme is switched.

## Simple Social Icons

When the Simple Social Icons plugin is active, its defaults are: centered, 40-pixel icons in `#333333` (`#666666` on hover) on white, with no border or radius.

## WooCommerce

- With WooCommerce active and Genesis Connect for WooCommerce missing, users who can manage WooCommerce see a dismissible notice asking them to install it. A dismissed notice returns when the theme is switched, or when WooCommerce or Genesis Connect for WooCommerce is deactivated.
- 8 products per page with Genesis Connect for WooCommerce; the small-screen breakpoint is 860 pixels (1200 with a sidebar).
- When the theme is activated from **Appearance**, **Themes** while WooCommerce is active, and when WooCommerce is activated, the single product image is set to 655 pixels wide and catalog images to 500 pixels, cropped 1:1. These are WooCommerce settings and can be changed afterward.
- Gallery thumbnails are 180 by 180, cropped.
- The *Description* heading on the product tab is removed.

## Demo content

[`xml/brief-demo-content.xml`](https://github.com/UAMS-Web/brief-arprevention/blob/main/xml/brief-demo-content.xml) can be imported with **Tools**, **Import**, **WordPress** to see the theme with sample posts and pages.
