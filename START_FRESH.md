# 🚀 התחלה מחדש - מדריך מלא

## שלב 1: אפס את Git (2 דקות)

### פתח Terminal חדש

1. לחץ `⌘ + Space`
2. הקלד: `Terminal`
3. לחץ Enter

### הרץ את הפקודות:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הסר את כל ה-git (לא ימחק את הקבצים!)
rm -rf .git

# אתחל Git מחדש
git init

# הוסף את כל הקבצים
git add .

# צור commit ראשון
git commit -m "Initial commit - Claude AI Summarizer"
```

---

## שלב 2: צור Personal Access Token (3 דקות)

### 2.1. לך ל-GitHub

1. לך ל: **[github.com/settings/tokens](https://github.com/settings/tokens)**
2. לחץ **"Generate new token (classic)"**

### 2.2. מלא את הפרטים

1. **Note**: "WordPress Plugin"
2. **Expiration**: בחר תאריך (או ללא תפוגה)
3. **Select scopes**: סמן:
   - ✅ **repo** (כל ה-repo permissions)
   - ✅ **workflow** (חשוב! ל-GitHub Actions)
4. לחץ **"Generate token"**
5. **העתק את ה-Token** (תראה אותו רק פעם אחת!)

---

## שלב 3: חבר ל-GitHub והעלה (2 דקות)

### 3.1. חבר ל-GitHub

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# חבר ל-GitHub (החלף opkl10 בשם המשתמש שלך)
git remote add origin https://github.com/opkl10/teenights-ai-summery.git

# הגדר branch
git branch -M main

# בדוק שזה עובד
git remote -v
```

### 3.2. העלה ל-GitHub

```bash
# דחוף ל-GitHub
git push -u origin main
```

**כשתבקש:**
- **Username**: `opkl10` (או שם המשתמש שלך)
- **Password**: ה-Personal Access Token שיצרת (לא הסיסמה!)

---

## שלב 4: בדוק שהעלה (1 דקה)

1. לך ל-GitHub → Repository `teenights-ai-summery`
2. אתה אמור לראות את כל הקבצים!
3. לך ל-**Actions** - אמור להיות workflow שרץ

---

## שלב 5: התקן ב-WordPress (3 דקות)

### 5.1. העלה את ה-Plugin

**אפשרות 1: דרך FTP/SSH**
- העתק את התיקייה `claude-ai-summarizer` ל-`wp-content/plugins/`

**אפשרות 2: דרך ZIP**
- צור ZIP מהתיקייה
- WordPress Admin → Plugins → Add New → Upload Plugin

### 5.2. הגדר את ה-Plugin

1. **WordPress Admin** → **Settings** → **Claude Summarizer**
2. מלא:
   - **Claude API Key** (מ-[console.anthropic.com](https://console.anthropic.com/))
   - **GitHub Repository**: `opkl10/teenights-ai-summery`
   - ✅ **עדכון אוטומטי**
3. לחץ **"Save Changes"**

---

## ✅ מוכן!

עכשיו כל פעם שאתה עושה שינוי:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
git add .
git commit -m "Update to v1.1.2"
git push origin main
```

ואז ב-WordPress → Settings → "בדוק עכשיו" → "התקן עדכון עכשיו"

---

## 🔧 אם יש שגיאות

### שגיאת "workflow scope"
- ודא שיצרת token עם **workflow scope**
- או הסר את ה-workflow: `git rm --cached .github/workflows/auto-update.yml`

### שגיאת "remote origin already exists"
- עדכן: `git remote set-url origin https://github.com/opkl10/teenights-ai-summery.git`

### שגיאת authentication
- ודא שהכנסת את ה-Token (לא הסיסמה)
- נסה ליצור token חדש

---

**זה הכל! 🎉**
