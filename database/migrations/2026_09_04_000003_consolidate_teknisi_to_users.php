<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom teknisi ke users jika belum ada (untuk 1 table)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'teknisi_kode')) $table->string('teknisi_kode')->nullable()->after('telegram_username');
            if (!Schema::hasColumn('users', 'teknisi_keahlian')) $table->json('teknisi_keahlian')->nullable()->after('teknisi_kode');
            if (!Schema::hasColumn('users', 'teknisi_zona')) $table->json('teknisi_zona')->nullable()->after('teknisi_keahlian');
            if (!Schema::hasColumn('users', 'teknisi_sertifikasi')) $table->json('teknisi_sertifikasi')->nullable()->after('teknisi_zona');
            if (!Schema::hasColumn('users', 'teknisi_rating')) $table->decimal('teknisi_rating', 15, 2)->default(0)->after('teknisi_sertifikasi');
            if (!Schema::hasColumn('users', 'teknisi_total_tiket')) $table->integer('teknisi_total_tiket')->default(0)->after('teknisi_rating');
            if (!Schema::hasColumn('users', 'teknisi_total_revisi')) $table->integer('teknisi_total_revisi')->default(0)->after('teknisi_total_tiket');
            if (!Schema::hasColumn('users', 'teknisi_latitude')) $table->decimal('teknisi_latitude', 15, 8)->nullable()->after('teknisi_total_revisi');
            if (!Schema::hasColumn('users', 'teknisi_longitude')) $table->decimal('teknisi_longitude', 15, 8)->nullable()->after('teknisi_latitude');
            if (!Schema::hasColumn('users', 'teknisi_waktu_posisi')) $table->dateTime('teknisi_waktu_posisi')->nullable()->after('teknisi_longitude');
            if (!Schema::hasColumn('users', 'teknisi_status')) $table->string('teknisi_status', 50)->nullable()->after('teknisi_waktu_posisi');
        });

        // 2. Migrasi data teknisi → users (role=teknisi)
        $teknisis = DB::table('teknisi')->get();
        foreach ($teknisis as $tek) {
            $userId = $tek->teknisi_id_user;
            $user = null;
            if ($userId) {
                $user = DB::table('users')->where('id', $userId)->first();
            }
            if (!$user) {
                // cari user by email/telepon/nama, atau buat baru
                $user = DB::table('users')->where('email', 'like', '%'.strtolower(str_replace(' ', '', $tek->teknisi_nama)).'%')->first();
            }
            if (!$user) {
                // buat user baru dari teknisi
                $email = strtolower(str_replace([' ', '-'], '.', $tek->teknisi_nama)).'.teknisi@ams.test';
                // pastikan unique
                $baseEmail = $email; $i=1;
                while (DB::table('users')->where('email', $email)->exists()) {
                    $email = str_replace('@', $i.'@', $baseEmail); $i++;
                }
                $userId = DB::table('users')->insertGetId([
                    'name' => $tek->teknisi_nama,
                    'email' => $email,
                    'phone' => $tek->teknisi_telepon,
                    'telegram_id' => $tek->teknisi_telegram_id,
                    'role' => 'teknisi',
                    'password' => Hash::make('password'),
                    'teknisi_kode' => $tek->teknisi_kode,
                    'teknisi_keahlian' => $tek->teknisi_keahlian,
                    'teknisi_zona' => $tek->teknisi_zona,
                    'teknisi_sertifikasi' => $tek->teknisi_sertifikasi,
                    'teknisi_rating' => $tek->teknisi_rating,
                    'teknisi_total_tiket' => $tek->teknisi_total_tiket,
                    'teknisi_total_revisi' => $tek->teknisi_total_revisi,
                    'teknisi_latitude' => $tek->teknisi_latitude,
                    'teknisi_longitude' => $tek->teknisi_longitude,
                    'teknisi_waktu_posisi' => $tek->teknisi_waktu_posisi,
                    'teknisi_status' => $tek->teknisi_status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                // update master_group_user mapping nanti via teknisi_id -> userId
                // simpan mapping untuk update FK lain
                DB::table('teknisi')->where('teknisi_id', $tek->teknisi_id)->update(['teknisi_id_user' => $userId]);
            } else {
                // update user existing dengan data teknisi
                DB::table('users')->where('id', $user->id)->update([
                    'role' => 'teknisi',
                    'phone' => $user->phone ?? $tek->teknisi_telepon,
                    'telegram_id' => $user->telegram_id ?? $tek->teknisi_telegram_id,
                    'teknisi_kode' => $tek->teknisi_kode,
                    'teknisi_keahlian' => $tek->teknisi_keahlian,
                    'teknisi_zona' => $tek->teknisi_zona,
                    'teknisi_sertifikasi' => $tek->teknisi_sertifikasi,
                    'teknisi_rating' => $tek->teknisi_rating,
                    'teknisi_total_tiket' => $tek->teknisi_total_tiket,
                    'teknisi_total_revisi' => $tek->teknisi_total_revisi,
                    'teknisi_latitude' => $tek->teknisi_latitude,
                    'teknisi_longitude' => $tek->teknisi_longitude,
                    'teknisi_waktu_posisi' => $tek->teknisi_waktu_posisi,
                    'teknisi_status' => $tek->teknisi_status,
                ]);
                $userId = $user->id;
            }

            // 3. Migrasi master_group_teknisi → master_group_user
            $groupLinks = DB::table('master_group_teknisi')->where('master_group_teknisi_id_teknisi', $tek->teknisi_id)->get();
            foreach ($groupLinks as $gl) {
                $exists = DB::table('master_group_user')->where('master_group_user_id_group', $gl->master_group_teknisi_id_group)->where('master_group_user_id_user', $userId)->exists();
                if (!$exists) {
                    DB::table('master_group_user')->insert([
                        'master_group_user_id_group' => $gl->master_group_teknisi_id_group,
                        'master_group_user_id_user' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 4. Migrasi FK lain: tiket, batch_tiket, riwayat_service, kategori_teknisi, jadwal_service jika ada
            // tiket
            DB::table('tiket')->where('tiket_id_teknisi', $tek->teknisi_id)->update(['tiket_id_teknisi' => $userId]);
            // batch_tiket
            if (Schema::hasTable('batch_tiket') && Schema::hasColumn('batch_tiket', 'batch_tiket_id_teknisi')) {
                DB::table('batch_tiket')->where('batch_tiket_id_teknisi', $tek->teknisi_id)->update(['batch_tiket_id_teknisi' => $userId]);
            }
            // riwayat_service
            if (Schema::hasTable('riwayat_service') && Schema::hasColumn('riwayat_service', 'riwayat_service_id_teknisi')) {
                DB::table('riwayat_service')->where('riwayat_service_id_teknisi', $tek->teknisi_id)->update(['riwayat_service_id_teknisi' => $userId]);
            }
            // kategori_teknisi
            if (Schema::hasTable('kategori_teknisi')) {
                DB::table('kategori_teknisi')->where('teknisi_id', $tek->teknisi_id)->update(['teknisi_id' => $userId]);
            }
        }

        // 5. Drop tabel lama (setelah data termigrasi)
        Schema::dropIfExists('kategori_teknisi');
        Schema::dropIfExists('master_group_teknisi');
        Schema::dropIfExists('teknisi');
    }

    public function down(): void
    {
        // tidak reversible secara penuh — buat ulang teknisi kosong jika rollback
        Schema::create('teknisi', function (Blueprint $table) {
            $table->id('teknisi_id');
            $table->unsignedBigInteger('teknisi_id_user')->nullable()->index();
            $table->string('teknisi_kode');
            $table->string('teknisi_nama');
            $table->string('teknisi_telepon')->nullable();
            $table->json('teknisi_keahlian')->nullable();
            $table->json('teknisi_zona')->nullable();
            $table->json('teknisi_sertifikasi')->nullable();
            $table->decimal('teknisi_rating', 15, 2)->default(0);
            $table->integer('teknisi_total_tiket')->default(0);
            $table->integer('teknisi_total_revisi')->default(0);
            $table->decimal('teknisi_latitude', 15, 8)->nullable();
            $table->decimal('teknisi_longitude', 15, 8)->nullable();
            $table->dateTime('teknisi_waktu_posisi')->nullable();
            $table->string('teknisi_status');
            $table->timestamps();
        });
        Schema::create('master_group_teknisi', function (Blueprint $table) {
            $table->id('master_group_teknisi_id');
            $table->unsignedBigInteger('master_group_teknisi_id_group')->index();
            $table->unsignedBigInteger('master_group_teknisi_id_teknisi')->index();
            $table->timestamps();
            $table->unique(['master_group_teknisi_id_group', 'master_group_teknisi_id_teknisi'], 'uniq_group_teknisi');
        });
        Schema::create('kategori_teknisi', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('kategori_id')->index();
            $table->unsignedBigInteger('teknisi_id')->index();
            $table->timestamps();
            $table->unique(['kategori_id', 'teknisi_id']);
        });
    }
};
