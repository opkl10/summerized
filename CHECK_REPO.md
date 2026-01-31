# 🔍 בדיקת Repository

## הבעיה

השגיאה "Repository not found" יכולה להופיע גם אם ה-repository קיים, אבל **אין בו Release**.

## איך לבדוק:

### 1. בדוק שה-repository קיים

פתח בדפדפן:
```
https://github.com/opkl10/summerized
```

אם זה לא עובד, בדוק:
- האם שם המשתמש נכון? (`opkl10`)
- האם שם ה-repository נכון? (`summerized`)

### 2. בדוק אם יש Releases

פתח:
```
https://github.com/opkl10/summerized/releases
```

**אם אין Releases:**
- זה הבעיה! צריך ליצור Release ראשון

### 3. איך ליצור Release

**אם יש GitHub Actions:**
1. דחוף שינוי ל-GitHub
2. GitHub Actions ייצור Release אוטומטית

**אם אין GitHub Actions:**
1. לך ל-GitHub → `opkl10/summerized` → **Releases**
2. לחץ **"Create a new release"**
3. מלא:
   - **Tag**: `v1.2.1` (לפי הגרסה ב-`claude-ai-summarizer.php`)
   - **Title**: `Release v1.2.1`
   - **Description**: תיאור השינויים
4. **חשוב:** העלה קובץ ZIP של הפלאגין כ-Asset
5. לחץ **"Publish release"**

---

## פתרון מהיר:

אם אין Release, צור אחד:

```bash
cd /Users/omerokon/Desktop/bf6/claude-ai-summarizer

# צור ZIP
cd ..
zip -r claude-ai-summarizer-1.2.1.zip claude-ai-summarizer/ \
  -x "*.git*" ".github/*" "*.md" "*.sh" "FIX_*" "QUICK_*" "START_*" "RESET_*" "UPDATE_*" "NEXT_*" "MANUAL_*" "CHECK_*"

# העלה ל-GitHub Releases ידנית
```

---

**אחרי יצירת Release, הפלאגין יזהה את העדכון!** ✅
