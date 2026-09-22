<?php

namespace App\Libraries;

use CodeIgniter\Debug\ExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Exceptions as ExceptionsConfig;
use Throwable;

class CustomExceptionHandler implements ExceptionHandlerInterface
{
    protected ExceptionsConfig $config;
    protected ExceptionHandler $defaultHandler;

    public function __construct(ExceptionsConfig $config)
    {
        $this->config = $config;
        $this->defaultHandler = new ExceptionHandler($config);
    }

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode
    ): void {
        // 1. Kumpulkan data konteks permintaan untuk investigasi teknis
        $uri       = $request instanceof IncomingRequest ? $request->getUri()->getPath() : 'CLI';
        $method    = $request instanceof IncomingRequest ? $request->getMethod() : 'CLI';
        $ip        = $request instanceof IncomingRequest ? $request->getIPAddress() : '127.0.0.1';
        $agent     = $request instanceof IncomingRequest ? (string)$request->getUserAgent() : 'CLI';
        
        $sessionUser = null;
        $userId      = null;
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['admin_id'])) {
            $userId      = $_SESSION['admin_id'];
            $sessionUser = ($_SESSION['admin_username'] ?? 'User') . ' (ID: ' . $userId . ')';
        }

        $exClass   = $exception::class;
        $exMessage = $exception->getMessage();
        $exFile    = clean_path($exception->getFile());
        $exLine    = $exception->getLine();

        $logMessage = sprintf(
            "UNHANDLED EXCEPTION [%d] %s: %s\n" .
            "  Endpoint  : %s %s\n" .
            "  Client IP : %s\n" .
            "  User      : %s\n" .
            "  User-Agent: %s\n" .
            "  Location  : %s on line %d\n" .
            "  Trace     :\n%s",
            $statusCode,
            $exClass,
            $exMessage,
            $method,
            $uri,
            $ip,
            $sessionUser ?: 'Tamu (Unauthenticated)',
            $agent,
            $exFile,
            $exLine,
            render_backtrace($exception->getTrace())
        );

        // 2. Catat ke file log terpusat (writable/logs)
        log_message('critical', $logMessage);

        // 3. Catat ke tabel audit_log jika bukan 404
        if ($statusCode !== 404) {
            try {
                \App\Models\AuditLogModel::record(
                    'SYSTEM_EXCEPTION',
                    sprintf("[HTTP %d] %s: %s di %s:%d (%s %s)", $statusCode, $exClass, substr($exMessage, 0, 150), basename($exFile), $exLine, $method, $uri),
                    $userId
                );
            } catch (\Throwable) {
                // Abaikan jika database unreachable
            }
        }

        // 4. Jika permintaan adalah AJAX / JSON API (misalnya Kiosk API), berikan respon JSON bersih
        if ($request instanceof IncomingRequest) {
            $isAjax = $request->isAJAX();
            $wantsJson = str_contains($request->getHeaderLine('accept'), 'application/json') || str_starts_with($uri, 'kiosk/');

            if ($isAjax || $wantsJson) {
                if (!headers_sent()) {
                    header('Content-Type: application/json; charset=UTF-8', true, $statusCode);
                }

                $jsonResponse = [
                    'success' => false,
                    'status'  => $statusCode,
                    'error'   => $statusCode === 404 ? 'Not Found' : ($statusCode === 403 ? 'Forbidden' : 'Internal Server Error'),
                    'message' => $statusCode === 404 
                        ? 'Sumber daya atau endpoint yang diminta tidak ditemukan.' 
                        : ($statusCode === 403 
                            ? 'Akses ditolak: Anda tidak memiliki izin untuk tindakan ini.' 
                            : 'Terjadi kendala sistem teknis pada server. Permintaan Anda gagal diproses.'),
                ];

                echo json_encode($jsonResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit($exitCode);
            }

            // Pastikan header accept mengandung text/html agar render view HTML selalu terpanggil
            if (!str_contains($request->getHeaderLine('accept'), 'text/html')) {
                $request->setHeader('Accept', 'text/html');
            }
        }

        // 5. Serahkan ke handler default untuk render view kustom (error_403.php, error_404.php, error_500.php, production.php)
        $this->defaultHandler->handle($exception, $request, $response, $statusCode, $exitCode);
    }
}
