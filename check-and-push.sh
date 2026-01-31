#!/bin/bash

# סקריפט משופר לבדיקה ודחיפה

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

echo "🔍 בודק מצב Git..."
echo ""

# הצג מצב
echo "📊 מצב נוכחי:"
git status --short
echo ""

# בדוק אם יש שינויים לא שמורים
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  נמצאו שינויים לא שמורים!"
    echo ""
    echo "הקבצים השתנו:"
    git status --short
    echo ""
    read -p "האם לשמור אותם? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git add .
        read -p "הכנס הודעת commit: " commit_message
        if [ -z "$commit_message" ]; then
            commit_message="Update plugin"
        fi
        git commit -m "$commit_message"
        echo "✅ שינויים נשמרו!"
        echo ""
    else
        echo "❌ בוטל - השינויים לא נשמרו"
        exit 0
    fi
fi

# נסה fetch (אבל אל תכשל)
echo "🔄 בודק מצב ב-GitHub..."
git fetch origin 2>/dev/null || echo "⚠️  לא ניתן להתחבר ל-GitHub (זה בסדר, נמשיך)"

# בדוק commits שלא נדחפו
unpushed=$(git log origin/main..HEAD --oneline 2>/dev/null)
unpushed_count=$(echo "$unpushed" | grep -c . 2>/dev/null || echo "0")

if [ "$unpushed_count" -eq 0 ]; then
    echo "✅ אין commits לדחיפה"
    echo ""
    echo "💡 אם הוספת שינויים, ודא ששמרת אותם:"
    echo "   1. git add ."
    echo "   2. git commit -m 'הודעת commit'"
    echo "   3. ./check-and-push.sh"
    exit 0
fi

echo "📦 נמצאו $unpushed_count commits לדחיפה:"
echo "$unpushed" | head -5
echo ""

read -p "האם לדחוף ל-GitHub? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ בוטל"
    exit 0
fi

echo "⬆️  דוחף ל-GitHub..."
git push origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ הושלם!"
    echo ""
    echo "📋 מה קורה עכשיו:"
    echo "1. GitHub Actions ייצור Release אוטומטית (תוך 1-2 דקות)"
    echo "2. לך ל-GitHub → Actions כדי לראות את ה-progress"
    echo "3. לך ל-GitHub → Releases כדי לראות את ה-Release החדש"
else
    echo ""
    echo "❌ שגיאה! בדוק את ה-logs למעלה"
    exit 1
fi
