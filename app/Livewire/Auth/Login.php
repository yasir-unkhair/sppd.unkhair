<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $username;
    public $password;
    public $tahun;
    public $bool_peserta = 0;

    public function render()
    {
        $data = [
            'judul' => 'Login Aplikasi'
        ];

        return view('livewire.auth.login2', $data)
            ->extends('layouts.auth2')
            ->section('content');
    }

    public function checklogin()
    {
        // sleep(3);
        $this->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
            'tahun' => 'required'
        ]);

        // dd($this);
        $login = User::where(['username' => $this->username])->first();

        $err = 0;

        if (!$login) {
            $err++;
            flash('error', 'Username ' . $this->username . ' tidak di kenali, silahkan perika kembali!!');
            return $this->redirect(route('auth.login'));
        } else {
            $password = password_verify($this->password, $login->password);
            // dd($password);
            if (!$password) {
                flash('error', 'Password anda salah, silahkan ketik kembali!!');
                return $this->redirect(route('auth.login'));
            }

            if (!$login->is_active) {
                Auth::logout();
                flash('error', 'Akun anda sedang dinonaktifkan!!');
                return $this->redirect(route('auth.login'));
            }

            // if (!$login->hasRole(['developper', 'admin'])) {
            //     flash('error', 'Username ' . $this->username . ' tidak di kenali, silahkan perika kembali!!');
            //     return $this->redirect(route('auth.login'));
            // }
        }

        if (!$err) {
            Auth::login($login);
            $role = $login->roles()->count();

            if ($role == 1) {
                $role = '';
                foreach ($login->roles()->get() as $r) {
                    $role = $r->name;
                }
                session()->put([
                    'role' => $role,
                    'tahun' => $this->tahun
                ]);

                alert()->success('Success', 'Berhasil Login, Selamat datang ' . $login->name);

                if (in_array($role, ['keuangan', 'kepegawaian'])) {
                    return $this->redirect(route($role . '.dashboard'));
                }

                return $this->redirect(route('admin.dashboard'));
            } else {
                session()->put([
                    'role' => NULL,
                    'tahun' => $this->tahun
                ]);
                alert()->success('Success', 'Berhasil Login, Selamat datang ' . $login->name);
                return $this->redirect(route('admin.dashboard'));
            }
        }
    }
}
