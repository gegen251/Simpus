<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'username',
        'role',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Catat aksi sensitif secara otomatis dari sesi aktif dan request
     */
    public static function record(string $action, string $description, ?int $userId = null): bool
    {
        $session = session();
        $request = service('request');

        $userId = $userId ?? $session->get('admin_id');
        $username = $session->get('admin_username') ?? ($userId ? 'user_' . $userId : 'sistem');
        $role = $session->get('admin_role') ?? 'unknown';

        $ip = $request->getIPAddress();
        $userAgent = substr((string) $request->getUserAgent(), 0, 255);

        $model = new self();
        return (bool) $model->insert([
            'user_id'     => $userId,
            'username'    => $username,
            'role'        => $role,
            'action'      => strtoupper($action),
            'description' => $description,
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
