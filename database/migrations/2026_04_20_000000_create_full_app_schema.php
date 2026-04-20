<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('year', 255);
                $table->integer('is_active')->default('1');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['year'], 'academic_years_year_unique');
            });
        }

        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->string('slug', 255);
                $table->text('content');
                $table->longText('image')->nullable();
                $table->enum('category', ['news','event'])->default('news');
                $table->integer('is_published')->default('0');
                $table->timestamp('published_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['slug'], 'activities_slug_unique');
            });
        }

        if (!Schema::hasTable('archive_types')) {
            Schema::create('archive_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('archives')) {
            Schema::create('archives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('archive_type_id');
                $table->string('title', 255);
                $table->string('file_path', 255);
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['user_id'], 'archives_user_id_foreign');
                $table->index(['archive_type_id'], 'archives_archive_type_id_foreign');
            });
        }

        if (!Schema::hasTable('book_borrowings')) {
            Schema::create('book_borrowings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_id');
                $table->string('nomor_buku', 255)->nullable();
                $table->enum('borrower_type', ['student','teacher'])->nullable();
                $table->unsignedBigInteger('student_id')->nullable();
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->string('peminjam', 255);
                $table->string('identitas_peminjam', 255)->nullable();
                $table->string('kelas_peminjam', 255)->nullable();
                $table->date('tanggal_pinjam');
                $table->integer('jumlah_pinjam')->default('1');
                $table->date('tanggal_kembali')->nullable();
                $table->integer('is_returned')->default('0');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['book_id'], 'book_borrowings_book_id_foreign');
                $table->index(['student_id'], 'book_borrowings_student_id_foreign');
                $table->index(['teacher_id'], 'book_borrowings_teacher_id_foreign');
            });
        }

        if (!Schema::hasTable('book_conditions')) {
            Schema::create('book_conditions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_id');
                $table->integer('jumlah_buku')->default('0');
                $table->enum('kondisi', ['laik','tidak_laik'])->default('laik');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['book_id'], 'book_conditions_book_id_foreign');
            });
        }

        if (!Schema::hasTable('book_types')) {
            Schema::create('book_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('judul_buku', 255);
                $table->string('penerbit', 255);
                $table->string('pengarang', 255);
                $table->string('tahun_perolehan');
                $table->string('asal_usul', 255);
                $table->unsignedBigInteger('book_type_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['book_type_id'], 'books_book_type_id_foreign');
            });
        }

        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key', 255);
                $table->text('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key', 255);
                $table->string('owner', 255);
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('carousel_images')) {
            Schema::create('carousel_images', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->string('image_path', 255);
                $table->integer('order')->default('0');
                $table->integer('is_active')->default('1');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('committee_activities')) {
            Schema::create('committee_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('committee_program_id');
                $table->string('name', 255);
                $table->decimal('unit_price', 15, 2)->default('0.00');
                $table->integer('quantity')->default('1');
                $table->decimal('cost', 15, 2);
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['committee_program_id'], 'committee_activities_committee_program_id_foreign');
            });
        }

        if (!Schema::hasTable('committee_expenditures')) {
            Schema::create('committee_expenditures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('committee_activity_id')->nullable();
                $table->string('expenditure_number', 255);
                $table->date('date');
                $table->text('description');
                $table->decimal('amount', 15, 2);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['expenditure_number'], 'committee_expenditures_expenditure_number_unique');
                $table->index(['committee_activity_id'], 'committee_expenditures_committee_activity_id_foreign');
            });
        }

        if (!Schema::hasTable('committee_fees')) {
            Schema::create('committee_fees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_year_id');
                $table->unsignedBigInteger('school_class_id');
                $table->decimal('amount', 15, 2);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['academic_year_id'], 'committee_fees_academic_year_id_foreign');
                $table->index(['school_class_id'], 'committee_fees_school_class_id_foreign');
            });
        }

        if (!Schema::hasTable('committee_payments')) {
            Schema::create('committee_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('committee_fee_id');
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['student_id'], 'committee_payments_student_id_foreign');
                $table->index(['committee_fee_id'], 'committee_payments_committee_fee_id_foreign');
            });
        }

        if (!Schema::hasTable('committee_programs')) {
            Schema::create('committee_programs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_year_id');
                $table->string('name', 255);
                $table->decimal('budget', 15, 2);
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['academic_year_id'], 'committee_programs_academic_year_id_foreign');
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid', 255);
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->default('current_timestamp()');
                $table->unique(['uuid'], 'failed_jobs_uuid_unique');
            });
        }

        if (!Schema::hasTable('information')) {
            Schema::create('information', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('content');
                $table->integer('is_important')->default('0');
                $table->integer('is_active')->default('1');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id', 255);
                $table->string('name', 255);
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->text('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue', 255);
                $table->longText('payload');
                $table->unsignedInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
                $table->index(['queue'], 'jobs_queue_index');
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email', 255);
                $table->string('token', 255);
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('pmb_registrations')) {
            Schema::create('pmb_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('registration_number', 255);
                $table->string('nama', 255);
                $table->string('nisn', 255);
                $table->string('nik', 255);
                $table->string('birth_place', 255);
                $table->date('birth_date');
                $table->text('address');
                $table->enum('registration_type', ['baru','pindahan']);
                $table->string('mother_name', 255);
                $table->string('father_name', 255);
                $table->string('guardian_name', 255)->nullable();
                $table->string('phone_number', 255);
                $table->string('academic_year', 255);
                $table->longText('kk_attachment');
                $table->longText('birth_certificate_attachment');
                $table->longText('ijazah_attachment');
                $table->enum('status', ['pending','approved','rejected'])->default('pending');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['registration_number'], 'pmb_registrations_registration_number_unique');
                $table->unique(['nik'], 'pmb_registrations_nik_unique');
                $table->unique(['nisn'], 'pmb_registrations_nisn_unique');
            });
        }

        if (!Schema::hasTable('public_complaints')) {
            Schema::create('public_complaints', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('phone', 255);
                $table->string('email', 255);
                $table->enum('type', ['Aduan','Saran']);
                $table->text('description');
                $table->string('complaint_code', 255);
                $table->text('response')->nullable();
                $table->enum('status', ['pending','responded'])->default('pending');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['complaint_code'], 'public_complaints_complaint_code_unique');
            });
        }

        if (!Schema::hasTable('school_classes')) {
            Schema::create('school_classes', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('grade', 255);
                $table->string('academic_year', 255);
                $table->integer('is_active')->default('1');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('school_facilities')) {
            Schema::create('school_facilities', function (Blueprint $table) {
                $table->id();
                $table->string('image', 255);
                $table->text('description');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('school_profiles')) {
            Schema::create('school_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->default('SMP Negeri 6 Sudimoro');
                $table->text('address')->nullable();
                $table->string('city', 255)->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('email', 255)->nullable();
                $table->text('vision')->nullable();
                $table->text('mission')->nullable();
                $table->text('history')->nullable();
                $table->longText('logo')->nullable();
                $table->string('logo_ssn', 255)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('brand_subtitle', 255)->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id', 255);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity');
                $table->index(['user_id'], 'sessions_user_id_index');
                $table->index(['last_activity'], 'sessions_last_activity_index');
            });
        }

        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 255);
                $table->text('value')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['key'], 'settings_key_unique');
            });
        }

        if (!Schema::hasTable('social_media')) {
            Schema::create('social_media', function (Blueprint $table) {
                $table->id();
                $table->string('platform', 255);
                $table->string('icon', 255);
                $table->string('url', 255);
                $table->integer('is_active')->default('1');
                $table->integer('order')->default('0');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_class_id');
                $table->string('name', 255);
                $table->enum('gender', ['male','female']);
                $table->string('nis', 255)->nullable();
                $table->string('enrollment_year');
                $table->integer('is_active')->default('1');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('nisn', 255)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->string('status_lulus', 255)->nullable();
                $table->string('ijazah_file', 255)->nullable();
                $table->string('nama_ayah', 255)->nullable();
                $table->string('nama_ibu', 255)->nullable();
                $table->text('alamat')->nullable();
                $table->index(['school_class_id'], 'students_school_class_id_foreign');
            });
        }

        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('code', 255);
                $table->string('name', 255);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['code'], 'subjects_code_unique');
            });
        }

        if (!Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('nip', 255)->nullable();
                $table->string('position', 255)->nullable();
                $table->string('education', 255)->nullable();
                $table->longText('photo')->nullable();
                $table->text('bio')->nullable();
                $table->integer('is_active')->default('1');
                $table->integer('order')->default('0');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('teaching_modules')) {
            Schema::create('teaching_modules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_year_id');
                $table->unsignedBigInteger('subject_id');
                $table->string('title', 255)->nullable();
                $table->text('description')->nullable();
                $table->string('file_path', 255);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unsignedBigInteger('school_class_id')->nullable();
                $table->index(['academic_year_id'], 'teaching_modules_academic_year_id_foreign');
                $table->index(['subject_id'], 'teaching_modules_subject_id_foreign');
                $table->index(['school_class_id'], 'teaching_modules_school_class_id_foreign');
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('email', 255);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password', 255);
                $table->enum('role', ['admin','admin_komite','teacher','student','library_staff'])->nullable()->default('admin');
                $table->string('remember_token', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['email'], 'users_email_unique');
            });
        }

    }

    public function down(): void
    {
        // Reverse logic (optional, user didn't ask for deletion safe)
    }
};
