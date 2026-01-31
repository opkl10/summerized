#!/bin/bash

# סקריפט לתיקון שגיאת workflow scope

echo "🔧 מתקן שגיאת workflow scope..."
echo ""

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

echo "📋 בוחר פתרון:"
echo "1. הסר workflow מה-commit (מהיר, בלי GitHub Actions)"
echo "2. יצירת token חדש עם workflow scope (מומלץ, עם GitHub Actions)"
echo ""
read -p "בחר אופציה (1 או 2): " choice

if [ "$choice" = "1" ]; then
    echo ""
    echo "🗑️  מסיר workflow מה-commit..."
    
    # בטל את ה-commit האחרון (אבל שמור את השינויים)
    git reset --soft HEAD~1
    
    # הסר את ה-workflow מה-git
    git reset HEAD .github/workflows/auto-update.yml 2>/dev/null
    
    # צור commit חדש
    git add .
    git commit -m "Initial commit - Claude AI Summarizer"
    
    echo ""
    echo "⬆️  דוחף ל-GitHub..."
    git push origin main
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ הושלם!"
        echo ""
        echo "⚠️  הערה: GitHub Actions לא יעבוד בלי workflow scope"
        echo "אבל אתה עדיין יכול לעדכן ידנית ב-WordPress"
    else
        echo ""
        echo "❌ שגיאה! בדוק את ה-logs למעלה"
    fi
    
elif [ "$choice" = "2" ]; then
    echo ""
    echo "📝 יצירת token חדש:"
    echo ""
    echo "1. לך ל-GitHub → Settings → Developer settings → Personal access tokens"
    echo "2. לחץ 'Generate new token (classic)'"
    echo "3. סמן: ✅ repo + ✅ workflow"
    echo "4. לחץ 'Generate token'"
    echo "5. העתק את ה-Token"
    echo ""
    read -p "לחץ Enter אחרי שיצרת את ה-Token..."
    
    echo ""
    echo "🔄 מנסה push שוב..."
    echo "כשתבקש password, הכנס את ה-Token החדש (לא הסיסמה!)"
    echo ""
    
    git push origin main
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ הושלם!"
    else
        echo ""
        echo "❌ שגיאה! ודא שהכנסת את ה-Token הנכון"
    fi
    
else
    echo "❌ בחירה לא תקינה"
    exit 1
fi
