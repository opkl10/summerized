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
# נסה fetch קודם (אבל אל תכשל אם זה לא עובד)
git fetch origin 2>/dev/null || true

# בדוק אם יש commits מקומיים שלא נדחפו
unpushed_commits=$(git log origin/main..HEAD --oneline 2>/dev/null)
unpushed_count=$(echo "$unpushed_commits" | grep -c . || echo "0")

# אם אין שינויים ולא staged ולא commits לדחיפה
if [ -z "$(git status --porcelain)" ] && [ "$unpushed_count" -eq 0 ]; then
    echo "✅ הכל מעודכן - אין שינויים לדחיפה"
    echo ""
    echo "💡 טיפ: אם הוספת שינויים, ודא ששמרת אותם ב-git:"
    echo "   git add ."
    echo "   git commit -m 'הודעת commit'"
    exit 0
fi

# אם יש commits לדחיפה, הצג אותם
if [ "$unpushed_count" -gt 0 ]; then
    echo "📦 נמצאו $unpushed_count commits לדחיפה:"
    echo "$unpushed_commits" | head -5
    echo ""
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
