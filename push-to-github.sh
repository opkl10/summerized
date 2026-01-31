#!/bin/bash

# סקריפט לדחיפה ל-GitHub

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

echo "🔄 דוחף שינויים ל-GitHub..."
echo ""

# בדוק אם יש שינויים
if [ -z "$(git status --porcelain)" ]; then
    echo "⚠️  אין שינויים לדחיפה"
    exit 0
fi

# הוסף קבצים
echo "📝 מוסיף קבצים..."
git add .

# Commit
echo "💾 יוצר commit..."
git commit -m "Update to v1.2.0 - Add icon toggle and inline button positions"

# Push
echo "⬆️  מעלה ל-GitHub..."
git push origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ הושלם!"
    echo ""
    echo "📋 מה קורה עכשיו:"
    echo "1. GitHub Actions ייצור Release v1.2.0 אוטומטית (תוך 1-2 דקות)"
    echo "2. לך ל-GitHub → Actions כדי לראות את ה-progress"
    echo "3. לך ל-GitHub → Releases כדי לראות את ה-Release החדש"
    echo ""
    echo "🔍 בדוק:"
    echo "   - GitHub → https://github.com/opkl10/summerized → Actions"
    echo "   - GitHub → https://github.com/opkl10/summerized → Releases"
    echo ""
else
    echo ""
    echo "❌ שגיאה! בדוק את ה-logs למעלה"
    exit 1
fi
