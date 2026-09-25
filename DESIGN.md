# Design System: Islamic Editorial Minimalist 2026

<!-- impeccable:design-schema 1 -->

## Design Direction & Philosophy

AL-HIKMAH mengadopsi estetika **Islamic Editorial Minimalist 2026**: memadukan ketenangan nuansa Islami modern, keanggunan tata letak editorial, dan kejernihan antarmuka web masa kini. Pendekatan ini menyingkirkan elemen dekoratif berlebihan (AI slop, glow neon, gradient acak) demi menghadirkan kenyamanan visual, keterbacaan tinggi, serta rasa amanah dan profesional bagi orang tua dan santri.

## Three Dials

- **ENERGY: 1 (Calm & Authoritative)**: Memberikan kesan damai, teduh, dan berwibawa khas bimbingan Al-Qur'an.
- **RHYTHM: 2 (Harmonious with Purposeful Breaks)**: Variasi tata letak yang organik sesuai hierarki konten (kartu unggulan asimetris, grid yang tidak seragam kaku).
- **MOTION: 1 (Subtle & Purposeful)**: Micro-interaction halus dan transisi transparan saat navigasi di-scroll, tanpa animasi heboh atau distracting.

## Color Palette

### Light Theme (Default)
- **Canvas / Body**: `#fafaf9` (Warm Alabaster Stone)
- **Surface / Card**: `#ffffff` (Pure White)
- **Primary Deep Emerald**: `#064e3b` (Emerald 900 - Headings & Primary actions)
- **Primary Mid Emerald**: `#047857` (Emerald 700 - Hover & Accents)
- **Primary Soft Tint**: `#ecfdf5` (Emerald 50 - Badges & Soft fills)
- **Secondary Accent**: `#92400e` / `#d97706` (Warm Ochre - Khusus penanda penting/bintang)
- **Text Primary**: `#0f172a` (Slate 900 - Kontras WCAG AAA)
- **Text Secondary**: `#334155` (Slate 700 - Keterbacaan optimal)
- **Text Muted**: `#64748b` (Slate 500)
- **Borders & Dividers**: `rgba(6, 78, 59, 0.08)` / `#e7e5e4`

### Dark Theme
- **Canvas / Body**: `#09120e` (Deep Forest Obsidian)
- **Surface / Card**: `#112019` (Elevated Emerald Slate)
- **Primary Accent**: `#10b981` (Emerald 500)
- **Primary Soft Tint**: `rgba(16, 185, 129, 0.12)`
- **Text Primary**: `#f8fafc` (Slate 50)
- **Text Secondary**: `#cbd5e1` (Slate 300)
- **Text Muted**: `#94a3b8` (Slate 400)
- **Borders & Dividers**: `rgba(255, 255, 255, 0.09)`

## Typography & Hierarchy

- **Font Family**: `'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
- **Headings**:
  - H1: `2.5rem` to `3.25rem` (clamp), letter-spacing `-0.03em`, weight 700, line-height 1.15
  - H2: `2.0rem` to `2.5rem`, letter-spacing `-0.025em`, weight 700
  - H3: `1.5rem` to `1.75rem`, letter-spacing `-0.02em`, weight 600
- **Body**: `1rem` to `1.05rem`, line-height 1.65 to 1.7, weight 400
- **Micro-copy & Badges**: `0.75rem` to `0.85rem`, weight 500, letter-spacing `0.02em`

## Component Standards (Craft Floor & Antislop Compliance)

1. **Navbar**:
   - Header ultra-ramping (`h: 68px` normal, `60px` scrolled).
   - Glassmorphism presisi (`backdrop-filter: blur(12px)` dengan background transparan halus).
   - Menu links dengan underline hover indicator halus (tidak kaku).
   - Mobile drawer rapi dan nyaman disentuh (min tap target 44px).
2. **Buttons & CTAs**:
   - Primary: Deep Emerald solid dengan hover elevasi mikro (`translateY(-1px)`).
   - Secondary: Outline halus dengan border netral dan hover emerald tint.
   - Text CTA spesifik (contoh: "Coba Sesi Gratis (15 Menit)", "Konsultasi via WhatsApp"), bukan teks generik.
3. **Cards & Badges**:
   - Radius modern: `12px` to `16px` (tidak bulat pil berlebihan untuk container).
   - Border hairline tipis `1px solid var(--border-color)`.
   - Shadow ultra-subtle ambient (`0 4px 20px -2px rgba(6,78,59,0.04)`), bukan drop-shadow tebal.
4. **Copywriting & Quality Gates**:
   - Tanpa karakter em dash (`—`), digantikan tanda koma, titik dua, atau kurung.
   - Semua data biaya dan program bersumber dari sistem nyata.
   - Kontras warna lulus standar WCAG AA (rasio minimal 4.5:1 untuk teks normal).
