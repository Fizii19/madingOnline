---
name: MonoSoft Digital Bulletin
colors:
  surface: '#f9f9f9'
  surface-dim: '#dadada'
  surface-bright: '#f9f9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f3'
  surface-container: '#eeeeee'
  surface-container-high: '#e8e8e8'
  surface-container-highest: '#e2e2e2'
  on-surface: '#1b1b1b'
  on-surface-variant: '#4c4546'
  inverse-surface: '#303030'
  inverse-on-surface: '#f1f1f1'
  outline: '#7e7576'
  outline-variant: '#cfc4c5'
  surface-tint: '#5e5e5e'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#1b1b1b'
  on-primary-container: '#848484'
  inverse-primary: '#c6c6c6'
  secondary: '#5d5e61'
  on-secondary: '#ffffff'
  secondary-container: '#dfdfe2'
  on-secondary-container: '#616365'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#1b1b1b'
  on-tertiary-container: '#848484'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e2e2e2'
  primary-fixed-dim: '#c6c6c6'
  on-primary-fixed: '#1b1b1b'
  on-primary-fixed-variant: '#474747'
  secondary-fixed: '#e2e2e5'
  secondary-fixed-dim: '#c6c6c9'
  on-secondary-fixed: '#1a1c1e'
  on-secondary-fixed-variant: '#454749'
  tertiary-fixed: '#e2e2e2'
  tertiary-fixed-dim: '#c6c6c6'
  on-tertiary-fixed: '#1b1b1b'
  on-tertiary-fixed-variant: '#474747'
  background: '#f9f9f9'
  on-background: '#1b1b1b'
  surface-variant: '#e2e2e2'
typography:
  display:
    fontFamily: Manrope
    fontSize: 48px
    fontWeight: '800'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.3'
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-caps:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1.0'
    letterSpacing: 0.1em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-padding: 40px
  gutter: 24px
  card-padding: 24px
  stack-gap: 16px
---

## Brand & Style
The design system focuses on a refined Neumorphic (Soft UI) aesthetic that bridges the gap between digital interfaces and physical tactile surfaces. The brand personality is organized, approachable, and serene, mimicking a clean, high-end physical bulletin board. 

The primary design movement is **Neomorphism**, characterized by extruded surfaces that emerge from the background using dual-light and dark shadows rather than traditional borders or flat layers. This creates a "squishy" or "molded" feel. To ensure the interface remains professional and accessible, we utilize high-contrast typography (Solid Black) against the monochromatic base, using the four pastel accents sparingly for categorization and status indicators.

## Colors
This design system utilizes a monochromatic foundation with high-chroma pastel accents. 
- **Base Surface**: All backgrounds and component surfaces use `#F0F0F3`. Neumorphism fails if the element color differs from the background; they must be identical to create the "extrusion" illusion.
- **Contrast**: Text and critical icons use pure Black (`#000000`) to ensure AAA accessibility against the light grey base.
- **Accents**: Soft Blue, Green, Yellow, and Purple are reserved for "pinned" categories, tags, or active toggle states. These should appear as inset (sunken) shapes or small vibrant markers on the extruded cards.

## Typography
We use **Manrope** for its modern, geometric construction that complements the rounded corners of the UI. It provides excellent legibility in a Neumorphic environment where visual clutter is minimized. 

**JetBrains Mono** is used for labels, metadata (dates, categories, board IDs), and secondary "system" information to provide a slight technical contrast to the soft organic shapes of the cards. All body text should maintain a high contrast ratio against the background, avoiding the common Neumorphic pitfall of grey-on-grey text.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy to simulate the structured nature of a bulletin board. 
- **Grid**: A 12-column system with a maximum width of 1440px. 
- **Breathing Room**: Large gutters (24px) and significant margins (40px) are required to allow the shadows of the Neumorphic elements to "breathe" without overlapping and creating muddy visuals.
- **Rhythm**: All spacing is based on an 8px scale. Components should never be crowded; the shadows require clear space to define the depth of the extrusion.

## Elevation & Depth
Depth is the core of this design system. It is achieved through two specific shadow values:
1.  **Outer Extrusion (Raised)**: Applied to cards, buttons, and input containers. Use a light shadow (`#FFFFFF`) at -8px -8px and a dark shadow (`#AEAEC0` at 50% opacity) at 8px 8px.
2.  **Inner Inset (Sunken)**: Applied to active buttons, checked boxes, or text input fields when focused. Use a light inner-shadow at 8px 8px and a dark inner-shadow at -8px -8px.

Avoid using standard drop shadows or borders. The transition between "raised" and "sunken" states provides the primary interactive feedback.

## Shapes
All interactive elements use **Rounded** corners (0.5rem base). 
- Large containers (Bulletin Cards) should use `rounded-xl` (1.5rem) to emphasize the soft, molded plastic aesthetic.
- Small elements (Chips, Icons) should use a full pill-shape to maintain the organic feel.
- Sharp corners are strictly prohibited as they break the liquid-molded illusion of Neumorphism.

## Components
- **Bulletin Cards**: Large raised surfaces. Use a subtle accent color strip at the top (Soft Blue, Green, etc.) to denote categories. Title text in Headline-MD.
- **Buttons**:
  - *Default*: Raised extrusion. Text is Black, Bold.
  - *Pressed/Active*: Inset (sunken) shadow. This state change is the only visual feedback needed.
- **Input Fields**: These should appear sunken (inset shadow) by default to indicate they are "hollow" areas ready to be filled with information.
- **Chips/Tags**: Small raised surfaces. When selected, they change to an inset shadow and the background color switches from the base grey to one of the four accent colors.
- **Checkboxes/Radios**: Small square or circular inset areas. When checked, a solid black dot or check appears, and the inset shadow becomes more pronounced.
- **Pinned Items**: For "pinned" posts, use a small 3D-effect circle in one of the accent colors at the top corner of a card, mimicking a physical push-pin.