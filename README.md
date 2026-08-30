# 🏋️ RKT FitVibe - Complete Fitness Platform

A comprehensive PHP-based fitness and gym management system with modern UI/UX design, video integration, and community features.

## ✨ Key Features

### 🔐 User Management
- **Registration & Authentication**: Secure user signup and login system
- **Profile Management**: Personalized fitness profiles with goals and preferences
- **Session Management**: Secure session handling and user state management

### 👨‍💼 Trainer Network
- **Trainer Profiles**: Detailed trainer information with specializations and ratings
- **Advanced Filtering**: Search by location, specialization, rating, and price
- **Booking System**: Schedule sessions with preferred trainers
- **Indian Trainers**: Featured trainers like Tanavi, Khushi, and Ritika

### 🎥 Video Workout Library
- **YouTube Integration**: Real YouTube videos embedded for workouts
- **Category Filtering**: Filter by workout type, difficulty, and duration
- **Video Player**: Dedicated player with view tracking and related videos
- **Trainer Integration**: Videos linked to trainer profiles

### 📋 Fitness Planning
- **Personalized Plans**: Custom workout and diet plans based on user goals
- **BMI Calculator**: Calculate and track Body Mass Index with recommendations
- **Calorie Calculator**: Daily calorie needs based on activity level and goals
- **Progress Tracking**: Monitor workouts, weight changes, and achievements

### 🤝 Community Features
- **Success Stories**: Real user transformations with before/after statistics
- **Social Interaction**: Community posts, comments, and support
- **Achievement Sharing**: Celebrate milestones and progress

### 🛠️ Tools & Calculators
- **BMI Calculator**: Body Mass Index calculation with health recommendations
- **Fitness Tools**: Various calculators for fitness planning
- **Progress Monitoring**: Track various fitness metrics

### 📱 Modern Design
- **Responsive Layout**: Works perfectly on desktop, tablet, and mobile
- **Modern UI/UX**: Clean, intuitive interface with smooth animations
- **Dark/Light Themes**: Theme switching capabilities
- **Accessibility**: Screen reader friendly and keyboard navigable

## 🗄️ Database

- **Database**: `fitness_db`
- **Tables**: users, trainers, plans, sessions, community
- **Connection**: Supports both PDO and mysqli

## 🚀 Quick Start Guide

### Prerequisites
- WAMP/XAMPP Server (Apache + PHP 7.4+ + MySQL 8.0+)
- Web browser with JavaScript enabled
- Internet connection (for YouTube videos)

### Installation Steps
1. **Server Setup**: Start your WAMP services (Apache + MySQL)
2. **Database**: Create `fitness_db` database and import schema
3. **Access**: Visit `http://localhost/gymmm/`
4. **Register**: Create your fitness profile
5. **Explore**: Browse trainers, videos, and fitness plans

### 📱 Navigation Guide

| Page | URL | Purpose |
|------|-----|----------|
| **Home** | `/?page=home` | Landing page with success stories and features |
| **Registration** | `/?page=registration` | Sign up and create fitness profile |
| **Login** | `/?page=login` | User authentication |
| **Dashboard** | `/?page=dashboard` | Personalized control panel |
| **Trainers** | `/?page=trainer` | Browse trainers + workout videos |
| **Videos** | `/?page=videos` | Workout video library |
| **Video Player** | `/?page=video-player` | Individual video viewing |
| **Fitness Plans** | `/?page=plan` | Personalized workout plans |
| **Tools** | `/?page=tool` | BMI & fitness calculators |
| **Community** | `/?page=community` | Success stories and social features |
| **Diet Plans** | `/?page=diet` | Nutrition and meal planning |
| **Yoga** | `/?page=yoga` | Yoga-specific content |
| **Help Center** | `/?page=help` | FAQs and support |
| **About Us** | `/?page=about` | Company information |

### 🎯 User Journey
1. **New Users**: Home → Registration → Profile Setup → Dashboard
2. **Existing Users**: Home → Login → Dashboard → Explore Features
3. **Trainer Search**: Dashboard → Trainers → Filter → Book Session
4. **Workout Videos**: Trainers → "Explore Workouts" → Video Player
5. **Fitness Planning**: Dashboard → Plans → BMI Calculator → Custom Plan

## 📁 Project Structure

```
gymmm/
├── index.php              # Main entry point
├── .htaccess              # URL rewriting rules
├── assets/                # CSS, images, and static files
├── pages/                 # Application pages
├── includes/              # Configuration and utilities
├── crud/                  # Database operations
├── api/                   # API endpoints
├── templates/             # Reusable templates
├── dev-tools/             # Development utilities
├── docs/                  # Documentation
└── README.md              # This file
```

## 🛠️ System Status

All core components are operational:
- ✅ PHP Runtime
- ✅ Database Connection  
- ✅ CSS Styling
- ✅ Image Assets
- ✅ CRUD Operations

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+ with session management
- **Database**: MySQL 8.0+ with prepared statements
- **Server**: Apache Web Server (WAMP/XAMPP)
- **External APIs**: YouTube Embed API for video integration
- **Security**: Password hashing, SQL injection prevention, XSS protection

## 📁 Database Structure

Core tables include:
- **users**: User profiles, goals, and authentication
- **trainers**: Trainer profiles with specializations and ratings
- **videos**: YouTube video library with categories and metadata
- **video_categories**: Workout categories (HIIT, Strength, Yoga, etc.)
- **fitness_plans**: Personalized workout and diet plans

## 👥 For Developers

### Development Environment
- **IDE**: VS Code recommended with PHP extensions
- **Debug Tools**: Available at `/dev-tools/` directory
- **Database Management**: phpMyAdmin interface
- **Version Control**: Git-ready project structure

### Key Development Features
- **MVC Pattern**: Organized code structure with separation of concerns
- **Prepared Statements**: SQL injection prevention
- **Session Security**: Secure user authentication and state management
- **Responsive Design**: Mobile-first CSS approach
- **Error Handling**: Comprehensive error logging and user feedback

## 📚 Documentation

For complete technical documentation, see:
- **`WEBSITE_DOCUMENTATION.md`**: Comprehensive technical guide
  - Installation & setup instructions
  - Complete database schema
  - Security implementation details
  - API documentation
  - Deployment guidelines
  - Troubleshooting guide

### Quick References
- **File Structure**: See documentation for complete directory layout
- **Database Schema**: Full table definitions and relationships
- **Security Features**: Authentication, validation, and protection measures
- **Deployment Guide**: Production setup and optimization

## 🚀 Recent Enhancements

### Latest Features (v1.0)
- ✅ **Video Integration**: Real YouTube videos embedded throughout platform
- ✅ **Trainer Profiles**: Enhanced with Indian trainers and specializations
- ✅ **Help Center**: Comprehensive FAQ and support system
- ✅ **About Page**: Company information and team details
- ✅ **Home Navigation**: Easy access to help and about from homepage
- ✅ **Video Player**: Dedicated player with view tracking and related content
- ✅ **Workout Explorer**: Toggle-able video section in trainer page

### Upcoming Features
- 🔄 **Payment Integration**: Trainer booking payments
- 🔄 **Mobile App**: React Native companion app
- 🔄 **Advanced Analytics**: Detailed progress tracking
- 🔄 **Social Features**: Enhanced community interactions

## 🖒 Support & Troubleshooting

### Common Issues
- **Videos not loading**: Check internet connection and YouTube accessibility
- **Database errors**: Verify WAMP MySQL service and credentials
- **Session issues**: Clear browser cookies and restart WAMP
- **Style problems**: Clear browser cache and check CSS file paths

### Getting Help
- Check `WEBSITE_DOCUMENTATION.md` for detailed troubleshooting
- Visit the Help Center page within the application
- Contact development team for technical support

## 📝 License & Copyright

© 2024 RKT FitVibe. All rights reserved.

This is proprietary software developed for the RKT FitVibe fitness platform. Unauthorized reproduction or distribution is prohibited.

---

**Transform Your Body, Transform Your Life** 💪

*Made with ❤️ by the RKT FitVibe development team*
