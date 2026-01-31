# 🚀 מדריך התקנה ועדכון - Claude AI Summarizer

**Repository:** `teenights-ai-summery`

---

## חלק א': התקנה ראשונית

### שלב 1: צור Repository ב-GitHub

1. לך ל: **[github.com/new](https://github.com/new)**
2. **Repository name**: `teenights-ai-summery`
3. לחץ **"Create repository"**
4. **העתק את ה-URL** (תצטרך אותו)

### שלב 2: צור Personal Access Token

1. GitHub → **Settings** → **Developer settings** → **Personal access tokens** → **Tokens (classic)**
2. לחץ **"Generate new token (classic)"**
3. מלא:
   - **Note**: "WordPress Plugin"
   - **Expiration**: בחר תאריך
   - **Select scopes**: סמן:
     - ✅ **repo** (כל ה-repo permissions)
     - ✅ **workflow** (ל-GitHub Actions)
4. לחץ **"Generate token"**
5. **העתק את ה-Token** (תראה אותו רק פעם אחת!)

### שלב 3: העלה את הקוד ל-GitHub

#### 3.1. פתח Terminal

- לחץ `⌘ + Space`
- הקלד: `Terminal`
- לחץ Enter

#### 3.2. עבור לתיקייה

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
```

#### 3.3. אתחל Git והעלה

```bash
# אתחל Git
git init

# הוסף קבצים
git add .

# צור commit
git commit -m "Initial commit - Claude AI Summarizer"

# בדוק אם יש כבר remote
git remote -v

# אם אין remote, הוסף אותו (החלף YOUR_USERNAME):
# git remote add origin https://github.com/YOUR_USERNAME/teenights-ai-summery.git

# אם יש שגיאה "remote origin already exists", עדכן אותו:
# git remote set-url origin https://github.com/YOUR_USERNAME/teenights-ai-summery.git

# אם ה-remote כבר נכון, דלג על זה והמשך ל-push

# הגדר branch
git branch -M main

# העלה ל-GitHub
git push -u origin main
```

**כשתבקש username:** הכנס את שם המשתמש שלך  
**כשתבקש password:** הכנס את ה-Personal Access Token (לא הסיסמה!)

#### 3.4. בדוק שהעלה

1. לך ל-GitHub → Repository `teenights-ai-summery`
2. אתה אמור לראות את כל הקבצים!

### שלב 4: התקן את ה-Plugin ב-WordPress

**אפשרות 1: דרך FTP/SSH**
1. העתק את התיקייה `claude-ai-summarizer` ל-`wp-content/plugins/`
2. ודא שהתיקייה נקראת `claude-ai-summarizer`

**אפשרות 2: דרך ZIP**
1. צור ZIP מהתיקייה `claude-ai-summarizer`
2. WordPress Admin → Plugins → Add New → Upload Plugin
3. בחר את ה-ZIP ולחץ "Install Now"
4. לחץ "Activate Plugin"

### שלב 5: הגדר את ה-Plugin ב-WordPress

1. **WordPress Admin** → **Settings** → **Claude Summarizer**

2. מלא את הפרטים:

   **Claude API Key:**
   - קבל מ-[console.anthropic.com](https://console.anthropic.com/)
   - הכנס את ה-Key

   **GitHub Repository:**
   ```
   YOUR_USERNAME/teenights-ai-summery
   ```
   (החלף YOUR_USERNAME בשם המשתמש שלך)

   **עדכון אוטומטי:**
   - ✅ סמן "אפשר עדכון אוטומטי מ-GitHub"
   
   **התקנה אוטומטית:**
   - (אופציונלי) סמן "התקן עדכונים אוטומטית"

3. לחץ **"Save Changes"**

---

## חלק ב': איך לעדכן את ה-Plugin

### כל פעם שאתה עושה שינוי ב-Cursor:

#### צעד 1: ערוך את הקוד

1. פתח את הקבצים ב-Cursor
2. עשה את השינויים
3. **חשוב:** עדכן את הגרסה ב-`claude-ai-summarizer.php`

   פתח `claude-ai-summarizer.php` ומצא:
   ```php
   Version: 1.1.0
   ```
   
   שנה ל:
   ```php
   Version: 1.1.1  // או כל גרסה חדשה
   ```

4. שמור: `⌘ + S`

#### צעד 2: העלה ל-GitHub

פתח Terminal והרץ:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# הוסף את השינויים
git add .

# צור commit
git commit -m "Update to v1.1.1"

# העלה ל-GitHub
git push origin main
```

#### צעד 3: מה קורה עכשיו?

1. **GitHub Actions** ייצור Release אוטומטית (תוך 1-2 דקות)
2. **WordPress** יבדוק עדכונים כל שעתיים (או תוכל לבדוק ידנית)

#### צעד 4: עדכן ב-WordPress

**אופציה 1: עדכון ידני**

1. **WordPress Admin** → **Settings** → **Claude Summarizer**
2. לחץ **"בדוק עכשיו"**
3. אם יש עדכון, תראה: **"גרסה חדשה זמינה: 1.1.1"**
4. לחץ **"התקן עדכון עכשיו"**

**אופציה 2: עדכון אוטומטי**

אם הפעלת "התקנה אוטומטית":
- ה-plugin יבדוק עדכונים כל שעתיים
- אם נמצא עדכון, תראה התראה
- העדכון יתקין אוטומטית

---

## חלק ג': פתרון בעיות

### ❌ שגיאה: "remote origin already exists"

**הבעיה:** כבר הגדרת remote בעבר.

**פתרון:**
```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# עדכן את ה-remote (החלף YOUR_USERNAME)
git remote set-url origin https://github.com/YOUR_USERNAME/teenights-ai-summery.git

# בדוק שזה עובד
git remote -v

# המשך עם push
git push origin main
```

---

### ❌ שגיאה: "workflow scope" או "refusing to allow Personal Access Token"

**הבעיה:** ה-Token שלך לא כולל את ה-scope `workflow`.

**פתרון מהיר: הסר את ה-Workflow מה-Commit (2 דקות)**

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# בטל את ה-commit האחרון (אבל שמור את השינויים)
git reset --soft HEAD~1

# הסר את ה-workflow מה-git (אבל שמור אותו במחשב)
git reset HEAD .github/workflows/auto-update.yml

# צור commit חדש בלי ה-workflow
git add .
git commit -m "Initial commit - Claude AI Summarizer"

# דחוף
git push origin main
```

**הערה:** זה אומר ש-GitHub Actions לא יעבוד, אבל אתה עדיין יכול לעדכן ידנית ב-WordPress.

**פתרון מלא: צור Token חדש עם Workflow (אם אתה רוצה GitHub Actions)**

1. GitHub → **Settings** → **Developer settings** → **Personal access tokens** → **Tokens (classic)**
2. לחץ **"Generate new token (classic)"**
3. סמן:
   - ✅ **repo** (כל ה-repo permissions)
   - ✅ **workflow** (חשוב! זה מה שחסר)
4. לחץ **"Generate token"**
5. **העתק את ה-Token** (תראה אותו רק פעם אחת!)
6. נסה push שוב - כשתבקש password, הכנס את ה-Token החדש

---

### ❌ שגיאה: "failed to push some refs"

**הבעיה:** לא יכול לדחוף ל-GitHub.

**פתרון מהיר:**

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# אם זה repository חדש, דחוף בכוח:
git push -u origin main --force

# או אם יש שגיאת workflow, הסר אותו:
git rm --cached .github/workflows/auto-update.yml
git add .
git commit --amend -m "Initial commit"
git push -u origin main --force
```

**ראה `FIX_PUSH_ERROR.md` לפתרונות מפורטים.**

---

### ❌ שגיאה: "git push לא עובד" או "authentication failed"

**הבעיה:** בעיה בהתחברות ל-GitHub.

**פתרון:**

1. **ודא שה-Token נכון:**
   - GitHub → Settings → Developer settings → Personal access tokens
   - ודא שה-token קיים ולא פג תוקף

2. **נקה credentials ישנים:**
   ```bash
   # macOS
   git credential-osxkeychain erase
   host=github.com
   protocol=https
   # לחץ Enter פעמיים
   ```

3. **נסה push שוב:**
   ```bash
   git push origin main
   ```
   - Username: שם המשתמש שלך
   - Password: ה-Personal Access Token

---

### ❌ שגיאה: "GitHub Actions לא יוצר Release"

**הבעיה:** ה-workflow לא רץ או נכשל.

**פתרון:**

1. **בדוק ב-GitHub:**
   - לך ל-Repository → **Actions**
   - לחץ על ה-workflow האחרון
   - בדוק אם יש שגיאות

2. **בדוק את הקובץ:**
   - ודא שהקובץ `.github/workflows/auto-update.yml` קיים
   - ודא שהגרסה ב-`claude-ai-summarizer.php` עדכנה

3. **נסה שוב:**
   - עדכן גרסה
   - `git add . && git commit -m "Update" && git push`

---

### ❌ שגיאה: "עדכון לא מתקין ב-WordPress"

**הבעיה:** ה-plugin לא מתעדכן.

**פתרון:**

1. **בדוק הגדרות:**
   - WordPress → Settings → Claude Summarizer
   - ודא ש-"עדכון אוטומטי" מופעל
   - ודא ש-GitHub Repository נכון

2. **בדוק ידנית:**
   - לחץ "בדוק עכשיו"
   - אם יש עדכון, לחץ "התקן עדכון עכשיו"

3. **בדוק הרשאות:**
   - ודא שיש הרשאות כתיבה ל-`wp-content/plugins/`
   - בדוק עם מנהל האתר

---

### ❌ שגיאה: "לא רואה עדכון זמין"

**הבעיה:** WordPress לא מוצא עדכון.

**פתרון:**

1. **בדוק ב-GitHub:**
   - לך ל-Repository → **Releases**
   - ודא שיש Release חדש

2. **בדוק את הגרסה:**
   - ודא שהגרסה ב-`claude-ai-summarizer.php` עדכנה
   - ודא שה-Release ב-GitHub עם אותה גרסה

3. **נסה שוב:**
   - לחץ "בדוק עכשיו" ב-WordPress
   - המתן כמה דקות ונסה שוב

---

### ❌ שגיאה: "command not found" ב-Terminal

**הבעיה:** Git לא מותקן.

**פתרון:**

1. **התקן Git:**
   ```bash
   # macOS
   xcode-select --install
   ```

2. **או התקן דרך Homebrew:**
   ```bash
   brew install git
   ```

---

### ❌ שגיאה: "Permission denied"

**הבעיה:** אין הרשאות לכתוב.

**פתרון:**

1. **בדוק הרשאות:**
   ```bash
   ls -la /Users/omerokon/Desktop/bf6/claude-ai-summarizer
   ```

2. **תקן הרשאות:**
   ```bash
   chmod -R 755 /Users/omerokon/Desktop/bf6/claude-ai-summarizer
   ```

---

## חלק ד': רשימת בדיקה

### לפני כל עדכון:

- [ ] עדכנת את הגרסה ב-`claude-ai-summarizer.php`
- [ ] שמרת את כל הקבצים ב-Cursor
- [ ] הרצת `git add .`
- [ ] הרצת `git commit -m "message"`
- [ ] הרצת `git push origin main`
- [ ] בדקת ב-GitHub → Actions (workflow רץ)
- [ ] בדקת ב-GitHub → Releases (Release נוצר)
- [ ] בדקת ב-WordPress → Settings → "בדוק עכשיו"

---

## חלק ה': טיפים

### גרסאות (Semantic Versioning)

- `1.0.0` → `1.0.1` = תיקון קטן (bug fix)
- `1.0.0` → `1.1.0` = תכונה חדשה (new feature)
- `1.0.0` → `2.0.0` = שינוי גדול (major change)

### Commit Messages

כתוב הודעות ברורות:
- `"Add custom button color"`
- `"Fix API error handling"`
- `"Update to v1.1.1"`

### בדיקה לפני Push

תמיד בדוק:
1. שהקוד עובד
2. שאין שגיאות
3. שהגרסה עדכנה

---

## סיכום - תהליך עדכון

```
1. ערוך ב-Cursor
   ↓
2. עדכן גרסה ב-claude-ai-summarizer.php
   ↓
3. שמור (⌘ + S)
   ↓
4. Terminal: git add . && git commit -m "message" && git push
   ↓
5. המתן 2-3 דקות
   ↓
6. WordPress → Settings → "בדוק עכשיו" → "התקן עדכון עכשיו"
   ↓
7. ✅ ה-plugin עודכן!
```

---

**זה הכל! 🎉**

**בעיות נוספות?** בדוק את ה-logs ב-GitHub Actions וב-WordPress.
