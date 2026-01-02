<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanController extends Controller
{
    public function index()
    {
        $years = Order::selectRaw('YEAR(order_date) as year')
            ->whereNotNull('order_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('admin.laporan.index', compact('years'));
    }
    
    public function exportCsv(Request $request)
    {
        $query = Project::with(['client', 'order.orderItems.service', 'order.orderItems.servicePackage'])
            ->join('orders', 'projects.order_id', '=', 'orders.id');
        
        // Filter by year if provided
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('orders.order_date', $request->year);
        }
        
        // Filter by date range if provided
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('orders.order_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('orders.order_date', '<=', $request->end_date);
        }
        
        $projects = $query->select('projects.*')->orderBy('orders.order_date', 'asc')->get();
        
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Management System')
            ->setTitle('Laporan Project')
            ->setSubject('Laporan Project')
            ->setDescription('Export data project dari management system');
        
        // Header row
        $headers = [
            'Timestamp',
            'Nomor PKS',
            'Nama Client',
            'No. Telp',
            'Nama Usaha',
            'Alamat E-mail',
            'Alamat',
            'Jenis Project',
            'Nama Project',
            'Paket',
            'Tanggal Mulai Kontrak',
            'Waktu Kontrak',
            'Tanggal Berakhir Kontrak',
            'Nilai Kontrak',
            'Penanggungjawab Pekerjaan',
            'Sumber Referensi/Informasi',
            'Status',
            'Keterangan',
            'Telah Dibayar',
            'Kekurangan',
            'Keterangan Pembayaran'
        ];
        
        // Write header
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:U1')->applyFromArray($headerStyle);
        
        // Data rows
        $row = 2;
        foreach ($projects as $project) {
            $client = $project->client;
            $order = $project->order;
            
            // Get service/package name
            $jenisProject = '';
            $namaProject = '';
            $paketName = '-';
            if ($order && $order->orderItems->count() > 0) {
                $firstItem = $order->orderItems->first();
                if ($firstItem->service) {
                    $jenisProject = $firstItem->service->category->name ?? '';
                    $namaProject = $firstItem->service->name;
                    if ($firstItem->servicePackage) {
                        $paketName = $firstItem->servicePackage->name;
                    }
                }
            }
            
            // Generate PKS Number if empty
            $pksNumber = $project->pks_number;
            if (empty($pksNumber)) {
                // Generate from project_code (PRJ-2024-0001 -> PKS/2024/0001)
                $pksNumber = str_replace(['PRJ-', '-'], ['PKS/', '/'], $project->project_code);
            }
            
            // Calculate duration if empty
            $duration = $project->duration;
            if (empty($duration) && $project->start_date && $project->end_date) {
                $start = \Carbon\Carbon::parse($project->start_date);
                $end = \Carbon\Carbon::parse($project->end_date);
                $diffInDays = $start->diffInDays($end);
                
                if ($diffInDays < 7) {
                    // Kurang dari 1 minggu = tampilkan hari
                    $duration = $diffInDays . ' Hari';
                } elseif ($diffInDays == 7) {
                    $duration = '1 Minggu';
                } elseif ($diffInDays == 14) {
                    $duration = '2 Minggu';
                } elseif ($diffInDays < 30) {
                    // 1-4 minggu
                    $weeks = floor($diffInDays / 7);
                    $duration = $weeks . ' Minggu';
                } else {
                    // 30 hari atau lebih = hitung bulan
                    $months = floor($diffInDays / 30);
                    $remainingDays = $diffInDays % 30;
                    
                    if ($months == 1) {
                        $duration = $remainingDays > 0 ? "1 Bulan {$remainingDays} Hari" : '1 Bulan';
                    } else {
                        $duration = $remainingDays > 0 ? "{$months} Bulan {$remainingDays} Hari" : "{$months} Bulan";
                    }
                }
            }
            
            // Calculate kekurangan (sisa pembayaran)
            $nilaiKontrak = $order ? $order->total_amount : 0;
            $telahDibayar = $order ? $order->paid_amount : 0;
            $kekurangan = $nilaiKontrak - $telahDibayar;
            
            // Status keterangan
            $statusKeterangan = '';
            if ($order) {
                switch ($order->payment_status) {
                    case 'paid':
                        $statusKeterangan = 'Lunas';
                        break;
                    case 'refunded':
                        $statusKeterangan = 'Refund';
                        break;
                    case 'pending':
                    case 'pending_review':
                        $statusKeterangan = $kekurangan > 0 ? 'Kurang' : 'Lunas';
                        break;
                    default:
                        $statusKeterangan = ucfirst($order->payment_status);
                }
            }
            
            $rowData = [
                $order && $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i:s') : '',
                $pksNumber,
                $client ? $client->name : '',
                $client ? $client->phone : '',
                $client ? $client->company_name : '',
                $client ? $client->email : '',
                $client ? $client->address : '',
                $jenisProject,
                $namaProject,
                $paketName,
                $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d F Y') : '',
                $duration ?? '-',
                $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d F Y') : '',
                $nilaiKontrak,
                $project->pic_internal ?? '-',
                $client ? ($client->referral_source ?? '-') : '-',
                $project->status === 'completed' ? 'TRUE' : 'FALSE',
                $project->status === 'completed' ? 'Done' : ucfirst($project->status),
                $telahDibayar > 0 ? $telahDibayar : 0,
                $kekurangan > 0 ? $kekurangan : 0,
                $statusKeterangan
            ];
            
            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            
            $row++;
        }
        
        // Format currency columns (N, S, T = Nilai Kontrak, Telah Dibayar, Kekurangan)
        $lastRow = $row - 1;
        $sheet->getStyle('N2:N' . $lastRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');
        $sheet->getStyle('S2:S' . $lastRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');
        $sheet->getStyle('T2:T' . $lastRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');
        
        // Auto-size all columns
        foreach (range('A', 'U') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add borders to all data
        $sheet->getStyle('A1:U' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        // Generate filename
        $filename = 'laporan_project_' . date('Y-m-d_His') . '.xlsx';
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        
        // Output to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
