#!/usr/bin/env bash
# lint-docs.sh - Skrip Audit Kebersihan Dokumentasi (Anti-AI Slop)

DOC_FILE="tentang.md"

if [ ! -f "$DOC_FILE" ]; then
    echo "Berkas $DOC_FILE tidak ditemukan!"
    exit 1
fi

echo "====================================================="
echo "   AUDIT HYGIENE DOKUMENTASI (ANTI-AI SLOP) - $DOC_FILE"
echo "====================================================="

echo ""
echo "1. [PELANGGARAN] Deteksi Karakter Em-Dash (—):"
EM_DASH_COUNT=$(grep -c "—" "$DOC_FILE" || true)
if [ "$EM_DASH_COUNT" -gt 0 ]; then
    echo "⚠️  Ditemukan $EM_DASH_COUNT baris dengan em-dash. Contoh:"
    grep -n "—" "$DOC_FILE" | head -10
else
    echo "✅ Nol em-dash terdeteksi."
fi

echo ""
echo "2. [PELANGGARAN] Deteksi Buzzwords AI Slop:"
BUZZWORDS="transformative|game-changer|seamless|elevate|delve|testament|groundbreaking|revolutionize|unparalleled"
BUZZWORD_COUNT=$(grep -Eci "$BUZZWORDS" "$DOC_FILE" || true)
if [ "$BUZZWORD_COUNT" -gt 0 ]; then
    echo "⚠️  Ditemukan $BUZZWORD_COUNT baris dengan buzzwords. Contoh:"
    grep -Eni "$BUZZWORDS" "$DOC_FILE" | head -10
else
    echo "✅ Nol buzzwords terdeteksi."
fi

echo ""
echo "3. [PELANGGARAN] Deteksi Negative Parallelism (Bukan X, tapi Y):"
PARALLELISM="bukan sekadar|bukan hanya|tidak hanya"
PARALLEL_COUNT=$(grep -Eci "$PARALLELISM" "$DOC_FILE" || true)
if [ "$PARALLEL_COUNT" -gt 0 ]; then
    echo "⚠️  Ditemukan $PARALLEL_COUNT baris dengan negative parallelism. Contoh:"
    grep -Eni "$PARALLELISM" "$DOC_FILE" | head -10
else
    echo "✅ Nol negative parallelism terdeteksi."
fi

echo ""
echo "====================================================="
TOTAL_VIOLATIONS=$((EM_DASH_COUNT + BUZZWORD_COUNT + PARALLEL_COUNT))
if [ "$TOTAL_VIOLATIONS" -eq 0 ]; then
    echo "🎉 HASIL: DOKUMENTASI BERSIH (0 Pelanggaran Slop)"
    exit 0
else
    echo "⚠️  HASIL: Ditemukan total $TOTAL_VIOLATIONS indikasi slop. Perlu pembersihan manual/otomatis."
    exit 1
fi
