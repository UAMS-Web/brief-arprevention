Written against v1.0.1.

A page normally shows the site header, its title, the content and the site footer. Two templates change that: choose one under **Template** in the page's settings in the block editor.

## Steps

1. Open the page in the editor.
2. In the page settings sidebar, find **Template**.
3. Choose:
   - **Blocks** for a page built from blocks that supplies its own heading. The page keeps the site header and footer, loses its title and breadcrumbs, and is always full width.
   - **Landing** for a page that should stand alone, such as a campaign page. The page loses the site header, the menus, the site footer (but not the footer widget area) and the skip links, and gets the `landing-page` body class.
4. Update the page and view it.

**Note:** a Landing page has no menu and no skip links, so the page itself must give the reader every link they need.

## Reference

- [Page templates](Page-templates.md): exactly what each template removes.
