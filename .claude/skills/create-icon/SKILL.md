---
name: create-icon
description: Use when creating new map marker icons for the Art Routes plugin - generates consistent SVG icons that match the existing style
---

# Create Map Marker Icon

## Overview

This skill creates SVG icons for the Art Routes plugin. All icons follow a consistent style: circular background with white symbol, 32x32 viewBox, optimized for map markers.

## Icon Style Guide

**Specifications:**
- ViewBox: `0 0 32 32`
- Background: Colored circle with `r="14"` centered at `cx="16" cy="16"`
- Symbol: White (`fill="white"` or `stroke="white"`)
- Stroke width: 2px for outlines, 2.5-3px for bold elements
- Format: Clean SVG, no unnecessary attributes

**Color Palette (Tailwind-inspired):**
| Color | Hex | Use for |
|-------|-----|---------|
| Green | `#22c55e` | Start, nature, parks |
| Red | `#ef4444` | End, stops, restaurants |
| Blue | `#3b82f6` | Information, water |
| Purple | `#8b5cf6` | Art, culture |
| Pink | `#ec4899` | Music, entertainment |
| Orange | `#f97316` | Theater, performance |
| Cyan | `#06b6d4` | Poetry, literature |
| Brown | `#854d0e` | Café, coffee |
| Amber | `#f59e0b` | Houses, venues |
| Teal | `#0d9488` | Transport |
| Gray | `#64748b` | Utilities (toilet, etc.) |
| Indigo | `#6366f1` | Photo, viewpoint |
| Dark gray | `#374151` | Numbered markers |

## Template

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
  <circle cx="16" cy="16" r="14" fill="#COLOR"/>
  <!-- Symbol elements here in white -->
</svg>
```

## Process

1. **Understand the request** - What concept should the icon represent?
2. **Choose a color** - Pick from the palette based on the category
3. **Design the symbol** - Keep it simple, recognizable at 24-32px
4. **Write the SVG** - Use the template, add symbol elements
5. **Save the file** - `assets/icons/{name}.svg` (lowercase, hyphens)

## Examples

**Simple shape (info icon):**
```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
  <circle cx="16" cy="16" r="14" fill="#3b82f6"/>
  <circle cx="16" cy="10" r="2" fill="white"/>
  <rect x="14" y="14" width="4" height="10" rx="1" fill="white"/>
</svg>
```

**Path-based (music icon):**
```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
  <circle cx="16" cy="16" r="14" fill="#ec4899"/>
  <path d="M12 22 L12 11 L22 9 L22 20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"/>
  <circle cx="12" cy="22" r="3" fill="white"/>
  <circle cx="22" cy="20" r="3" fill="white"/>
</svg>
```

**Numbered marker:**
```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
  <circle cx="16" cy="16" r="14" fill="#374151"/>
  <text x="16" y="22" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-weight="bold" font-size="16">1</text>
</svg>
```
(Use font-size="13" for 2-digit numbers)

## Existing Icons

Check `assets/icons/` for existing icons before creating duplicates:

**Cultural:** art, sculpture, music, theater, poetry, cafe, restaurant, house, park, festival, museum, church
**Utility:** start, end, info, parking, toilet, wheelchair, bus, train, bike, photo, viewpoint, marker
**Numbers:** number-1 through number-20

## Testing

After creating an icon:
1. View it in a browser at actual size (32x32px) to verify legibility
2. Check it appears in the plugin's icon selector dropdown
3. Test on a map to ensure it displays correctly

## Notes

- Keep symbols simple - they must be recognizable at small sizes
- Use filled shapes rather than thin strokes where possible
- The white symbol should have good contrast against the colored background
- Avoid text except for numbered markers
- Legacy "WB plattegrond-" icons are in `assets/icons/legacy/` for reference
