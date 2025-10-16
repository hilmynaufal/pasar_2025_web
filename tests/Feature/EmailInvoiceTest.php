<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\InvoiceMail;

class EmailInvoiceTest extends TestCase
{
    public function test_invoice_mail_can_be_created()
    {
        // Data untuk testing
        $emailData = [
            'id' => 'RTB-20250115123456789',
            'tanggal_transaksi' => '2025-01-15 12:34:56',
            'nominal_transaksi' => 50000,
            'metode_pembayaran' => 'Tunai',
            'nama_pedagang' => 'John Doe',
            'kode_kios' => 'K001',
            'jenis_akun' => 'Pedagang',
            'nama_pasar' => 'Pasar Test',
            'nama_petugas' => 'Petugas A',
            'nama_distrik' => 'Distrik A',
            'status' => 'SUCCESS'
        ];

        // Buat instance InvoiceMail
        $mail = new InvoiceMail($emailData);

        // Assert bahwa mail dibuat dengan benar
        $this->assertEquals($emailData, $mail->data);
        $this->assertEquals('Invoice Pembayaran - John Doe', $mail->envelope()->subject);
    }

    public function test_invoice_mail_has_correct_content()
    {
        // Data untuk testing
        $emailData = [
            'id' => 'RTB-20250115123456789',
            'tanggal_transaksi' => '2025-01-15 12:34:56',
            'nominal_transaksi' => 50000,
            'metode_pembayaran' => 'Tunai',
            'nama_pedagang' => 'John Doe',
            'kode_kios' => 'K001',
            'jenis_akun' => 'Pedagang',
            'nama_pasar' => 'Pasar Test',
            'nama_petugas' => 'Petugas A',
            'nama_distrik' => 'Distrik A',
            'status' => 'SUCCESS'
        ];

        // Buat instance InvoiceMail
        $mail = new InvoiceMail($emailData);

        // Render email content
        $content = $mail->render();

        // Assert bahwa content mengandung data yang benar
        $this->assertStringContainsString('RTB-20250115123456789', $content);
        $this->assertStringContainsString('John Doe', $content);
        $this->assertStringContainsString('K001', $content);
        $this->assertStringContainsString('Pasar Test', $content);
        $this->assertStringContainsString('50.000', $content);
        $this->assertStringContainsString('Tunai', $content);
        $this->assertStringContainsString('SUCCESS', $content);
    }

    public function test_invoice_mail_can_be_sent()
    {
        // Fake mail untuk testing
        Mail::fake();

        // Data untuk testing
        $emailData = [
            'id' => 'RTB-20250115123456789',
            'tanggal_transaksi' => '2025-01-15 12:34:56',
            'nominal_transaksi' => 50000,
            'metode_pembayaran' => 'Tunai',
            'nama_pedagang' => 'John Doe',
            'kode_kios' => 'K001',
            'jenis_akun' => 'Pedagang',
            'nama_pasar' => 'Pasar Test',
            'nama_petugas' => 'Petugas A',
            'nama_distrik' => 'Distrik A',
            'status' => 'SUCCESS'
        ];

        // Kirim email
        Mail::to('test@example.com')->send(new InvoiceMail($emailData));

        // Assert bahwa email dikirim
        Mail::assertSent(InvoiceMail::class, function ($mail) use ($emailData) {
            return $mail->hasTo('test@example.com') &&
                   $mail->data['nama_pedagang'] === 'John Doe' &&
                   $mail->data['kode_kios'] === 'K001';
        });
    }
}