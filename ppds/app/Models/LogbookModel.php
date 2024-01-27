<?php

namespace App\Models;

use CodeIgniter\Model;

class LogbookModel extends Model
{
    protected $table = 'log_book';
    protected $allowedFields = [
        'judul',
        'keterangan',
        'id_ppds',
        'id_stase',
        'waktu',
        'pasien',
        'jenis_kelamin',
        'usia',
        'jenis_tindakan'
    ];

    public function getLogbook()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('log_book');

        $builder->select('*,ci_users.nama_lengkap,log_book.id AS id_logbook');
        $builder->join('ci_users', 'ci_users.id = log_book.id_ppds', 'LEFT');
        // $builder->join('stase', 'stase.id = log_book.id_stase', 'LEFT');
        if (session('role') != 1) {
            $builder->where(['log_book.id_ppds' => session('user_id')]);
        }
        $result = $builder->get();
        return $result->getResultArray();

    }

    public function detail($id_logbook)
    {
        $db = \Config\Database::connect();

        $builder = $db->table('log_book');
        $builder->select('*,log_book.id AS id_logbook, log_book.usia AS usia_pasien,log_book.jenis_kelamin AS gender_pasien');
        $builder->join('ci_users', 'ci_users.id = log_book.id_ppds', 'LEFT');
        $builder->where(['log_book.id' => $id_logbook]);
        $result = $builder->get();
        return $result->getRowObject();
    }
}
