<?php if (!session()->get('logged_in')) {
    return redirect()->to('/login');
} ?>
<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2>Tambah Prediksi Diabetes</h2>
    <form id="riwayatForm" method="post" action="/riwayat/store">
        <input type="hidden" name="pasien_id" value="<?= $pasien_id ?>">

        <div class="mb-3">
            <label for="tinggi" class="form-label">Tinggi Badan (cm)</label>
            <input type="number" class="form-control" id="tinggi" name="tinggi" required>
        </div>
        <div class="mb-3">
            <label for="berat" class="form-label">Berat Badan (kg)</label>
            <input type="number" class="form-control" id="berat" name="berat" required>
        </div>
        <div class="mb-3">
            <label for="gdp" class="form-label">GDP (mg/dL)</label>
            <input type="number" class="form-control" id="gdp" name="gdp" required>
        </div>
        <div class="mb-3">
            <label for="tekanan_darah" class="form-label">Tekanan Darah (mmHg)</label>
            <input type="number" class="form-control" id="tekanan_darah" name="tekanan_darah" required>
        </div>

        <!-- Tombol Prediksi & Simpan -->
        <button type="button" id="predictBtn" class="btn btn-primary">Prediksi</button>
        <button type="submit" id="saveBtn" class="btn btn-success" disabled>Simpan</button>
    </form>

    <!-- Hasil Prediksi -->
    <div id="predictionResult" class="mt-3"></div>
</div>

<script>
    document.getElementById('predictBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('riwayatForm'));
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        fetch('/riwayat/predict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                const resultDiv = document.getElementById('predictionResult');
                resultDiv.innerHTML = ''; // Clear previous results

                if (result.hasil === 1) {
                    resultDiv.innerHTML = `<div class="alert alert-danger" role="alert"><strong>Hasil:</strong> Diabetes</div>`;
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-success" role="alert"><strong>Hasil:</strong> Tidak Diabetes</div>`;
                }

                document.getElementById('saveBtn').disabled = false; // Aktifkan tombol Simpan
            })
            .catch(error => console.error('Error:', error));
    });
</script>

<?= $this->endSection() ?>