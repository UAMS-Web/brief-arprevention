Written against v1.0.1.

The theme provides two page templates, in [`page-templates/`](https://github.com/UAMS-Web/brief-arprevention/blob/main/page-templates). A page without one uses the Genesis default: the site header, the page title, the content, and the layout chosen for the page or the site.

| | Default | Blocks | Landing |
| --- | --- | --- | --- |
| Site header | shown | shown | removed |
| Header and footer menus | shown | shown | removed |
| Skip links | shown | shown | removed |
| Page title (entry header) | shown | removed | shown |
| Breadcrumbs | per Genesis settings | removed | per Genesis settings |
| Layout | per page or site | full width, always | per page or site |
| Site footer | shown | shown | removed (the footer widget area still shows if it has widgets) |
| Body class added | none | none | `landing-page` |

## Blocks

Template name **Blocks**, file [`page-templates/blocks.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/page-templates/blocks.php). For a page whose blocks supply their own heading, such as one that opens with a Cover block.

## Landing

Template name **Landing**, file [`page-templates/landing.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/page-templates/landing.php). For a page that stands alone. With the menus and skip links gone, every link the reader needs has to be on the page.

See [Choosing a page template](Choosing-a-page-template.md) for how to apply one.
