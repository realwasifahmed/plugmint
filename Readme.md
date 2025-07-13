# Plugmint – The Modern Plugin Framework for WordPress

## **What is Plugmint?**

Plugmint is a next-generation, Laravel-inspired plugin development framework for WordPress. It lets you build modern, scalable plugins using a clean MVC structure, PSR-4 autoloading, a fluent migration engine, and a developer-friendly CLI—all without Composer or heavy dependencies.
Plugmint aims to make WordPress plugin development as enjoyable and productive as Laravel or Symfony, but 100% native and WP-friendly.

---

## **Core Features (so far)**

### 1. **Modern MVC Structure**

* **Controllers:** Encapsulate admin menus, submenus, endpoints, and business logic using static `boot()` or `register_menu()` methods.
* **Models (coming soon):** ORM-style ActiveRecord base class, making `$wpdb` simple, secure, and modern.
* **Views:** Blade-inspired PHP view system, loaded via the `view()` helper for modular, testable UIs.
* **Services/Helpers:** Clean organization for reusable business logic and utilities.

### 2. **Folder Structure**

```
plugmint/
├── mintman                # CLI entrypoint
├── plugmint.php           # Main WP plugin file
├── composer.json          # Autoloader only (no packages needed)
├── includes/
│   ├── Console/           # CLI command handlers (Artisan-style)
│   ├── Controllers/       # MVC controllers (admin pages, APIs, etc.)
│   ├── Database/
│   │    ├── migrations/   # Migration files (timestamped)
│   │    ├── MigrationManager.php
│   │    ├── Schema.php    # Schema builder (fluent migrations)
│   │    └── Blueprint.php
│   ├── Helpers/           # Global helpers (views, assets, etc.)
│   ├── Models/            # (Coming soon) ORM models
│   ├── Services/          # (Coming soon) App services
│   ├── Views/             # Blade-inspired view files
│   └── init.php           # Bootstraps autoloader, helpers, controllers, etc.
```

---

### 3. **PSR-4 Autoloading (No Composer Needed for End User)**

* Fast, native PHP autoloader using `composer.json` for class discovery.
* No heavy Composer/3rd party dependencies—only use for autoload.

---

### 4. **Developer CLI (Artisan-Style, Called `mintman`)**

Plugmint provides a **dedicated CLI for plugin developers** (works like Laravel Artisan):

#### **Available Commands**

| Command                            | Description                                            |
| ---------------------------------- | ------------------------------------------------------ |
| `php mintman migrate`              | Runs all new migrations (creates DB tables, versioned) |
| `php mintman make:migration NAME`  | Generates a new migration stub with Schema builder     |
| `php mintman make:controller NAME` | Scaffolds a new controller in MVC folder               |
| `php mintman make:admin_page NAME` | Scaffolds a new controller + view for a parent menu    |
| `php mintman make:submenu`         | Interactive submenu creator (asks for parent/child)    |
| `php mintman drop:table table`     | Drops a table and removes migration records            |
| `php mintman hello`                | Prints a test message                                  |

> **All commands are modular and extendable.**
> Future: make\:model, make\:service, make\:factory, etc.

---

### 5. **Powerful Migration Engine**

* Inspired by Laravel: timestamped, class-based migrations with `up()`/`down()` methods.
* **Schema Builder:** Write migrations using fluent syntax (`Schema::create()`, `$table->string()`, etc.).
* **Migration Table:** Tracks which migrations have run, prevents duplicates, supports rollbacks.
* **Down Support:** Rollback/dropping tables using `Schema::drop()`.
* **Automatic class name parsing:** Ensures unique migrations per class/file.
* **Smart generator:** Warns on duplicate migration class names or files.

**Example Migration:**

```php
use Plugmint\Database\Schema;

class CreateOrdersTable
{
    public static function up()
    {
        Schema::create('orders', function($table) {
            $table->id();
            $table->string('customer_name');
            $table->timestamps();
        });
    }
    public static function down()
    {
        Schema::drop('orders');
    }
}
```

---

### 6. **Helpers**

* **view(\$name, \$data):**
  Loads a PHP view from `includes/views`, passing an array of variables—modular, secure, and clean.

  ```php
  view('Admin/orders', ['title' => 'Orders']);
  ```
* **asset(\$path):**
  Returns a public URL to a file in `includes/Assets`—perfect for CSS/JS/images in views.

  ```php
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
  ```

---

### 7. **Admin Menu & Submenu Generators**

* Instantly scaffold parent/child admin menus with auto-wired controller and view files.
* Controllers use static `boot()` and register themselves on plugin load.

---

### 8. **Migration Table Management**

* Dropping a table via CLI (`drop:table`) also cleans up any related migration records for that table.
* Migration tracking table is always in sync with actual DB schema.

---

## **Planned / Next Steps**

* **Models:**
  Plug-and-play ActiveRecord (Eloquent-style) base class for instant CRUD, validation, and relationships.
* **Services:**
  Service container for reusable, testable business logic modules.
* **Factories:**
  Seed/test your plugin with dummy data, à la Laravel factories.
* **More CLI Commands:**
  `make:model`, `make:factory`, `make:service`, `db:seed`, etc.
* **Blade-style Templating:**
  Optionally, Blade/mini-twig for next-level views.
* **Hooks Loader:**
  Smart way to register WP hooks via class methods, not global code.
* **WP-CLI Bridge:**
  Optional: expose all mintman commands via native WP-CLI.
* **Documentation & Branding:**
  Full README, code samples, onboarding docs, logo, CLI ASCII art, etc.

---

## **How To Start (For Devs)**

1. **Clone the repo or download the starter ZIP.**
2. **Install:**

   * Place in `wp-content/plugins/`
   * Run `composer dump-autoload` if you add new classes (no packages required).
3. **Activate Plugmint** in WP admin.
4. **Run CLI commands from terminal:**

   ```
   php mintman make:controller MyController
   php mintman make:migration create_products_table
   php mintman migrate
   ```
5. **Write your business logic in Controllers/Models/Views as in modern frameworks.**

---

## **Why Plugmint?**

* Blazing fast, modern plugin architecture—no bloat, no Composer drama.
* MVC structure makes plugins **testable**, **maintainable**, and **scalable**.
* CLI boosts dev productivity (no more copy/paste boilerplate).
* Migration system keeps all DB changes tracked, versioned, and reliable.
* Ready for teams, agencies, power users, and future open-source domination.

---

## **Get Involved / Feedback**

* **Ideas? Bugs? PRs?**
  This project is just getting started—add your requests, and help shape the next big thing in WordPress plugin dev!
* **Branding, docs, or design help welcome!**

---

*Keep checking back—more features dropping soon!*