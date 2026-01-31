# ⚡ תיקון מהיר - שגיאת Workflow

## הבעיה

השגיאה אומרת שה-Token שלך לא כולל `workflow` scope.

## פתרון מהיר (1 דקה)

### הסר את ה-Workflow ודחוף שוב:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הסר את ה-workflow מה-git
git rm --cached .github/workflows/auto-update.yml

# עדכן את ה-commit
git add .
git commit --amend -m "Initial commit - Claude AI Summarizer"

# דחוף
git push -u origin main --force
```

**זה יעבוד מיד! ✅**

---

## מה זה אומר?

- ✅ הקוד יעלה ל-GitHub
- ❌ GitHub Actions לא יעבוד (אבל זה לא חובה)
- ✅ אתה עדיין יכול לעדכן ידנית ב-WordPress

---

## אם אתה רוצה GitHub Actions (מאוחר יותר)

1. צור Personal Access Token חדש עם **workflow scope**
2. הוסף את ה-workflow בחזרה:
   ```bash
   git add .github/workflows/auto-update.yml
   git commit -m "Add GitHub Actions workflow"
   git push origin main
   ```

---

**זה הכל! 🎉**
