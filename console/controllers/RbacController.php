<?php
namespace console\controllers;

use yii\console\Controller;
use yii\rbac\DbManager;

class RbacController extends Controller
{
    /**
     * Initialize RBAC storage: creates "admin" role and "accessBackend" permission.
     */
    public function actionInit(): int
    {
        /** @var DbManager $auth */
        $auth = \Yii::$app->authManager;

        // Clean existing RBAC data (safe on fresh project)
        $auth->removeAll(); // WARNING: drops all roles/permissions/assignments

        // Permission to access backend
        $access = $auth->createPermission('accessBackend');
        $access->description = 'Access to admin panel (backend)';
        $auth->add($access);

        // Admin role
        $admin = $auth->createRole('admin');
        $auth->add($admin);

        // Admin inherits backend access
        $auth->addChild($admin, $access);

        $this->stdout("RBAC initialized: role 'admin' and permission 'accessBackend' created.\n");
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * Assign "admin" role to a user by ID.
     * @param int $userId
     */
    public function actionAssign(int $userId): int
    {
        $auth = \Yii::$app->authManager;
        $admin = $auth->getRole('admin');

        if (!$admin) {
            $this->stderr("Role 'admin' not found. Run: php yii rbac/init\n");
            return self::EXIT_CODE_ERROR;
        }

        // Remove previous assignments (optional cleanup)
        $auth->revokeAll($userId);

        $auth->assign($admin, $userId);
        $this->stdout("Role 'admin' assigned to user #{$userId}\n");
        return self::EXIT_CODE_NORMAL;
    }
}
