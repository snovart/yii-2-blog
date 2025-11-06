
# Yii2 Blog Project (Advanced Template)

This project is a simple blog built with **Yii 2 Advanced Template**, configured for **frontend (site)** and **backend (admin panel)**.

---

## 1. Project Setup

### 1.1 Clone or Create Project
```bash
composer create-project yiisoft/yii2-app-advanced blog-yii2
cd blog-yii2
php init
```
Select **Development** environment.

---

## 2. Configure Environment

### 2.1 Language and Timezone
In `common/config/main.php`:
```php
'language' => 'ru-RU',
'sourceLanguage' => 'en-US',
'timeZone' => 'Europe/Kyiv',
```

### 2.2 Database Connection
In `common/config/main-local.php`:
```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=blog_yii2',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
],
```

---

## 3. Run Initial Migrations

Create user table and migration history:
```bash
php yii migrate
```

Then enable **RBAC (Role-Based Access Control)**:

In `common/config/main.php` add:
```php
'authManager' => [
    'class' => yii\rbac\DbManager::class,
],
```

Run RBAC migrations:
```bash
php yii migrate --migrationPath=@yii/rbac/migrations
```

---

## 4. Run Development Servers

Frontend (site):
```bash
php -S localhost:8080 -t frontend/web
```

Backend (admin panel):
```bash
php -S localhost:8081 -t backend/web
```

Frontend: http://localhost:8080  
Backend: http://localhost:8081

---

## 5. User Registration and Email Verification

- Register a new user on the **frontend**.
- Confirmation email is stored locally at `frontend/runtime/mail/*.eml`.
- Open `.eml` file and click the verification link to activate the account.
- Alternatively, activate manually via SQL:
  ```sql
  UPDATE user SET status = 10, verification_token = NULL WHERE email = 'your@email.com';
  ```

---

## 6. RBAC Roles Setup

### 6.1 Create RBAC Controller
`console/controllers/RbacController.php`:

```php
namespace console\controllers;

use yii\console\Controller;
use yii\rbac\DbManager;

class RbacController extends Controller
{
    public function actionInit(): int
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();

        $access = $auth->createPermission('accessBackend');
        $access->description = 'Access to admin panel (backend)';
        $auth->add($access);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $access);

        echo "RBAC initialized: role 'admin' and permission 'accessBackend' created.\n";
        return self::EXIT_CODE_NORMAL;
    }

    public function actionAssign(int $userId): int
    {
        $auth = \Yii::$app->authManager;
        $admin = $auth->getRole('admin');

        if (!$admin) {
            echo "Role 'admin' not found. Run: php yii rbac/init\n";
            return self::EXIT_CODE_ERROR;
        }

        $auth->revokeAll($userId);
        $auth->assign($admin, $userId);
        echo "Role 'admin' assigned to user #{$userId}\n";
        return self::EXIT_CODE_NORMAL;
    }
}
```

### 6.2 Initialize and Assign
```bash
php yii rbac/init
php yii rbac/assign 1
```

---

## 7. Restrict Backend Access to Admin Only

In `backend/config/main.php` add before `components`:

```php
'as access' => [
    'class' => yii\filters\AccessControl::class,
    'rules' => [
        [
            'allow' => true,
            'roles' => ['admin'],
        ],
    ],
    'denyCallback' => function () {
        throw new \yii\web\ForbiddenHttpException('Access denied');
    },
],
```

Only users with the **admin** role can open the backend.

---

## 8. Next Step

Next step: create the `Post` entity (migration, model, CRUD).
This will allow admins to manage blog articles from the backend.

---

**Author:** snovart  
**Framework:** Yii 2 Advanced  
**PHP:** 8.3.x  
**DB:** MySQL (WampServer)
