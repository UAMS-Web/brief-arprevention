Written against v1.0.1.

- [The theme cannot be activated: The parent theme is missing](#the-theme-cannot-be-activated-the-parent-theme-is-missing)
- [A Gravity form has no submit button](#a-gravity-form-has-no-submit-button)
- [An editor cannot find Customize, Widgets or Themes](#an-editor-cannot-find-customize-widgets-or-themes)
- [Footer menu items are missing](#footer-menu-items-are-missing)
- [A notice asks to install Genesis Connect for WooCommerce](#a-notice-asks-to-install-genesis-connect-for-woocommerce)
- [A page shows no header, menu or footer](#a-page-shows-no-header-menu-or-footer)

## The theme cannot be activated: The parent theme is missing

**Cause:** the theme is a Genesis child theme, and Genesis is not in `wp-content/themes/`.

**Fix:** install the Genesis theme beside this one, then activate this theme. See [Installation (README)](https://github.com/UAMS-Web/brief-arprevention#readme).

## A Gravity form has no submit button

**Cause:** the theme removes the submit button from the Gravity Forms forms with ids `1` and `2` (`gform_submit_button_1` and `gform_submit_button_2` in [`functions.php`](https://github.com/UAMS-Web/brief-arprevention/blob/main/functions.php)). A new form that takes one of those ids loses its button too.

**Fix:** use a form with another id, or change the theme through a pull request if those forms need a button.

## An editor cannot find Customize, Widgets or Themes

**Cause:** the theme hides those pages from the Editor role and leaves **Menus**. See [Theme features](Theme-features.md#editor-access-to-menus).

**Fix:** an administrator makes those changes.

## Footer menu items are missing

**Cause:** the Footer Menu shows one level only; submenu items are not shown.

**Fix:** move the items to the top level of the menu.

## A notice asks to install Genesis Connect for WooCommerce

**Cause:** WooCommerce is active and the Genesis Connect for WooCommerce plugin is not, so WooCommerce pages do not use the Genesis layout.

**Fix:** install and activate Genesis Connect for WooCommerce, or dismiss the notice. It returns after the theme is switched, or after WooCommerce or Genesis Connect for WooCommerce is deactivated.

## A page shows no header, menu or footer

**Cause:** the page uses the **Landing** template.

**Fix:** choose the default template, or **Blocks** to keep the header and footer without the page title. See [Page templates](Page-templates.md).
