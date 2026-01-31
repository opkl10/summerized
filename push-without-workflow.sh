#!/bin/bash

# סקריפט לדחיפה בלי workflow (אם יש בעיה עם token)

echo "🔄 דוחף ל-GitHub בלי workflow..."
echo ""

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

# הסר את ה-workflow מה-commit הנוכחי
if [ -f ".github/workflows/auto-update.yml" ]; then
    echo "📝 מסיר workflow מה-commit..."
    git reset HEAD~1 2>/dev/null || echo "אין commit קודם"
    git rm --cached .github/workflows/auto-update.yml 2>/dev/null || echo "קובץ לא ב-git"
    
    # הוסף הכל חוץ מה-workflow
    git add .
    git reset .github/workflows/auto-update.yml 2>/dev/null
    
    read -p "הכנס הודעת commit: " commit_message
    if [ -z "$commit_message" ]; then
        commit_message="Initial commit (without workflow)"
    fi
    
    git commit -m "$commit_message"
fi

# דחוף
echo "⬆️  דוחף ל-GitHub..."
git push origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ הושלם!"
    echo ""
    echo "⚠️  הערה: GitHub Actions לא יעבוד בלי workflow scope"
    echo "📖 ראה FIX_WORKFLOW_ERROR.md להוספת workflow scope"
else
    echo ""
    echo "❌ שגיאה! בדוק את ה-logs למעלה"
    exit 1
fi
