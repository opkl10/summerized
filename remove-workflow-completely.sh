#!/bin/bash

# הסר את ה-workflow לגמרי מה-git

cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer || exit 1

echo "🗑️  מסיר את ה-workflow מה-git..."

# הסר את ה-workflow מה-git (אבל שמור אותו בקבצים)
git rm --cached .github/workflows/auto-update.yml

# הוסף את השינוי
git add .

# צור commit חדש
git commit -m "Remove workflow file from git"

echo ""
echo "✅ ה-workflow הוסר מה-git!"
echo ""
echo "📤 עכשיו דחוף:"
echo "   git push -u origin main --force"
echo ""
