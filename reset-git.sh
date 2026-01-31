#!/bin/bash

# סקריפט לאיפוס Git והתחלה מחדש

echo "🔄 מאפס Git ומתחיל מהתחלה..."
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

git remote add origin https://github.com/$username/teenights-ai-summery.git
git branch -M main

echo ""
echo "✅ Git אופס!"
echo ""
echo "📋 צעדים הבאים:"
echo "1. צור Personal Access Token עם workflow scope (ראה SETUP_GUIDE.md)"
echo "2. הרץ: git push -u origin main"
echo "3. כשתבקש password, הכנס את ה-Token"
echo ""
