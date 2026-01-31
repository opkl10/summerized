# Claude AI Summarizer - WordPress Plugin

Plugin ל-WordPress לסיכום פוסטים ומאמרים חכם באמצעות Claude AI.

## תכונות

- ✅ **כפתור סיכום אוטומטי** - מוסיף כפתור "סכם עם AI" לכל פוסט
- ✅ **סיכום חכם** - שימוש ב-Claude AI לסיכום איכותי
- ✅ **תמיכה במודלים שונים** - Sonnet, Opus, Haiku
- ✅ **בחירת אורך סיכום** - קצר, בינוני, ארוך
- ✅ **Caching** - שמירת סיכומים למניעת קריאות מיותרות
- ✅ **Shortcode** - `[claude_summary]` להצגת סיכום
- ✅ **Gutenberg Block** - הוספת סיכום דרך ה-editor
- ✅ **עדכון אוטומטי** - עדכון מ-GitHub ללא צורך בהעלאה ידנית

## התקנה

### דרך 1: העלאה ידנית

1. הורד את התיקייה `claude-ai-summarizer`
2. העלה ל-`wp-content/plugins/`
3. הפעל את ה-plugin מ-WordPress Admin → Plugins

### דרך 2: דרך GitHub

```bash
cd wp-content/plugins
git clone https://github.com/YOUR_USERNAME/claude-ai-summarizer.git
```

## הגדרה

1. **קבל Claude API Key:**
   - היכנס ל-[Anthropic Console](https://console.anthropic.com/)
   - צור API Key חדש

2. **הגדר את ה-Plugin:**
   - עבור ל-Settings → Claude Summarizer
   - הכנס את ה-API Key
   - בחר מודל ואורך סיכום
   - הפעל "הצגת כפתור אוטומטית"

3. **מוכן לשימוש!**
   - הכפתור יופיע אוטומטית בכל פוסט
   - לחץ על "סכם עם AI" לסיכום

## שימוש

### כפתור אוטומטי

אם הפעלת "הצגת כפתור אוטומטית", הכפתור יופיע אוטומטית בכל פוסט.

### Shortcode

השתמש ב-shortcode להצגת סיכום:

```
[claude_summary]
```

עם פרמטרים:

```
[claude_summary post_id="123" length="long"]
```

### Gutenberg Block

1. פתח את ה-editor של פוסט
2. לחץ על "+" להוספת block
3. חפש "Claude Summary"
4. הוסף את ה-block

## עדכון אוטומטי

### מדריך מלא

ראה **`SETUP_GUIDE.md`** למדריך מפורט שלב-אחר-שלב:
- התקנה ראשונית
- איך לעדכן את ה-plugin
- פתרון כל הבעיות הנפוצות

### תהליך מהיר

1. ערוך ב-Cursor → עדכן גרסה → שמור
2. Terminal: `git add . && git commit -m "Update" && git push`
3. WordPress → Settings → "בדוק עכשיו" → "התקן עדכון עכשיו"

## מבנה הקבצים

```
claude-ai-summarizer/
├── claude-ai-summarizer.php    # קובץ ראשי
├── templates/
│   └── admin-page.php          # דף הגדרות
├── assets/
│   ├── frontend.css            # עיצוב frontend
│   ├── frontend.js             # סקריפט frontend
│   ├── admin.css               # עיצוב admin
│   └── admin.js                # סקריפט admin
└── README.md
```

## תמיכה

לבעיות או שאלות:
- פתח issue ב-GitHub
- צור קשר דרך האתר

## רישיון

GPL v2 or later

---

**הערה:** השימוש ב-Claude API כרוך בעלויות. בדוק את המחירים ב-[Anthropic Pricing](https://www.anthropic.com/pricing)
test
