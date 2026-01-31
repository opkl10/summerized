# 🚀 עדכון גרסה אוטומטי - מדריך

## איך זה עובד?

כשאתה מעדכן את הגרסה בקוד (`claude-ai-summarizer.php`), GitHub Actions יזהה את השינוי וייצור Release אוטומטית!

---

## 📝 שיטה 1: עדכון ידני

1. פתח את `claude-ai-summarizer.php`
2. עדכן את הגרסה בשני מקומות:
   ```php
   * Version: 1.3.1  // כאן
   
   define('CLAUDE_SUMMARIZER_VERSION', '1.3.1');  // וגם כאן
   ```
3. שמור את הקובץ
4. דחוף ל-GitHub:
   ```bash
   cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
   git add claude-ai-summarizer.php
   git commit -m "Update version to 1.3.1"
   git push origin main
   ```
5. GitHub Actions ייצור Release אוטומטית תוך 1-2 דקות! ✅

---

## 🎯 שיטה 2: עדכון אוטומטי עם Script

יש לך script שמעדכן את הגרסה אוטומטית:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer
./update-version.sh [major|minor|patch] [commit message]
```

### דוגמאות:

**עדכון patch (1.3.1 → 1.3.2):**
```bash
./update-version.sh patch "תיקון באג"
```

**עדכון minor (1.3.1 → 1.4.0):**
```bash
./update-version.sh minor "הוספת תכונה חדשה"
```

**עדכון major (1.3.1 → 2.0.0):**
```bash
./update-version.sh major "שינוי גדול"
```

### מה ה-Script עושה?

1. ✅ בודק שאין שינויים לא שמורים
2. ✅ מעדכן את הגרסה בשני המקומות בקוד
3. ✅ שומר ב-git
4. ✅ דוחף ל-GitHub (אם תרצה)
5. ✅ GitHub Actions ייצור Release אוטומטית!

---

## 🔍 איך לבדוק שהכל עובד?

### 1. בדוק את GitHub Actions:
- לך ל: https://github.com/opkl10/summerized/actions
- תראה את ה-workflow רץ
- אם יש שגיאה, תראה אותה שם

### 2. בדוק את Releases:
- לך ל: https://github.com/opkl10/summerized/releases
- תראה את ה-Release החדש עם קובץ ZIP

### 3. בדוק ב-WordPress:
- לך ל: **Settings → Claude Summarizer → עדכון אוטומטי**
- לחץ **"בדוק עכשיו"**
- תראה את הגרסה החדשה!

---

## ⚠️ חשוב לדעת:

### GitHub Actions יוצר Release רק אם:
- ✅ הגרסה השתנתה (לא יוצר Release כפול)
- ✅ יש שינויים בקוד (PHP, CSS, JS)
- ✅ ה-push הוא ל-`main` branch

### אם הגרסה לא השתנתה:
- ⏭️ GitHub Actions ידלג על יצירת Release
- תראה הודעה: "Version unchanged. Skipping release."

---

## 🐛 פתרון בעיות:

### Release לא נוצר?

1. **בדוק את GitHub Actions:**
   - לך ל-Actions ב-GitHub
   - בדוק אם יש שגיאות

2. **ודא שהגרסה השתנתה:**
   - בדוק שהגרסה ב-`claude-ai-summarizer.php` שונה מהגרסה האחרונה ב-Releases

3. **ודא שיש שינויים בקוד:**
   - GitHub Actions רץ רק אם יש שינויים ב-PHP, CSS, או JS

4. **בדוק את ה-logs:**
   - ב-GitHub Actions, לחץ על ה-run הכושל
   - תראה את ה-logs עם הסיבה

---

## 💡 טיפים:

1. **תמיד עדכן את הגרסה לפני דחיפה:**
   ```bash
   ./update-version.sh patch "תיקון"
   ```

2. **בדוק את ה-Release אחרי יצירה:**
   - ודא שיש קובץ ZIP
   - ודא שהגרסה נכונה

3. **השתמש ב-commit messages ברורים:**
   ```bash
   ./update-version.sh minor "הוספת אפשרות צבע חלונית"
   ```

---

## 📚 עוד מידע:

- **SETUP_GUIDE.md** - מדריך הגדרה מלא
- **UPDATE_SYSTEM_GUIDE.md** - מדריך מפורט למערכת העדכונים
- **push-to-github.sh** - script לדחיפה ל-GitHub

---

**עכשיו אתה יכול לעדכן גרסאות בקלות! 🎉**
