# 🔄 איפוס והתחלה מחדש

## איך לאפס ולהתחיל מהתחלה

### שלב 1: אפס את Git (2 דקות)

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הסר את כל ה-git (זה לא ימחק את הקבצים!)
rm -rf .git

# אתחל Git מחדש
git init

# הוסף את כל הקבצים
git add .

# צור commit ראשון
git commit -m "Initial commit - Claude AI Summarizer"
```

### שלב 2: חבר ל-GitHub (1 דקה)

```bash
# חבר ל-GitHub (החלף opkl10 בשם המשתמש שלך)
git remote add origin https://github.com/opkl10/teenights-ai-summery.git

# הגדר branch
git branch -M main

# בדוק שזה עובד
git remote -v
```

### שלב 3: צור Token חדש עם Workflow Scope (3 דקות)

**חשוב:** כדי להימנע משגיאת workflow, צור token עם workflow scope:

1. GitHub → **Settings** → **Developer settings** → **Personal access tokens** → **Tokens (classic)**
2. לחץ **"Generate new token (classic)"**
3. מלא:
   - **Note**: "WordPress Plugin"
   - **Select scopes**: סמן:
     - ✅ **repo** (כל ה-repo permissions)
     - ✅ **workflow** (חשוב!)
4. לחץ **"Generate token"**
5. **העתק את ה-Token** (תראה אותו רק פעם אחת!)

### שלב 4: העלה ל-GitHub (1 דקה)

```bash
# דחוף ל-GitHub
git push -u origin main
```

**כשתבקש:**
- **Username**: `opkl10` (או שם המשתמש שלך)
- **Password**: ה-Personal Access Token החדש (לא הסיסמה!)

---

## או: הסר רק את ה-Workflow (מהיר יותר)

אם אתה לא רוצה ליצור token חדש:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הסר את ה-workflow מה-git (אבל שמור אותו במחשב)
git rm --cached .github/workflows/auto-update.yml

# עדכן את ה-commit
git add .
git commit --amend -m "Initial commit - Claude AI Summarizer (without workflow)"

# דחוף
git push -u origin main --force
```

**הערה:** זה אומר ש-GitHub Actions לא יעבוד, אבל אתה עדיין יכול לעדכן ידנית.

---

## איפוס מלא (אם הכל תקוע)

אם אתה רוצה להתחיל ממש מהתחלה:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הסר את כל ה-git
rm -rf .git

# אתחל מחדש
git init
git add .
git commit -m "Initial commit - Claude AI Summarizer"

# חבר ל-GitHub
git remote add origin https://github.com/opkl10/teenights-ai-summery.git
git branch -M main

# דחוף (עם token חדש עם workflow scope)
git push -u origin main
```

---

## בדיקה

לאחר ה-push:

1. לך ל-GitHub → Repository `teenights-ai-summery`
2. אתה אמור לראות את כל הקבצים!
3. אם יש `.github/workflows/auto-update.yml`, לך ל-Actions - אמור להיות workflow שרץ

---

## אם עדיין יש שגיאה

אם עדיין יש שגיאת workflow:

1. ודא שיצרת token עם **workflow scope**
2. או הסר את ה-workflow (ראה "או: הסר רק את ה-Workflow" למעלה)

---

**זה הכל! 🎉**
