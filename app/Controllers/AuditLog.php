<?php

namespace App\Controllers;

use App\Models\AuditLogModel;

class AuditLog extends BaseController
{
    protected $auditModel;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $keyword   = $this->request->getGet('q');
        $action    = $this->request->getGet('action');
        $dateStart = $this->request->getGet('start');
        $dateEnd   = $this->request->getGet('end');

        $builder = $this->auditModel;

        if (!empty($keyword)) {
            $builder = $builder->groupStart()
                               ->like('username', $keyword)
                               ->orLike('description', $keyword)
                               ->orLike('ip_address', $keyword)
                               ->orLike('action', $keyword)
                               ->groupEnd();
        }

        if (!empty($action)) {
            $builder = $builder->where('action', strtoupper($action));
        }

        if (!empty($dateStart)) {
            $builder = $builder->where('created_at >=', $dateStart . ' 00:00:00');
        }

        if (!empty($dateEnd)) {
            $builder = $builder->where('created_at <=', $dateEnd . ' 23:59:59');
        }

        $logs = $builder->orderBy('id', 'DESC')->findAll(100);

        // Hitung statistik
        $totalLogs    = $this->auditModel->countAllResults(false);
        $totalRole    = $this->auditModel->where('action', 'UBAH_ROLE')->countAllResults(false);
        $totalReset   = $this->auditModel->where('action', 'RESET_PASSWORD')->countAllResults(false);
        $totalHapus   = $this->auditModel->like('action', 'HAPUS_')->countAllResults(false);

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if (!empty($action)) $activeFilterCount++;
        if (!empty($dateStart) || !empty($dateEnd)) $activeFilterCount++;

        $data = [
            'title'             => 'Audit Log & Jejak Keamanan Sistem',
            'active_menu'       => 'audit_log',
            'logs'              => $logs,
            'keyword'           => $keyword,
            'selectedAction'    => $action,
            'dateStart'         => $dateStart,
            'dateEnd'           => $dateEnd,
            'activeFilterCount' => $activeFilterCount,
            'stats'             => [
                'total' => $totalLogs,
                'role'  => $totalRole,
                'reset' => $totalReset,
                'hapus' => $totalHapus,
            ]
        ];

        return view('audit_log/index', $data);
    }
}
