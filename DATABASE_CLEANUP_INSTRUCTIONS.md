# 🗄️ Database Cleanup Instructions

## Problem
Your admin panel is showing **OLD tables** from previous schema files:
- ❌ `workout_plans` (old)
- ❌ `workout_sessions` (old)
- ❌ `trainer_sessions` (old)
- ❌ `community_posts` (old)
- ❌ `community_comments` (old)
- ❌ `progress_tracking` (old)

## Solution
Import the **CLEAN_DATABASE.sql** file to get only the 7 correct tables.

---

## 📋 Correct Tables (What You Should Have)

1. ✅ **users** - User accounts
2. ✅ **trainers** - Trainer profiles
3. ✅ **plans** - Workout/diet plans (replaces workout_plans)
4. ✅ **sessions** - Training sessions (replaces workout_sessions & trainer_sessions)
5. ✅ **community** - Community posts (replaces community_posts & community_comments)
6. ✅ **video_categories** - Video categories
7. ✅ **videos** - Workout videos

---

## 🚀 Method 1: Using phpMyAdmin (Recommended)

### Step 1: Open phpMyAdmin
1. Go to: http://localhost/phpmyadmin
2. Login (usually no password needed)

### Step 2: Drop Old Database
1. Click on **"fitness_db"** in the left sidebar
2. Click the **"Operations"** tab
3. Scroll down to **"Remove database"**
4. Click **"Drop the database (DROP)"**
5. Click **OK** to confirm

### Step 3: Import Clean Database
1. Click **"Import"** tab at the top
2. Click **"Choose File"** button
3. Navigate to: `E:\wamp64\www\gymmm\CLEAN_DATABASE.sql`
4. Select the file
5. Click **"Go"** button at the bottom
6. Wait for "Import has been successfully finished" message

### Step 4: Verify
1. Click on **"fitness_db"** in left sidebar
2. You should see exactly **7 tables**:
   - community
   - plans
   - sessions
   - trainers
   - users
   - video_categories
   - videos

---

## 🚀 Method 2: Using MySQL Command Line

### Step 1: Open Command Prompt
```bash
cd E:\wamp64\www\gymmm
```

### Step 2: Login to MySQL
```bash
mysql -u root -p
```
(Press Enter if no password)

### Step 3: Import Database
```sql
source CLEAN_DATABASE.sql
```

### Step 4: Verify
```sql
USE fitness_db;
SHOW TABLES;
```
You should see exactly 7 tables.

---

## 🚀 Method 3: Using Admin Panel SQL Query Tool

### Step 1: Login to Admin Panel
Go to: http://localhost/gymmm/admin

### Step 2: Go to SQL Query
Click "SQL Query" in the sidebar

### Step 3: Run Drop Commands
Copy and paste these one at a time:

```sql
DROP TABLE IF EXISTS community_comments;
DROP TABLE IF EXISTS community_posts;
DROP TABLE IF EXISTS progress_tracking;
DROP TABLE IF EXISTS trainer_sessions;
DROP TABLE IF EXISTS workout_sessions;
DROP TABLE IF EXISTS workout_plans;
```

### Step 4: Verify Tables
In admin dashboard, you should now see only 7 tables.

---

## ⚠️ Important Notes

### Before Importing:
1. **Backup your data** if you have any important user/trainer data
2. **Export users table** if you have registered users you want to keep
3. The clean database includes sample videos and trainers

### After Importing:
1. **Test your website** - http://localhost/gymmm
2. **Check admin panel** - http://localhost/gymmm/admin
3. **Verify all 7 tables appear**
4. **Register a new test user** to confirm it works

### What Gets Reset:
- ❌ All user accounts (you'll need to register again)
- ❌ All custom trainers (sample trainers will be added)
- ❌ All community posts
- ❌ All sessions/bookings
- ✅ Videos and categories will be populated with samples

---

## 🎯 After Import Checklist

- [ ] Only 7 tables visible in admin panel
- [ ] No old tables (workout_plans, workout_sessions, etc.)
- [ ] Sample videos loaded (10 videos)
- [ ] Sample trainers loaded (4 trainers)
- [ ] Video categories loaded (8 categories)
- [ ] Homepage loads without errors
- [ ] Can register new user
- [ ] Can login with new user
- [ ] Videos page shows workout videos

---

## 🔄 Alternative: Keep Existing Data

If you want to keep your existing users/trainers but remove old tables:

### Option A: Export Important Data First
1. In phpMyAdmin, export these tables:
   - users
   - trainers (if you have custom trainers)
2. Import CLEAN_DATABASE.sql
3. Re-import your exported users/trainers tables

### Option B: Manually Drop Old Tables
Just run these SQL commands:
```sql
DROP TABLE IF EXISTS community_comments;
DROP TABLE IF EXISTS community_posts;
DROP TABLE IF EXISTS progress_tracking;
DROP TABLE IF EXISTS trainer_sessions;
DROP TABLE IF EXISTS workout_sessions;
DROP TABLE IF EXISTS workout_plans;
```

This keeps your existing data but removes duplicate/old tables.

---

## 📞 Need Help?

### Common Issues:

**Issue 1: "Table already exists" error**
- Solution: Drop the database first, then import

**Issue 2: "Cannot delete parent row" error**
- Solution: Drop tables in reverse order or use `DROP DATABASE`

**Issue 3: Admin panel still shows old tables**
- Solution: Clear your browser cache and refresh

**Issue 4: Lost my users after import**
- Solution: Export users table before importing next time

---

## ✅ Success Indicators

After a successful import, you should see:

### In Admin Panel:
- Dashboard shows 7 tables
- Users table (empty or with your data)
- 10 sample videos
- 8 video categories
- 4 sample trainers
- Clean, organized table list

### On Website:
- Videos page shows 10 workout videos
- Trainers page shows 4 trainers
- Can register and login
- No database errors

---

## 🎉 You're Done!

Your database is now clean with only the necessary 7 tables that match your application structure. The admin panel will show a clean, organized list of tables.

**File Location:** `E:\wamp64\www\gymmm\CLEAN_DATABASE.sql`
