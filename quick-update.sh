#!/bin/bash

# סקריפט מהיר לעדכון הפלאגין
# Cursor → GitHub → WordPress

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

echo "🔄 מעדכן את הפלאגין..."
echo ""

# בדוק אם יש שינויים
if [ -z "$(git status --porcelain)" ]; then
    echo "⚠️  אין שינויים לעדכן"
    read -p "האם אתה רוצה להמשיך בכל זאת? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 0
    fi
fi

# קבל הודעת commit
read -p "הכנס הודעת commit (או Enter לברירת מחדל): " commit_message
if [ -z "$commit_message" ]; then
    commit_message="Update plugin"
fi

# הוסף קבצים
echo "📝 מוסיף קבצים..."
git add .

# Commit
echo "💾 יוצר commit..."
git commit -m "$commit_message"

# Push
echo "⬆️  מעלה ל-GitHub..."
git push origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ הושלם!"
    echo ""
    echo "📋 מה קורה עכשיו:"
    echo ""
    echo "1. אם יש GitHub Actions:"
    echo "   - GitHub Actions ייצור Release אוטומטית (תוך 1-2 דקות)"
    echo "   - לך ל-GitHub → Actions כדי לראות את ה-progress"
    echo ""
    echo "2. ב-WordPress:"
    echo "   - לך ל-Settings → Claude Summarizer"
    echo "   - לחץ 'בדוק עכשיו'"
    echo "   - אם יש עדכון, לחץ 'התקן עדכון עכשיו'"
    echo ""
    echo "🔍 בדוק:"
    echo "   - GitHub → Actions (workflow רץ)"
    echo "   - GitHub → Releases (Release חדש)"
    echo ""
else
    echo ""
    echo "❌ שגיאה! בדוק את ה-logs למעלה"
    exit 1
fi
