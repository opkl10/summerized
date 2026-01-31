#!/bin/bash

# סקריפט להעלאת הקוד ל-GitHub
# שם Repository: teenights-ai-summery

echo "🚀 מתחיל העלאת הקוד ל-GitHub..."
echo ""

# בדוק אם git מוגדר
if [ ! -d ".git" ]; then
    echo "📦 מאתחל Git..."
    git init
else
    echo "✅ Git כבר מאותחל"
fi

# הוסף קבצים
echo "📝 מוסיף קבצים..."
git add .

# Commit
echo "💾 יוצר commit..."
git commit -m "Initial commit - Claude AI Summarizer" || echo "⚠️  אין שינויים חדשים"

# בדוק אם יש remote
if git remote get-url origin &> /dev/null; then
    echo "✅ Remote כבר מוגדר: $(git remote get-url origin)"
    read -p "האם אתה רוצה לעדכן את ה-remote? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git remote remove origin
        git remote add origin https://github.com/YOUR_USERNAME/teenights-ai-summery.git
        echo "✅ Remote עודכן"
    fi
else
    echo "🔗 מוסיף remote..."
    echo ""
    echo "⚠️  חשוב: החלף YOUR_USERNAME בשם המשתמש שלך ב-GitHub!"
    echo ""
    read -p "הכנס את שם המשתמש שלך ב-GitHub: " username
    git remote add origin https://github.com/$username/teenights-ai-summery.git
    echo "✅ Remote נוסף: https://github.com/$username/teenights-ai-summery.git"
fi

# הגדר branch
git branch -M main

# Push
echo ""
echo "⬆️  מעלה ל-GitHub..."
echo "⚠️  אם GitHub מבקש ממך להתחבר, השתמש ב-Personal Access Token!"
echo ""
git push -u origin main

echo ""
echo "✅ הושלם!"
echo ""
echo "📋 צעדים הבאים:"
echo "1. לך ל-GitHub → Repository → Settings → Secrets and variables → Actions"
echo "2. הוסף 2 secrets: WORDPRESS_WEBHOOK_URL ו-WEBHOOK_SECRET"
echo "3. הגדר Webhook ב-GitHub → Settings → Webhooks"
echo "4. הגדר את ה-Plugin ב-WordPress → Settings → Claude Summarizer"
echo ""
echo "📖 ראה COMPLETE_SETUP_GUIDE.md למדריך מפורט"
