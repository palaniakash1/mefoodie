


User opens site → Browser fetches location → 
PHP fetches nearby businesses (via Haversine query) → 
Results rendered dynamically → 
Optional search with suggestions → 
Paginated navigation through results → 
Optional business registration popup.



# 🍽️ MeFoodie – Tamil Nadu Restaurant & Business Listing Platform

A lightweight, PHP-based web application to discover nearby restaurants and food businesses across Tamil Nadu.  
Built with **TailwindCSS**, **PHP**, **MySQL**, and **Vanilla JS**, MeFoodie offers location-based search, real-time suggestions, and clean pagination.

---

## 🚀 Features

### 🧭 Location-Based Discovery
- Automatically detects the user's current location.
- Fetches and sorts restaurants by nearest distance.
- Fallbacks to a default list if geolocation is denied.

### 🔍 Smart Search with Suggestions
- AJAX-based instant search suggestions.
- “Full” and “Suggest” search modes.
- Keyword-sensitive filtering (`veg`, `non-veg`, `biryani`, etc.).

### 🧱 Pagination
- Handles large datasets efficiently.
- Displays paginated navigation like:
[Prev] 1 2 3 ... 20 ... 50 [Next]


### 🏢 Business Registration
- Popup form for restaurant owners to register.
- Fields: Name, Email, Phone, FSSAI, City, District, Website, Tags.
- Auto success popup on submission.
- Only admin-approved businesses are displayed.

### 🖥️ Frontend UI
- Responsive layout using TailwindCSS.
- Modern grid cards with shadow hover effects.
- Typing animation for dynamic headings.

---

## 🧰 Tech Stack

| Component | Technology |
|------------|-------------|
| Frontend | HTML, CSS, TailwindCSS, JavaScript |
| Backend | PHP (MySQLi) |
| Database | MySQL |
| Hosting | Apache / Nginx / Hostinger |
| APIs | HTML5 Geolocation API |

---

## 📂 Folder Structure

```
mefoodie
├─ .htaccess
├─ 404.php
├─ index.php
├─ private
│  ├─ admin-login.php
│  ├─ admin-logout.php
│  ├─ admin.php
│  ├─ database.php
│  ├─ db_credentials.php
│  ├─ functions.php
│  ├─ get_city.php
│  ├─ initialize.php
│  ├─ ip_address.php
│  ├─ shared
│  │  ├─ admin_header.php
│  │  ├─ footer.php
│  │  └─ header.php
│  └─ update_status.php
└─ public
   ├─ assets
   │  └─ Mefoodie-header-bg.png
   ├─ config.php
   ├─ favicon.ico.png
   ├─ get_nearby_businesses.php
   ├─ register.php
   ├─ search.php
   ├─ search_businesses.php
   └─ stylesheets
      └─ style.css

```

---

## ⚙️ Installation

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/<your-username>/mefoodie.git
cd mefoodie


🧑‍🏭 Author

Akash Palani
Associate Director & Software Developer at DigiMaraa Technologies
📧 palani.maraa@gmail.com

🌐 www.digimaraa.com