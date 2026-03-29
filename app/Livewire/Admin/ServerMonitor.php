<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ServerMonitor extends Component
{
    public array $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'cpu' => $this->getCpuUsage(),
            'ram' => $this->getMemoryUsage(),
            'disk' => $this->getDiskUsage(),
            'uptime' => $this->getUptime(),
            'system' => [
                'os' => PHP_OS_FAMILY,
                'php' => PHP_VERSION,
                'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            ],
        ];
    }

    private function getCpuUsage(): float
    {
        $cores = $this->getCpuCoreCount();

        if (PHP_OS_FAMILY === 'Windows') {
            try {
                $cmd = 'powershell -command "Get-CimInstance Win32_Processor | Select-Object -ExpandProperty LoadPercentage"';
                $output = shell_exec($cmd);
                return round((float) trim($output), 1);
            } catch (\Exception $e) {
                return 0.0;
            }
        }

        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            // sys_getloadavg [1min, 5min, 15min]
            $percentage = ($load[0] / $cores) * 100;
            return round($percentage, 1);
        }

        return 0.0;
    }

    private function getCpuCoreCount(): int
    {
        if (PHP_OS_FAMILY === 'Windows') {
            try {
                $cmd = 'powershell -command "(Get-CimInstance Win32_Processor).NumberOfCores"';
                $output = shell_exec($cmd);
                return max(1, (int) trim($output));
            } catch (\Exception $e) {
                return 1;
            }
        }

        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            return max(1, count($matches[0]));
        }

        return 1;
    }

    private function getMemoryUsage(): array
    {
        if (PHP_OS_FAMILY === 'Windows') {
            try {
                $totalCmd = 'powershell -command "Get-CimInstance Win32_OperatingSystem | Select-Object -ExpandProperty TotalVisibleMemorySize"';
                $freeCmd = 'powershell -command "Get-CimInstance Win32_OperatingSystem | Select-Object -ExpandProperty FreePhysicalMemory"';
                
                $total = (float) trim(shell_exec($totalCmd)) * 1024; // KB to B
                $free = (float) trim(shell_exec($freeCmd)) * 1024; // KB to B
                
                $used = $total - $free;
                $percentage = ($total > 0) ? ($used / $total) * 100 : 0;

                return [
                    'total' => $total,
                    'used' => $used,
                    'free' => $free,
                    'percentage' => round($percentage, 1),
                ];
            } catch (\Exception $e) {
                return ['total' => 0, 'used' => 0, 'free' => 0, 'percentage' => 0];
            }
        }

        // Linux parsing /proc/meminfo
        if (file_exists('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/', $meminfo, $totalMatches);
            preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $freeMatches);
            
            $total = (float) ($totalMatches[1] ?? 0) * 1024; // KB to B
            $available = (float) ($freeMatches[1] ?? 0) * 1024; // KB to B
            $used = $total - $available;
            
            $percentage = ($total > 0) ? ($used / $total) * 100 : 0;

            return [
                'total' => $total,
                'used' => $used,
                'free' => $available,
                'percentage' => round($percentage, 1),
            ];
        }

        return ['total' => 0, 'used' => 0, 'free' => 0, 'percentage' => 0];
    }

    private function getDiskUsage(): array
    {
        $path = base_path();
        $total = @disk_total_space($path) ?: 0;
        $free = @disk_free_space($path) ?: 0;
        $used = $total - $free;
        $percentage = ($total > 0) ? ($used / $total) * 100 : 0;

        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'percentage' => round($percentage, 1),
        ];
    }

    private function getUptime(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            try {
                $cmd = 'powershell -command "round((Get-Date) - (Get-CimInstance Win32_OperatingSystem).LastBootUpTime).TotalSeconds"';
                $seconds = (int) trim(shell_exec($cmd));
                
                $days = floor($seconds / 86400);
                $hours = floor(($seconds % 86400) / 3600);
                $minutes = floor(($seconds % 3600) / 60);

                if ($days > 0) return "{$days}j {$hours}h";
                if ($hours > 0) return "{$hours}h {$minutes}min";
                return "{$minutes}min";
            } catch (\Exception $e) {
                return 'N/A';
            }
        }

        if (file_exists('/proc/uptime')) {
            $uptime = explode(' ', file_get_contents('/proc/uptime'));
            $seconds = (int) $uptime[0];
            
            $days = floor($seconds / 86400);
            $hours = floor(($seconds % 86400) / 3600);
            $minutes = floor(($seconds % 3600) / 60);

            if ($days > 0) return "{$days}j {$hours}h";
            return "{$hours}h {$minutes}min";
        }

        return 'N/A';
    }

    public function render()
    {
        return view('livewire.admin.server-monitor');
    }
}
