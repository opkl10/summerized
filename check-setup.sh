#!/bin/bash

# סקריפט לבדיקת הגדרת GitHub

echo "🔍 בודק הגדרת GitHub..."
echo ""

# בדוק אם git מוגדר
if [ ! -d ".git" ]; then
    echo "❌ Git לא מאותחל. הרץ: git init"
    exit 1
else
    echo "✅ Git מאותחל"
fi

# בדוק אם יש remote
if ! git remote get-url origin &> /dev/null; then
    echo "❌ אין remote מוגדר. הרץ: git remote add origin YOUR_REPO_URL"
    exit 1
else
    echo "✅ Remote מוגדר: $(git remote get-url origin)"
fi

# בדוק אם יש GitHub Actions
if [ ! -f ".github/workflows/auto-update.yml" ]; then
    echo "❌ GitHub Actions לא נמצא"
else
    echo "✅ GitHub Actions קיים"
fi

# בדוק את הגרסה ב-plugin
VERSION=$(grep "Version:" claude-ai-summarizer.php | head -1 | sed -e 's/.*Version: *\([0-9.]*\).*/\1/')
echo "✅ גרסה נוכחית: $VERSION"

# בדוק אם יש uncommitted changes
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  יש שינויים שלא נשמרו. הרץ: git add . && git commit -m 'message'"
else
    echo "✅ אין שינויים שלא נשמרו"
fi

echo ""
echo "📋 צעדים הבאים:"
echo "1. ודא שה-Secrets מוגדרים ב-GitHub (WORDPRESS_WEBHOOK_URL, WEBHOOK_SECRET)"
echo "2. ודא שה-Webhook מוגדר ב-GitHub"
echo "3. ודא שההגדרות ב-WordPress נכונות"
echo "4. עדכן גרסה ב-claude-ai-summarizer.php"
echo "5. הרץ: git add . && git commit -m 'Update' && git push origin main"
echo ""
