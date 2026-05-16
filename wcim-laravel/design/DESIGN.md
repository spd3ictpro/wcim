---
name: Clinical Precision
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#424750'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#727781'
  outline-variant: '#c2c6d1'
  surface-tint: '#27609d'
  primary: '#003461'
  on-primary: '#ffffff'
  primary-container: '#004b87'
  on-primary-container: '#8abcff'
  inverse-primary: '#a3c9ff'
  secondary: '#006b60'
  on-secondary: '#ffffff'
  secondary-container: '#7ef7e4'
  on-secondary-container: '#007166'
  tertiary: '#6e0004'
  on-tertiary: '#ffffff'
  tertiary-container: '#990007'
  on-tertiary-container: '#ffa094'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d3e4ff'
  primary-fixed-dim: '#a3c9ff'
  on-primary-fixed: '#001c38'
  on-primary-fixed-variant: '#004882'
  secondary-fixed: '#7ef7e4'
  secondary-fixed-dim: '#5fdac8'
  on-secondary-fixed: '#00201c'
  on-secondary-fixed-variant: '#005048'
  tertiary-fixed: '#ffdad5'
  tertiary-fixed-dim: '#ffb4aa'
  on-tertiary-fixed: '#410001'
  on-tertiary-fixed-variant: '#930007'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-lg:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  headline-lg-mobile:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  margin-mobile: 1rem
  margin-desktop: 2rem
  gutter: 1.5rem
  container-max: 1280px
---

## Brand & Style

The design system is engineered for the high-stakes environment of wound care management. The brand personality is **authoritative, dependable, and meticulously organized**. It seeks to evoke a sense of calm efficiency for clinicians who need to manage inventory quickly and accurately.

The visual style follows a **Corporate Modern** approach with a focus on high-utility information density. It prioritizes clarity over decoration, using ample white space to reduce cognitive load and crisp, clinical lines to suggest professional hygiene and precision.

## Colors

The palette is derived from institutional medical standards to instantly communicate trust.

*   **Primary (Deep Blue):** Used for navigation, primary actions, and brand identification. It represents stability and professional rigor.
*   **Secondary (Medical Green):** Used for success states, active inventory markers, and "in-stock" indicators.
*   **Tertiary (Emergency Red):** Reserved strictly for critical warnings, such as "out of stock" or "expired" items, ensuring high visibility in clinical settings.
*   **Neutrals:** A range of cool grays (Slate) provides a clean backdrop, while crisp white backgrounds maintain a "sterile" UI feel.

## Typography

This design system utilizes **Manrope** for headings to provide a modern, structural feel, and **Inter** for all functional UI elements and body text to ensure maximum legibility at small sizes—essential for inventory lists and data tables.

All typography maintains a high contrast ratio against backgrounds to ensure readability under the harsh lighting typical of clinical environments. Labels use a slightly increased letter spacing and semi-bold weight to distinguish metadata from content.

## Layout & Spacing

The layout uses a **fluid grid system** that adapts to the varied devices used in healthcare settings, from handheld tablets during rounds to desktop workstations at nursing stations.

*   **Desktop:** A 12-column grid with 24px gutters. Content is housed in structured "modules" to separate different categories of inventory.
*   **Mobile/Tablet:** A flexible 4 or 8 column grid with 16px margins.
*   **Rhythm:** A 4px baseline shift is used for tight data clusters, while a 16px/24px increment is used for structural spacing between sections.

## Elevation & Depth

To maintain a clean and sterile aesthetic, the design system avoids heavy drop shadows. Instead, it utilizes **Tonal Layers and Low-Contrast Outlines**.

*   **Surface-Level 0:** Light gray (#F8FAFC) for the main application background.
*   **Surface-Level 1:** White (#FFFFFF) for cards and content containers, defined by a 1px border (#E2E8F0).
*   **Interactive Elevation:** A very subtle, highly diffused shadow (4px blur, 5% opacity) is used only to indicate that a component like a modal or a dropdown is floating above the main interface.

## Shapes

The shape language is **Soft (Level 1)**. Elements feature a 0.25rem (4px) radius. This provides a professional balance: the corners are soft enough to feel modern and accessible, but sharp enough to maintain a sense of precision, alignment, and institutional order. Large containers like cards may scale up to a 0.5rem (8px) radius to create a distinct visual container.

## Components

### Buttons
*   **Primary:** Solid Deep Blue with white text. High-contrast, used for "Save," "Order," or "Submit."
*   **Secondary:** Medical Green outline or subtle tint for "Edit" or "Add Item."
*   **Ghost:** Text-only for "Cancel" or "View Details" to maintain hierarchy.

### Inventory Cards
Cards display item names in Manrope Bold with quantity levels highlighted using colored chips. If an item is low, the chip background becomes a light tint of the Tertiary Red with dark red text.

### Data Tables
Tables are the heart of the system. They use Inter 14px for high information density. Rows should have a subtle hover state (#F1F5F9) to help clinicians track their eyes across horizontal data points.

### Status Chips
*   **In Stock:** Green tint background / Green text.
*   **Low Stock:** Amber tint background / Amber text.
*   **Expired/Critical:** Red tint background / Red text.

### Inputs
Search bars and quantity inputs use a 1px border. When focused, the border transitions to Primary Blue with a subtle 2px outer glow of the same color to indicate activity.