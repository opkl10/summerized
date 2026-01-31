#!/bin/bash

# סקריפט להתחלה מחדש מלאה

echo "🔄 מאפס הכל ומתחיל מהתחלה..."
echo ""
echo "⚠️  זה יסיר את כל ה-git history אבל לא ימחק את הקבצים!"
echo ""
read -p "האם אתה בטוח? (y/n) " -n 1 -r
echo

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "בוטל"
    exit 0
fi

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

# הסר את כל ה-git
echo "🗑️  מסיר git..."
rm -rf .git

# אתחל מחדש
echo "📦 מאתחל Git..."
git init

# הוסף קבצים
echo "📝 מוסיף קבצים..."
git add .

# Commit
echo "💾 יוצר commit..."
git commit -m "Initial commit - Claude AI Summarizer"

# חבר ל-GitHub
echo "🔗 מחבר ל-GitHub..."
read -p "הכנס את שם המשתמש שלך ב-GitHub: " username

if [ -z "$username" ]; then
    username="opkl10"
fi

git remote add origin https://github.com/$username/teenights-ai-summery.git 2>/dev/null || git remote set-url origin https://github.com/$username/teenights-ai-summery.git
git branch -M main

echo ""
echo "✅ Git אופס והתחבר ל-GitHub!"
echo ""
echo "📋 צעדים הבאים:"
echo ""
echo "1. צור Personal Access Token:"
echo "   - לך ל: https://github.com/settings/tokens"
echo "   - לחץ 'Generate new token (classic)'"
echo "   - סמן: ✅ repo + ✅ workflow"
echo "   - העתק את ה-Token"
echo ""
echo "2. דחוף ל-GitHub:"
echo "   git push -u origin main"
echo "   (כשתבקש password, הכנס את ה-Token)"
echo ""
echo "3. התקן ב-WordPress והגדר"
echo ""
