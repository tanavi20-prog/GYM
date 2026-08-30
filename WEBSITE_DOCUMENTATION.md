# RKT FitVibe - Complete Website Documentation

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [System Requirements](#system-requirements)
3. [Installation & Setup](#installation--setup)
4. [File Structure](#file-structure)
5. [Database Schema](#database-schema)
6. [Features & Functionality](#features--functionality)
7. [Page Documentation](#page-documentation)
8. [API Endpoints](#api-endpoints)
9. [User Roles & Permissions](#user-roles--permissions)
10. [Security Features](#security-features)
11. [Deployment Guide](#deployment-guide)
12. [Troubleshooting](#troubleshooting)
13. [Development Guidelines](#development-guidelines)

---

## 🎯 Project Overview

**RKT FitVibe** is a comprehensive fitness platform designed to help users achieve their fitness goals through personalized workout plans, expert trainer guidance, and community support.

### Key Features
- User registration and authentication
- Personalized fitness plans based on user goals
- Video workout library with YouTube integration
- Trainer profiles and workout programs
- Community features and success stories
- BMI and fitness calculators
- Progress tracking and analytics
- Responsive design for all devices

### Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap-inspired responsive design
- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Server**: Apache (WAMP/XAMPP)
- **External APIs**: YouTube API for video integration

---

## ⚙️ System Requirements

### Minimum Requirements
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache Web Server
- 500MB disk space
- 2GB RAM

### Recommended Development Environment
- WAMP Server 3.2+ (Windows)
- XAMPP 8.0+ (Cross-platform)
- phpMyAdmin for database management
- VS Code or similar IDE

---

## 🚀 Installation & Setup

### 1. Server Setup
```bash
# Download and install WAMP Server
# Start Apache and MySQL services
# Ensure PHP and MySQL are running
```

### 2. Database Setup
1. Access phpMyAdmin (http://localhost/phpmyadmin)
2. Create database: `fitness_db`
3. Import the database schema from `/database/schema.sql`
4. Import sample data from `/database/sample_data.sql`

### 3. Configuration
1. Copy the project to your web server directory:
   ```
   E:\wamp64\www\gymmm\
   ```

2. Update database configuration in `/includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'fitness_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('APP_URL', 'http://localhost/gymmm');
   ```

3. Set appropriate file permissions (if on Linux/Mac)

### 4. Access the Website
- Visit: `http://localhost/gymmm`
- Default admin credentials will be provided in setup

---

## 📁 File Structure

```
gymmm/
├── index.php                 # Main router and entry point
├── includes/
│   ├── config.php           # Database and app configuration
│   ├── session.php          # Session management
│   ├── functions.php        # Helper functions
│   └── auth.php             # Authentication functions
├── pages/
│   ├── home.php             # Homepage with hero and features
│   ├── login.php            # User login page
│   ├── registration.php     # User registration page
│   ├── dashboard.php        # User dashboard
│   ├── profile.php          # User profile management
│   ├── trainer.php          # Trainer listings and search
│   ├── plan.php             # Fitness plans and recommendations
│   ├── tool.php             # BMI and fitness calculators
│   ├── community.php        # Community features
│   ├── videos.php           # Video workout library
│   ├── video-player.php     # Individual video player
│   ├── explore-workouts.php # Workout exploration
│   ├── help.php             # Help center and FAQs
│   ├── about.php            # About us page
│   ├── diet.php             # Diet plans and nutrition
│   └── yoga.php             # Yoga-specific content
├── templates/
│   ├── layout.php           # Main layout template
│   ├── header.php           # Site header
│   └── footer.php           # Site footer
├── assets/
│   ├── css/
│   │   ├── style.css        # Main stylesheet
│   │   └── components.css   # Component-specific styles
│   ├── js/
│   │   ├── main.js          # Main JavaScript functions
│   │   └── components.js    # Component-specific JS
│   └── images/              # Static images
├── database/
│   ├── schema.sql           # Database structure
│   └── sample_data.sql      # Sample data for testing
└── admin/
    ├── dashboard.php        # Admin dashboard
    ├── manage-users.php     # User management
    └── manage-videos.php    # Video management
```

---

## 🗄️ Database Schema

### Core Tables

#### `users`
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    age INT,
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    fitness_goal ENUM('weight_loss', 'muscle_gain', 'endurance', 'general'),
    activity_level ENUM('sedentary', 'light', 'moderate', 'very_active', 'extra_active'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `trainers`
```sql
CREATE TABLE trainers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(200),
    experience_years INT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    location VARCHAR(100),
    bio TEXT,
    image_url VARCHAR(500),
    hourly_rate DECIMAL(8,2),
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `videos`
```sql
CREATE TABLE videos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    youtube_id VARCHAR(20) NOT NULL,
    category_id INT,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced'),
    duration_minutes INT,
    view_count INT DEFAULT 0,
    trainer_id INT,
    thumbnail_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES video_categories(id),
    FOREIGN KEY (trainer_id) REFERENCES trainers(id)
);
```

#### `video_categories`
```sql
CREATE TABLE video_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    icon VARCHAR(50)
);
```

#### `fitness_plans`
```sql
CREATE TABLE fitness_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_name VARCHAR(100),
    goal VARCHAR(50),
    duration_weeks INT,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced'),
    workout_days_per_week INT,
    plan_details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## ✨ Features & Functionality

### 1. User Management
- **Registration**: Email-based registration with profile setup
- **Authentication**: Secure login with session management
- **Profile Management**: Update personal information, fitness goals
- **Password Security**: Hashed passwords with PHP password_hash()

### 2. Fitness Planning
- **Goal-Based Plans**: Personalized plans based on user goals
- **BMI Calculator**: Calculate and track BMI with recommendations
- **Calorie Calculator**: Daily calorie needs based on activity level
- **Progress Tracking**: Track workouts, weight changes, achievements

### 3. Video Library
- **YouTube Integration**: Embedded YouTube videos for workouts
- **Category Filtering**: Filter by workout type, difficulty, duration
- **Search Functionality**: Search videos by title, trainer, or keywords
- **View Tracking**: Track video views and popular content

### 4. Trainer Network
- **Trainer Profiles**: Detailed profiles with specializations and ratings
- **Search & Filter**: Find trainers by location, specialization, price
- **Rating System**: User ratings and reviews for trainers
- **Booking System**: Schedule sessions with trainers

### 5. Community Features
- **Success Stories**: Share and view transformation stories
- **User Interactions**: Comments, likes, and community support
- **Achievement Sharing**: Share milestones and achievements

---

## 📄 Page Documentation

### Homepage (`pages/home.php`)
**Purpose**: Landing page showcasing platform features and success stories

**Key Sections**:
- Hero section with motivational quotes rotation
- Success stories carousel
- Popular workout programs
- Feature highlights
- Call-to-action sections
- Help and About navigation

**Dynamic Content**:
- Rotating motivational quotes every 2 seconds
- Conditional content based on login status
- Success story testimonials with before/after stats

### User Dashboard (`pages/dashboard.php`)
**Purpose**: Personalized user control panel

**Features**:
- Fitness plan progress
- Recent workout history
- Quick stats (BMI, calories, workouts completed)
- Upcoming trainer sessions
- Community activity feed

### Trainer Page (`pages/trainer.php`)
**Purpose**: Browse and filter available trainers

**Features**:
- Advanced filtering (location, specialization, rating, price)
- Trainer cards with ratings and availability
- Integration with video workouts
- Booking functionality
- Toggle-able workout video section

### Video Library (`pages/videos.php`)
**Purpose**: Browse workout videos by category

**Features**:
- Category-based filtering
- Difficulty level selection
- Duration filtering
- Search functionality
- YouTube video embedding

### Video Player (`pages/video-player.php`)
**Purpose**: Individual video viewing with related content

**Features**:
- Full YouTube video embedding
- View count tracking
- Related video suggestions
- Trainer information display
- Progress tracking integration

### Help Center (`pages/help.php`)
**Purpose**: User support and documentation

**Sections**:
- Getting Started Guide
- Frequently Asked Questions
- Troubleshooting
- Contact Form
- Feature Tutorials

### About Us (`pages/about.php`)
**Purpose**: Company information and team details

**Sections**:
- Mission and Vision
- Company Story
- Core Values
- Team Members
- Service Offerings

---

## 🔐 Security Features

### Authentication
- Password hashing using PHP `password_hash()`
- Session management with secure tokens
- CSRF protection for forms
- Input validation and sanitization

### Data Protection
- SQL injection prevention with prepared statements
- XSS protection through output escaping
- Secure file upload handling
- Database connection security

### Access Control
- Role-based permissions (user, trainer, admin)
- Session timeout handling
- Secure logout functionality
- Page access restrictions

---

## 🚀 Deployment Guide

### Production Setup
1. **Server Requirements**:
   - PHP 7.4+ with required extensions
   - MySQL 8.0+ database server
   - Apache web server with mod_rewrite
   - SSL certificate for HTTPS

2. **Configuration Updates**:
   ```php
   // config.php for production
   define('DB_HOST', 'your-db-host');
   define('DB_NAME', 'fitness_db_prod');
   define('DB_USER', 'db_username');
   define('DB_PASS', 'secure_password');
   define('APP_URL', 'https://yourdomain.com');
   define('DEBUG_MODE', false);
   ```

3. **Security Checklist**:
   - Enable HTTPS with SSL certificate
   - Set secure database credentials
   - Configure proper file permissions
   - Enable error logging (disable display errors)
   - Set up regular database backups

### Performance Optimization
- Enable gzip compression
- Optimize images and assets
- Implement caching strategies
- Database query optimization
- CDN integration for static assets

---

## 🔧 Troubleshooting

### Common Issues

#### Database Connection Errors
```
Problem: "Database connection failed"
Solution: 
1. Check MySQL service is running
2. Verify credentials in config.php
3. Ensure database exists
4. Check PHP MySQL extension is enabled
```

#### Session Issues
```
Problem: "Session not working / Users logged out frequently"
Solution:
1. Check session.save_path in php.ini
2. Verify session files permissions
3. Ensure cookies are enabled
4. Check session timeout settings
```

#### YouTube Videos Not Loading
```
Problem: "Videos not displaying or playing"
Solution:
1. Verify YouTube IDs are correct
2. Check internet connectivity
3. Ensure JavaScript is enabled
4. Verify YouTube API quotas (if using API)
```

#### CSS/JS Not Loading
```
Problem: "Styles or scripts not applying"
Solution:
1. Check file paths in templates
2. Verify Apache mod_rewrite is enabled
3. Clear browser cache
4. Check file permissions
```

---

## 👨‍💻 Development Guidelines

### Code Standards
- Follow PSR-12 PHP coding standards
- Use meaningful variable and function names
- Comment complex logic and functions
- Maintain consistent indentation (4 spaces)

### Database Best Practices
- Use prepared statements for all queries
- Index frequently queried columns
- Normalize data appropriately
- Regular backup procedures

### Security Guidelines
- Never store plain text passwords
- Validate all user inputs
- Escape all outputs
- Use HTTPS in production
- Regular security updates

### Testing Procedures
1. Test all user registration/login flows
2. Verify all database operations
3. Test responsive design on multiple devices
4. Cross-browser compatibility testing
5. Performance testing with sample data

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks
- Weekly database backups
- Monthly security updates
- Quarterly performance reviews
- Annual security audits

### Monitoring
- Server uptime monitoring
- Database performance tracking
- User activity analytics
- Error log monitoring

### Contact Information
- **Development Team**: [Your contact information]
- **Technical Support**: [Support contact]
- **Emergency Contact**: [Emergency contact]

---

## 📈 Future Enhancements

### Planned Features
- Mobile application development
- Advanced analytics dashboard
- Social media integration
- Payment gateway integration
- Multi-language support
- Advanced booking system
- Real-time chat with trainers

### Technical Improvements
- API development for mobile apps
- Database optimization
- Caching implementation
- CDN integration
- Microservices architecture

---

## 📝 Version History

### Version 1.0.0 (Current)
- Initial release with core features
- User registration and authentication
- Trainer profiles and video library
- Basic fitness planning tools

### Upcoming Releases
- Version 1.1.0: Enhanced video features
- Version 1.2.0: Advanced trainer booking
- Version 2.0.0: Mobile application

---

*Last Updated: January 2024*
*Documentation Version: 1.0*

For technical support or questions about this documentation, please contact the development team.