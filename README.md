# 🍲 MediMeal (Medi-ආහාර)

**Intelligent Medical Meal Recommendation System for Chronic Conditions**

A web-based application that provides personalized meal recommendations for patients with chronic medical conditions, powered by AI logic and Sri Lankan cuisine. MediMeal matches food choices to medical conditions while respecting user preferences and allergies.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Setup Steps](#setup-steps)
  - [Verification](#verification)
- [User Guide](#user-guide)
  - [Getting Started](#getting-started)
  - [Using the Recommendation System](#using-the-recommendation-system)
  - [Viewing Your Profile & History](#viewing-your-profile--history)
- [How It Works](#how-it-works)
  - [System Architecture](#system-architecture)
  - [Supported Medical Conditions](#supported-medical-conditions)
  - [Multi-Disease Logic](#multi-disease-logic)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Development & Extending](#development--extending)
  - [Adding New Meals](#adding-new-meals)
  - [Adding New Medical Conditions](#adding-new-medical-conditions)
  - [Adding New Allergies](#adding-new-allergies)
- [Roadmap](#roadmap)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

---

## Features

✅ **AI-Powered Recommendations** — Uses SWI-Prolog logic engine (not just database queries)  
✅ **Multi-Condition Support** — Meals safe for 2+ simultaneous chronic diseases  
✅ **Persistent History** — Users can review and track past recommendations  
✅ **Medical Accuracy** — Every meal tagged with disease-safety levels and preparation notes  
✅ **Localization** — Sri Lankan-focused cuisine with Sinhala language support  
✅ **Allergy Safety** — Explicit allergen tagging for all meals  
✅ **User Profiles** — Track dietary preferences, medical history, and recommendations  
✅ **Fully Responsive** — Modern UI with particle animations and ambient gradients  

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| **Backend** | PHP 8.2.12+ with MySQLi |
| **Database** | MySQL/MariaDB 10.4.32 (port 3307) |
| **AI Engine** | SWI-Prolog — Logic-based meal recommendation |
| **Frontend** | HTML5, CSS3 (custom design system), JavaScript |
| **Authentication** | PHP Sessions |
| **Server** | XAMPP (Apache) |

---

## Installation

### Prerequisites

Before setting up MediMeal, ensure you have:

- **XAMPP** (Apache, PHP 8.2.12+, MySQL 10.4.32)
  - Download from: https://www.apachefriends.org/
  - PHP should be in `C:\xampp\php\`
  - MySQL should run on port 3307 (configured in XAMPP)

- **SWI-Prolog** (latest version)
  - Download from: https://www.swi-prolog.org/
  - Install at: `C:\Program Files\swipl\bin\swipl.exe`
  - Verify installation: Open Command Prompt and run `swipl --version`

- **Git** (optional, for cloning the repository)
  - Download from: https://git-scm.com/

### Setup Steps

#### 1. Download/Clone the Project

```bash
# Option A: Using Git (if installed)
git clone <repository-url>
cd First_Year_asma

# Option B: Manual download
# Extract the project folder to: C:\xampp\htdocs\First_Year_software_project\
```

Ensure the project is at: `C:\xampp\htdocs\First_Year_software_project\`

#### 2. Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** for both Apache and MySQL
   - Apache should show status "Running" on port 80
   - MySQL should show status "Running" on port 3307

#### 3. Import Database

1. Open browser and go to: `http://localhost/phpmyadmin/`
2. Click **Databases** tab
3. Create a new database:
   - **Database name:** `medimeal`
   - **Collation:** `utf8mb4_unicode_ci`
   - Click **Create**
4. Select the `medimeal` database
5. Click **Import** tab
6. Choose file: `medimeal.sql` (in your project folder)
7. Click **Import**
   - You should see success message and tables created

#### 4. Verify Database Configuration

1. Open [database.php](database.php) in your editor
2. Verify the connection settings:
   ```php
   $servername = "127.0.0.1:3307";  // MySQL host and port
   $username = "root";               // MySQL user
   $password = "";                   // MySQL password (empty)
   $database = "medimeal";           // Database name
   ```
3. Save if any changes needed

#### 5. Verify Prolog Path

1. Open [recommend.php](recommend.php)
2. Verify the Prolog executable path (should be in the `shell_exec()` call):
   ```php
   $prolog_path = "C:\\Program Files\\swipl\\bin\\swipl.exe";
   ```
3. Adjust if SWI-Prolog is installed elsewhere

### Verification

1. Open browser and go to: `http://localhost/First_Year_software_project/`
   - You should see the MediMeal home page
2. Click **Sign Up** and create a test account
3. Login with your test credentials
4. Click **Get Recommendations**
5. Select any medical condition, age group, diet preference, and allergies
6. Click **Recommend** — you should see meal suggestions
   - ✅ If meals appear → Installation successful!
   - ❌ If error → See [Troubleshooting](#troubleshooting) section

---

## User Guide

### Getting Started

#### Register an Account

1. Go to `http://localhost/First_Year_software_project/`
2. Click **Sign Up** button
3. Enter your details:
   - Full Name
   - Email
   - Password (at least 8 characters recommended)
4. Click **Register**
5. You'll be redirected to login page

#### Login

1. Click **Sign In** button on home page
2. Enter your email and password
3. Click **Login**
4. You're now in the main dashboard

### Using the Recommendation System

1. Click **Get Recommendations** from the dashboard
2. Select your medical conditions (you can choose multiple):
   - Diabetes
   - Hypertension (High Blood Pressure)
   - Heart Disease
   - CKD (Chronic Kidney Disease)
   - High Cholesterol
3. Choose your age group:
   - Child, Young Adult, Adult, Elderly
4. Select your diet preference:
   - Vegetarian
   - Non-Vegetarian
5. Declare any allergies you have:
   - Milk, Egg, Peanut, Tree Nuts, Fish, Shellfish, Soy, Wheat, Sesame
6. Click **Get Meal Recommendations**
7. View personalized meal suggestions based on your selections
   - Each meal shows: Name, Type (Breakfast/Lunch/Dinner), Preparation notes

### Viewing Your Profile & History

1. Click **My Profile** from the dashboard
2. View your account information:
   - User stats
   - Total recommendations made
   - Recommendation history
3. See all past meal recommendations with timestamps
   - Click on any recommendation to see which conditions/allergies were selected

---

## How It Works

### System Architecture

The MediMeal recommendation system uses a **Prolog-based AI engine** to intelligently match meals to medical conditions.

**Request Flow:**

```
1. User selects conditions, age, preference, allergies
   ↓
2. Frontend (Front_End.php) sends JSON to backend
   ↓
3. Backend (recommend.php) validates input and maps to Prolog atoms
   Example: "Diabetes" → "diabetes", "High BP" → "hypertension"
   ↓
4. Backend calls SWI-Prolog with built query
   Example: recommend([diabetes,hypertension],adult,veg,[peanut])
   ↓
5. Prolog engine (foodie.pl) evaluates meal predicates
   - Checks each meal against disease safety rules
   - Filters by age appropriateness
   - Applies diet preference filter
   - Removes allergen-tagged meals
   ↓
6. Prolog returns matching meals to backend
   ↓
7. Backend parses output, saves to database
   (recommendation_history table with timestamp)
   ↓
8. Frontend receives JSON response and displays meals
```

### Supported Medical Conditions

| Condition | Meals Optimized For |
|-----------|-------------------|
| **Diabetes** | Low sugar, low oil, high fiber meals |
| **Hypertension** | Low sodium, low oil meals |
| **Heart Disease** | Low cholesterol, low saturated fat meals |
| **CKD** (Chronic Kidney Disease) | Low potassium, low phosphorus, low sodium meals |
| **High Cholesterol** | Low cholesterol, high fiber meals |

### Multi-Disease Logic

**Key Feature:** When selecting 2+ conditions, MediMeal finds meals that are safe for **ALL** selected diseases simultaneously (intersection-safe).

**Example:**
- User selects: Diabetes + Hypertension
- MediMeal returns only meals that are:
  - Safe for diabetics (low sugar)
  - **AND** safe for hypertensive patients (low sodium)
  - No meal that's good for one but bad for the other

---

## Project Structure

```
First_Year_software_project/
├── README.md                    # This file
├── database.php                 # MySQL connection configuration
├── medimeal.sql                 # Database schema and initial data
├── foodie.pl                    # Prolog AI engine (meal database & logic)
│
├── Front_End.php                # Main recommendation interface
├── recommend.php                # Backend API for recommendations
├── userProfile.php              # User profile and history page
│
├── home.php                     # Landing page with intro
├── register.php                 # Registration form
├── login.php                    # Login form
├── SignUp.php                   # Alternative signup page
├── SignIn.php                   # Alternative signin page
├── logout.php                   # Session cleanup
│
├── images/                      # Images and assets folder
│   └── (meal images, logos, etc.)
```

---

## API Documentation

### Recommendation Endpoint

**POST** `/recommend.php`

#### Request Parameters

```json
{
  "diseases": ["diabetes", "hypertension"],
  "age": "adult",
  "preference": "vegetarian",
  "allergies": ["peanut", "milk"]
}
```

| Parameter | Type | Required | Values |
|-----------|------|----------|--------|
| `diseases` | Array | Yes | `["diabetes", "hypertension", "heart_disease", "ckd", "high_cholesterol"]` |
| `age` | String | Yes | `"child"`, `"young"`, `"adult"`, `"elderly"` |
| `preference` | String | Yes | `"vegetarian"`, `"non_vegetarian"` |
| `allergies` | Array | No | `["milk", "egg", "peanut", "tree_nuts", "fish", "shellfish", "soy", "wheat", "sesame"]` |

#### Response Format

**Success (200 OK):**
```json
{
  "status": "success",
  "meals": [
    {
      "name": "Kurakkan Roti with Dhal Curry",
      "type": "breakfast",
      "notes": "No salt, no sugar, low oil",
      "safe_for": ["diabetes", "hypertension"]
    },
    {
      "name": "Ash Plantain Curry with Gotu Kola",
      "type": "lunch",
      "notes": "Prepared with minimal oil",
      "safe_for": ["hypertension", "heart_disease"]
    }
  ],
  "count": 2,
  "recommendation_id": 42
}
```

**Error (400 Bad Request):**
```json
{
  "status": "error",
  "message": "Invalid disease selected. Choose from: diabetes, hypertension, heart_disease, ckd, high_cholesterol"
}
```

#### Example Request (JavaScript)

```javascript
fetch('/First_Year_asma/recommend.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    diseases: ['diabetes', 'hypertension'],
    age: 'adult',
    preference: 'vegetarian',
    allergies: ['peanut']
  })
})
.then(response => response.json())
.then(data => {
  if (data.status === 'success') {
    console.log('Meals:', data.meals);
  } else {
    console.error('Error:', data.message);
  }
});
```

---

## Development & Extending

### Adding New Meals

To add a new meal recommendation:

1. **Edit [foodie.pl](foodie.pl)**

2. **Find the appropriate meal section:**
   - Breakfast meals: `% BREAKFAST MEALS`
   - Lunch meals: `% LUNCH MEALS`
   - Dinner meals: `% DINNER MEALS`

3. **Add a new meal predicate:**
   ```prolog
   breakfast_meal('Ragi Porridge with Gotu Kola', veg, [], [diabetes, hypertension]).
   ```

   **Syntax:**
   ```prolog
   breakfast_meal('MEAL_NAME', DIET_TYPE, [ALLERGIES], [SAFE_FOR_DISEASES]).
   ```

   - `DIET_TYPE`: `veg` or `non_veg`
   - `[ALLERGIES]`: List of allergens (e.g., `[milk, egg]` or `[]` for none)
   - `[SAFE_FOR_DISEASES]`: List of diseases this meal is safe for

4. **Test the new meal:**
   - Open Command Prompt
   - Navigate to project folder
   - Run: `swipl -s foodie.pl`
   - Test query: `?- breakfast_meal('Ragi Porridge with Gotu Kola', D, A, S).`
   - You should see: `D = veg, A = [], S = [diabetes, hypertension]`

### Adding New Medical Conditions

To add a new medical condition:

1. **Update [Front_End.php](Front_End.php)**
   - Find the condition dropdown/selector
   - Add new option: `<option value="new_condition">New Condition</option>`

2. **Update [foodie.pl](foodie.pl)**
   - Add the new condition to any relevant meal predicates
   - Example: If adding "Obesity", update meals with `[obesity]` in their safe_for list
   - Create Prolog rules for the new condition if needed

3. **Update [recommend.php](recommend.php)**
   - Add validation for the new condition name in the input validation section
   - Map the UI value to Prolog atom if different

4. **Update [medimeal.sql](medimeal.sql)**
   - Optional: Update database documentation/comments if you're storing conditions elsewhere

5. **Test thoroughly:**
   - Register a test account
   - Select new condition on Front_End
   - Verify meals returned are correct

### Adding New Allergies

Similar to conditions:

1. **Update [Front_End.php](Front_End.php)** — Add allergy checkbox/option
2. **Update [foodie.pl](foodie.pl)** — Tag meals with the new allergy
3. **Update [recommend.php](recommend.php)** — Add to allergy validation list
4. **Test** — Verify allergy filtering works

---

## Roadmap

### Version 1.1 (Q3 2026)
- [ ] Real-time meal search and filtering
- [ ] Favorite meals bookmark system
- [ ] Meal nutritional information display (calories, protein, etc.)

### Version 1.2 (Q4 2026)
- [ ] Multi-language support (Tamil, English variants)
- [ ] Export recommendation history as PDF
- [ ] Email recommendations to users

### Version 2.0 (2027)
- [ ] Mobile app (React Native — iOS & Android)
- [ ] Nutritional calculator integration
- [ ] Doctor dashboard for patient monitoring
- [ ] Real-time meal search with advanced filters
- [ ] Integration with food delivery services

### Version 3.0+ (Future)
- [ ] Machine learning to predict user preferences
- [ ] Community recipe sharing and ratings
- [ ] Grocery list auto-generation from meals
- [ ] Barcode scanner for instant meal safety check

---

## Troubleshooting

### Issue: "Prolog not found" Error

**Symptoms:** Recommendation page shows error or blank results

**Solutions:**
1. Verify SWI-Prolog is installed at: `C:\Program Files\swipl\bin\swipl.exe`
2. Check [recommend.php](recommend.php) has correct path in `shell_exec()` call
3. Test Prolog from Command Prompt:
   ```cmd
   "C:\Program Files\swipl\bin\swipl.exe" --version
   ```
4. If error, reinstall SWI-Prolog and select "Add to PATH" during installation

### Issue: "Database Connection Error"

**Symptoms:** Login page shows database error, cannot access site

**Solutions:**
1. Verify MySQL is running in XAMPP Control Panel
2. Check [database.php](database.php) settings:
   - Host: `127.0.0.1:3307`
   - User: `root`
   - Password: (empty)
   - Database: `medimeal`
3. Open phpMyAdmin (`http://localhost/phpmyadmin/`)
4. Verify `medimeal` database exists with tables
5. If database missing, re-import [medimeal.sql](medimeal.sql)

### Issue: "Recommendation Not Showing"

**Symptoms:** Page loads but no meals returned after clicking "Recommend"

**Solutions:**
1. Check browser console (F12) for JavaScript errors
2. Verify [foodie.pl](foodie.pl) has valid Prolog syntax:
   ```cmd
   swipl -s foodie.pl -g "halt."
   ```
   Should show no errors
3. Test Prolog manually:
   ```cmd
   swipl -s foodie.pl -g "breakfast_meal(X,Y,Z,W), write(X), nl, halt."
   ```
   Should print meal names
4. Check Apache error log in `C:\xampp\apache\logs\error.log`

### Issue: "Login Fails" or "Registration Error"

**Symptoms:** Cannot create account or login

**Solutions:**
1. Verify database `users` table exists:
   - Open phpMyAdmin → `medimeal` database → check `users` table
2. Test database connection with [database.php](database.php):
   ```php
   <?php include 'database.php'; echo "Connected!"; ?>
   ```
3. Check MySQL error log in `C:\xampp\mysql\data\mysql.err`
4. Ensure XAMPP MySQL is running on port 3307

### Issue: "500 Internal Server Error"

**Symptoms:** Any page shows 500 error

**Solutions:**
1. Enable PHP error logging in `C:\xampp\php\php.ini`:
   ```
   error_reporting = E_ALL
   display_errors = On
   ```
2. Restart Apache
3. Check error log: `C:\xampp\apache\logs\error.log`
4. Verify all `.php` files use proper syntax (no unclosed tags)

---



## Credits

**MediMeal Project** — Academic Project  
**University:**  NIBM   
**Semester:** First Year  
**Academic Year:** 2025–2026

**Project Team:**
- Asma, Sandali, Menushi — Project Lead & Development


**Special Thanks:**
- SWI-Prolog community for the powerful logic engine
- XAMPP/Apache Friends for the development environment
- Sri Lankan culinary resources for meal data

---


## License
This project is licensed under the MediMeal Academic Use License.
See the LICENSE file for details.

---

**Last Updated:** May 18, 2026  
**Version:** 1.0  
**Status:** Active Development

**Questions?** Check [Troubleshooting](#troubleshooting) or open an issue in the repository.

Happy recommending! 🍲✨
