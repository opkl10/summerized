#!/bin/bash

# סקריפט לתיקון remote

echo "🔧 בודק remote נוכחי..."
echo ""

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

# בדוק מה ה-remote הנוכחי
current_remote=$(git remote get-url origin 2>/dev/null)

if [ -z "$current_remote" ]; then
    echo "❌ אין remote מוגדר"
    echo ""
    read -p "הכנס את שם המשתמש שלך ב-GitHub: " username
    git remote add origin https://github.com/$username/teenights-ai-summery.git
    echo "✅ Remote נוסף"
else
    echo "📋 Remote נוכחי: $current_remote"
    echo ""
    
    if [[ "$current_remote" == *"teenights-ai-summery"* ]]; then
        echo "✅ Remote כבר נכון!"
        echo ""
        read -p "האם אתה רוצה לעדכן אותו בכל זאת? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            read -p "הכנס את שם המשתמש שלך ב-GitHub: " username
            git remote set-url origin https://github.com/$username/teenights-ai-summery.git
            echo "✅ Remote עודכן"
        fi
    else
        echo "⚠️  Remote מצביע ל-repository אחר"
        echo ""
        read -p "האם אתה רוצה לעדכן ל-teenights-ai-summery? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            read -p "הכנס את שם המשתמש שלך ב-GitHub: " username
            git remote set-url origin https://github.com/$username/teenights-ai-summery.git
            echo "✅ Remote עודכן ל-teenights-ai-summery"
        fi
    fi
fi

echo ""
echo "📋 Remote נוכחי:"
git remote -v

echo ""
echo "✅ מוכן! עכשיו תוכל להריץ:"
echo "   git push origin main"
