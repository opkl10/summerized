#!/bin/bash

# סקריפט לדחיפה ל-GitHub

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

echo "🔄 דוחף שינויים ל-GitHub..."
echo ""

# בדוק אם יש שינויים לא staged
if [ -n "$(git status --porcelain)" ]; then
    echo "📝 נמצאו שינויים לא שמורים..."
    read -p "האם להוסיף ולשמור אותם? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git add .
        read -p "הכנס הודעת commit (או Enter לברירת מחדל): " commit_message
        if [ -z "$commit_message" ]; then
            commit_message="Update plugin"
        fi
        git commit -m "$commit_message"
    fi
fi

# בדוק אם יש commits שלא נדחפו
unpushed=$(git log origin/main..HEAD --oneline 2>/dev/null | wc -l | tr -d ' ')
if [ "$unpushed" -eq 0 ] && [ -z "$(git status --porcelain)" ]; then
    echo "✅ הכל מעודכן - אין שינויים לדחיפה"
    exit 0
fi

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
