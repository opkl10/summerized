# 📦 עדכון ידני - הוראות

## למה עדכון ידני?

אם יש בעיות עם מערכת העדכונים האוטומטית, עדיף לעדכן ידנית פעם אחת כדי לוודא שהכל עובד.

---

## שלב 1: צור ZIP מהפלאגין

### דרך 1: דרך Terminal (מומלץ)

```bash
cd /Users/omerokon/Desktop/bf6
zip -r claude-ai-summarizer.zip claude-ai-summarizer/ \
  -x "*.git*" ".github/*" "*.md" "*.sh" "*.txt" "FIX_*" "QUICK_*" "START_*" "RESET_*" "UPDATE_*" "NEXT_*" "MANUAL_*"
```

זה ייצור קובץ `claude-ai-summarizer.zip` בתיקייה `bf6`.

### דרך 2: דרך Finder

1. לך ל-`/Users/omerokon/Desktop/bf6/claude-ai-summarizer`
2. בחר את כל הקבצים והתיקיות (חוץ מ-`.git`)
3. לחץ ימין → "Compress"
4. שנה שם ל-`claude-ai-summarizer.zip`

---

## שלב 2: העלה ל-WordPress

### דרך 1: דרך WordPress Admin (מומלץ)

1. לך ל-**WordPress Admin** → **Plugins**
2. **הסר את הפלאגין הישן:**
   - לחץ **"Deactivate"** על "Claude AI Summarizer"
   - לחץ **"Delete"** (זה לא ימחק את ההגדרות!)
3. **העלה את החדש:**
   - לחץ **"Add New"**
   - לחץ **"Upload Plugin"**
   - בחר את `claude-ai-summarizer.zip`
   - לחץ **"Install Now"**
   - לחץ **"Activate Plugin"**

### דרך 2: דרך FTP/SSH

1. **הסר את התיקייה הישנה:**
   ```bash
   # דרך SSH
   rm -rf /path/to/wordpress/wp-content/plugins/claude-ai-summarizer
   ```

2. **העלה את החדשה:**
   - פתח את ה-ZIP
   - העתק את התיקייה `claude-ai-summarizer` ל-`wp-content/plugins/`

3. **הפעל:**
   - לך ל-WordPress Admin → Plugins
   - הפעל את "Claude AI Summarizer"

---

## שלב 3: בדוק שהכל עובד

1. **בדוק שמירה:**
   - לך ל-Settings → Claude Summarizer
   - שנה הגדרה כלשהי
   - לחץ "שמור שינויים"
   - אמור לראות הודעת הצלחה ודף ההגדרות הרגיל

2. **בדוק עדכון אוטומטי:**
   - ודא ש-"עדכון אוטומטי" מסומן
   - ודא ש-"GitHub Repository" מוגדר: `opkl10/summerized`
   - לחץ "בדוק עכשיו"

---

## מה הלאה?

לאחר שהעדכון הידני עובד, אתה יכול:

1. **להמשיך עם עדכונים ידניים** - כל פעם שתעדכן, צור ZIP חדש והעלה
2. **להפעיל עדכונים אוטומטיים** - הפלאגין יבדוק כל 2 שעות ויודיע על עדכונים

---

## טיפים

- **שמור את ה-ZIP** - אם משהו לא עובד, תוכל לחזור לגרסה הקודמת
- **גבה את ההגדרות** - לפני עדכון, העתק את ההגדרות (API Key, וכו')
- **בדוק את הגרסה** - אחרי עדכון, ודא שהגרסה עודכנה ב-Settings

---

**זה הכל! 🎉**
