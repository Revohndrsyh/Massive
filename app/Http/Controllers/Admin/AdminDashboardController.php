<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KuesionerResponse;
use App\Models\SusResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $susResponses = $this->susQuery($filters)
            ->latest()
            ->paginate(10, ['*'], 'sus_page')
            ->withQueryString();

        $kuesionerResponses = $this->kuesionerQuery($filters)
            ->latest()
            ->paginate(10, ['*'], 'kuesioner_page')
            ->withQueryString();

        return view('admin.dashboard', compact('filters', 'susResponses', 'kuesionerResponses'));
    }

    public function exportSus(Request $request, string $format)
    {
        $this->abortUnsupportedFormat($format);

        $rows = $this->susQuery($this->filters($request))
            ->latest()
            ->get()
            ->map(fn (SusResponse $response) => $this->susRow($response))
            ->all();

        return $this->download($format, 'sus-responses', $this->susHeadings(), $rows);
    }

    public function exportKuesioner(Request $request, string $format)
    {
        $this->abortUnsupportedFormat($format);

        $rows = $this->kuesionerQuery($this->filters($request))
            ->latest()
            ->get()
            ->map(fn (KuesionerResponse $response) => $this->kuesionerRow($response))
            ->all();

        return $this->download($format, 'kuesioner-responses', $this->kuesionerHeadings(), $rows);
    }

    private function filters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
    }

    private function susQuery(array $filters): Builder
    {
        return SusResponse::query()
            ->with('user')
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $this->filterByUser($query, $search))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to));
    }

    private function kuesionerQuery(array $filters): Builder
    {
        return KuesionerResponse::query()
            ->with('user')
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $this->filterByUser($query, $search))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to));
    }

    private function filterByUser(Builder $query, string $search): void
    {
        $query->whereHas('user', function (Builder $userQuery) use ($search) {
            $userQuery->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('nama_usaha', 'like', "%{$search}%")
                ->orWhere('kategori_usaha', 'like', "%{$search}%");
        });
    }

    private function download(string $format, string $baseName, array $headings, array $rows)
    {
        $filename = $baseName . '-' . now()->format('Ymd-His') . '.' . $format;

        if ($format === 'xlsx') {
            return $this->downloadXlsx($filename, $headings, $rows);
        }

        return response()->streamDownload(function () use ($headings, $rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headings, ';');

            foreach ($rows as $row) {
                fputcsv($file, $row, ';');
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function downloadXlsx(string $filename, array $headings, array $rows): BinaryFileResponse
    {
        $path = tempnam(sys_get_temp_dir(), 'admin-export-');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml([$headings, ...$rows]));
        $zip->close();

        return response()
            ->download($path, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
            ->deleteFileAfterSend(true);
    }

    private function worksheetXml(array $rows): string
    {
        $xmlRows = [];
        foreach (array_values($rows) as $rowIndex => $row) {
            $cells = [];
            foreach (array_values($row) as $columnIndex => $value) {
                $coordinate = $this->columnName($columnIndex + 1) . ($rowIndex + 1);
                $escaped = htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8');
                $cells[] = "<c r=\"{$coordinate}\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
            }
            $number = $rowIndex + 1;
            $xmlRows[] = "<row r=\"{$number}\">" . implode('', $cells) . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
            . implode('', $xmlRows)
            . '</sheetData></worksheet>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }
        return $name;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Export" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    private function abortUnsupportedFormat(string $format): void
    {
        if (! in_array($format, ['csv', 'xlsx'], true)) {
            abort(404);
        }
    }

    private function susHeadings(): array
    {
        return ['user_id', 'nama', 'email', 'sus_1', 'sus_2', 'sus_3', 'sus_4', 'sus_5', 'sus_6', 'sus_7', 'sus_8', 'sus_9', 'sus_10', 'skor_sus', 'grade', 'keterangan', 'tanggal'];
    }

    private function susRow(SusResponse $response): array
    {
        return [
            $response->user_id,
            $response->user?->name ?? 'User #' . $response->user_id,
            $response->user?->email ?? '-',
            $response->sus_1,
            $response->sus_2,
            $response->sus_3,
            $response->sus_4,
            $response->sus_5,
            $response->sus_6,
            $response->sus_7,
            $response->sus_8,
            $response->sus_9,
            $response->sus_10,
            number_format((float) $response->skor_sus, 1, '.', ''),
            $response->grade,
            $response->grade_label,
            $response->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
        ];
    }

    private function kuesionerHeadings(): array
    {
        $headings = ['user_id', 'nama', 'email', 'nama_usaha', 'kategori_usaha'];

        foreach (array_keys($this->kuesionerQuestions()) as $index => $column) {
            $number = $index + 1;
            $headings[] = "pertanyaan_{$number}";
            $headings[] = "jawaban_{$number}";
            $headings[] = "opini_{$number}";
        }

        $headings[] = 'tanggal';

        return $headings;
    }

    private function kuesionerRow(KuesionerResponse $response): array
    {
        $row = [
            $response->user_id,
            $response->user?->name ?? 'User #' . $response->user_id,
            $response->user?->email ?? '-',
            $response->user?->nama_usaha ?? '-',
            $response->user?->kategori_usaha ?? '-',
        ];

        foreach ($this->kuesionerQuestions() as $column => $question) {
            $row[] = $question;
            $row[] = $response->{$column};
            $row[] = $response->{"opini_{$column}"};
        }

        $row[] = $response->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i:s');

        return $row;
    }

    private function kuesionerQuestions(): array
    {
        return [
            'kualitas_produk' => 'Seberapa baik kualitas produk atau layanan yang Anda hasilkan saat ini?',
            'efisiensi_operasional' => 'Seberapa lancar proses operasional usaha Anda sehari-hari?',
            'penuhi_permintaan' => 'Seberapa mampu usaha Anda memenuhi permintaan atau pesanan dari pelanggan?',
            'kualitas_sdm' => 'Seberapa terampil karyawan atau tim Anda dalam menjalankan usaha?',
            'efektivitas_pemasaran' => 'Seberapa efektif cara pemasaran yang Anda lakukan saat ini?',
            'pemasaran_digital' => 'Seberapa mampu Anda memasarkan produk secara online (media sosial, marketplace, dll)?',
            'kepuasan_pelanggan' => 'Seberapa baik tingkat repeat customer dan minimnya komplain dari pelanggan?',
            'jangkauan_pasar' => 'Seberapa luas jangkauan pasar dan dikenalnya usaha Anda oleh masyarakat?',
            'kelola_cashflow' => 'Seberapa baik Anda dalam mengelola uang masuk dan keluar usaha?',
            'akses_modal' => 'Seberapa mudah Anda mendapatkan tambahan modal usaha jika dibutuhkan?',
            'harga_keuntungan' => 'Seberapa baik Anda dalam menentukan harga jual agar tetap mendapat keuntungan?',
            'teknologi_operasional' => 'Seberapa sering Anda menggunakan teknologi (HP, komputer, aplikasi) untuk menjalankan usaha?',
            'aplikasi_bisnis' => 'Seberapa mampu Anda menggunakan aplikasi pencatatan keuangan atau stok barang?',
            'kesiapan_teknologi' => 'Seberapa siap usaha Anda untuk beralih menggunakan teknologi digital?',
            'kesulitan_usaha' => 'Seberapa besar kesulitan yang Anda rasakan dalam menjalankan usaha sehari-hari?',
            'kebutuhan_pelatihan' => 'Seberapa besar kebutuhan Anda terhadap pelatihan atau bimbingan usaha?',
            'strategi_jangka_panjang' => 'Seberapa jelas rencana dan arah pengembangan usaha Anda ke depan?',
        ];
    }
}
