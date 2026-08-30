# 🎯 Admin Panel User Guide

## Overview
Your improved admin panel now clearly shows the **7 core database tables** with better organization and descriptions.

---

## 🔐 Login Information

**URL:** http://localhost/gymmm/admin

**Credentials:**
- **Username:** admin
- **Password:** admin123

---

## 📊 Dashboard Layout

### 1. **Statistics Cards** (Top)
Shows key metrics at a glance:

- **Database Tables** - Should show "7" (7 core tables)
- **Total Records** - Combined count from all tables
- **Registered Users** - Number of user accounts
- **Workout Videos** - Number of videos in library

### 2. **Database Tables** (Main Section)
Lists all 7 tables with:
- ✅ **Icon** - Visual identifier
- ✅ **Name** - Clear table name
- ✅ **Description** - What the table stores
- ✅ **Statistics** - Record count and size
- ✅ **Action Button** - "View & Edit" button

---

## 🗄️ The 7 Core Tables

### 1. 👥 **Users**
- **What:** Registered users and their profiles
- **Contains:** Email, name, age, gender, fitness goals
- **Actions:** View, add, edit, delete user accounts

### 2. 🏋️ **Trainers**
- **What:** Personal trainers and fitness coaches
- **Contains:** Name, specialties, rating, experience
- **Actions:** Manage trainer profiles

### 3. 📋 **Plans**
- **What:** Workout and diet plans
- **Contains:** Plan type, difficulty, duration, price
- **Actions:** Create and manage fitness plans

### 4. 📅 **Sessions**
- **What:** Training sessions and bookings
- **Contains:** User, trainer, date, status
- **Actions:** Schedule and track sessions

### 5. 💬 **Community**
- **What:** Community posts and discussions
- **Contains:** Title, content, type, likes
- **Actions:** Moderate community content

### 6. 📁 **Video Categories**
- **What:** Video category types
- **Contains:** Name, icon, color, description
- **Actions:** Manage video classifications

### 7. 🎥 **Videos**
- **What:** Workout video library
- **Contains:** Title, YouTube URL, difficulty, duration
- **Actions:** Add, edit, remove workout videos

---

## 🎨 Sidebar Navigation (Organized)

The sidebar is now organized into clear sections:

### 📊 **Core Tables**
- Users
- Trainers
- Plans
- Sessions

### 💬 **Community**
- Posts

### 🎥 **Videos**
- All Videos
- Categories

### 🛠️ **Tools**
- SQL Query
- Export Data

---

## ✨ Key Features

### 1. **View & Edit Records**
Click any table's "View & Edit" button to:
- 📋 See all records in a table
- ➕ Add new records
- ✏️ Edit existing records
- 🗑️ Delete records
- 🔍 Search through data
- 📊 Sort by any column
- 📄 Navigate with pagination

### 2. **Search Functionality**
- Type in the search box to filter records
- Searches across all columns
- Instant results

### 3. **Export Data**
- Export any table to CSV format
- Export multiple tables as ZIP
- Excel-compatible format

### 4. **SQL Query Tool**
- Run custom SELECT queries
- View database schema
- Try sample queries
- Safe (only SELECT allowed)

---

## 📖 How to Use

### Viewing Table Data
1. Click Dashboard in sidebar
2. Find the table you want
3. Click "View & Edit" button
4. Browse the records

### Adding New Records
1. Open any table
2. Click "Add New Record" button
3. Fill in the form
4. Click "Add Record"

### Editing Records
1. Open any table
2. Click the blue edit ✏️ button on a record
3. Modify the fields
4. Click "Update Record"

### Deleting Records
1. Open any table
2. Click the red delete 🗑️ button on a record
3. Confirm deletion

### Searching Data
1. Open any table
2. Type in the search box at top
3. Press Enter or click away
4. Results filter automatically

### Exporting Data
1. Click "Export Data" in sidebar
2. Choose individual table or multiple tables
3. Click export button
4. File downloads automatically

---

## 🎯 Common Tasks

### Task 1: Add a New User
1. Go to Users table
2. Click "Add New Record"
3. Fill in: email, password, name, age, etc.
4. Click "Add Record"

### Task 2: Add a Workout Video
1. Go to Videos table
2. Click "Add New Record"
3. Fill in: title, YouTube URL, difficulty
4. Select category
5. Click "Add Record"

### Task 3: Manage Trainers
1. Go to Trainers table
2. View all trainer profiles
3. Add new trainers or edit existing ones

### Task 4: View User Activity
1. Go to Sessions table
2. See all training sessions
3. Filter by user or trainer

### Task 5: Export Database Backup
1. Go to Export Data
2. Select all tables
3. Click "Export Selected as ZIP"
4. Save the file

---

## 🔧 Understanding the Database

### Data Relationships

```
Users ────┐
          ├──> Sessions <── Trainers
          │         └──> Plans
          └──> Community

Videos ──> Video Categories
```

- **Users** can have **Sessions** with **Trainers**
- **Sessions** can use **Plans**
- **Users** create **Community** posts
- **Videos** belong to **Video Categories**

---

## ⚠️ Important Notes

### Security
- ✅ Only you (admin) can access this panel
- ✅ CSRF protection on all forms
- ✅ SQL injection protection
- ✅ Session timeout after 30 minutes

### Data Safety
- ⚠️ Deleted records cannot be recovered
- ⚠️ Always confirm before deleting
- ⚠️ Export backups regularly
- ✅ All changes are instant

### Best Practices
1. **Regular Backups** - Export data weekly
2. **Test Changes** - Try with test data first
3. **Check Dependencies** - Some deletions affect other tables
4. **Use Search** - Find records quickly before editing

---

## 🐛 Troubleshooting

### "No tables showing"
**Solution:** Import the CLEAN_DATABASE.sql file

### "Wrong number of tables"
**Solution:** You have old duplicate tables. Run database cleanup.

### "Can't add/edit records"
**Solution:** Check required fields (marked with *)

### "Session expired"
**Solution:** Login again. Sessions timeout after 30 minutes.

### "Table not found"
**Solution:** Refresh the page or go back to dashboard

---

## 📚 Table Field Reference

### Users Table Fields
- `id` - Auto-generated
- `email` - User's email (unique)
- `password` - Hashed password
- `name` - Full name
- `age` - Age in years
- `gender` - male/female/other
- `weight` - Current weight (kg)
- `height` - Height (cm)
- `fitnessgoal` - Fitness objective
- `dietarypreference` - Diet type
- `activitylevel` - beginner/intermediate/advanced

### Videos Table Fields
- `id` - Auto-generated
- `title` - Video title
- `description` - Video description
- `youtube_url` - Full YouTube URL
- `youtube_id` - Video ID from URL
- `duration_minutes` - Video length
- `difficulty` - beginner/intermediate/advanced
- `category_id` - Links to video_categories
- `instructor_name` - Trainer name
- `calories_per_minute` - Burn rate

### Trainers Table Fields
- `id` - Auto-generated
- `name` - Trainer name
- `email` - Contact email
- `phone` - Phone number
- `specialties` - JSON array of skills
- `experience_years` - Years of experience
- `rating` - 0.0 to 5.0
- `hourly_rate` - Rate per hour
- `bio` - Profile description
- `certifications` - JSON array
- `available` - TRUE/FALSE

---

## 🎉 Tips & Tricks

1. **Use Keyboard Shortcuts**
   - Ctrl+Enter in SQL Query to execute
   - Tab in forms for quick navigation

2. **Sort Tables**
   - Click any column header to sort
   - Click again to reverse order

3. **Quick Navigation**
   - Use sidebar for fast table switching
   - Use Dashboard for overview

4. **Batch Operations**
   - Export multiple tables at once
   - Use SQL Query for bulk updates

5. **Mobile Access**
   - Admin panel works on tablets
   - Sidebar collapses on mobile

---

## 📞 Support

If you encounter issues:
1. Check this guide first
2. Try refreshing the page
3. Clear browser cache
4. Re-import CLEAN_DATABASE.sql if needed
5. Check that WAMP/XAMPP is running

---

## ✅ Quick Reference

| Task | Location | Action |
|------|----------|--------|
| View all tables | Dashboard | Overview of all 7 tables |
| Add user | Users table | "Add New Record" button |
| Add video | Videos table | "Add New Record" button |
| Export backup | Export Data | Select all → Export ZIP |
| Run query | SQL Query | Type query → Execute |
| Search records | Any table | Use search box at top |
| Edit record | Any table | Click blue edit button |
| Delete record | Any table | Click red delete button |

---

**Last Updated:** After clean database import
**Tables Count:** 7 core tables
**Admin Panel Version:** Enhanced & Improved
