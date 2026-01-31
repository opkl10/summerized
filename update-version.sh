#!/bin/bash

# סקריפט לעדכון גרסה אוטומטי
# שימוש: ./update-version.sh [major|minor|patch] [commit message]

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

# בדוק אם יש שינויים לא שמורים
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  יש שינויים לא שמורים!"
    echo "אנא שמור את השינויים לפני עדכון גרסה:"
    echo "   git add ."
    echo "   git commit -m 'הודעת commit'"
    exit 1
fi

# קבל את הגרסה הנוכחית
CURRENT_VERSION=$(grep "Version:" claude-ai-summarizer.php | head -1 | sed -e 's/.*Version: *\([0-9.]*\).*/\1/')
CURRENT_VERSION_DEFINE=$(grep "define('CLAUDE_SUMMARIZER_VERSION'" claude-ai-summarizer.php | head -1 | sed -e "s/.*'\([0-9.]*\)'.*/\1/")

if [ "$CURRENT_VERSION" != "$CURRENT_VERSION_DEFINE" ]; then
    echo "⚠️  הגרסאות לא תואמות!"
    echo "Version header: $CURRENT_VERSION"
    echo "Version define: $CURRENT_VERSION_DEFINE"
    exit 1
fi

echo "📦 גרסה נוכחית: $CURRENT_VERSION"
echo ""

# קבל את סוג העדכון (major, minor, patch)
UPDATE_TYPE=${1:-patch}

# חישוב גרסה חדשה
IFS='.' read -ra VERSION_PARTS <<< "$CURRENT_VERSION"
MAJOR=${VERSION_PARTS[0]}
MINOR=${VERSION_PARTS[1]}
PATCH=${VERSION_PARTS[2]}

case $UPDATE_TYPE in
    major)
        MAJOR=$((MAJOR + 1))
        MINOR=0
        PATCH=0
        ;;
    minor)
        MINOR=$((MINOR + 1))
        PATCH=0
        ;;
    patch)
        PATCH=$((PATCH + 1))
        ;;
    *)
        echo "❌ שגיאה: סוג עדכון לא תקין. השתמש ב: major, minor, או patch"
        exit 1
        ;;
esac

NEW_VERSION="$MAJOR.$MINOR.$PATCH"

echo "🆕 גרסה חדשה: $NEW_VERSION"
echo ""

# עדכן את הגרסה בקובץ
sed -i '' "s/Version: $CURRENT_VERSION/Version: $NEW_VERSION/" claude-ai-summarizer.php
sed -i '' "s/define('CLAUDE_SUMMARIZER_VERSION', '$CURRENT_VERSION_DEFINE');/define('CLAUDE_SUMMARIZER_VERSION', '$NEW_VERSION');/" claude-ai-summarizer.php

echo "✅ הגרסה עודכנה בקוד"
echo ""

# הוסף את השינויים
git add claude-ai-summarizer.php

# קבל הודעת commit
COMMIT_MSG=${2:-"Update version to $NEW_VERSION"}

# Commit
git commit -m "$COMMIT_MSG"

echo "✅ השינויים נשמרו ב-git"
echo ""

# שאל אם לדחוף ל-GitHub
read -p "האם לדחוף ל-GitHub? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "⬆️  דוחף ל-GitHub..."
    git push origin main
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ הושלם!"
        echo ""
        echo "📋 מה קורה עכשיו:"
        echo "1. GitHub Actions יזהה את שינוי הגרסה"
        echo "2. ייצור Release v$NEW_VERSION אוטומטית (תוך 1-2 דקות)"
        echo "3. הפלאגין ב-WordPress יזהה את העדכון בבדיקה הבאה"
        echo ""
        echo "🔍 בדוק:"
        echo "   - GitHub Actions: https://github.com/opkl10/summerized/actions"
        echo "   - Releases: https://github.com/opkl10/summerized/releases"
        echo ""
    else
        echo ""
        echo "❌ שגיאה בדחיפה! בדוק את ה-logs למעלה"
        exit 1
    fi
else
    echo ""
    echo "💡 כדי לדחוף מאוחר יותר:"
    echo "   git push origin main"
fi
