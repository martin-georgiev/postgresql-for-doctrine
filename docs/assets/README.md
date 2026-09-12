# Project mark

The elephant head carries both parents: PostgreSQL blue (`#336791`) and the
elePHPant's purple (`#777BB4`). The chevron it reaches toward is Doctrine
orange (`#FC6A31`) — the animal produces the `->`, rather than standing beside it.

| File | Use |
|------|-----|
| `logo.svg` | Light grounds. Keyline `#141A22`, separator `#FFFFFF`. |
| `logo-dark.svg` | Dark grounds. Keyline `#EDF1F6`, separator `#11161D`. |
| `logo-{16,32,48}.png` | Favicons and small UI chrome. |
| `logo-{64,128}.png` | Inline documentation headers. |
| `logo-180.png` | `apple-touch-icon`. |
| `logo-{256,512}.png` | Package listings, social previews, presentations. |

Prefer the SVG wherever the consumer renders it: it is the source of truth and
stays crisp at any size. The PNGs are rasterised from `logo.svg` at 1x for
consumers that cannot take SVG.

Markdown that follows the reader's colour scheme:

```markdown
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg">
  <img src="assets/logo.svg" alt="" width="64" height="64">
</picture>
```
