# 🚀 מדריך מלא - מערכת עדכונים אוטומטית לפלאגין

מדריך מקיף מאפס ועד הסוף להגדרת מערכת עדכונים אוטומטית מ-GitHub ל-WordPress Plugin.

---

## 📋 תוכן עניינים

1. [יצירת GitHub Repository](#1-יצירת-github-repository)
2. [יצירת Personal Access Token](#2-יצירת-personal-access-token)
3. [העלאת הקוד ל-GitHub](#3-העלאת-הקוד-ל-github)
4. [הגדרת GitHub Actions (אופציונלי)](#4-הגדרת-github-actions-אופציונלי)
5. [התקנת הפלאגין ב-WordPress](#5-התקנת-הפלאגין-ב-wordpress)
6. [הגדרת מערכת העדכונים](#6-הגדרת-מערכת-העדכונים)
7. [איך לעדכן את הפלאגין](#7-איך-לעדכן-את-הפלאגין)
8. [פתרון בעיות](#8-פתרון-בעיות)

---

## 1. יצירת GitHub Repository

### שלב 1.1: צור Repository חדש

1. לך ל-[github.com/new](https://github.com/new)
2. מלא:
   - **Repository name**: `claude-ai-summarizer` (או שם אחר)
   - **Description**: "WordPress Plugin for AI Summarization"
   - **Visibility**: ✅ **Public** (חובה ל-GitHub Actions)
   - **אל תסמן** "Add README" או "Add .gitignore"
3. לחץ **"Create repository"**

### שלב 1.2: העתק את ה-URL

לאחר יצירת ה-repository, העתק את ה-URL:
- `https://github.com/YOUR_USERNAME/claude-ai-summarizer.git`

---

## 2. יצירת Personal Access Token

### שלב 2.1: צור Token

1. לך ל-[github.com/settings/tokens](https://github.com/settings/tokens)
2. לחץ **"Generate new token (classic)"**
3. מלא:
   - **Note**: "WordPress Plugin Updates"
   - **Expiration**: בחר תאריך (או "No expiration")
   - **Select scopes**: סמן:
     - ✅ **repo** (כל ה-permissions)
     - ✅ **workflow** (חשוב! ל-GitHub Actions)
4. לחץ **"Generate token"**
5. **העתק את ה-Token מיד!** (תראה אותו רק פעם אחת)

### שלב 2.2: שמור את ה-Token

שמור את ה-Token במקום בטוח - תצטרך אותו לדחיפת קוד ל-GitHub.

---

## 3. העלאת הקוד ל-GitHub

### שלב 3.1: אתחל Git (אם עדיין לא)

פתח Terminal והרץ:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# אם אין git, אתחל:
git init
git add .
git commit -m "Initial commit - Claude AI Summarizer"
```

### שלב 3.2: חבר ל-GitHub

```bash
# החלף YOUR_USERNAME ו-REPO_NAME בשמות שלך
git remote add origin https://github.com/YOUR_USERNAME/REPO_NAME.git
git branch -M main
```

### שלב 3.3: דחוף ל-GitHub

```bash
git push -u origin main
```

**כשתבקש:**
- **Username**: שם המשתמש שלך ב-GitHub
- **Password**: ה-Personal Access Token שיצרת (לא הסיסמה!)

---

## 4. הגדרת GitHub Actions (אופציונלי)

### אופציה A: עם GitHub Actions (מומלץ)

אם יש לך Personal Access Token עם **workflow scope**, GitHub Actions יעבוד אוטומטית!

**מה קורה:**
- כל push ל-`main` → GitHub Actions יוצר Release אוטומטית
- ה-Release כולל קובץ ZIP מוכן להתקנה
- הפלאגין ב-WordPress יזהה את ה-Release החדש

**אין צורך לעשות כלום** - זה עובד אוטומטית! ✅

### אופציה B: בלי GitHub Actions

אם אין לך token עם workflow scope, או שאתה לא רוצה GitHub Actions:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הסר את ה-workflow מה-git
git rm --cached .github/workflows/auto-update.yml
git commit -m "Remove workflow"
git push origin main
```

**מה לעשות במקום:**
- צור Release ידנית ב-GitHub:
  1. לך ל-GitHub → Releases → "Create a new release"
  2. Tag: `v1.1.0` (לפי הגרסה ב-`claude-ai-summarizer.php`)
  3. צור ZIP מהתיקייה והעלה כ-Asset

---

## 5. התקנת הפלאגין ב-WordPress

### שלב 5.1: העלה את הפלאגין

**אפשרות 1: דרך FTP/SSH**
- העתק את התיקייה `claude-ai-summarizer` ל-`wp-content/plugins/`

**אפשרות 2: דרך ZIP**
- צור ZIP מהתיקייה `claude-ai-summarizer`
- WordPress Admin → **Plugins** → **Add New** → **Upload Plugin**
- בחר את ה-ZIP והעלה

### שלב 5.2: הפעל את הפלאגין

1. לך ל-**Plugins** ב-WordPress Admin
2. מצא **"Claude AI Summarizer"**
3. לחץ **"Activate"**

---

## 6. הגדרת מערכת העדכונים

### שלב 6.1: הגדר את ה-Plugin

1. לך ל-**Settings** → **Claude Summarizer**
2. מלא את השדות:

#### הגדרות בסיסיות:
- **Claude API Key**: מפתח API מ-[console.anthropic.com](https://console.anthropic.com/)
- **Model**: `claude-3-5-sonnet-20241022` (או אחר)
- **Summary Length**: `medium` / `short` / `long`

#### הגדרות עדכון:
- **GitHub Repository**: `YOUR_USERNAME/REPO_NAME` (לדוגמה: `opkl10/claude-ai-summarizer`)
- ✅ **עדכון אוטומטי**: סמן אם אתה רוצה שהפלאגין יבדוק אוטומטית לעדכונים
- ✅ **התקנה אוטומטית**: סמן רק אם אתה סומך על ה-repository (לא מומלץ)

3. לחץ **"Save Changes"**

### שלב 6.2: בדוק חיבור ל-GitHub

1. בגלל **"סטטוס עדכון"** בתחתית הדף
2. לחץ **"בדוק עכשיו"**
3. אמור לראות:
   - ✅ **גרסה נוכחית**: `1.1.0`
   - ✅ **עדכון זמין**: "לא" (אם אין עדכון)
   - ✅ **בדיקה אחרונה**: תאריך ושעה

---

## 7. איך לעדכן את הפלאגין

### שיטה 1: עדכון אוטומטי (אם מופעל)

1. ערוך קבצים ב-Cursor
2. עדכן את הגרסה ב-`claude-ai-summarizer.php`:
   ```php
   Version: 1.1.1  // עדכן את המספר
   ```
3. דחוף ל-GitHub:
   ```bash
   cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
   git add .
   git commit -m "Update to v1.1.1"
   git push origin main
   ```
4. **אם יש GitHub Actions:**
   - חכה 1-2 דקות
   - GitHub Actions ייצור Release אוטומטית
   - הפלאגין ב-WordPress יזהה את העדכון (תוך שעה, או מיד אם לחצת "בדוק עכשיו")

5. **ב-WordPress:**
   - לך ל-**Settings** → **Claude Summarizer**
   - לחץ **"בדוק עכשיו"**
   - אם יש עדכון, לחץ **"התקן עדכון עכשיו"**

### שיטה 2: עדכון ידני

1. ערוך קבצים ב-Cursor
2. עדכן גרסה ודחוף ל-GitHub (כמו למעלה)
3. **צור Release ידנית:**
   - לך ל-GitHub → **Releases** → **"Create a new release"**
   - **Tag**: `v1.1.1` (לפי הגרסה)
   - **Title**: `Release v1.1.1`
   - **Description**: תיאור השינויים
   - **Attach files**: העלה ZIP של הפלאגין
   - לחץ **"Publish release"**
4. **ב-WordPress:**
   - לחץ **"בדוק עכשיו"**
   - לחץ **"התקן עדכון עכשיו"**

### שיטה 3: סקריפט מהיר

השתמש בסקריפט `update-now.sh`:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
./update-now.sh
```

הסקריפט יבקש הודעת commit ויעלה הכל אוטומטית.

---

## 8. פתרון בעיות

### שגיאה: "remote origin already exists"

**פתרון:**
```bash
git remote set-url origin https://github.com/YOUR_USERNAME/REPO_NAME.git
```

### שגיאה: "workflow scope required"

**פתרון 1 (מומלץ):**
- צור Personal Access Token חדש עם **workflow scope**
- השתמש בו לדחיפה

**פתרון 2:**
- הסר את ה-workflow:
  ```bash
  git rm --cached .github/workflows/auto-update.yml
  git commit -m "Remove workflow"
  git push origin main
  ```

### שגיאה: "Authentication failed"

**פתרון:**
- ודא שהכנסת את ה-Personal Access Token (לא הסיסמה!)
- נסה ליצור token חדש

### הפלאגין לא מזהה עדכונים

**בדוק:**
1. ✅ GitHub Repository נכון ב-Settings?
2. ✅ יש Release ב-GitHub?
3. ✅ הגרסה ב-Release גדולה מהגרסה הנוכחית?
4. ✅ לחצת "בדוק עכשיו"?

**פתרון:**
- בדוק את ה-Release ב-GitHub - ודא שיש קובץ ZIP
- ודא שהגרסה ב-`claude-ai-summarizer.php` נמוכה מהגרסה ב-Release

### GitHub Actions לא יוצר Release

**בדוק:**
1. לך ל-GitHub → **Actions**
2. בדוק אם יש שגיאות
3. ודא שה-workflow רץ על push ל-`main`

**פתרון:**
- ודא שה-repository הוא **Public**
- ודא שיש token עם **workflow scope**
- בדוק את ה-logs ב-Actions

### עדכון נכשל בהתקנה

**פתרון:**
1. בדוק הרשאות כתיבה ב-`wp-content/plugins/`
2. ודא שיש מספיק מקום בדיסק
3. נסה להוריד את ה-ZIP ידנית ולהעלות דרך WordPress

---

## 📝 סיכום - תהליך עדכון מהיר

```bash
# 1. ערוך קבצים ב-Cursor

# 2. עדכן גרסה ב-claude-ai-summarizer.php
# Version: 1.1.2

# 3. דחוף ל-GitHub
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
git add .
git commit -m "Update to v1.1.2"
git push origin main

# 4. (אם יש GitHub Actions) חכה 1-2 דקות

# 5. ב-WordPress → Settings → Claude Summarizer
#    → לחץ "בדוק עכשיו" → "התקן עדכון עכשיו"
```

---

## ✅ רשימת בדיקה

- [ ] GitHub Repository נוצר
- [ ] Personal Access Token נוצר (עם repo + workflow scopes)
- [ ] קוד עלה ל-GitHub
- [ ] GitHub Actions עובד (או Release ידני נוצר)
- [ ] פלאגין מותקן ב-WordPress
- [ ] הגדרות מוגדרות (GitHub Repository, API Key)
- [ ] בדיקת עדכון עובדת
- [ ] עדכון ראשון הותקן בהצלחה

---

**זה הכל! 🎉**

עכשיו יש לך מערכת עדכונים אוטומטית מלאה!
