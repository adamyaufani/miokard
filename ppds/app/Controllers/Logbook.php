<?php

namespace App\Controllers;

use App\Models\LogbookModel;
use App\Models\StaseModel;

class Logbook extends BaseController
{
    protected $logbook_model;
    protected $stase_model;

    public function __construct()
    {
        $this->logbook_model = new LogbookModel();
        $this->stase_model = new StaseModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Logbook',
            'page_header' => 'Logbook PPDS',
            'query' => $this->logbook_model->getLogbook()
        ];

        return view('logbook/index', $data);
    }

    public function index_json()
    {
        $data = [
            'title' => 'Logbook',
            'page_header' => 'Logbook PPDS',
            'query' => $this->logbook_model->getLogbook()
        ];

        return view('logbook/index', $data);
    }

    public function index_fiter_json()
    {
        $data = [
            'title' => 'Logbook',
            'page_header' => 'Logbook PPDS',
            'query' => $this->logbook_model->getLogbook()
        ];

        return view('logbook/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Logbook',
            'page_header' => 'Logbook PPDS',
            'validation' => \Config\Services::validation(),
            'stase' => $this->stase_model->getAllStase()
        ];

        return view('logbook/tambah', $data);
    }

    public function post()
    {
        if (!$this->validate([
            'judul' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'judul tugas wajib diisi'
                ]
            ],
            'keterangan' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'keterangan wajib diisi'
                ]
            ],
            'waktu' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'waktu wajib diisi',
                ]
            ],
            'stase' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'stase wajib diisi',
                ]
            ],
            'pasien' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'nama pasien wajib diisi',
                ]
            ],
            'usia' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'usia pasien wajib diisi',
                ]
            ],
            'jenis_kelamin' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'jenis kelamin pasien wajib diisi',
                ]
            ],
            'jenis_tindakan' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'jenis tindakan wajib diisi',
                ]
            ],
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $data = [
            'judul' => $this->request->getVar('judul'),
            'keterangan' => $this->request->getVar('keterangan'),
            'id_ppds' => session('user_id'),
            'waktu' => $this->request->getVar('waktu'),
            'id_stase' => $this->request->getVar('stase'),
            // 'id_spv' => $this->request->getVar('id_spv'),
            'pasien' => $this->request->getVar('pasien'),
            'usia' => $this->request->getVar('usia'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            // 'rekam_medik' => $this->request->getFile('rekam_medik'),
            'jenis_tindakan' => $this->request->getVar('jenis_tindakan'),
        ];
        // dd($data);
        if ($this->logbook_model->insert($data)) {
            return redirect()->to(base_url('/logbook'))->with('success', 'logbook baru berhasil ditambahkan!');
        } else {
            return redirect()->back()->with('warning', 'terjadi kesalahan :(');
        }
    }

    public function detail($id_logbook)
    {
        $data = [
            'title' => 'Detail Logbook',
            'page_header' => 'Detail Logbook',
            'logbook' => $this->logbook_model->detail($id_logbook),
        ];
        // dd($data);
        return view('logbook/detail', $data);
    }

    public function delete($id_logbook)
    {
        $data_logbook = $this->logbook_model->detail($id_logbook);
        if ($data_logbook->id_ppds == session('user_id')) {
            if ($this->logbook_model->delete($id_logbook)) {
                return redirect()->to(base_url('logbook/'))->with('success', 'Logbook berhasil dihapus!');
            }
        } else {
            return redirect()->to(base_url('logbook/'))->with('danger', 'Anda tidak punya hak untuk menghapus file ini!');
        }
    }

    public function edit($id_logbook = 0)
    {
        $data = [
            'title' => 'Logbook',
            'page_header' => 'Edit logbook',
            'validation' => \Config\Services::validation(),
            'logbook' => $this->logbook_model->detail($id_logbook),
        ];

        return view('logbook/edit', $data);
    }

    public function update()
    {
        if (!$this->validate([
            'judul' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'judul tugas wajib diisi'
                ]
            ],
            'keterangan' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'keterangan wajib diisi'
                ]
            ],
            'waktu' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'waktu wajib diisi',
                ]
            ],
            'pasien' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'nama pasien wajib diisi',
                ]
            ],
            'usia' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'usia pasien wajib diisi',
                ]
            ],
            'jenis_kelamin' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'jenis kelamin pasien wajib diisi',
                ]
            ],
            'jenis_tindakan' => [
                'rules' => ['required'],
                'errors' => [
                    'required' => 'jenis tindakan wajib diisi',
                ]
            ],
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $id_logbook = $this->request->getVar('id_logbook');

        $data = [
            'judul' => $this->request->getVar('judul'),
            'keterangan' => $this->request->getVar('keterangan'),
            'id_ppds' => session('user_id'),
            'waktu' => $this->request->getVar('waktu'),
            // 'id_spv' => $this->request->getVar('id_spv'),
            'pasien' => $this->request->getVar('pasien'),
            'usia' => $this->request->getVar('usia'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            // 'rekam_medik' => $this->request->getFile('rekam_medik'),
            'jenis_tindakan' => $this->request->getVar('jenis_tindakan'),
        ];
        // dd($data);
        if ($this->logbook_model->update($id_logbook, $data)) {
            return redirect()->to(base_url('/logbook'))->with('success', 'logbook berhasil diperbarui!');
        } else {
            return redirect()->back()->with('warning', 'terjadi kesalahan :(');
        }
    }
}
