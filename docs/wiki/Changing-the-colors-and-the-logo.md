<!-- cspell:ignore Customizer -->

Written against v1.0.1.

The theme has two colors and a logo width, all set in the Customizer by a user who can open it (an administrator; see [Theme features](Theme-features.md#editor-access-to-menus)).

## Steps

1. Go to **Appearance**, **Customize**.
2. Open **Colors**.
3. Set **Text Color**. It colors body text, headings, links, menu links, the site title and form fields, and it is the background of buttons and of a header menu item given the `menu-highlight` class. The default is `#333`.
4. Set **Link Color**. It is the highlight drawn under links in the content, in widgets, in the post meta, in the footer and on the current menu item. The default is `#fff385`.
5. Open **Site Identity** and set **Logo Width**, the largest width in pixels the logo is shown at. The default is `350`; a value under `100`, or one that is not a number, is refused with a message under the field.
6. Select **Publish**.

**Note:** pick a text color dark enough to read on the white page, and a link color light enough that dark text stays readable over its highlight. The theme does not check contrast.

The theme writes CSS for a color only when it differs from the default, so setting a color back to its default removes the theme's override. Both colors also appear in the block editor's color palette, as **Text color** and **Link color**, so a block colored with them follows a later change.

## What the logo width changes

- At **600 pixels or more**, the logo is centered and the header menu moves below it.
- A short logo is padded from the top so it lines up with the menu.

## Reference

- [Customizer settings](Customizer-settings.md): each setting's id, default and validation.
- [Theme features](Theme-features.md#block-editor): the editor color palette and font sizes.
