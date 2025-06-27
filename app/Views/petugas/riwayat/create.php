<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Tambah Data Prediksi Diabetes</h2>

    </div>

    <form id="riwayatForm" method="post" action="<?= site_url('/petugas/riwayat/store') ?>">
        <?= csrf_field() ?> <input type="hidden" name="pasien_id" value="<?= esc($pasien_id, 'attr') ?>">
        <input type="hidden" name="hasil_prediksi_value" id="hasil_prediksi_value_input">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="tinggi" class="form-label">Tinggi Badan (cm)</label>
                <input type="number" class="form-control <?= (session('errors.tinggi')) ? 'is-invalid' : '' ?>" id="tinggi" name="tinggi" value="<?= old('tinggi') ?>" placeholder="Contoh: 170" min="1" required>
                <?php if (session('errors.tinggi')) : ?>
                    <div class="invalid-feedback"><?= session('errors.tinggi') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="berat" class="form-label">Berat Badan (kg)</label>
                <input type="number" step="0.1" class="form-control <?= (session('errors.berat')) ? 'is-invalid' : '' ?>" id="berat" name="berat" value="<?= old('berat') ?>" placeholder="Contoh: 65.5" min="1" required>
                <?php if (session('errors.berat')) : ?>
                    <div class="invalid-feedback"><?= session('errors.berat') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="gdp" class="form-label">Gula Darah Puasa (GDP) (mg/dL)</label>
                <input type="number" class="form-control <?= (session('errors.gdp')) ? 'is-invalid' : '' ?>" id="gdp" name="gdp" value="<?= old('gdp') ?>" placeholder="Contoh: 90" min="1" required>
                <?php if (session('errors.gdp')) : ?>
                    <div class="invalid-feedback"><?= session('errors.gdp') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="tekanan_darah" class="form-label">Tekanan Darah Sistolik (mmHg)</label>
                <input type="number" class="form-control <?= (session('errors.tekanan_darah')) ? 'is-invalid' : '' ?>" id="tekanan_darah" name="tekanan_darah" value="<?= old('tekanan_darah') ?>" placeholder="Contoh: 120" min="1" required>
                <?php if (session('errors.tekanan_darah')) : ?>
                    <div class="invalid-feedback"><?= session('errors.tekanan_darah') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 d-flex flex-column flex-sm-row justify-content-sm-end align-items-stretch">
            <a href="javascript:history.back()" class="btn btn-outline-secondary me-2 mb-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button type="button" id="predictBtn" class="btn btn-primary mb-2 me-2">
                <i class="fas fa-magic me-2"></i>Prediksi
            </button>
            <button type="submit" id="saveBtn" class="btn btn-success mb-2 me-2" disabled>
                <i class="fas fa-save me-2"></i>Simpan
            </button>
        </div>
    </form>

    <div id="predictionResultArea" class="mt-4" style="display: none;">
        <div id="predictionResultContent">
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const predictBtn = document.getElementById('predictBtn');
        const saveBtn = document.getElementById('saveBtn');
        const riwayatForm = document.getElementById('riwayatForm');
        const predictionResultArea = document.getElementById('predictionResultArea');
        const predictionResultContentDiv = document.getElementById('predictionResultContent');
        const hasilPrediksiValueInput = document.getElementById('hasil_prediksi_value_input');

        predictBtn.addEventListener('click', function() {
            // Validasi form sederhana sebelum fetch (opsional, karena server-side tetap utama)
            if (!riwayatForm.checkValidity()) {
                riwayatForm.reportValidity(); // Tampilkan pesan validasi HTML5 bawaan
                return;
            }

            const formData = new FormData(riwayatForm);
            const data = {};
            formData.forEach((value, key) => {
                // Mengembalikan cara data dikumpulkan seperti skrip asli Anda:
                // semua nilai dari FormData akan menjadi string.
                data[key] = value;
            });

            // UI Update: Tombol Prediksi
            predictBtn.disabled = true;
            predictBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memprediksi...';
            predictionResultArea.style.display = 'none'; // Sembunyikan hasil lama
            saveBtn.disabled = true; // Nonaktifkan tombol simpan selama prediksi baru

            fetch('<?= site_url('/petugas/riwayat/predict') ?>', {
                    method: 'POST',
                    headers: {
                        // Hanya header yang ada di skrip asli Anda
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data) // Kirim data sebagai JSON string
                })
                .then(response => {
                    if (!response.ok) {
                        // Mencoba mendapatkan detail error jika response adalah JSON
                        return response.json().then(errData => {
                            throw {
                                status: response.status,
                                data: errData
                            };
                        }).catch(() => {
                            // Jika response bukan JSON atau error parsing, buat error umum
                            throw {
                                status: response.status,
                                data: {
                                    message: `Error ${response.status}: ${response.statusText}`
                                }
                            };
                        });
                    }
                    return response.json();
                })
                .then(result => {
                    predictionResultArea.style.display = 'block';
                    predictionResultContentDiv.innerHTML = ''; // Clear previous results

                    let resultMessageHTML = '';
                    let alertClass = '';
                    let iconClass = '';
                    let predictionValueForInput = null; // Default null

                    // Sesuaikan 'result.hasil' dengan key yang benar dari response JSON backend Anda.
                    // Skrip asli Anda menggunakan 'result.hasil'.
                    if (result.hasOwnProperty('hasil')) { // Pastikan properti 'hasil' ada
                        if (result.hasil === 1) {
                            resultMessageHTML = '<strong>Hasil Prediksi:</strong> <span class="fw-bold text-danger">Berpotensi Diabetes</span>';
                            alertClass = 'alert-danger';
                            iconClass = 'fa-triangle-exclamation';
                            predictionValueForInput = 1;
                        } else if (result.hasil === 0) {
                            resultMessageHTML = '<strong>Hasil Prediksi:</strong> <span class="fw-bold text-success">Tidak Berpotensi Diabetes</span>';
                            alertClass = 'alert-success';
                            iconClass = 'fa-check-circle';
                            predictionValueForInput = 0;
                        } else {
                            resultMessageHTML = '<strong>Hasil Prediksi:</strong> Respons tidak dikenali.';
                            alertClass = 'alert-warning';
                            iconClass = 'fa-question-circle';
                        }
                    } else {
                        resultMessageHTML = '<strong>Hasil Prediksi:</strong> Format respons dari server tidak sesuai.';
                        alertClass = 'alert-warning';
                        iconClass = 'fa-exclamation-circle';
                    }

                    predictionResultContentDiv.innerHTML = `
                        <div class="alert ${alertClass} d-flex align-items-center" role="alert">
                            <i class="fas ${iconClass} fa-2x me-3"></i>
                            <div>${resultMessageHTML}</div>
                        </div>`;

                    if (predictionValueForInput !== null) {
                        hasilPrediksiValueInput.value = predictionValueForInput;
                        saveBtn.disabled = false; // Aktifkan tombol Simpan jika hasil valid
                    } else {
                        hasilPrediksiValueInput.value = ''; // Kosongkan jika hasil tidak valid
                        saveBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    predictionResultArea.style.display = 'block';
                    let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (error && error.data && error.data.message) {
                        errorMessage = error.data.message;
                    } else if (error && error.message) {
                        errorMessage = error.message;
                    }

                    predictionResultContentDiv.innerHTML = `
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-arrow-left-circle fa-2x me-3"></i>
                            <div><strong>Gagal Memprediksi:</strong> ${errorMessage}</div>
                        </div>`;
                    saveBtn.disabled = true;
                    hasilPrediksiValueInput.value = '';
                })
                .finally(() => {
                    // UI Update: Kembalikan tombol Prediksi ke state normal
                    predictBtn.disabled = false;
                    predictBtn.innerHTML = '<i class="fas fa-magic me-2"></i>Prediksi';
                });
        });

        // Mencegah submit form utama jika tombol Simpan masih disabled atau belum ada hasil
        riwayatForm.addEventListener('submit', function(event) {
            if (saveBtn.disabled || hasilPrediksiValueInput.value === '') {
                event.preventDefault();
                // Tampilkan alert atau pesan lain jika diperlukan
                const alertPlaceholder = document.createElement('div');
                alertPlaceholder.innerHTML = `
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        Harap lakukan prediksi dan dapatkan hasil yang valid sebelum menyimpan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                // Sisipkan di atas tombol atau di tempat yang sesuai
                // Misalnya, di atas elemen dengan id predictionResultArea
                if (predictionResultArea.style.display === 'none' || hasilPrediksiValueInput.value === '') {
                    predictionResultArea.parentNode.insertBefore(alertPlaceholder, predictionResultArea);
                } else {
                    predictionResultArea.insertBefore(alertPlaceholder, predictionResultArea.firstChild);
                }

                // Hapus alert setelah beberapa detik
                setTimeout(() => {
                    const activeAlert = document.querySelector('.alert-dismissible');
                    if (activeAlert) {
                        bootstrap.Alert.getOrCreateInstance(activeAlert).close();
                    }
                }, 5000);
            }
        });
    });
</script>
<?= $this->endSection() ?>