# url-shortener
# Laravel URL Shortener - Complete Implementation Guide

## 📋 Project Summary

This is a complete Laravel URL Shortener application with:
- Single company with multiple users
- 5 role types with specific permissions
- URL shortening service
- Click tracking
- Authentication & authorization
- Invitation system
- Comprehensive test suite

## ✅ All Requirements Implemented

### ✓ Architecture
- Single company (not multi-tenant)
- Multiple users per company
- 5 Roles: SuperAdmin, Admin, Member, Sales, Manager

### ✓ Authorization Rules
- **SuperAdmin**: No URL creation, No URL list viewing
- **Admin**: No URL creation, Can see company URLs (except own)
- **Member**: No URL creation, Can see URLs (except own)
- **Sales**: Can create URLs, Can see own URLs
- **Manager**: Can create URLs, Can see own URLs

### ✓ Invitation System
- SuperAdmin cannot invite Admin
- Admin can only invite Sales/Manager (not Admin/Member)
- Invitations with unique tokens
- 7-day expiration

### ✓ URL Shortener
- Only Sales & Manager can create
- Authenticated access required
- Click tracking
- Unique short codes
- Proper visibility filtering

### ✓ Tests
- All role permissions tested
- URL creation restrictions verified
- URL visibility rules validated
- Authentication checks passed

## 🗂️ File Structure

### Models (app/Models/)
```
Company.php          - Single company model
Role.php            - 5 role types
User.php            - Users with roles
ShortUrl.php        - Short URLs with tracking
Invitation.php      - User invitations
```

### Controllers (app/Http/Controllers/)
```
AuthController.php         - Login/register/logout
ShortUrlController.php     - URL CRUD & redirect
DashboardController.php    - Dashboard display
InvitationController.php   - Invitation flow
```

### Migrations (database/migrations/)
```
create_companies_table
create_roles_table
create_users_table
create_short_urls_table
create_invitations_table
```

### Tests (tests/Feature/)
```
AuthTest.php              - Authentication tests
UrlCreationTest.php       - Creation permissions
UrlVisibilityTest.php     - Visibility rules
UrlRedirectTest.php       - Redirect & tracking
```

### Views (resources/views/)
```
layouts/app.blade.php     - Main layout
auth/login.blade.php      - Login page
dashboard/index.blade.php - Dashboard
short-urls/*              - URL pages
invitations/*             - Invitation pages
```

## 🔑 Key Implementation Details

### Role Checking
```php
// User model methods
public function isSuperAdmin(): bool
public function isAdmin(): bool
public function isMember(): bool
public function isSales(): bool
public function isManager(): bool
public function canCreateUrl(): bool
public function canInviteUsers(): bool
public function canViewAllCompanyUrls(): bool
```

### URL Creation Authorization
```php
// Only Sales & Manager can create
if (!Auth::user()->canCreateUrl()) {
    abort(403, 'You cannot create short URLs.');
}
```

### URL Visibility Logic
```php
// Admin sees company URLs except own
if ($user->isAdmin()) {
    $query = $query->exceptUser($user->id);
}

// Member sees company URLs except own
if ($user->isMember()) {
    $query = $query->exceptUser($user->id);
}
```

### Short URL Redirect
```php
// Requires authentication
if (!Auth::check()) {
    return redirect()->route('login');
}

// Track clicks
$shortUrl->incrementClicks();

// Redirect to original
return redirect()->away($shortUrl->original_url);
```

## 📊 Database Schema

### companies table
```
id, name, created_at, updated_at
```

### roles table
```
id, name (SuperAdmin, Admin, Member, Sales, Manager)
```

### users table
```
id, company_id (FK), role_id (FK), 
name, email, password, remember_token,
created_at, updated_at
```

### short_urls table
```
id, user_id (FK), company_id (FK),
short_code (unique), original_url,
clicks (default 0), last_accessed,
created_at, updated_at
```

### invitations table
```
id, company_id (FK), email, role_id (FK),
token (unique), accepted_at,
expires_at, created_at, updated_at
```

## 🚀 Installation & Setup

### 1. Create Laravel Project
```bash
composer create-project laravel/laravel url-shortener
cd url-shortener
```

### 2. Copy Files
- Copy all models to `app/Models/`
- Copy all controllers to `app/Http/Controllers/`
- Copy all migrations to `database/migrations/`
- Copy all seeders to `database/seeders/`
- Copy all views to `resources/views/`
- Copy tests to `tests/Feature/`

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite

# Or MySQL:
DB_CONNECTION=mysql
DB_DATABASE=url_shortener
```

### 4. Run Setup
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan serve
```

### 5. Test
```bash
php artisan test
```

## 🧪 Test Cases

### Test 1: Only Sales/Manager can create URLs
```php
// Sales can create
$sales = User::where('email', 'sales@example.com')->first();
$response = $this->actingAs($sales)->post('/short-urls', [
    'original_url' => 'https://example.com'
]);
$this->assertEquals(302, $response->status());

// Admin cannot create
$admin = User::where('email', 'admin@example.com')->first();
$response = $this->actingAs($admin)->post('/short-urls', [...]);
$this->assertEquals(403, $response->status());
```

### Test 2: Admin sees company URLs (except own)
```php
$admin = User::where('email', 'admin@example.com')->first();
$sales = User::where('email', 'sales@example.com')->first();

$adminUrl = ShortUrl::factory()
    ->create(['user_id' => $admin->id]);
$salesUrl = ShortUrl::factory()
    ->create(['user_id' => $sales->id]);

$response = $this->actingAs($admin)->get('/short-urls');

// Should see sales URL
$this->assertStringContainsString($salesUrl->short_code, $response->getContent());
// Should NOT see own URL
$this->assertStringNotContainsString($adminUrl->short_code, $response->getContent());
```

### Test 3: Short URL requires authentication
```php
$shortUrl = ShortUrl::factory()->create();

// Unauthenticated
$response = $this->get('/s/' . $shortUrl->short_code);
$this->assertEquals(302, $response->status());
$this->assertTrue(str_contains($response->getTargetUrl(), 'login'));

// Authenticated
$user = User::factory()->create();
$response = $this->actingAs($user)->get('/s/' . $shortUrl->short_code);
$this->assertEquals(302, $response->status()); // Redirect to original URL
```

### Test 4: Click tracking
```php
$user = User::factory()->create();
$shortUrl = ShortUrl::factory()->create(['clicks' => 0]);

$this->actingAs($user)->get('/s/' . $shortUrl->short_code);

// Verify clicks incremented
$this->assertEquals(1, $shortUrl->fresh()->clicks);
```

## 📝 Default Test Credentials

After seeding:
| Email | Password | Role |
|-------|----------|------|
| superadmin@example.com | password | SuperAdmin |
| admin@example.com | password | Admin |
| member@example.com | password | Member |
| sales@example.com | password | Sales |
| manager@example.com | password | Manager |

## 🔒 Security Features

✓ Password hashing with bcrypt
✓ CSRF protection on all forms
✓ SQL injection prevention (Eloquent)
✓ XSS protection (Blade escaping)
✓ Role-based authorization
✓ Company data isolation
✓ Invitation token verification
✓ Expiring invitations

## 🎯 Key Routes

### Public (Require Auth)
```
GET  /login                    - Login page
POST /login                    - Submit login
GET  /register                 - Register page
POST /register                 - Submit register
POST /logout                   - Logout
GET  /s/{shortCode}            - Redirect (requires auth)
```

### Protected
```
GET  /dashboard                - Dashboard
GET  /dashboard/urls           - URL list
GET  /short-urls               - URLs (filtered)
GET  /short-urls/create        - Create form
POST /short-urls               - Store URL
GET  /short-urls/{id}          - Show details
DELETE /short-urls/{id}        - Delete URL
GET  /invitations/create       - Invite form
POST /invitations              - Send invite
GET  /invitations/{token}      - Accept form
POST /invitations/{token}      - Accept invite
```

## 🐛 Troubleshooting

### Migration Error
```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Tests Failing
```bash
# Reset test database
php artisan test --refresh-database

# Run specific test
php artisan test tests/Feature/UrlCreationTest.php
```

### Database Issues
```bash
# Reset everything
php artisan migrate:fresh --seed

# Check database
php artisan tinker
# > User::count()
```

## 📚 API Response Examples

### Create Short URL
```
POST /short-urls
{
    "original_url": "https://example.com"
}

Response: 302 Redirect to /short-urls/{id}
```

### List URLs
```
GET /short-urls
Response: HTML table with URLs
```

### Redirect
```
GET /s/abc12345
Response: 302 Redirect to original URL
```

## 🔄 Workflow Examples

### As Sales User
1. Login with sales@example.com
2. Create short URL
3. View own URLs
4. Access short URL
5. See click tracked

### As Admin
1. Login with admin@example.com
2. Cannot create URLs
3. Can see team URLs (except own)
4. Can invite Sales/Manager
5. Cannot see SuperAdmin features

### As SuperAdmin
1. Login with superadmin@example.com
2. Cannot create URLs
3. Cannot see URLs list
4. Can invite Admins (but not to new company)
5. View system overview

## ✨ Features Implemented

✅ User authentication
✅ Role-based access control
✅ URL shortening
✅ Click tracking
✅ Invitation system
✅ Company management
✅ Dashboard
✅ Authorization checks
✅ Comprehensive tests
✅ Clean code structure
✅ Proper error handling
✅ Session management

## 🚀 Production Checklist

Before deploying:
- [ ] Set APP_DEBUG=false
- [ ] Generate unique APP_KEY
- [ ] Configure production database
- [ ] Set up proper email service
- [ ] Configure Redis/Cache
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Set proper file permissions
- [ ] Enable HTTPS

## 📞 AI Tools Disclosure

**Tools Used:**
- ChatGPT: Laravel validation syntax, Eloquent relationships
- Cursor IDE: Code completion, Laravel patterns
- Laravel Documentation: Official framework guides

---

**Complete, tested, and production-ready Laravel URL Shortener! 🚀**
