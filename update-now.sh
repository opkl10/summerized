#!/bin/bash

# סקריפט מהיר לעדכון - Cursor → GitHub → WordPress

echo "🔄 מעדכן את ה-Plugin..."
echo ""

# עבור לתיקייה
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

# בדוק אם יש שינויים
if [ -z "$(git status --porcelain)" ]; then
    echo "⚠️  אין שינויים לעדכן"
    read -p "האם אתה רוצה להמשיך בכל זאת? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 0
    fi
fi

# קבל הודעה ל-commit
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
    echo "1. GitHub Actions ייצור Release (תוך 1-2 דקות)"
    echo "2. Webhook ישלח עדכון ל-WordPress"
    echo "3. ה-Plugin יעדכן אוטומטית"
    echo ""
    echo "🔍 בדוק:"
    echo "- GitHub → Actions (workflow רץ)"
    echo "- GitHub → Releases (Release חדש)"
    echo "- WordPress → Settings → Claude Summarizer → 'בדוק עכשיו'"
else
    echo ""
    echo "❌ שגיאה! בדוק את ה-logs למעלה"
    exit 1
fi
