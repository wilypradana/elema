<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda E-Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            margin: 0;
        }

        .background {
            position: relative;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://ucarecdn.com/b8ce9be7-ed23-4743-b98c-07e13f1c8d8f/image.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
            opacity: 0.2;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .content-box {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 10px;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
        }

        .toggle-role {
            cursor: pointer;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: #0d6efd;
            text-decoration: underline;
        }

        /* Alert Experimental Styles */
        .experimental-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 16px 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            animation: slideInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 400px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .experimental-alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #ffeaa7);
            background-size: 300% 100%;
            animation: gradientShift 3s ease infinite;
        }

        .alert-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .alert-icon-small {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            animation: pulse 2s ease-in-out infinite;
            flex-shrink: 0;
        }

        .alert-title-small {
            color: #2d3748;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .alert-message-small {
            color: #4a5568;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
        }

        .alert-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            font-size: 18px;
            color: #718096;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .alert-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #2d3748;
        }

        .alert-contact {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .alert-contact-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .alert-contact-link:hover {
            color: #764ba2;
            text-decoration: none;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .alert-hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .experimental-alert {
                top: 10px;
                max-width: 350px;
                padding: 14px 20px;
            }
            
            .alert-title-small {
                font-size: 15px;
            }
            
            .alert-message-small {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <!-- Experimental Alert -->
    <div class="experimental-alert" id="experimentalAlert">
        <button class="alert-close" onclick="closeAlert()">&times;</button>
        <div class="alert-header">
            <div class="alert-icon-small">⚗️</div>
            <h6 class="alert-title-small">Mode Eksperimental</h6>
        </div>
        <p class="alert-message-small">
            Sistem ini sedang dalam tahap pengembangan dan pengujian aktif. 
            Fitur dan fungsionalitas dapat berubah sewaktu-waktu.
        </p>
        <div class="alert-contact">
            <a href="mailto:tkjmu?subject=Feedback%20E-Learning%20Eksperimental&body=Halo,%0A%0ASaya%20ingin%20memberikan%20feedback%20mengenai%20sistem%20e-learning%20yang%20sedang%20dalam%20tahap%20eksperimental:%0A%0A" 
               class="alert-contact-link">
                <i class="fas fa-envelope"></i>
                Hubungi Support
            </a>
        </div>
    </div>

    <div class="background">
        <div class="row text-center w-100 px-3">
            <!-- Informasi Aplikasi -->
            <div class="col-md-8 mb-3 d-flex align-items-center justify-content-center order-1 order-md-1">
                <div class="content-box text-center">
                    <img src="https://ucarecdn.com/32b7ea92-19ac-4651-aafd-fd25068ece2f/image.png" alt="Logo SMK Mulia Buana"
                        style="max-height: 100px; border-radius: 5%;" class="mb-3">
                    <h4>Selamat Datang di E-Learning SMK Mulia Buana</h4>
                    <p class="mt-3">
                        Aplikasi ini dirancang untuk mendukung proses belajar mengajar secara digital.
                        Siswa dapat mengakses materi, tugas, dan kuis online. Guru dapat memberikan soal,
                        mengelola nilai siswa dengan mudah dan efisien.
                    </p>
                </div>
            </div>

            <!-- Card Login Form Toggle -->
            <div class="col-md-4 mb-3 order-2 order-md-2 mx-auto">
                <div class="card p-3">
                    <div id="toggleRole" class="toggle-role text-end">👨‍🏫 Pindah ke Login Guru</div>

                    <!-- Form Siswa -->
                    <form id="siswaForm" class="active" method="POST" action="{{ route('login.siswa') }}">
                        @csrf
                        <h5 class="mb-3">Login Siswa</h5>
                        <!-- Menampilkan pesan error dari session jika ada -->
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <input type="email" class="form-control mb-2" placeholder="Email Siswa" name="email"
                            required>

                        <!-- Password input with toggle eye icon -->
                        <div class="form-group position-relative">
                            <input type="password" id="password" class="form-control mb-2" placeholder="Password"
                                name="password" required>
                            <button type="button" id="togglePassword" class="position-absolute"
                                style="top: 50%; right: 10px; transform: translateY(-50%); background: transparent; border: none; font-size: 18px;">
                                <i class="fas fa-eye"></i> <!-- Default eye icon -->
                            </button>
                        </div>

                        <input type="hidden" name="role" value="siswa">
                        <button type="submit" class="btn btn-success w-100 mt-2">Masuk sebagai Siswa</button>
                    </form>

                    <!-- Form Guru -->
                    <form id="guruForm" class="d-none" method="POST" action="{{ route('login.guru') }}">
                        @csrf
                        <h5 class="mb-3">Login Guru</h5>
                        <!-- Menampilkan pesan error dari session jika ada -->
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <input type="email" class="form-control mb-2" placeholder="Email Guru" name="email"
                            required>

                        <!-- Password input with toggle eye icon -->
                        <div class="form-group position-relative">
                            <input type="password" id="guruPassword" class="form-control mb-2"
                                placeholder="Password Guru" name="password" required>
                            <button type="button" id="toggleGuruPassword" class="position-absolute"
                                style="top: 50%; right: 10px; transform: translateY(-50%); background: transparent; border: none; font-size: 18px;">
                                <i class="fas fa-eye"></i> <!-- Default eye icon -->
                            </button>
                        </div>

                        <input type="hidden" name="role" value="guru">
                        <button type="submit" class="btn btn-primary w-100 mt-2">Masuk sebagai Guru</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle Script -->
    <script>
        // Alert Close Function
        function closeAlert() {
            document.getElementById('experimentalAlert').classList.add('alert-hidden');
        }

        // Auto hide alert after 10 seconds
        setTimeout(() => {
            closeAlert();
        }, 10000);

        const toggleBtn = document.getElementById('toggleRole');
        const siswaForm = document.getElementById('siswaForm');
        const guruForm = document.getElementById('guruForm');

        let isSiswa = true;

        toggleBtn.addEventListener('click', () => {
            isSiswa = !isSiswa;
            siswaForm.classList.toggle('d-none', !isSiswa);
            guruForm.classList.toggle('d-none', isSiswa);
            toggleBtn.textContent = isSiswa ? '👨‍🏫 Pindah ke Login Guru' : '🎓 Pindah ke Login Siswa';
        });

        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            // Toggle the input type between 'password' and 'text'
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;

            // Toggle the eye icon between open and closed
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' :
                '<i class="fas fa-eye-slash"></i>';
        });

        // Toggle password visibility for Guru Form
        const toggleGuruPassword = document.getElementById('toggleGuruPassword');
        const guruPassword = document.getElementById('guruPassword');

        toggleGuruPassword.addEventListener('click', function() {
            // Toggle the input type between 'password' and 'text'
            const type = guruPassword.type === 'password' ? 'text' : 'password';
            guruPassword.type = type;

            // Toggle the eye icon between open and closed
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' :
                '<i class="fas fa-eye-slash"></i>';
        });
    </script>
</body>

</html>